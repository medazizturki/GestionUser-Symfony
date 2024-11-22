<?php

namespace App\Controller;

use App\Entity\Ressource;
use App\Form\RessourceType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\RessourceRepository;


#[Route('/ressource')]
class RessourceController extends AbstractController

{

    #[Route('/afficheM', name: 'afficheMo')]
    public function show_mobile(RessourceRepository $ressourceRepository,SerializerInterface $serializerInterface){
   $ressources=$ressourceRepository->findAll();
   $json=$serializerInterface->serialize($ressources,'json',['groups'=>'ressources']);
   dump($ressources);
   die;
}


#[Route('/Addjson', name: 'app_addjson')]
public function addjson(Request $request)
{
    $ressource = new Ressource();
    $typeRessource = $request->query->get("typeRessource");
    $disponibiliteRessource = $request->query->get("disponibiliteRessource");
    $nomRessource = $request->query->get("nomRessource");
    

    // Set the properties of the $produit object directly
    $ressource->setTypeRessource($typeRessource);
    $ressource->setDisponibiliteRessource($disponibiliteRessource);
    $ressource->setNomRessource($nomRessource);
   

    $em = $this->getDoctrine()->getManager();

    $em->persist($ressource);
    $em->flush();

    $serializer = new Serializer([new ObjectNormalizer()]);
    $formatted = $serializer->normalize($ressource);
    return new JsonResponse($formatted);
}




    
    #[Route('/', name: 'app_ressource_index', methods: ['GET'])]
    public function index(  EntityManagerInterface $entityManager): Response
    {
        $ressources = $entityManager
            ->getRepository(Ressource::class)
            ->findAll();
  
        return $this->render('ressource/index.html.twig', [
            'ressources' => $ressources,
        ]);
    }
//Recherche 
#[Route('/searchRessource', name: 'app_ressource_search')]
public function searchRessource( Request $request, EntityManagerInterface $entityManager): Response {
    $ressource=   $request->get('ressource');
 
    
    if($ressource == ""){
        $ressources = $entityManager
        ->getRepository(Ressource::class)
        ->findAll();
    }else{
        $ressources = $entityManager
        ->getRepository(Ressource::class)->findBy(
            ['nomRessource'=> $ressource]
        );
    }
return $this->render('ressource/index.html.twig', [
        'ressources' => $ressources,
    ]);

}
 //PDF génération

    #[Route('/front', name: 'app_ressource_indexFront', methods: ['GET'])]
    public function indexfront(EntityManagerInterface $entityManager): Response
    {
        $ressources = $entityManager
            ->getRepository(Ressource::class)
            ->findAll();

        return $this->render('ressource/indexFront.html.twig', [
            'ressources' => $ressources,
        ]);
    }

    #[Route('/new', name: 'app_ressource_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $ressource = new Ressource();
        $form = $this->createForm(RessourceType::class, $ressource);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($ressource);
            $entityManager->flush();

            return $this->redirectToRoute('app_ressource_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('ressource/new.html.twig', [
            'ressource' => $ressource,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_ressource_show', methods: ['GET'])]
    public function show(Ressource $ressource): Response
    {
        return $this->render('ressource/show.html.twig', [
            'ressource' => $ressource,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_ressource_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Ressource $ressource, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RessourceType::class, $ressource);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_ressource_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('ressource/edit.html.twig', [
            'ressource' => $ressource,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_ressource_delete', methods: ['POST'])]
    public function delete(Request $request, Ressource $ressource, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ressource->getId(), $request->request->get('_token'))) {
            $entityManager->remove($ressource);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_ressource_index', [], Response::HTTP_SEE_OTHER);
    }


    




    
   





}
