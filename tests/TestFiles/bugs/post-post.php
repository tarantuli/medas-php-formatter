<?php

declare(strict_types=1);

namespace App\Entity\Project\Post;

use ApiPlatform\Metadata\{ApiResource, Delete, Get, GetCollection, Patch, Post};
use App\Entity\Project\Post\Post as PostPost;
use App\State\PostProcessor;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\{Column, Entity};

#[ApiResource]
#[Get, GetCollection, Patch, Post(processor: PostProcessor::class), Delete]
#[Entity]
class TextPost extends PostPost
{
    // Rich text (HTML); this used to be a VARCHAR(255), see migration Version20261005120000
    #[Column(type: Types::TEXT, length: 65535)]
    public string $text = '';
}
