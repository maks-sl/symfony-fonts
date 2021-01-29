<?php

declare(strict_types=1);

namespace App\Tests\Functional\Users;

use App\Model\User\Entity\Email;
use App\Model\User\Entity\Id;
use App\Model\User\Entity\Name;
use App\Tests\Builder\User\UserBuilder;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UsersFixture extends Fixture
{
    public const DISPLAYED_USER_ID = '00000000-0000-0000-0000-000000000001';
    public const EXISTING_USER_EMAIL = 'exesting-user@app.loc';

    public function load(ObjectManager $manager): void
    {
        $existing = (new UserBuilder())
            ->withName(new Name('Existing', 'User'))
            ->withCredentials(new Email(self::EXISTING_USER_EMAIL), 'hash')
            ->confirmed()->build();

        $manager->persist($existing);

        $displayed = (new UserBuilder())
            ->withCredentials(new Email('displayed-user@app.loc'), 'hash')
            ->withName(new Name('Displayed', 'User'))
            ->withId(new Id(self::DISPLAYED_USER_ID))
            ->confirmed()
            ->build();

        $manager->persist($displayed);

        $manager->flush();
    }
}
