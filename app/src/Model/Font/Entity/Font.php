<?php

declare(strict_types=1);

namespace App\Model\Font\Entity;

use App\Model\Font\Entity\Face\Face;
use App\Model\Font\Entity\File\File;

use App\Model\Font\Entity\Face\Id as FaceId;
use App\Model\Font\Entity\File\Id as FileId;
use App\Model\Font\Entity\File\Info;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="font_fonts", indexes={
 *     @ORM\Index(columns={"date"})
 * })
 */
class Font
{
    /**
     * @ORM\Column(type="font_id")
     * @ORM\Id
     */
    private $id;
    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable")
     */
    private $date;
    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $filesUpdatedAt;
    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $slug;
    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $name;
    /**
     * @var string
     * @ORM\Column(type="string", length=128)
     */
    private $author;
    /**
     * @var Status
     * @ORM\Column(type="font_status", length=16)
     */
    private $status;
    /**
     * @var Status
     * @ORM\Column(type="font_license", length=16)
     */
    private $license;
    /**
     * @var ArrayCollection|Language[]
     * @ORM\Column(type="font_languages")
     */
    private $languages;
    /**
     * @var ArrayCollection|File[]
     * @ORM\OneToMany(targetEntity="App\Model\Font\Entity\File\File", mappedBy="font", orphanRemoval=true, cascade={"all"})
     * @ORM\OrderBy({"date" = "ASC"})
     */
    private $files;
    /**
     * @var ArrayCollection|Face[]
     * @ORM\OneToMany(targetEntity="App\Model\Font\Entity\Face\Face", mappedBy="font", orphanRemoval=true, cascade={"all"})
     * @ORM\OrderBy({"sort" = "ASC"})
     */
    private $faces;

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
        $this->faces = new ArrayCollection();
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

//    FACES

    /**
     * @return Face[]
     */
    public function getFaces(): array
    {
        $sortedFaces = $this->faces->toArray();
        usort($sortedFaces, [Face::class, 'compareSort']);
        return $sortedFaces;
    }

    public function associateFaces(): void
    {
        foreach ($this->files as $current) {
            $ext = $current->getInfo()->getExt();
            if (in_array($ext, ['ttf', 'eot', 'woff', 'woff2'])) {

                $name = $current->getInfo()->getName();
                $expr = new Comparison('name', Comparison::EQ, $name);
                $criteria = (new Criteria())->where($expr);

                if ($this->faces->matching($criteria)->isEmpty()) {
                    $this->addFace(FaceId::next(), $name);
                }
                /** @var $face Face */
                $face = $this->faces->matching($criteria)->first();
                if (!$face->hasFile($current)) {
                    $face->addFile($current);
                    $current->assignFace($face);
                }
            }
        }
        foreach ($this->faces as $current) {
            $filesByFaceName = $this->files->filter(function(File $file, $key) use ($current) {
                return $file->getInfo()->getName() === $current->getName();
            });
            if ($filesByFaceName->isEmpty()) {
                $this->removeFace($current->getId());
            }
        }
    }

    public function sortFaces(array $sortedIds): void
    {
        $idsCheck = $this->faces->forAll(function ($key, Face $face) use ($sortedIds) {
            return in_array($face->getId()->getValue(), $sortedIds);
        });
        if (!$idsCheck) {
            throw new \DomainException('Ids list is inconsistent.');
        }

        $sortOffset = $this->faces->last()->getSort()+1;
        foreach ($this->faces as $face) {
            $key = array_search($face->getId()->getValue(), $sortedIds);
            $face->setSort($sortOffset+$key);
        }
    }

    public function hasFaces(): bool
    {
        return count($this->getFaces()) > 0;
    }

    private function addFace(FaceId $id, string $name): void
    {
        $sort = 1;
        if ($this->faces && $lastFace = $this->faces->last()) {
            /** @var $lastFace Face */
            $sort = $lastFace->getSort() + 1;
        }
        $this->faces->add(new Face($this, $id, $name, $sort));
    }

    private function removeFace(FaceId $id): void
    {
        foreach ($this->faces as $current) {
            if ($current->getId()->isEqual($id)) {
                $this->faces->removeElement($current);
                return;
            }
        }
        throw new \DomainException('Face is not found.');
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

    public function hasFile(string $name, string $ext): bool
    {
        return (bool) $this->findFile($name, $ext);
    }

    public function hasCss(): bool
    {
        return count($this->findFilesByExt('css')) > 0;
    }

    public function hasZip(): bool
    {
        return count($this->findFilesByExt('zip')) > 0;
    }

    public function isZipConsistent(): bool
    {
        if ($zipFile = current($this->findFilesByExt('zip'))) {
            return $zipFile->getDate() >= $this->filesUpdatedAt;
        }
        return false;
    }

}
