<?php

namespace App\Controller;

use App\Entity\Reponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use App\Repository\ReponseRepository;
use App\Repository\ReclamationRepository;
class ReponseMobileController extends AbstractController
{
    #[Route('/reponse/mobile', name: 'app_reponse_mobile')]
    public function index(): Response
    {
        return $this->render('reponse_mobile/index.html.twig', [
            'controller_name' => 'ReponseMobileController',
        ]);
    }

    #[Route('/reponseJSON', name: 'lapp_Reponse_mobile')]
    public function AllReponse(Request $request,NormalizerInterface $Normalizer,ReponseRepository $rep )
    {
        //Nous utilisons la Repository pour récupérer les objets que nous avons dans la base de données
        $rec=$rep->findall();

        //Nous utilisons la fonction normalize qui transforme en format JSON nos donnée qui sont
        $jsonContent = $Normalizer->normalize($rec, 'json', ['groups' => 'post:read']);


        return new Response(json_encode($jsonContent));
    }
    #[Route('/addreponseJSON2', name: 'aapp_reponse_mobile2')]
    public function addJSON3(Request $request, NormalizerInterface $normalizer, EntityManagerInterface $entityManager,ReclamationRepository $rep): Response
    {
        $categorie = new Reponse();


        $recl=$rep->find($request->get('idrep'));
        $categorie->setReponse($request->get('reponse'));
        $categorie->setIdReclamation($recl);
        $entityManager->persist($categorie);
        $entityManager->flush();

       
        return new JsonResponse('categorie ajouté',200);
    }
  
    #[Route('/editreponseJSON', name: 'eapp_reponse_mobile')]
    public function editJSON2(Request $request,ReponseRepository $rep, NormalizerInterface $normalizer, EntityManagerInterface $entityManager): Response
    {
        $categorie = $rep->find($request->get('id'));


        
        $categorie->setReponse($request->get('reponse'));
        
        $entityManager->flush();

        
        return new JsonResponse('produit modifié',200);
    }

    #[Route('/addreponseJSON', name: 'aapp_reponse_mobile')]
    public function addJSON2(Request $request, NormalizerInterface $normalizer, EntityManagerInterface $entityManager): Response
    {
        $categorie = new Reponse();


        
        $categorie->setReponse($request->get('reponse'));

        $entityManager->persist($categorie);
        $entityManager->flush();

       
        return new JsonResponse('categorie ajouté',200);
    }

    

    #[Route('/removereponseJSON', name: 'rapp_reponse_mobile')]
    public function removeJSON2(Request $request,ReponseRepository $rep, NormalizerInterface $normalizer, EntityManagerInterface $entityManager): Response
    {
        $categorie = $rep->find($request->get('id'));


        $entityManager->remove($categorie);


        $entityManager->flush();

        
        return new JsonResponse('categorie supprimé',200);
    }   
}
