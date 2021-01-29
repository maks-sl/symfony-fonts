<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Model\User\Entity\Email;
use App\Model\User\Entity\Role;
use App\Model\User\Service\PasswordHasher;
use App\Tests\Builder\User\UserBuilder;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AuthFixture extends Fixture
{
    private $hasher;

    public function __construct(PasswordHasher $hasher)
    {
        $this->hasher = $hasher;
    }

    public static function userCredentials(): array
    {
        return [
            'PHP_AUTH_USER' => 'auth-user@app.loc',
            'PHP_AUTH_PW' => 'password',
        ];
    }

    public static function adminCredentials(): array
    {
        return [
            'PHP_AUTH_USER' => 'auth-admin@app.loc',
            'PHP_AUTH_PW' => 'password',
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $hash = $this->hasher->hash('password');

        $user = (new UserBuilder())
            ->withCredentials(new Email('auth-user@app.loc'), $hash)
            ->confirmed()
            ->build();
        $manager->persist($user);

        $admin = (new UserBuilder())
            ->withCredentials(new Email('auth-admin@app.loc'), $hash)
            ->confirmed()
            ->withRole(Role::admin())
            ->build();
        $manager->persist($admin);

        $manager->flush();
    }
}
