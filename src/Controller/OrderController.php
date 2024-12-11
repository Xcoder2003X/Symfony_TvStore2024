<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\OrderRepository;
use App\Entity\Order;
use App\Entity\Product;

class OrderController extends AbstractController
{
    private $orderRepository;
    private $entityManager ;

    public function __construct(OrderRepository $orderRepository,ManagerRegistry $doctrine){

        $this-> orderRepository = $orderRepository;
        $this-> entityManager = $doctrine->getManager();

    }


    #[Route('/order', name: 'app_order')]
    public function index(): Response
    {
        $order = new Order();
        return $this->render('order/user.html.twig', [
            'controller_name' => 'OrderController',
            "user" => $this->getUser(),
        ]);
    }


    // changer status

    #[Route('/orders', name: 'orders_list')]
    public function index1(): Response
    {
        $orders = $this->orderRepository->findAll();
        return $this->render('order/index.html.twig', [
            'controller_name' => 'OrderController',
            "orders" => $orders

        ]);
    }



    #[Route('/user/orders', name: 'user_order_list')]
    public function userOrders(): Response
    {

        if(!$this->getUser()){      // si User not logged in
            return $this->redirectToRoute("app_login");
         }

        return $this->render('order/user.html.twig', [
            "user" => $this->getUser(),
        ]);

    }



    #[Route('/store/order/{product}', name: 'order_store')]
    public function store(Product $product): Response
    {

        if(!$this->getUser()){      // si User not logged in
           return $this->redirectToRoute("app_login");
        }

        $order = new Order;
 
        $order->setPname($product->getName());
        $order->setPrice($product->getPrice());
        $order->setUser($this->getUser());
        $order->setStatus("pending !!!");

            $this->entityManager->persist($order);
            $this->entityManager->flush();

            $this->addFlash('success', 'Order Saved successfully!');

            return $this->redirectToRoute('user_order_list');
        
    }


    #[Route('/update/order/{id}/{status}', name: 'order_status_update')]
    public function orderStatusUpdate(Order $order,$status): Response
    {
        $order -> setStatus($status);  // symfony knows the order is the order with id passed in url
        
        $this->entityManager->persist($order);
        $this->entityManager->flush();

        $this->addFlash('success', 'Status Updated successfully!');

        return $this->redirectToRoute('user_order_list');

    }


    // delete order

    #[Route('update/order/{order}', name: 'order_delete')]
    public function orderDelete(Order $order): Response
    {
        $this->entityManager->remove($order);
        $this->entityManager->flush();

        // Add a flash message
        $this->addFlash('success', 'order deleted successfully!');

        return $this->redirectToRoute('user_order_list');

    }
    
}
