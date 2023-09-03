<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Products;
use App\Service\AlzaParser;
use App\Form\ProductsType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use App\Service\ImageUploaderService;

class ProductController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager, ImageUploaderService $imageUploader)
    {
        $this->entityManager = $entityManager;
        $this->imageUploader = $imageUploader;
    }


    #[Route('/create-product', name: 'create_product', methods: ['POST', 'GET'])]
    public function createProduct(Request $request, AlzaParser $alzaParser): Response
    {

        $form = $this->createForm(ProductsType::class); //Создаем форму на основе ProductsType
        $form->handleRequest($request); // Обработка данных из запроса
        $product = new Products();

        if ($form->isSubmitted() && $form->isValid()) { // Проверяем на форме должан быть нажата кнопка и валидность
            $url = $form->get('alzaUrl')->getData(); // Получаем данные
            $productData = $alzaParser->parseProductInfo($url); //Парсим
            if ($productData) {

                $product->setName($productData['name']);
                $product->setPrice($productData['price']);
                $imageFileName = $productData['image'];

                $fileName = $this->imageUploader->uploadImageFromUrl($imageFileName); // Загружаем изображение по URL

                $product->setImage($fileName);

                $product->setDescription($productData['description']);
                $product->setAlzaUrl($url);

                $this->entityManager->persist($product);
                $this->entityManager->flush();

                return $this->redirectToRoute('product_list');
            } else {
                $this->addFlash('error', 'Не удалось получить информацию о продукте от Alza.');
            }
        }

        return $this->render('products/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/', name: 'product_list')]
    public function productList(): Response
    {
        $products = $this->entityManager->getRepository(Products::class)->findAll();

        return $this->render('products/list.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/product/{id}', name: 'product_show')]
    public function showProduct(Request $request): Response
    {
        $product = $this->entityManager->getRepository(Products::class)->find($request->attributes->get('id'));


        return $this->render('products/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/product/{id}/edit', name: 'product_edit', methods: ['POST', 'GET'])]
    public function editProduct(Request $request): Response
    {

        $product = $this->entityManager->getRepository(Products::class)->find($request->attributes->get('id'));

        $form = $this->createForm(ProductsType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $imageFile = $form['image']->getData();
                if ($imageFile) {
                    $fileName = $this->imageUploader->upload($imageFile);
                    $product->setImage($fileName);
                }

                $this->entityManager->flush();
            } catch (FileException $e) {
                $this->addFlash('error', 'Не удалось загрузить изображение.');
            }

            return $this->redirectToRoute('product_list');

        }

        return $this->render('products/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/product/{id}/delete', name: 'product_delete', methods: ['POST'])]
    public function deleteProduct(Request $request): Response
    {

        $product = $this->entityManager->getRepository(Products::class)->find($request->attributes->get('id'));

        $this->entityManager->remove($product);
        $this->entityManager->flush();

        return $this->redirectToRoute('product_list');
    }
}
