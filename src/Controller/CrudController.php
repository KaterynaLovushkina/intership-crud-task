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
    #[Route('/', name: 'main')]
    public function index(
        EntityManagerInterface $entityManager,
        Request $request,
        PaginatorInterface $paginator
    ): Response {
        $crud = new Crud();

        $pages = $this->createPagination($entityManager, $request, $paginator);
        $form = $this->createCrudForm($crud);

        return $this->render('crud/index.html.twig', [
            'list' => $pages,
            'form' => $form,
        ]);
    }

    #[Route('/create', name: 'create')]
    public function create(
        EntityManagerInterface $entityManager,
        Request $request,
        PaginatorInterface $paginator
    ) {
        $crud = new Crud();

        $form = $this->createCrudForm($crud);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($crud);
            $entityManager->flush();

            $this->addFlash('notice', 'Comment is successfully added');

            return $this->redirectToRoute('main');
        }
        $pages = $this->createPagination($entityManager, $request, $paginator);

        return $this->render('crud/index.html.twig', [
            'list' => $pages,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function delete(EntityManagerInterface $entityManager, $id): Response
    {
        $crud = $entityManager->getRepository(Crud::class)->find($id);
        if (!$crud) {
            throw new NotFoundHttpException('The item does not exist with this id.');
        }
        $entityManager->remove($crud);
        $entityManager->flush();

        $this->addFlash('notice', 'Comment is successfully deleted');

        return $this->redirectToRoute('main');
    }

    private function createPagination(
        EntityManagerInterface $entityManager,
        Request $request,
        PaginatorInterface $paginator
    ): SlidingPagination {
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
