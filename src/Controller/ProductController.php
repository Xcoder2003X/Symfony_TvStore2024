<?php

namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Product;  // The entity your form is tied to
use App\Form\ProductType;  // The form type class you created
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Category;
use App\Entity\Review;





class ProductController extends AbstractController
{
    #[Route('/product', name: 'product_list')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $products = $entityManager->getRepository(Product::class)->findAll();
        return $this->render('product/index.html.twig', [
            "products" => $products,
        ]);
    }

    // Request --> we will passe something



    #[Route('/store/product', name: 'product_store')]
    public function store(Request $request , EntityManagerInterface $entityManager): Response
    {
        $product = new Product();

        //$this->createForm function to create a form , the form here will take ProductType structure
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);// traiter les infos passe dans url par forme

        if ($form->isSubmitted() && $form->isValid()) {
            $image= $request->files->get("product")["image"];
            // $image_name (date+""+the original name inside user computer)
            $image_name = time()."".$image->getClientOriginalName();
            $image->move($this->getParameter("image_directory"),$image_name);  
            // changer le nom du image du product from the name passed on the form to image_name wich is date_creation + nom image on device
            $product->setImage($image_name);
            // Save the product to the database
            $entityManager->persist($product);
            $entityManager->flush();

            // Add a flash message
            $this->addFlash('success', 'Product created successfully!');

            // Redirect to another page, e.g., the product list
            return $this->redirectToRoute('product_list');
        }

        return $this->render('product/create.html.twig', [
            "form" => $form,
        ]);
    }




    #[Route('/product/details/{id}', name: 'product_show')]
    public function show(EntityManagerInterface $entityManager , $id): Response
    {
        $products = $entityManager->getRepository(Product::class)->findAll();
        $product = $entityManager->getRepository(Product::class)->find($id);
        return $this->render('product/details.html.twig', [
            "product" => $product,
            "products" => $products,
        ]);
    }


    #[Route('/product/edit/{id}', name: 'product_edit')]
    public function edit(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $product = $entityManager->getRepository(Product::class)->find($id);
        if (!$product) {
            throw $this->createNotFoundException('No product found for id ' . $id);
        }
    
        //The form is then populated with the current request data.
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
    
        // si la forme a ete valide et envoyer
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
    
            $this->addFlash('success', 'Product updated successfully!');
    
            return $this->redirectToRoute('product_list');
        }
    
        // si la forme n est pas envoyee en doit rester dans edit.twig qu on doit
        // la passer form et product
        return $this->render('product/edit.html.twig', [
            'form' => $form->createView(),
            'product' => $product,
        ]);

        
    }
    
    


    // delete product

    #[Route('/product/delete/{id}', name: 'product_delete')]
    public function delete(Product $product,EntityManagerInterface $entityManager): Response
    {
        // Filesyst class peux acceder a vos fichiers
        $filesystem = new Filesystem();
        $imagePath = "uploads/".$product->getImage();
        if ($filesystem->exists($imagePath)) {
            $filesystem->remove($imagePath);
        }
        
        $reviews = $product->getReviews();
        foreach ($reviews as $review) {
        
         $entityManager->remove($review);

        }

        $entityManager->remove($product);
        $entityManager->flush();

        // Add a flash message
        $this->addFlash('success', 'Product deleted successfully!');

        return $this->redirectToRoute('product_list');
    }


    // show the product by category

    #[Route('/product/{category}', name: 'product_show_category')]
    public function showbyCategory(EntityManagerInterface $entityManager ,Category $category): Response
    {
        $categories = $entityManager->getRepository(Category::class)->findAll();
        return $this->render('home/index.html.twig', [
            "products" => $category->getProducts(), // getProducts fonction de la relation , ne return que les produits apparient a la category passee
            "categories" => $categories,
        ]);
    }

    #[Route('/admin', name: 'admin')]
    public function verifAdmin(): Response
    {
        return $this->render('home/adminverif.html.twig', [
            
        ]);
    }

}


