<?php

namespace App\Controller;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\Material\BarChart;
use App\Repository\DemandeRepository;
use App\Entity\Demande;
use App\Entity\Offre;
use App\Service\Asma;
use Symfony\Component\Mime\Email;
use App\Form\DemandeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\FileType;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Doctrine\Persistence\ManagerRegistry;
use App\Service\UploaderService;
use App\Entity\User;
use App\Form\userType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf;
use PhpOffice\PhpSpreadsheet\Reader\Exception;







#[Route('/demande')]
class DemandeController extends AbstractController
{

    #[Route('/statisdemande', name: 'app_demande_statisdemande', methods: ['GET'])]
    public function statisreclamation(DemandeRepository $demandeRepository)
    {
        //on va chercher les categories
        $rech = $demandeRepository->barDep();
        $arr = $demandeRepository->barArr();
        
        $bar = new barChart ();
        $bar->getData()->setArrayToDataTable(
            [['demande', 'traitement'],
             ['en cours de traitement', intVal($rech)],
             ['Traité', intVal($arr)],
            
    
            ]
        );
    
        $bar->getOptions()->setTitle('les Demandes');
        $bar->getOptions()->getHAxis()->setTitle('Nombre de demande');
        $bar->getOptions()->getHAxis()->setMinValue(0);
        $bar->getOptions()->getVAxis()->setTitle('etat');
        $bar->getOptions()->SetWidth(800);
        $bar->getOptions()->SetHeight(400);
         
    
        return $this->render('demande/statis.html.twig', array('bar'=> $bar )); 
    
    }



  
#[Route('/a', name: 'app_demande_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $demandes = $entityManager
            ->getRepository(Demande::class)
            ->findAll();

        return $this->render('demande/index.html.twig', [
            'demandes' => $demandes,
        ]);
    }
    #[Route('/f', name: 'app_demande_indexf', methods: ['GET'])]
    public function indexf(EntityManagerInterface $entityManager): Response
    {
        $demandes = $entityManager
            ->getRepository(Demande::class)
            ->findAll();

        return $this->render('demande/indexf.html.twig', [
            'demandes' => $demandes,
        ]);
    }

   
    #[Route('/', name: 'app_demande_new', methods: ['GET', 'POST'])]
   
    public function new(Request $request, EntityManagerInterface $entityManager,SluggerInterface $slugger): Response
   {
       
        $demande = new Demande();
        $form = $this->createForm(DemandeType::class, $demande);#Générer le rendu du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
           
            $cv = $form->get('cv')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($cv) {
                $originalFilename = pathinfo($cv->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$cv->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $cv->move(
                        $this->getParameter('demande_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $demande->setCv($newFilename);
            }

            $entityManager->persist($demande);
            $entityManager->flush();

            return $this->redirectToRoute('app_demande_index', [], Response::HTTP_SEE_OTHER);
}

        return $this->renderForm('demande/new.html.twig', [
            'demande' => $demande,
            'form' => $form,
        ]);
    }

    #[Route('/newfront', name: 'app_demande_newfront', methods: ['GET', 'POST'])]
    public function newfront(Request $request, EntityManagerInterface $entityManager): Response
    {
        $demande = new Demande();
        $form = $this->createForm(DemandeType::class, $demande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
           

            $entityManager->persist($demande);
            $entityManager->flush();

            return $this->redirectToRoute('app_demande_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('demande/newfront.html.twig', [
            'demande' => $demande,
            'form' => $form,
        ]);
    }
    #[Route('/{id}', name: 'app_demande_show', methods: ['GET'])]
    public function show(Demande $demande): Response
    {
        return $this->render('demande/show.html.twig', [
            'demande' => $demande,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_demande_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Demande $demande, EntityManagerInterface $entityManager, SluggerInterface $slugger  ): Response
    {   
        
        $form = $this->createForm(DemandeType::class, $demande);
        $form->handleRequest($request);
                
        if ($form->isSubmitted() && $form->isValid()) {
            $cv = $form->get('cv')->getData();
            $fichier = md5(uniqid()).'.'.$cv->guessExtension();
            $cv->move(
                $this->getParameter('demande_directory'),
                $fichier);
                $demande->setCv($fichier);
            
            $entityManager->flush();

            return $this->redirectToRoute('app_demande_index', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->renderForm('demande/edit.html.twig', [
            'demande' => $demande,
            'form' => $form,
            

        ]);
    }
    #[Route('/{id}/repondre', name: 'app_demande_repondre', methods: ['GET', 'POST'])]
    public function Repondre(Request $request, EntityManagerInterface $entityManager, $id ,Asma $mailer): Response
    {   
        
        $demande = $entityManager->getRepository(Demande::class)->find($id);
            $id_client=$demande->getUser();
            $id_offre=$demande->getOffre();
            $client = $entityManager->getRepository(User::class)->find($id_client);
            $offre = $entityManager->getRepository(Offre::class)->find($id_offre);
            $to=$client->getEmail();
            $s=$offre->getNomOffre();
            $post='Nous vous remercions pour votre candidature au poste de '.$s.'Nous vous informant que Après examen minutieux de votre dossier, nous sommes intéressés par votre profil et souhaitons vous rencontrer personnellement.veuillez venir !';
            //dd($to);
            $mailer->sendEmail($post,$to);
            $demande->setTraitement ("Traité");
            
            $entityManager->flush();
       
       return $this->redirectToRoute('app_demande_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/{id}', name: 'app_demande_delete', methods: ['POST'])]
    public function delete(Request $request, Demande $demande, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$demande->getId(), $request->request->get('_token'))) {
            $entityManager->remove($demande);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_demande_index', [], Response::HTTP_SEE_OTHER);
    }
    private function generateUniqueFileName()
    {
        // md5() reduces the similarity of the file names generated by
        // uniqid(), which is based on timestamps
        return md5(uniqid());
    }
}
