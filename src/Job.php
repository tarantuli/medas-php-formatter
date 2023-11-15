<?php

declare(strict_types=1);

namespace Medas\PhpFormatter;

use Medas\PhpTokenizer\{TokenCollection, TokenTree};

class Job
{
    public TokenCollection $tokens;
    public TokenTree $tree;

    public function __construct(
        public readonly Settings\Settings $settings,
    )
    {
    }
}
