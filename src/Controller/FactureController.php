<?php

namespace App\Controller;

use App\Entity\Facture;
use App\Entity\Rendezvous;
use App\Form\FactureType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route ;

#[Route('/facture')]
class FactureController extends AbstractController
{
    //affiche back
    #[Route('/', name: 'app_facture_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $factures = $entityManager
            ->getRepository(Facture::class)
            ->findAll();

        return $this->render('facture/index.html.twig', [
            'factures' => $factures,
        ]);
    }
    //affiche fronte
    #[Route('/front', name: 'app_facture_front', methods: ['GET'])]
    public function front(EntityManagerInterface $entityManager): Response
    {
        $factures = $entityManager
            ->getRepository(Facture::class)
            ->findAll();

        return $this->render('facture/front.html.twig', [
            'factures' => $factures,
        ]);
    }
//add en back
    #[Route('/new', name: 'app_facture_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $facture = new Facture();
        $form = $this->createForm(FactureType::class, $facture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($facture);
            $entityManager->flush();

            return $this->redirectToRoute('app_facture_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('facture/new.html.twig', [
            'facture' => $facture,
            'form' => $form,
        ]);
    }
    //add en front
    #[Route('/newFront/{id}', name: 'app_facture_newFront', methods: ['GET', 'POST'])]
    public function newFront(Request $request, EntityManagerInterface $entityManager,Rendezvous $id): Response
    {
        $facture = new Facture();
        $facture->setRendezvous($id);
        $form = $this->createForm(FactureType::class, $facture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($facture);
            $entityManager->flush();
          $id_facture=$facture->getId();
            
            ($id_facture);
            
            return $this->redirectToRoute('app_facture_showFront',array ('id'=>$id_facture), Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('facture/newFront.html.twig', [
            'facture' => $facture,
            'form' => $form,
        ]);
    }

    #[Route('/newFront', name: 'app_facture_newFrontid', methods: ['GET', 'POST'])]
    public function newFront1(Request $request, EntityManagerInterface $entityManager,Rendezvous $id): Response
    {
        $facture = new Facture();
        $facture->setRendezvous($id);
        $form = $this->createForm(FactureType::class, $facture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($facture);
            $id=$facture->getId();
            dd($id);
           // $entityManager->flush();

            //return $this->redirectToRoute('app_facture_showFront', ['id'=>$id], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('facture/newFront.html.twig', [
            'facture' => $facture,
            'form' => $form,
        ]);
    }
    //show en back
    #[Route('/{id}', name: 'app_facture_show', methods: ['GET'])]
    public function show(Facture $facture): Response
    {
        return $this->render('facture/show.html.twig', [
            'facture' => $facture,
        ]);
    }
    //show en front
    #[Route('/{id}/front', name: 'app_facture_showFront', methods: ['GET'])]
    public function showFront(Facture $facture): Response
    {
        return $this->render('facture/showFront.html.twig', [
            'facture' => $facture,
        ]);
    }
    #[Route('/{id}/edit', name: 'app_facture_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Facture $facture, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FactureType::class, $facture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_facture_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('facture/edit.html.twig', [
            'facture' => $facture,
            'form' => $form,
        ]);
    }
    //edit en front 
    #[Route('/{id}/editFront', name: 'app_facture_editFront', methods: ['GET', 'POST'])]
    public function editFront(Request $request, Facture $facture, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FactureType::class, $facture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_facture_front', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('facture/editFront.html.twig', [
            'facture' => $facture,
            'form' => $form,
        ]);
    }
//delete en back
    #[Route('/{id}', name: 'app_facture_delete', methods: ['POST'])]
    public function delete(Request $request, Facture $facture, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$facture->getid(), $request->request->get('_token'))) {
            $entityManager->remove($facture);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_facture_index', [], Response::HTTP_SEE_OTHER);
    }
    //delete en front
    #[Route('/{id}/delete', name: 'app_facture_deleteFront', methods: ['POST'])]
    public function deleteFront(Request $request, Facture $facture, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$facture->getid(), $request->request->get('_token'))) {
            $entityManager->remove($facture);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_facture_front', [], Response::HTTP_SEE_OTHER);
    }


}
