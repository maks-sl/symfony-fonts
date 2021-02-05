<?php

declare(strict_types=1);

namespace App\Model\Font\Service\File;

use App\Model\Font\Entity\Font;
use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileManager
{
    private $ftpStorage;

    public function __construct(FilesystemOperator $ftpStorage)
    {
        $this->ftpStorage = $ftpStorage;
    }

    /**
     * @param UploadedFile[] $files
     * @param Font $font
     * @return AddedFile[]
     * @throws FilesystemException
     */
    public function uploadFiles(array $files, Font $font): array
    {
        $result = [];
        foreach ($files as $file) {

            $name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $ext = $file->getClientOriginalExtension();

            if ($this->isExtAllowed($ext) && !$font->hasFile($name, $ext)) {

                $dir = $font->getId()->getValue();
                $name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $ext = $file->getClientOriginalExtension();
                $output = $dir . '/' . $name. '.' . $ext;

                $stream = fopen($file->getRealPath(), 'rb+');
                $this->ftpStorage->writeStream($output, $stream);
                fclose($stream);

                $result[] = new AddedFile($dir, $name, $ext, $file->getSize());
            }
        }
        return $result;
    }

    /**
     * @param string $dir
     * @param string $name
     * @param string $ext
     * @throws FilesystemException
     */
    public function remove(string $dir, string $name, string $ext): void
    {
        $this->ftpStorage->delete($dir . '/' . $name. '.' . $ext);
    }

    /**
     * @param string $dir
     * @throws FilesystemException
     */
    public function removeDir(string $dir): void
    {
        $this->ftpStorage->deleteDirectory($dir);
    }

    private function isExtAllowed(string $ext): bool
    {
        return (bool) preg_match('/(eot|ttf|woff|woff2|svg|css)$/', $ext);
    }
}
