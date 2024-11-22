<?php

namespace App\Controller;

use App\Entity\Reclamation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use App\Repository\ReclamationRepository;



class ReclamationMobileController extends AbstractController
{
    #[Route('/reclamation/mobile', name: 'app_reclamation_mobile')]
    public function index(): Response
    {
        return $this->render('reclamation_mobile/index.html.twig', [
            'controller_name' => 'ReclamationMobileController',
        ]);
    }

    
    #[Route('/reclamationJSON', name: 'lapp_reclamation_mobile')]
    public function AllReclamation(Request $request,NormalizerInterface $Normalizer,ReclamationRepository $rep )
    {
        //Nous utilisons la Repository pour récupérer les objets que nous avons dans la base de données
        $rec = $rep->findAll();

        //Nous utilisons la fonction normalize qui transforme en format JSON nos donnée qui sont
        $jsonContent = $Normalizer->normalize($rec, 'json', ['groups' => 'post:read']);


        return new Response(json_encode($jsonContent));
    }
  
    #[Route('/editreclamationJSON', name: 'eapp_reclamation_mobile')]
    public function editJSON2(Request $request,ReclamationRepository $rep, NormalizerInterface $normalizer, EntityManagerInterface $entityManager): Response
    {
        $categorie = $rep->find($request->get('id'));


        
        $categorie->setNomReclamation($request->get('nom'));
        $categorie->setPrenomReclamation($request->get('prenom'));
        $categorie->setDestinationReclamation($request->get('destination'));
        $categorie->setTypeReclamation($request->get('type'));
        $categorie->setDescriptionReclamation($request->get('description'));

        $entityManager->flush();

        // generate a signed url and email it to the user
        #MAILER



        //$jsonContent=$normalizer->normalize($user,'json',['groups'=>'post:read']);
        return new JsonResponse('produit modifié',200);
    }

    #[Route('/addreclamationJSON', name: 'aapp_reclamation_mobile')]
    public function addJSON2(Request $request, NormalizerInterface $normalizer, EntityManagerInterface $entityManager): Response
    {
        $categorie = new Reclamation();


        
        $categorie->setNomReclamation($request->get('nom'));
        $categorie->setPrenomReclamation($request->get('prenom'));
        $categorie->setDestinationReclamation($request->get('destination'));
        $categorie->setTypeReclamation($request->get('type'));
        $categorie->setDescriptionReclamation($request->get('description'));



        $entityManager->persist($categorie);
        $entityManager->flush();

        // generate a signed url and email it to the user
        #MAILER



        //$jsonContent=$normalizer->normalize($user,'json',['groups'=>'post:read']);
        return new JsonResponse('categorie ajouté',200);
    }

    #[Route('/removereclamationJSON', name: 'rapp_reclamation_mobile')]
    public function removeJSON2(Request $request,ReclamationRepository $rep, NormalizerInterface $normalizer, EntityManagerInterface $entityManager): Response
    {
        $categorie = $rep->find($request->get('id'));


        $entityManager->remove($categorie);


        $entityManager->flush();

        // generate a signed url and email it to the user
        #MAILER



        //$jsonContent=$normalizer->normalize($user,'json',['groups'=>'post:read']);
        return new JsonResponse('categorie supprimé',200);
    }   

}
