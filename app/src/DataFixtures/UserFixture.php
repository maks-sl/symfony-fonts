<?php

namespace App\DataFixtures;

use App\Model\User\Entity\Email;
use App\Model\User\Entity\Id;
use App\Model\User\Entity\Role;
use App\Model\User\Entity\User;
use App\Model\User\Entity\Name;
use App\Model\User\Service\PasswordHasher;
use App\Model\User\Service\Tokenizer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixture extends Fixture
{
    private $hasher;
    private $tokenizer;

    public function __construct(PasswordHasher $hasher, Tokenizer $tokenizer)
    {
        $this->hasher = $hasher;
        $this->tokenizer = $tokenizer;
    }

    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 3; $i++) {
            $user = User::create(
                Id::next(),
                new \DateTimeImmutable(),
                new Email('user'.$i.'@app.loc'),
                $this->hasher->hash('password'),
                new Name('User'.$i, 'User')
            );
            $manager->persist($user);
        }

        $waitUser = User::signUpRequest(
            Id::next(),
            new \DateTimeImmutable(),
            new Email('wait@app.loc'),
            $this->hasher->hash('password'),
            new Name('Wait', 'Wait'),
            'token'
        );
        $manager->persist($waitUser);

        $admin = User::create(
            Id::next(),
            new \DateTimeImmutable(),
            new Email('admin@app.loc'),
            $this->hasher->hash('password'),
            new Name('Admin', 'Admin')
        );
        $admin->changeRole(new Role(Role::ADMIN));
        $manager->persist($admin);

        $manager->flush();
    }
}
