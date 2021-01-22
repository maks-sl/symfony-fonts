<?php

declare(strict_types=1);

namespace App\ReadModel\User;

use Doctrine\DBAL\Connection;

/**
 * This is repository to use from controllers, console commands, etc., where speed up is required.
 * Works through Doctrine DBAL and return light-weight DTO objects in most cases.
 */
class UserFetcher
{
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function findForAuthByEmail(string $email): ?AuthView
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'email',
                'password_hash',
                'TRIM(CONCAT(name_first, \' \', name_last)) AS name',
                'role',
                'status'
            )
            ->from('user_users')
            ->where('email = :email')
            ->setParameter(':email', $email)
            ->execute();

        if (!$result = $stmt->fetchAssociative()) {
            return null;
        }
        return AuthView::fromArray($result);
    }

    public function findByEmail(string $email): ?ShortView
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'email',
                'role',
                'status'
            )
            ->from('user_users')
            ->where('email = :email')
            ->setParameter(':email', $email)
            ->execute();

        if (!$result = $stmt->fetchAssociative()) {
            return null;
        }
        return ShortView::fromArray($result);
    }

    public function hasByEmail(string $email): bool
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select('COUNT (*)')
            ->from('user_users')
            ->where('email = :email')
            ->setParameter(':email', $email)
            ->execute();

        return $stmt->fetchOne() > 0;
    }
}