<?php

declare(strict_types=1);

namespace App\ReadModel\User;

use App\ReadModel\FromArrayTrait;

/**
 * @method static $this fromArray(array $array)
 */
class AuthView
{
    use FromArrayTrait;

    public $id;
    public $email;
    public $password_hash;
    public $name;
    public $role;
    public $status;
}
