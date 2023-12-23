<?php

namespace Shared\Authentication;

use Shared\Databases\Filter;

/**
 * This class represents an account
 */
class AccountBug1 extends AutoClasses\AbstractAccountAuto implements Interfaces\AccountInterface
{
    // Tables
    public const LOGON_PROPERTIES_TABLE = 'accountLogonProperties';
    public const SETTINGS_TABLE = 'accountSettings';

    public static function getReadableEntitiesForCaller(): array
    {
        return Filter::isEqual('ID', Caller::getAccountId());
    }
}
