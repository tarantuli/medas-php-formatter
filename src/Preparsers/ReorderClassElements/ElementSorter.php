<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Preparsers\ReorderClassElements;

use Medas\Core\Attributes\Service;

#[Service]
readonly class ElementSorter
{
    public function sort(ReorderingJob $job): void
    {
        $this->sortByKey($job);
        $this->moveNonPublicMethods($job);
    }

    private function sortByKey(ReorderingJob $job): void
    {
        usort($job->elements, fn(Element $a, Element $b) => $a->sortingKey <=> $b->sortingKey);
    }

    private function moveNonPublicMethods(ReorderingJob $job): void
    {
        $this->splitElements($job);

        whileTrue(fn() => $this->insertElement($job));
    }

    private function splitElements(ReorderingJob $job): void
    {
        foreach ($job->elements as $index => $element) {
            if ($element->isMethod === Properties::IS_NOT_METHOD) {
                continue;
            }

            if ($element->isStatic === Properties::IS_STATIC) {
                continue;
            }

            if ($element->isAbstract === Properties::IS_ABSTRACT) {
                continue;
            }

            if ($element->isMagicMethod !== Properties::IS_NOT_MAGIC_METHOD) {
                continue;
            }

            if ($element->accessModifier === Properties::IS_PUBLIC) {
                continue;
            }

            $job->unsortedElements[$element->name] = $element;

            unset($job->elements[$index]);
        }
    }

    private function insertElement(ReorderingJob $job): bool
    {
        if (null === $elementToInsert = $this->findElementToInsert($job)) {
            return false;
        }

        $insertAfterIndex = $this->findIndexToInsertAfter($job, $elementToInsert);

        $job->elements = array_merge(
            array_slice($job->elements, 0, $insertAfterIndex + 1),
            [$elementToInsert],
            array_slice($job->elements, $insertAfterIndex + 1)
        );

        return true;
    }

    private function findElementToInsert(ReorderingJob $job): Element|null
    {
        // Find the last non-inserted element that's referenced in the inserted elements
        foreach (array_reverse($job->elements) as $element) {
            foreach (array_reverse($element->methodReferences) as $reference) {
                if (array_key_exists($reference, $job->unsortedElements)) {
                    $unsortedElement = $job->unsortedElements[$reference];

                    unset($job->unsortedElements[$reference]);

                    return $unsortedElement;
                }
            }
        }

        // Find other non-referenced elements
        foreach ($job->unsortedElements as $reference => $unsortedElement) {
            unset($job->unsortedElements[$reference]);

            return $unsortedElement;
        }

        return null;
    }

    private function findIndexToInsertAfter(ReorderingJob $job, Element $elementToInsert): int|null
    {
        // Find the *last* currently inserted element that references this element
        foreach (array_reverse($job->elements, true) as $index => $element) {
            if (in_array($elementToInsert->name, $element->methodReferences)) {
                return $index;
            }
        }

        // It's not referenced, add it at the end
        return count($job->elements) - 1;
    }
}
