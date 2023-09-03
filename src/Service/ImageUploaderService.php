<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageUploaderService
{
    private $imageDirectory;
    private $httpClient;

    public function __construct(string $targetDirectory, HttpClientInterface $httpClient)
    {
        $this->imageDirectory = $targetDirectory;
        $this->httpClient = $httpClient;
    }

    public function upload(UploadedFile $file): string
    {
        $newFileName = md5(uniqid()) . '.' . $file->getClientOriginalExtension();

        $file->move($this->imageDirectory, $newFileName);

        return $newFileName;
    }

    public function uploadImageFromUrl(string $imageUrl): string
    {
        $response = $this->httpClient->request('GET', $imageUrl);

        if ($response->getStatusCode() === 200) {
            $content = $response->getContent();
            $extension = pathinfo($imageUrl, PATHINFO_EXTENSION);
            $fileName = md5(uniqid()) . '.' . $extension;
            $filePath = $this->imageDirectory . '/' . $fileName;

            try {
                file_put_contents($filePath, $content);
            } catch (FileException $e) {
                throw new FileException('Не удалось загрузить изображение.');
            }

            return $fileName;
        } else {
            throw new FileException('Не удалось получить изображение по URL-адресу.');
        }
    }
}
