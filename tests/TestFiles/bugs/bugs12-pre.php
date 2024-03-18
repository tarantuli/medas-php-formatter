<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\Invoice\{Invoice, InvoiceFileContent, InvoiceType};
use App\Entity\Project\Post\PostTarget;
use App\Entity\TextFragments\FragmentTag;
use App\Exceptions\DateIsEmpty;

class InvoiceFileContentCreator extends BaseFileContentCreator
{
    private Invoice $invoice;
    private int $totalCost;

    public function getFileContent(Invoice $invoice): InvoiceFileContent
    {
        if ($invoice->date === null) {
            throw new DateIsEmpty('De factuurdatum');
        }

        $this->invoice = $invoice;
        $this->totalCost = 0;

        $html = file_get_contents($this->projectDirectory . '/assets/invoice-template.html');
        $html = $this->replaceVariables($html, [
            'style' => $this->getStyle(),
            'logoUri' => $this->getLogoUri(),
            'address' => $this->getAddress($invoice->project->customer),
            'invoiceNumber' => $invoice->getNumber(),
            'invoiceDate' => $this->toNlDate($invoice->date),
            'quotationReference' => $this->getQuotationReference(),
            'posts' => $this->getPosts($invoice->project, $this->getPostTarget()),
            'signOff' => $this->getTaggedText(FragmentTag::INVOICE_SIGNOFF),
            'footer' => $this->getTaggedText(FragmentTag::DOCUMENT_FOOTER),
        ]);

        return new InvoiceFileContent($html);
    }

    private function getQuotationReference(): string
    {
        if (!$this->invoice->quotationVersion) {
            return '';
        }

        return sprintf('    <tr>
        <td>Offertenummer</td>
        <td>%s</td>
    </tr>', $this->invoice->quotationVersion->getNumber());
    }

    private function getPostTarget(): PostTarget
    {
        $targetId = match ($this->invoice->invoiceType->id) {
            InvoiceType::TOTAL_INVOICE => PostTarget::TOTAL_INVOICE,
            InvoiceType::MATERIAL_INVOICE => PostTarget::MATERIAL_INVOICE,
            InvoiceType::REMAINDER_INVOICE => PostTarget::REMAINDER_INVOICE,
            default => throw new \Exception('no post target mapped for invoice type ' . $this->invoice->invoiceType->id),
        };

        return $this->entityManager->find(PostTarget::class, $targetId);
    }
}
