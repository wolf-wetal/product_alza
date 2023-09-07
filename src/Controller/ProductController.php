<?php

namespace App\Controller;

use App\Repository\ProductsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Products;
use App\Service\AlzaParser;
use App\Form\ProductsType;
use App\Service\ImageUploaderService;

class ProductController extends AbstractController
{
    private $imageUploader;
    private $productsRepository;

    public function __construct(ImageUploaderService $imageUploader, ProductsRepository $productsRepository)
    {
        $this->imageUploader = $imageUploader;
        $this->productsRepository = $productsRepository;
    }


    #[Route('/create-product', name: 'create_product', methods: ['POST', 'GET'])]
    public function createProduct(Request $request, AlzaParser $alzaParser): Response
    {
        $form = $this->createForm(ProductsType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $url = $form->get('alzaUrl')->getData();
            $productData = $alzaParser->parseProductInfo($url);

            if ($productData) {
                $product = new Products(); // Создаем новый объект Product
                $product->setName($productData['name']);
                $product->setPrice($productData['price']);
                $imageFileName = $productData['image'];
                $fileName = $this->imageUploader->uploadImageFromUrl($imageFileName);
                $product->setImage($fileName);
                $product->setDescription($productData['description']);
                $product->setAlzaUrl($url);

                $this->productsRepository->save($product);

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
        $products = $this->productsRepository->findAllProducts();

        return $this->render('products/list.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/product/{id}', name: 'product_show')]
    public function showProduct(int $id): Response
    {
        $product = $this->productsRepository->findProductById($id);

        return $this->render('products/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/product/{id}/edit', name: 'product_edit', methods: ['POST', 'GET'])]
    public function editProduct(Request $request, int $id): Response
    {
        $product = $this->productsRepository->findProductById($id);
        $form = $this->createForm(ProductsType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form['image']->getData();

            if ($imageFile) {
                $fileName = $this->imageUploader->upload($imageFile);
                $product->setImage($fileName);
            }

            $this->productsRepository->save($product);

            return $this->redirectToRoute('product_list');
        }

        return $this->render('products/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/product/{id}/delete', name: 'product_delete', methods: ['POST'])]
    public function deleteProduct(int $id): Response
    {
        $product = $this->productsRepository->findProductById($id);

        if ($product) {
            $this->productsRepository->delete($product);
        }

        return $this->redirectToRoute('product_list');
    }

}
