<?php

namespace App\Controller;

use App\Entity\PdfGeneratorReclamation;
use App\Entity\Reclamation;
use App\Form\ReclamationType;
use App\Repository\ReclamationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\PieChart;

#[Route('/reclamation')]
class ReclamationController extends AbstractController
{



    //affichage
    #[Route('/', name: 'app_reclamation_index', methods: ['GET','POST'])]
    public function index(EntityManagerInterface $entityManager,Request $request,ReclamationRepository $reclamationRepository): Response
    {
        $reclamations = $entityManager
            ->getRepository(Reclamation::class)
            ->findAll();

            /////////
            $back = null;
            
            if($request->isMethod("POST")){
                if ( $request->request->get('optionsRadios')){
                    $SortKey = $request->request->get('optionsRadios');
                    switch ($SortKey){
                        case 'nomReclamation':
                            $reclamations = $reclamationRepository->SortBynomReclamation();
                            break;
    
                        case 'prenomReclamation':
                            $reclamations = $reclamationRepository->SortByprenomReclamation();
                            break;

                        case 'typeReclamation':
                            $reclamations = $reclamationRepository->SortBytypeReclamation();
                            break;
    
    
                    }
                }
                else
                {
                    $type = $request->request->get('optionsearch');
                    $value = $request->request->get('Search');
                    switch ($type){
                        case 'nomReclamation':
                            $reclamations = $reclamationRepository->findBynomReclamation($value);
                            break;
    
                        case 'prenomReclamation':
                            $reclamations = $reclamationRepository->findByprenomReclamation($value);
                            break;
    
                        case 'typeReclamation':
                            $reclamations = $reclamationRepository->findBytypeReclamation($value);
                            break;
    

                    }
                }

                if ( $reclamations){
                    $back = "success";
                }else{
                    $back = "failure";
                }
            }
                ////////

        return $this->render('reclamation/index.html.twig', [
            'reclamations' => $reclamations,
        ]);
    }
    
    #[Route('/statistique', name: 'stats3')]
            public function stat()
    {

        $repository = $this->getDoctrine()->getRepository(Reclamation::class);
        $reponse= $repository->findAll();

        $em = $this->getDoctrine()->getManager();


        $pr1 = 0;
        $pr2 = 0;


        foreach ($reponse as $reclamation) {
            if ($reclamation->getTypeReclamation() =="espritesprit")  :

                $pr1 += 1;
            else:

                $pr2 += 1;

            endif;

        }

        $pieChart = new PieChart();
        $pieChart->getData()->setArrayToDataTable(
            [['Prix', 'Nom'],
                ['reclamation ID ', $pr1],
                ['reclamation non ID', $pr2],
            ]
        );
        $pieChart->getOptions()->setTitle('Repartition Des Reclamations Reçues');
        $pieChart->getOptions()->setHeight(1000);
        $pieChart->getOptions()->setWidth(1400);
        $pieChart->getOptions()->getTitleTextStyle()->setBold(true);
        $pieChart->getOptions()->getTitleTextStyle()->setColor('black');
        $pieChart->getOptions()->getTitleTextStyle()->setItalic(true);
        $pieChart->getOptions()->getTitleTextStyle()->setFontName('Arial');
        $pieChart->getOptions()->getTitleTextStyle()->setFontSize(30);

       

        return $this->render('reclamation/stat.html.twig', array('piechart' => $pieChart));
    }     
//Ajout
    #[Route('/new', name: 'app_reclamation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reclamation = new Reclamation();
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contenu = $reclamation->getDescriptionReclamation(); // get the contenu from the Reclamation object
        $badWords = ['shit','fuck', 'Fuck off', 'piss off','bugger off','Bloody hell','Bastard','Bollocks','Motherfucker','Son of a bitch','Asshole','ass','va te faire foudre','nigga'];
        $cleanDescription = str_ireplace($badWords, '****', $contenu); // replace bad words with **

        $reclamation->setDescriptionReclamation($cleanDescription); // update the Reclamation object with the cleaned contenu
        
            $entityManager->persist($reclamation);
            $entityManager->flush();

            return $this->redirectToRoute('app_reclamation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reclamation/new.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form,
        ]);
    }
//afficher
    #[Route('/{id}', name: 'app_reclamation_show', methods: ['GET'])]
    public function show(Reclamation $reclamation): Response
    {
        return $this->render('reclamation/show.html.twig', [
            'reclamation' => $reclamation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_reclamation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_reclamation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reclamation/edit.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reclamation_delete', methods: ['POST'])]
    public function delete(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reclamation->getId(), $request->request->get('_token'))) {
            $entityManager->remove($reclamation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reclamation_index', [], Response::HTTP_SEE_OTHER);
    }
}
