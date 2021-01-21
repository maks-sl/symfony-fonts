<?php

declare(strict_types=1);

namespace App\ReadModel\User;

use Doctrine\DBAL\Connection;

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
}