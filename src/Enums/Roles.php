<?php

namespace Xguard\Tasklist\Enums;

use MyCLabs\Enum\Enum;

/**
 * Class Roles
 * @package Xguard\Tasklist\Enums
 *
 * @method static Roles ADMIN()
 * @method static Roles EMPLOYEE();
 */
class Roles extends Enum
{
    private const ADMIN = 'admin';
    private const EMPLOYEE = 'employee';
}
