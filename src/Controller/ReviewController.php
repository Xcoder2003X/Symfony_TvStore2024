<?php
namespace App\Controller;

use App\Entity\Product;
use App\Entity\Review;
use App\Form\ReviewType;
use App\Repository\ReviewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReviewController extends AbstractController
{
    #[Route('/product/{id}/review', name: 'product_review')]
    public function review(int $id, Request $request, EntityManagerInterface $entityManager, ReviewRepository $reviewRepository): Response
    {
        // Retrieve the product by ID
        $product = $entityManager->getRepository(Product::class)->find($id);
        if (!$product) {
            throw $this->createNotFoundException('Product not found');
        }

        // Create the form for Review
        $review = new Review();
        $form = $this->createForm(ReviewType::class, $review);
        $form->handleRequest($request);

        // If the form is submitted and valid, save the review
        if ($form->isSubmitted() && $form->isValid()) {
            $review->setProduct($product);
            $entityManager->persist($review);
            $entityManager->flush();

            // Redirect to the product detail page or review page
            return $this->redirectToRoute('product_review', ['id' => $id]);
        }

        // Fetch existing reviews for the product
        $reviews = $reviewRepository->findBy(['product' => $product]);
        $review_image = $product->getImage();
        $imagePath = 'uploads/'.$review_image;
        // Render the template with the form and reviews
        return $this->render('product/review.html.twig', [
            'Reviewform' => $form->createView(),  // Pass the form view to the template
            'reviews' => $reviews,
            'product' => $product,
            "imagePath" => $imagePath,
        ]);
    }
}
