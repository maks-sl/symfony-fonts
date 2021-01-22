<?php

declare(strict_types=1);

namespace App\ReadModel\User;

use App\ReadModel\FromArrayTrait;

/**
 * @method static $this fromArray(array $array)
 */
class ShortView
{
    use FromArrayTrait;

    public $id;
    public $email;
    public $role;
    public $status;
}
