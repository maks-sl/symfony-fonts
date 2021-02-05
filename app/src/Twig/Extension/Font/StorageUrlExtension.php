<?php

declare(strict_types=1);

namespace App\Twig\Extension\Font;

use App\Model\Font\Entity\File\File;
use App\Model\Font\Service\File\FileManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class StorageUrlExtension extends AbstractExtension
{
    private $fileManager;

    public function __construct(FileManager $fileManager)
    {
        $this->fileManager = $fileManager;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('font_file_url', [$this, 'url'], ['is_safe' => ['html']]),
        ];
    }

    public function url(File $file): string
    {
        $info = $file->getInfo();
        return $this->fileManager->publicUrl($info->getPath().'/'.$info->getName().'.'.$info->getExt());
    }
}