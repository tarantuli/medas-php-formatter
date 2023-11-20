<?php

declare(strict_types=1);

use Embedded\MyException;

try {
    // Do nothing
}
catch (MyException) {
    // Do nothing again
}
catch (Exception $exception) {
    // Again...
}
