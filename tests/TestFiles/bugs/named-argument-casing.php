<?php

declare(strict_types=1);

namespace App\Entity\Product;

use ApiPlatform\Metadata\{ApiProperty, ApiResource, Post};
use ApiPlatform\OpenApi\Model\{Operation, RequestBody};
use App\State\ProductImportProcessor;
use Doctrine\ORM\Mapping\{Column, Entity, GeneratedValue, Id};
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Attribute\{Uploadable, UploadableField};

#[Uploadable]
#[ApiResource(operations: [
    new Post(processor: ProductImportProcessor::class,
    deserialize: false,
    openapi: new Operation(requestBody: new RequestBody(content: new \ArrayObject(['multipart/form-data' => ['schema' => ['type' => 'object',
    'properties' => ['file' => ['type' => 'string',
    'format' => 'binary',
    ]
    ,
    ]
    ,
    ]
    ,
    ]
    ,
    ]
    )))),
])]
#[Entity]
class ProductImport
{
    #[Id, GeneratedValue, Column]
    public int $id;

    #[ApiProperty(types: ['https://schema.org/contentUrl'])]
    public string|null $contentUrl = null;

    #[UploadableField(mapping: 'product_import', fileNameProperty: 'filePath')]
    public File $file;

    #[Column(nullable: true)]
    public string|null $filePath = null;

    #[Column]
    public \DateTime $dateTime;

    #[Column(length: 10000)]
    public string $results;

    public function __construct()
    {
    }
}
