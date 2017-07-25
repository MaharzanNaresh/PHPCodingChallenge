<?php
declare(strict_types=1);

namespace Challenge;

class FileHandler
{
    const  RESOURCE = __DIR__ . '/../resources/';

    public function getUserData(string $message, string $extension, $filePath = null): array
    {
        if (empty($filePath)) {
            $filePath = readline($message);
        }
        $this->checkFileExists($filePath);
        $fileName = $this->getFileName($filePath);
        $this->validateFileExtension($fileName, $extension);
        $this->moveFiles($filePath, $fileName);

        return [
            'data' => !empty($fileName) ? $this->getData($fileName) : ''
        ];
    }

    public function getData(string $fileName)
    {
        if (empty($fileName)) {
            return false;
        }
        return file_get_contents(self::RESOURCE . $fileName);
    }

    public function getFileName(string $filePath): string
    {
        $fileData = explode(DIRECTORY_SEPARATOR, $filePath);
        if (count($fileData) === 0) {
            return '';
        }

        return end($fileData);

    }

    public function getFileExtension($fileName): string
    {
        if (empty($fileName)) {
            throw  new \RuntimeException("Invalid file name");
        }
        $arr = explode('.', $fileName);

        return end($arr);

    }

    public function moveFiles($filePath, $fileName): bool
    {
        return copy($filePath, self::RESOURCE . $fileName);
    }

    public function checkFileExists(string $filePath)
    {
        if (!file_exists($filePath)) {
            throw new \RuntimeException("$filePath does not exists. Please enter valid file path.");
        }
    }

    public function validateFileExtension(string $fileName, string $extension): bool
    {
        $fileExtension = $this->getFileExtension($fileName);

        if (strcmp($fileExtension, $extension) !== 0) {
            throw new \RuntimeException("Invalid file extension. File should be {.$extension}");
        }

        return true;
    }
}