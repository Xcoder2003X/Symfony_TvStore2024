<?php

namespace App\Controller;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Product;
use App\Entity\Category;
use Symfony\Component\HttpFoundation\RequestStack;

class HomeController extends AbstractController
{

    // craete session instance that will stock bool value of splash_shown
    private $requestStack; 

    public function __construct(RequestStack $requestStack) { 
        $this->requestStack = $requestStack; }

    #[Route('/', name: 'app_home')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $session = $this->requestStack->getSession();
        if (!$session->get('splash_shown')) { 
            $session->set('splash_shown', true);

            return $this->redirectToRoute('splash_screen'); 
        
        }
        $products = $entityManager->getRepository(Product::class)->findAll();
        $categories = $entityManager->getRepository(Category::class)->findAll();

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            "products" => $products,
            "categories" => $categories,
        ]);
    }


    #[Route('/splash', name: 'splash_screen')] 
    public function splash(): Response { 
        return $this->render('Error404/welcome.html.twig'); }


    

    // (?!...): Ensures that the slug does not start with any of the specified paths.
#[Route( '/{slug}', name: 'catch_all_except_specific', requirements: [
'slug' => '(?!register|login|product|store/product|orders|order|user/orders|store/order/\d+|update/order/\d+|product/d+/review).+']    
)] 
    public function index1($slug): Response
    {
        return $this->render('Error404/error.html', [
            'slug' => $slug,
        ]);
    }

}
