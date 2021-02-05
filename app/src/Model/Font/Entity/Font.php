<?php

declare(strict_types=1);

namespace App\Model\Font\Entity;

use Doctrine\Common\Collections\ArrayCollection;

class Font
{
    private $id;
    private $date;
    private $slug;
    private $name;
    private $author;
    private $status;
    private $license;
    private $languages;

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

}
