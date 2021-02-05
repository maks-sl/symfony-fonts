<?php

declare(strict_types=1);

namespace App\Model\Font\UseCase\Files\ClearCss;

use App\Model\Flusher;
use App\Model\Font\Entity\FontRepository;
use App\Model\Font\Entity\Id;
use App\Model\Font\Entity\File\Id as FileId;
use App\Model\Font\Entity\File\Info;

class Handler
{
    private $fonts;
    private $flusher;

    public function __construct(FontRepository $fonts, Flusher $flusher)
    {
        $this->fonts = $fonts;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): void
    {
        $font = $this->fonts->get(new Id($command->id));
        foreach ($command->files as $replacedFile) {
            $font->removeFile(new \DateTimeImmutable(), new FileId($replacedFile->getId()));
        }
        $this->flusher->flush();

        foreach ($command->files as $replacedFile) {
            $font->addFile(
                new \DateTimeImmutable(),
                FileId::next(),
                new Info(
                    $replacedFile->getPath(),
                    $replacedFile->getName(),
                    $replacedFile->getExt(),
                    $replacedFile->getSize()
                )
            );
        }
        $this->flusher->flush();
    }
}

