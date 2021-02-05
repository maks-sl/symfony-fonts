<?php

declare(strict_types=1);

namespace App\Model\Font\Entity;

use App\Model\Font\Entity\File\File;

use App\Model\Font\Entity\File\Id as FileId;
use App\Model\Font\Entity\File\Info;

use Doctrine\Common\Collections\ArrayCollection;

class Font
{
    private $id;
    private $date;
    private $filesUpdatedAt;
    private $slug;
    private $name;
    private $author;
    private $status;
    private $license;
    private $languages;
    private $files;

    /**
     * Font constructor.
     * @param Id $id
     * @param \DateTimeImmutable $date
     * @param string $slug
     * @param string $name
     * @param string $author
     * @param License $license
     * @param Language[] $languages
     */
    public function __construct(
        Id $id,
        \DateTimeImmutable $date,
        string $slug,
        string $name,
        string $author,
        License $license,
        array $languages
    )
    {
        $this->id = $id;
        $this->date = $date;
        $this->slug = $slug;
        $this->name = $name;
        $this->author = $author;
        $this->license = $license;
        $this->setLanguages($languages);
        $this->status = Status::hidden();
        $this->files = new ArrayCollection();
    }

    /**
     * @param string $slug
     * @param string $name
     * @param string $author
     * @param License $license
     * @param Language[] $languages
     */
    public function edit(string $slug, string $name, string $author, License $license, array $languages): void
    {
        $this->slug = $slug;
        $this->name = $name;
        $this->author = $author;
        $this->license = $license;
        $this->setLanguages($languages);
    }

    public function changeStatus(Status $status): void
    {
        if ($this->status->isEqual($status)) {
            throw new \DomainException('Status is already same.');
        }
        $this->status = $status;
    }

    public function isActive(): bool
    {
        return $this->status->isActive();
    }

    public function isHidden(): bool
    {
        return $this->status->isHidden();
    }

//    GETTERS

    public function getId(): Id
    {
        return $this->id;
    }

    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    public function getFilesUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->filesUpdatedAt;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function getStatus(): Status
    {
        return $this->status;
    }

    public function getLicense(): License
    {
        return $this->license;
    }

//    LANGUAGES

    /**
     * @return Language[]
     */
    public function getLanguages(): array
    {
        return $this->languages->toArray();
    }

    /**
     * @param Language[] $languages
     */
    public function setLanguages(array $languages): void
    {
        $this->languages = new ArrayCollection($languages);
    }

//    FILES

    /**
     * @return File[]
     */
    public function getFiles(): array
    {
        return $this->files->toArray();
    }

    /**
     * @param FileId $id
     * @return File
     */
    public function getFile(FileId $id): File
    {
        foreach ($this->files as $current) {
            if ($current->getId()->isEqual($id)) {
                return $current;
            }
        }
        throw new \DomainException('File is not found.');
    }

    /**
     * @param string $name
     * @param string $ext
     * @return File|null
     */
    public function findFile(string $name, string $ext): ?File
    {
        $filtered = $this->files->filter(function(File $file, $key) use ($name, $ext) {
            return $file->getInfo()->getName() === $name && $file->getInfo()->getExt() === $ext;
        });
        if (!$filtered->isEmpty()) {
            return $filtered->first();
        }
        return null;
    }

    /**
     * @param string $ext
     * @return File[]
     */
    public function findFilesByExt(string $ext): array
    {
        return $this->files->filter(function(File $file, $key) use ($ext) {
            return $file->getInfo()->getExt() === $ext;
        })->toArray();
    }

    public function addFile(\DateTimeImmutable $date, FileId $id, Info $info): void
    {
        foreach ($this->files as $current) {
            if ($current->getInfo()->isFileNameSame($info)) {
                throw new \DomainException('Filename is already exists.');
            }
        }
        $this->files->add(new File($this, $id, $date, $info));
        $this->filesUpdatedAt = $date;
    }

    public function removeFile(\DateTimeImmutable $date, FileId $id): void
    {
        foreach ($this->files as $current) {
            if ($current->getId()->isEqual($id)) {
                $this->files->removeElement($current);
                $this->filesUpdatedAt = $date;
                return;
            }
        }
        throw new \DomainException('File is not found.');
    }

}
