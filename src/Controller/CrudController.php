<?php

namespace App\Controller;

use App\Entity\Crud;
use App\Form\CrudType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class CrudController extends AbstractController
{
    // Main url to open
    #[Route('/', name: 'main')]
    public function index(
        EntityManagerInterface $entityManager,
        Request $request,
        PaginatorInterface $paginator
    ): Response {
        // Create a new instance of the Crud entity
        $crud = new Crud();

        // Retrieve paginated data and create a form
        $pages = $this->createPagination($entityManager, $request, $paginator);
        $form = $this->createCrudForm($crud);

        // Render the 'crud/index.html.twig' template with data
        return $this->render('crud/index.html.twig', [
            'list' => $pages,
            'form' => $form,
        ]);
    }

    // Method to create new item and add to database
    #[Route('/create', name: 'create')]
    public function create(
        EntityManagerInterface $entityManager,
        Request $request,
        PaginatorInterface $paginator
    ) {
        // Create a new instance of the Crud entity
        $crud = new Crud();

        // Create a form for CRUD data entry and handle form submission
        $form = $this->createCrudForm($crud);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Save the submitted data to the database
            $entityManager->persist($crud);
            $entityManager->flush();

            // Add a flash message to indicate successful data entry
            $this->addFlash('notice', 'Comment is successfully added');

            // Redirect to the 'main' route after successful submission
            return $this->redirectToRoute('main');
        }

        // Retrieve paginated data
        $pages = $this->createPagination($entityManager, $request, $paginator);

        // Render the 'crud/index.html.twig' template with data and form
        return $this->render('crud/index.html.twig', [
            'list' => $pages,
            'form' => $form->createView(),
        ]);
    }

    // Method to delete item and remove it from database
    #[Route('/delete/{id}', name: 'delete')]
    public function delete(EntityManagerInterface $entityManager, $id): Response
    {
        // Find the Crud entity by ID
        $crud = $entityManager->getRepository(Crud::class)->find($id);

        // If the entity doesn't exist, throw a NotFoundHttpException
        if (!$crud) {
            throw new NotFoundHttpException('The item does not exist with this id.');
        }

        // Remove the entity from the database
        $entityManager->remove($crud);
        $entityManager->flush();

        // Add a flash message to indicate successful deletion
        $this->addFlash('notice', 'Comment is successfully deleted');

        // Redirect to the 'main' route after successful deletion
        return $this->redirectToRoute('main');
    }

    private function createPagination(
        EntityManagerInterface $entityManager,
        Request $request,
        PaginatorInterface $paginator
    ): SlidingPagination {
        // Paginate the data using KnpPaginator and return the pagination object
        $pagination = $paginator->paginate(
            $entityManager->getRepository(Crud::class)->paginationQuery(),
            $request->query->get('page', 1),
            5
        );

        return $pagination;
    }

    private function createCrudForm(Crud $crud): Form
    {
        $form = $this->createForm(CrudType::class, $crud);

        return $form;
    }
}
