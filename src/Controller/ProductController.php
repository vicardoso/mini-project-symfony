<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\ProductRepository;
use App\Form\ProductType;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends AbstractController
{

  public function index()
  {
    $categories = $this->getDoctrine()
    ->getRepository(Category::class)
    ->retrieveHydratedCategories();
    $products = $this->getDoctrine()
    ->getRepository(Product::class)
    ->findAllProducts();
    return $this->render("product/index.html.twig", ['products' => $products, 'categories' => $categories]);
    
  }

  public function createProduct(EntityManagerInterface $em, Request $request)
  {
    $form = $this->createForm(ProductType::class);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
      $data = $form->getData();
      $product = new Product();
      $title = $form->get('title')->getData();
      $product->setTitle($title);
      $description = $form->get('description')->getData();
      $product->setDescription($description);
      $price = $form->get('price')->getData();
      $product->setPrice($price);
      $position = $form->get('position')->getData();
      $product->setPosition($position);
      $image = $form->get('image')->getData();
      $product->setImage($image);
      $category = $form->get('category')->getData();
      $product->setCategory($category);

      $em->persist($product);
      $em->flush();

    return $this->redirectToRoute("indexAction");
  }

  return $this->render("product/product.html.twig", ["our_form" => $form->createView()]);
}

  public function show($id, ProductRepository $productRepository)
  {
  $product = $productRepository
    ->find($id);

    if (!$product) {
      throw $this->createNotFoundException(
          'No product found for id '.$id
      );
  }

    $categoryName = $product->getCategory()->getTitle();

    return new Response('Produit : '.$product->getTitle().'<br /> Category: '
    .$categoryName.'<br /> Description : '
    .$product->getDescription().'<br /> Prix : '
    .$product->getPrice().'<br /> image : '.'<img src="'
    .$product->getImage().'" alt="image"/>');
  }

}

