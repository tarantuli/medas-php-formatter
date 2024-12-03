<?php

declare(strict_types=1);

use Medas\HttpRequestHandler\Attributes\BodyArgument;
use Medas\RestRequestHandler\Responses\SuccessResponse;
use Medas\Routing\{Methods\Post, Parameters\Constant};
use PhonoScripts\Backend\Dictionary\Word;

class Test
{
    #[Post(new Constant('approveWord'))]
    public function approveWord(
        #[BodyArgument('word.id')]
        int    $id,

        #[BodyArgument('word.approvedPhonemes')]
        string $approvedPhonemes
    ): SuccessResponse
    {
        /** @var Word $word */
        $word = em()->get($this->entityClass, $id);

        $word->isProcessed = true;
        $word->approvedPhonemes = $approvedPhonemes;

        em()->flush();

        return new SuccessResponse(true);
    }
}
