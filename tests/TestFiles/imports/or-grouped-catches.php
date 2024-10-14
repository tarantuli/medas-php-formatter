<?php

declare(strict_types=1);

namespace PhonoScripts\Backend\Controllers;

use Medas\DutchTextToIpa\Exceptions\{CannotPhonemizeWord, CannotSyllabilizeWord};
use Medas\HttpRequestHandler\{Authorization\PublicResource, ResponseTypes\Response};
use Medas\Routing\{Methods\Post, Parameters\Constant, Route};

#[Route([new Constant('phono-scripts'), new Constant('public')])]
readonly class ConvertInput
{
    #[Post, PublicResource]
    public function post(): Response
    {
        do {
            if (preg_match('/^(\W+)/u', $input, $match)) {
                if ($target === 'images') {
                    $html .= str_replace(' ', ' ', $match[1]);
                }
                else {
                    $html .= $match[1];
                }

                if (str_contains($match[1], "\n")) {
                    $html .= '<p style="' . $pStyle . '">';
                }

                $input = substr($input, strlen($match[1]));
            }

            if (preg_match('/^(\w+([\'-]\w+)?-?)/u', $input, $match)) {
                $word = $match[1];
                $input = substr($input, strlen($match[1]));

                try {
                    switch ($target) {
                        case 'images':
                            $imageSrc = $this->imageManager->get($word, 24);

                            $html .= sprintf(
                                '<img src="%s" alt="%s" title="%s">',
                                $imageSrc,
                                $word,
                                $word
                            );

                            break;

                        case 'phonemes':
                            $value = $this->phonemizer->phonemize(mb_strtolower($word))->phonemes;
                            $html .= $this->ucFirst($word, $value);

                            break;

                        case 'diacritical-script':
                            $value = $this->diacriticalScriptConverter->convert($word);
                            $html .= $this->ucFirst($word, $value);

                            break;

                        case 'ascii-script':
                            $value = $this->asciiScriptConverter->convert($word);
                            $html .= $this->ucFirst($word, $value);

                            break;
                    }
                }
                catch (CannotSyllabilizeWord|CannotPhonemizeWord) {
                    $html .= '<i>' . $word . '</i>';
                }
            }
        } while ($input !== '');
    }
}
