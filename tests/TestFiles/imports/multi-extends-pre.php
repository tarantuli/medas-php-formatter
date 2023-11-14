<?php

namespace Shared\Collections\Interfaces;

use ArrayAccess;
use Countable;
use Iterator;

interface CollectionInterface extends Iterator, ArrayAccess, Countable
{
}
