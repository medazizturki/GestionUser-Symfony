<?php

namespace App\Controller;
use App\Entity\PdfGeneratorService;
use App\Entity\Offre;
use App\Form\OffreType;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Asma; 
use Symfony\Component\Mime\Email;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use App\Repository\OffreRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPaginationInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
#[Route('/offre')]
class OffreController extends AbstractController
{

    #[Route('/f/{page}/{nbre}', name: 'app_offre_indexf', methods: ['GET'])]
    public function indexf(EntityManagerInterface $entityManager,ManagerRegistry $doctrine,$page ,$nbre): Response
    {
        $repository = $doctrine->getRepository(Offre::class);
        $nbOffre = $repository->count([]);
        $nbrePage = ceil($nbOffre / $nbre) ;
    
        $offres = $repository->findBy([], [],$nbre, (intval($page) - 1 ) * $nbre);
    
        return $this->render('offre/indexf.html.twig', [
            'offres' => $offres,
            'isPaginated' => true,
            'nbrePage' => $nbrePage,
            'page' => $page,
            'nbre' => $nbre
    
        ]);
    }
    
 #[Route('/pdf/offre', name: 'generator_service2')]
    public function pdfOffre(): Response
    { 
        $offre= $this->getDoctrine()
        ->getRepository(Offre::class)
        ->findAll();

   

        $html =$this->renderView('pdf/index.html.twig', ['offre' => $offre]);
        $pdfGeneratorService=new PdfGeneratorService();
        $pdf = $pdfGeneratorService->generatePdf($html);

        return new Response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="document.pdf"',

        ]);
       
    }


    #[Route("/Offre/{id}", name: "Offre")]
    public function offreId($id, NormalizerInterface $normalizer, OffreRepository $repo)
    {
        $offre = $repo->find($id);
        $offreNormalises = $normalizer->normalize($offre, 'json', ['groups' => "Offre"]);
        return new Response(json_encode($offreNormalises));
    }
    
#[Route('/Get', name: 'liste')] 
public function getoffre(OffreRepository $repo, SerializerInterface $serializer)
{
    $offre = $repo->findAll();
    //* Nous utilisons la fonction normalize qui transforme le tableau d'objets 
    //* students en  tableau associatif simple.
    // $studentsNormalises = $normalizer->normalize($students, 'json', ['groups' => "students"]);

    // //* Nous utilisons la fonction json_encode pour transformer un tableau associatif en format JSON
    // $json = json_encode($studentsNormalises);

    $json = $serializer->serialize($offre, 'json', ['groups' => "Offre"]);

    //* Nous renvoyons une réponse Http qui prend en paramètre un tableau en format JSON
    return new Response($json);
}

#[Route("/create", name: "addStudentJSON")]
public function addStudentJSON(Request $req,   NormalizerInterface $Normalizer)
{

    $em = $this->getDoctrine()->getManager();
    $Offre = new Offre();
    $Offre->setNomOffre($req->get('nomOffre'));
   // $Offre->setDatepubOffre($req->get('datepubOffre'));
    $em->persist($Offre);
    $em->flush();

    $jsonContent = $Normalizer->normalize($Offre, 'json', ['groups' => 'Offre']);
    return new Response(json_encode($jsonContent));
}



    #[Route('/', name: 'app_offre_index', methods: ['GET','POST'])]
    public function index(EntityManagerInterface $entityManager ,OffreRepository $offreRepository  ,Request $request  ): Response
    {
        $offres = $entityManager
            ->getRepository(Offre::class)
            ->findAll();
            $back = null;
            
            if($request->isMethod("POST")){
                if ( $request->request->get('optionsRadios')){
                    $SortKey = $request->request->get('optionsRadios');
                    switch ($SortKey){
                        case 'nomOffre':
                            $offres = $offreRepository->SortByNomoffre();
                            break;
                        case 'datepubOffre':
                            $offres = $offreRepository->SortByDate();
                            break;
    
                        

                        
    
    
                    }
                }
                else
                {
                    $type = $request->request->get('optionsearch');
                    $value = $request->request->get('Search');
                    switch ($type){
                        case 'nomOffre':
                            $offres = $offreRepository->findBynomoffre($value);
                            break;
                        case 'datepubOffre':
                            $offres = $offreRepository->findByDate($value);
                            break;
                        
    
    
                    }
                }

                if ( $offres){
                    $back = "success";
                }else{
                    $back = "failure";
                }
            }
            
        return $this->render('offre/index.html.twig', [
            'offres' => $offres,
            'back'=>$back
        ]);
    }
    
    /*public function myControllerMethod(Request $request, PaginatorInterface $paginator)
{
    $queryBuilder = $this->getDoctrine()->getRepository(Offre::class)->createQueryBuilder('e');
    $offres =$paginator->paginate($queryBuilder, $request->query->getInt('page', 1), 10);
    
    return $this->render('offre/index.html.twig', [
        'offres' => $offres,
    ]);
}*/
   
    #[Route('/new', name: 'app_offre_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager ,NotifierInterface $notifier,Asma $mailer): Response
    {
        $offre = new Offre();
        $form = $this->createForm(OffreType::class, $offre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($offre);
            $nomOffre = $form->get('nomOffre')->getData();
            $offres = $entityManager
            ->getRepository(Offre::class)
            ->findBy(['nomOffre'=>$nomOffre]);
            if (empty($offres)) 
           {
            $entityManager->flush();
            $users=$entityManager->getRepository(User::class)->findAll();
            
            foreach($users as $user)
            {
                $to=$user->getEmail();
                $twig = $this->container->get('twig');
             //  $html=$twig->render('offre/mail.html.twig');
             $post='nous vous informons qu une nouvelle offre de travail a été posté  ';
            $mailer->sendEmail($post,$to);
            }
            return $this->redirectToRoute('app_offre_index', [], Response::HTTP_SEE_OTHER);
           }
           else 
           {
            $notifier->send(new Notification('Offre existe déja ', ['browser']));
            return $this->redirectToRoute('app_offre_new', [], Response::HTTP_SEE_OTHER);

           }

        }

        return $this->renderForm('offre/new.html.twig', [
            'offre' => $offre,
            'form' => $form,
        ]);
    }
    
    #[Route('/{id}', name: 'app_offre_show', methods: ['GET'])]
    public function show(Offre $offre): Response
    {
        return $this->render('offre/show.html.twig', [
            'offre' => $offre,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_offre_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Offre $offre, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(OffreType::class, $offre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_offre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('offre/edit.html.twig', [
            'offre' => $offre,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_offre_delete', methods: ['POST'])]
    public function delete(Request $request, Offre $offre, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$offre->getId(), $request->request->get('_token'))) {
            $entityManager->remove($offre);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_offre_index', [], Response::HTTP_SEE_OTHER);
    }
    
}
