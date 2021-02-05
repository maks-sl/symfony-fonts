<?php

declare(strict_types=1);

namespace App\ReadModel\Font;

use App\Model\Font\Entity\Font;
use App\ReadModel\Font\Filter\Data as FilterData;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * This is repository to use from controllers, console commands, etc., where speed up is required.
 * Works through Doctrine DBAL and return light-weight DTO objects in most cases.
 */
class FontFetcher
{
    private $connection;
    private $paginator;
    private $repository;

    public function __construct(Connection $connection, EntityManagerInterface $em, PaginatorInterface $paginator)
    {
        $this->connection = $connection;
        $this->paginator = $paginator;
        $this->repository = $em->getRepository(Font::class);
    }

    /**
     * @param FilterData $data
     * @param int $page
     * @param int $size
     * @param ?string $sort
     * @param ?string $direction
     * @return PaginationInterface
     */
    public function all(FilterData $data, int $page, int $size, ?string $sort, ?string $direction): PaginationInterface
    {
        if (!\in_array($sort, [null, 'f.date', 'f.slug', 'f.name', 'f.status', 'faces_count'], true)) {
            throw new \UnexpectedValueException('Cannot sort by ' . $sort);
        }

        $qb = $this->connection->createQueryBuilder()
            ->select(
                'f.id',
                'f.date',
                'f.slug',
                'f.name',
                'f.status',
                '(SELECT COUNT(*) FROM font_faces face WHERE face.font_id = f.id) AS faces_count'
            )
            ->from('font_fonts', 'f');

        if ($data->slug) {
            $qb->andWhere($qb->expr()->like('LOWER(f.slug)', ':slug'));
            $qb->setParameter(':slug', '%' . mb_strtolower($data->slug) . '%');
        }

        if ($data->name) {
            $qb->andWhere($qb->expr()->like('LOWER(f.name)', ':name'));
            $qb->setParameter(':name', '%' . mb_strtolower($data->name) . '%');
        }

        if ($data->status) {
            $qb->andWhere('f.status = :status');
            $qb->setParameter(':status', $data->status);
        }

        if (!$sort) {
            $sort = 'date';
            $direction = $direction ?: 'desc';
        } else {
            $direction = $direction ?: 'asc';
        }

        $qb->orderBy($sort, $direction);

        return $this->paginator->paginate($qb, $page, $size);

    }

}
