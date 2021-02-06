<?php

declare(strict_types=1);

namespace App\Model\Font\Service\File;

use App\Model\Font\Entity\Font;
use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use ZipArchive;

use ZipStream\Exception\OverflowException;
use ZipStream\Option\Archive;
use ZipStream\ZipStream;

class FileManager
{
    private const ZIP_FILE_NAME = 'font.zip';

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
     * @param UploadedFile $file
     * @param Font $font
     * @return AddedFile[]
     * @throws FilesystemException
     */
    public function uploadZip(UploadedFile $file, Font $font): array
    {
        $files = [];
        $zip = new ZipArchive();
        $isOpen = $zip->open($file->getPathname());
        if ($isOpen === TRUE) {
            for ($i = 0; $i < $zip->count(); $i++) {
                $fileName = $zip->getNameIndex($i);
                $info = $zip->statIndex($i);
                if (!$fileName || !$info) {
                    throw new \DomainException("Error getting info of $i element when unzip for ".$font->getName());
                }
                $size = $info['size'];
                $ext = pathinfo($fileName, PATHINFO_EXTENSION);
                $name = pathinfo($fileName, PATHINFO_FILENAME);

                if ($this->isExtAllowed($ext) && !$font->hasFile($name, $ext)) {
                    if (!$res = $zip->getStream($fileName)) {
                        throw new \DomainException("Error reading file ".$fileName);
                    }
                    $this->ftpStorage->writeStream($font->getId()->getValue() . '/' . $name . '.' . $ext, $res);
                    if (is_resource($res)) {
                        fclose($res);
                    }
                    $files[] = new AddedFile($font->getId()->getValue(), $name, $ext, $size);
                }
            }
            $zip->close();
            return $files;

        } else {
            throw new \DomainException("Error opening archive #".$isOpen);
        }
    }

    /**
     * @param Font $font
     * @return AddedFile
     * @throws OverflowException
     * @throws FilesystemException
     */
    public function buildZip(Font $font): AddedFile
    {
        $path = $font->getId()->getValue();
        $name = pathinfo(self::ZIP_FILE_NAME, PATHINFO_FILENAME);
        $ext = pathinfo(self::ZIP_FILE_NAME, PATHINFO_EXTENSION);

        if ($font->hasFile($name, $ext)) {
            $this->ftpStorage->delete($path . '/' .self::ZIP_FILE_NAME);
        }

        $tempStream = fopen('php://memory', 'w+');
        $zipStreamOptions = new Archive();
        $zipStreamOptions->setOutputStream($tempStream);
        $zipStreamOptions->setZeroHeader(true);

        $zipStream = new ZipStream(self::ZIP_FILE_NAME, $zipStreamOptions);

        foreach ($font->getFiles() as $file) {
            if ($this->isExtAllowed($file->getInfo()->getExt())) {
                $fileName = $file->getInfo()->getName() .'.' . $file->getInfo()->getExt();
                if (!$resource = fopen($this->innerUrl . '/' . $path . '/' . $fileName, 'r')) {
                    throw new \DomainException('Error loading file by url');
                }
                $zipStream->addFileFromStream($fileName, $resource);
            }
        }
        $zipStream->finish();

        $size = fstat($tempStream)['size'];
        $this->ftpStorage->writeStream($path . '/' .self::ZIP_FILE_NAME, $tempStream);
        fclose($tempStream);

        return new AddedFile($path, $name, $ext, $size);
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
