<?php

namespace App\Controller;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Product;
use App\Entity\Category;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $products = $entityManager->getRepository(Product::class)->findAll();
        $categories = $entityManager->getRepository(Category::class)->findAll();

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            "products" => $products,
            "categories" => $categories,
        ]);
    }
}
