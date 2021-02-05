<?php

declare(strict_types=1);

namespace App\Model\Font\UseCase\Files\Remove;

use App\Model\Flusher;
use App\Model\Font\Entity\FontRepository;
use App\Model\Font\Entity\Id;
use App\Model\Font\Entity\File\Id as FileId;
use App\Model\Font\Service\File\FileManager;

class Handler
{
    private $fonts;
    private $flusher;
    private $fileManager;

    public function __construct(FontRepository $fonts, Flusher $flusher, FileManager $fileManager)
    {
        $this->fonts = $fonts;
        $this->flusher = $flusher;
        $this->fileManager = $fileManager;
    }

    public function handle(Command $command): void
    {
        $font = $this->fonts->get(new Id($command->id));
        $file = $font->getFile(new FileId($command->file));

        $font->removeFile(new \DateTimeImmutable(), $file->getId());
        $font->associateFaces();

        $this->flusher->flush();

        $this->fileManager->remove(
            $file->getInfo()->getPath(),
            $file->getInfo()->getName(),
            $file->getInfo()->getExt()
        );
    }
}

