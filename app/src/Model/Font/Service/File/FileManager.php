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
    private $publicUrl;
    private $innerUrl;

    public function __construct(FilesystemOperator $ftpStorage, string $publicUrl, string $innerUrl)
    {
        $this->ftpStorage = $ftpStorage;
        $this->publicUrl = $publicUrl;
        $this->innerUrl = $innerUrl;
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
     * @param Font $font
     * @return ReplacedFile[]
     * @throws FilesystemException
     */
    public function clearCss(Font $font): array
    {
        $result = [];
        $path = $font->getId()->getValue();

        foreach ($font->findFilesByExt('css') as $file) {
            $fileName = $file->getInfo()->getName() .'.' . $file->getInfo()->getExt();
            if (!$content = file_get_contents($this->innerUrl . '/' . $path . '/' . $fileName)) {
                throw new \DomainException('Error loading file content');
            }
            $newContent = trim(
                preg_replace("/\/\*(.|\n)*?\*\//", "",
                    preg_replace("/\/\/.*/", "", $content)
            ));
            $this->ftpStorage->delete($path . '/' . $fileName);
            $this->ftpStorage->write($path . '/' . $fileName, $newContent);
            $size = $this->ftpStorage->fileSize($path . '/' . $fileName);

            $result[] = new ReplacedFile(
                $file->getId()->getValue(),
                $path,
                $file->getInfo()->getName(),
                $file->getInfo()->getExt(),
                $size
            );
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

    public function publicUrl(string $path): string
    {
        return $this->publicUrl . '/' . $path;
    }

    private function isExtAllowed(string $ext): bool
    {
        return (bool) preg_match('/(eot|ttf|woff|woff2|svg|css)$/', $ext);
    }
}
