<?php

declare(strict_types=1);

namespace App\Model\Font\Entity;

use App\Model\EntityNotFoundException;
use Doctrine\ORM\EntityManagerInterface;

/**
 * This is repository to use from handlers only.
 * Works through Doctrine ORM.
 */
class FontRepository
{
    private $em;
    private $repo;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->repo = $em->getRepository(Font::class);
    }

    public function get(Id $id): Font
    {
        /** @var Font $font */
        if (!$font = $this->repo->find($id->getValue())) {
            throw new EntityNotFoundException('Font is not found.');
        }
        return $font;
    }

    public function add(Font $font): void
    {
        $this->em->persist($font);
    }

    public function remove(Font $font): void
    {
        $this->em->remove($font);
    }

}
