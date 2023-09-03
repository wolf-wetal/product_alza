<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ImageController extends AbstractController
{
    #[Route('/upload-image', name: 'upload_image', methods: 'POST')]
    public function uploadImage(Request $request): Response
    {
        $uploadedFile = $request->files->get('imageFile');

        // Не нашли файл
        if (!$uploadedFile) {
            return $this->json(['filename' => 'not_found']);
        }

        // Генерация уникального имени файла
        $newFileName = md5(uniqid()) . '.' . $uploadedFile->getClientOriginalExtension();

        // Сохранение файла в папку
        $uploadedFile->move(
            $this->getParameter('image_directory'), // Путь к папке для сохранения
            $newFileName
        );

        // Возвращение JSON-ответа с именем сохраненного файла
        return $this->json(['filename' => $newFileName]);
    }
}
