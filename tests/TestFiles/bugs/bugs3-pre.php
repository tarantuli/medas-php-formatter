<?php

namespace Shared\Beautifiers\Html;

use tidy;

class DocToFragmentx
{
    public function go(): string
    {
        $this->tidy();
        $this->stripAttributes();
        $this->replaceImgTags();
        $this->replaceNbsp();
        $this->replaceEmptyParagraphs();

        return $this->doc;
    }

    private function tidy(): void
    {
        if (!class_exists(tidy::class)) {
            return;
        }
    }
}
