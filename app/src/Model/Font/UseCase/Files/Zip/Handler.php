<?php

declare(strict_types=1);

namespace App\Model\Font\UseCase\Files\Zip;

use App\Model\Flusher;
use App\Model\Font\Entity\FontRepository;
use App\Model\Font\Entity\Id;
use App\Model\Font\Entity\File\Id as FileId;
use App\Model\Font\Entity\File\Info;
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
        if ($old = $font->findFile($command->file->getName(), $command->file->getExt())) {
            $font->removeFile(new \DateTimeImmutable(), $old->getId());
            $this->flusher->flush();
        }
        $font->addFile(
            new \DateTimeImmutable(),
            FileId::next(),
            new Info(
                $command->file->getPath(),
                $command->file->getName(),
                $command->file->getExt(),
                $command->file->getSize()
            )
        );
        $this->flusher->flush();
    }
}

