<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Form\Evenement1Type;
use App\Repository\EvenementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use App\Entity\PdfGeneratorService;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;


#[Route('/evenement')]
class EvenementController extends AbstractController
{


    #[Route("/evenement/{id}", name: "evenement")]
    public function offreId($id, NormalizerInterface $normalizer, EvenementRepository $repo)
    {
        $offre = $repo->find($id);
        $offreNormalises = $normalizer->normalize($offre, 'json', ['groups' => "Evenement"]);
        return new Response(json_encode($offreNormalises));
    }
    
#[Route('/Get', name: 'evenement')] 
public function getoffre(EvenementRepository $repo, SerializerInterface $serializer)
{
    $evenement = $repo->findAll();
    //* Nous utilisons la fonction normalize qui transforme le tableau d'objets 
    //* students en  tableau associatif simple.
    // $studentsNormalises = $normalizer->normalize($students, 'json', ['groups' => "students"]);

    // //* Nous utilisons la fonction json_encode pour transformer un tableau associatif en format JSON
    // $json = json_encode($studentsNormalises);

    $json = $serializer->serialize($evenement, 'json', ['groups' => "Evenement"]);

    //* Nous renvoyons une réponse Http qui prend en paramètre un tableau en format JSON
    return new Response($json);
}

#[Route("/Addjson", name: "Addjson")]
public function addStudentJSON(Request $req,   NormalizerInterface $Normalizer)
{

    $em = $this->getDoctrine()->getManager();
    $evenement = new Evenement();
    $evenement->setTypeEvenement($req->get('typeEvenement'));
    $evenement->setNomEvenement($req->get('nomEvenement'));
    $evenement->setLieuEvenement($req->get('lieuEvenement'));
    $em->persist($evenement);
    $em->flush();

    $jsonContent = $Normalizer->normalize($evenement, 'json', ['groups' => 'Evenement']);
    return new Response(json_encode($jsonContent));
}

    #[Route('/pdf', name: 'generator_service3')]
    public function pdfService(): Response
    { 
        $evenements= $this->getDoctrine()
        ->getRepository(Evenement::class)
        ->findAll();

   

        $html =$this->renderView('pdf/indexevenement.html.twig', ['evenements' => $evenements]);
        $pdfGeneratorService=new PdfGeneratorService();
        $pdf = $pdfGeneratorService->generatePdf($html);

        return new Response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="document.pdf"',
        ]);

    }


    
    #[Route('/stat', name: 'app_evenement_indexstat', methods: ['GET','POST'])]
    public function index3(EvenementRepository $evenementRepository): Response
    {
        $evenement = $evenementRepository->findAll();
        $nomEvenement = [];
        $color = [];
        $evcount = [];

        // On "démonte" les données pour les séparer tel qu'attendu par ChartJS
        foreach($evenement as $evenements){
            $nomEvenement[] = $evenements->getNomEvenement();
            $color[] = $evenements->getColor();
            $evcount[] = count($evenements->getParticipation());
        }

        
        return $this->render('evenement/stat.html.twig', [
            'nomEvenement' => json_encode($nomEvenement),
            'color' => json_encode($color),
            'evcount' => json_encode($evcount),
        ]);
    }



    #[Route('/show_in_map/{id}', name: 'app_evenement_map', methods: ['GET'])]
    public function Map( Evenement $id,EntityManagerInterface $entityManager ): Response
    {

        $id = $entityManager
            ->getRepository(Evenement::class)->findBy( 
                ['id'=>$id ]
            );
        return $this->render('evenement/api_arcgis.html.twig', [
            'evenements' => $id,
        ]);
    }




    
    #[Route('/', name: 'app_evenement_index', methods: ['GET','POST'])]
    public function index(EntityManagerInterface $entityManager,EvenementRepository $evenementRepository,Request $request): Response
    {
        $evenements = $entityManager
        ->getRepository(Evenement::class)
        ->findAll();

        /////////
        $back = null;
        
        if($request->isMethod("POST")){
            if ( $request->request->get('optionsRadios')){
                $SortKey = $request->request->get('optionsRadios');
                switch ($SortKey){
                    case 'nomEvenement':
                        $evenements = $evenementRepository->SortByNomEvenement();
                        break;

                    case 'typeEvenement':
                        $evenements = $evenementRepository->SortByTypeEvenement();
                        break;

                    case 'lieuEvenement':
                        $evenements = $evenementRepository->SortBylieuEvenement();
                        break;


                }
            }
            else
            {
                $type = $request->request->get('optionsearch');
                $value = $request->request->get('Search');
                switch ($type){
                    case 'nomEvenement':
                        $evenements = $evenementRepository->findBynomEvenement($value);
                        break;

                    case 'typeEvenement':
                        $evenements = $evenementRepository->findBylieuEvenement($value);
                        break;

                    case 'dateDebut':
                        $evenements = $evenementRepository->findBydateDebut($value);
                        break;

                    case 'dateFin':
                        $evenements = $evenementRepository->findBydateFin($value);
                        break;


                }
            }

            if ( $evenements){
                $back = "success";
            }else{
                $back = "failure";
            }
        }
            ////////

    return $this->render('evenement/index.html.twig', [
        'evenements' => $evenements,'back'=>$back
    ]);
    }

    #[Route('/front', name: 'app_evenement_indexFront', methods: ['GET'])]
    public function index2(EntityManagerInterface $entityManager,Request $request,PaginatorInterface $paginator): Response
    {
        $evenements = $entityManager
            ->getRepository(Evenement::class)
            ->findAll();

                $evenements = $paginator->paginate(
                $evenements, /* query NOT result */
                $request->query->getInt('page', 1),
                3
            );
            

        return $this->render('evenement/indexFront.html.twig', [
            'evenements' => $evenements,
        ]);
    }

    
    #[Route('/new', name: 'app_evenement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EvenementRepository $evenementRepository, EntityManagerInterface $entityManager): Response
    {
        $evenement = new Evenement();
        $form = $this->createForm(Evenement1Type::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $evenement->getImageEvenement();
            $filename = md5(uniqid()).'.'.$file->guessExtension();
            $file->move($this->getParameter('uploads'),$filename);
            $evenement->setImageEvenement($filename);
        $entityManager->persist($evenement);
        $entityManager->flush();

            return $this->redirectToRoute('app_evenement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('evenement/new.html.twig', [
            'evenement' => $evenement,
            'form' => $form,
        ]);
    }
    


    #[Route('/{id}', name: 'app_evenement_show', methods: ['GET'])]
    public function show(Evenement $evenement): Response
    {
        return $this->render('evenement/show.html.twig', [
            'evenement' => $evenement,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_evenement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Evenement $evenement, EvenementRepository $evenementRepository): Response
    {
        $form = $this->createForm(Evenement1Type::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $file = $evenement->getImageEvenement();
            $filename = md5(uniqid()).'.'.$file->guessExtension();
            $file->move($this->getParameter('uploads'),$filename);
            $evenement->setImageEvenement($filename);
            $evenementRepository->save($evenement, true);

            return $this->redirectToRoute('app_evenement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('evenement/edit.html.twig', [
            'evenement' => $evenement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_evenement_delete', methods: ['POST'])]
    public function delete(Request $request, Evenement $evenement, EvenementRepository $evenementRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$evenement->getId(), $request->request->get('_token'))) {
            $evenementRepository->remove($evenement, true);
        }

        return $this->redirectToRoute('app_evenement_index', [], Response::HTTP_SEE_OTHER);
    }
}
