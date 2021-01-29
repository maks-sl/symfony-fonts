<?php

declare(strict_types=1);

namespace App\Tests\Functional\Auth;

use App\Model\User\Entity\Email;
use App\Model\User\Service\PasswordHasher;
use App\Tests\Builder\User\UserBuilder;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AuthFixture extends Fixture
{
    public const WAIT_USER_CONFIRM_TOKEN = 'test-confirm-token';
    public const WAIT_USER_EMAIL = 'auth-wait@app.loc';
    public const CONFIRMED_USER_EMAIL = 'auth-confirmed@app.loc';

    private $hasher;

    public function __construct(PasswordHasher $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $hash = $this->hasher->hash('password');

        $wait = (new UserBuilder())
            ->withCredentials(new Email(self::WAIT_USER_EMAIL), $hash)
            ->withToken(self::WAIT_USER_CONFIRM_TOKEN)
            ->build();
        $manager->persist($wait);

        $confirmed = (new UserBuilder())
            ->withCredentials(new Email(self::CONFIRMED_USER_EMAIL), $hash)
            ->confirmed()
            ->build();
        $manager->persist($confirmed);

        $manager->flush();
    }
}
