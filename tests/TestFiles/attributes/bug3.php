<?php

declare(strict_types=1);

use ApiPlatform\Metadata\ApiResource;

/**
 * There should be no space between the closing square bracket and the comma in  "'ASC'],"
 */
#[ApiResource(order: ['name' => 'ASC'], paginationClientItemsPerPage: true, paginationEnabled: true)]
class Customer
{
}
