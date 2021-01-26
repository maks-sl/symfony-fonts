<?php

declare(strict_types=1);

namespace App\Model\User\Entity;

use App\Model\EntityNotFoundException;
use Doctrine\ORM\EntityManagerInterface;

/**
 * This is repository to use from handlers only.
 * Works through Doctrine ORM.
 */
class UserRepository
{
    private $em;
    /**
     * @var \Doctrine\ORM\EntityRepository
     */
    private $repo;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->repo = $em->getRepository(User::class);
    }

    public function get(Id $id): User
    {
        /** @var User $user */
        if (!$user = $this->repo->find($id->getValue())) {
            throw new EntityNotFoundException('User is not found.');
        }
        return $user;
    }

    /**
     * @param string $token
     * @return User|object|null
     */
    public function findByConfirmToken(string $token): ?User
    {
        return $this->repo->findOneBy(['confirmToken' => $token]);
    }

    public function hasByEmail(Email $email): bool
    {
        return $this->repo->count(['email' => $email->getValue()]) > 0;
    }

    public function add(User $user): void
    {
        $this->em->persist($user);
    }
}
