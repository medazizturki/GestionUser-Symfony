<?php

namespace App\Controller;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Repository\RendezvousRepository;
use App\Entity\Rendezvous;
use App\Form\RendezvousType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\MailerServiceRendezvous;
use Symfony\Component\Mime\Email;
use Endroid\QrCode\QrCode;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\PdfGeneratorService;


use Dompdf\Dompdf;
use Symfony\Flex\Options;

#[Route('/rendezvous')]
class RendezvousController extends AbstractController
{

    
    #[Route("/Get", name: "list")]
    //* Dans cette fonction, nous utilisons les services NormlizeInterface et StudentRepository, 
    //* avec la méthode d'injection de dépendances.
    public function getRendezvous(RendezvousRepository $repo, SerializerInterface $serializer)
    {
        $Rendezvous = $repo->findAll();
        //* Nous utilisons la fonction normalize qui transforme le tableau d'objets 
        //* students en  tableau associatif simple.
        // $studentsNormalises = $normalizer->normalize($students, 'json', ['groups' => "students"]);

        // //* Nous utilisons la fonction json_encode pour transformer un tableau associatif en format JSON
        // $json = json_encode($studentsNormalises);

        $json = $serializer->serialize($Rendezvous, 'json', ['groups' => "Rendezvous"]);

        //* Nous renvoyons une réponse Http qui prend en paramètre un tableau en format JSON
        return new Response($json);
    }


    #[Route('/pdf/rendezvous', name: 'generator_service1')]
    public function pdfService(): Response
    { 
        $rendezvous= $this->getDoctrine()
        ->getRepository(Rendezvous::class)
        ->findAll();

   

        $html =$this->renderView('pdf/indexrendezvous.html.twig', ['rendezvous' => $rendezvous]);
        $pdfGeneratorService=new PdfGeneratorService();
        $pdf = $pdfGeneratorService->generatePdf($html);

        return new Response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="document.pdf"',
        ]);

    }



    #[Route('/statistics', name: 'matches_Rendezvous')]
    public function statistics(ManagerRegistry $doctrine): Response {
        $em = $doctrine->getManager();
        $RendezvousRepository = $em->getRepository(Rendezvous::class);
    
        // Get the total number of matches
        $totalMatches = $RendezvousRepository->createQueryBuilder('m')
            ->select('COUNT(m.id)')
            ->getQuery()
            ->getSingleScalarResult();
    
        // Get the number of matches per team
        $teamMatches = $RendezvousRepository->createQueryBuilder('m')
            ->select('e.nomRendezvous AS RendezvousName', 'COUNT(m.id) AS RendezvousCount')
            ->leftJoin('m.User', 'e')
            ->groupBy('e.User')
            ->getQuery()
            ->getResult();
    
        return $this->render('rendezvous/statistics.html.twig', [
            'totalMatches' => $totalMatches,
            'teamMatches' => $teamMatches,
        ]);
    }
    #[Route('/show_in_map/{id}', name: 'app_Rendezvous_map', methods: ['GET'])]
    public function Map( Rendezvous $id,EntityManagerInterface $entityManager ): Response
    {

        $Rendezvous = $entityManager
            ->getRepository(Rendezvous::class)->findBy( 
                ['id'=>$id ]
            );
        return $this->render('rendezvous/api_arcgis.html.twig', [
            'rendezvouses' => $Rendezvous,
        ]);
    }
    #[Route('/Addjson', name: 'app_addjson')]
public function addjson(Request $request)
{
    
    $Rendezvous = new Rendezvous();
    $nomRendezvous = $request->query->get("nomRendezvous");


    // Set the properties of the $produit object directly
    $Rendezvous->setNomRendezvous($nomRendezvous);


    $em = $this->getDoctrine()->getManager();

    $em->persist($Rendezvous);
    $em->flush();

    $serializer = new Serializer([new ObjectNormalizer()]);
    $formatted = $serializer->normalize($Rendezvous);
    return new JsonResponse($formatted);
}
    
  /*  #[Route("/addRendezvousJSON/new", name: "addRendezvousJSON")]
    public function addRendezvousJSON(Request $req,   NormalizerInterface $Normalizer)
    {

        $em = $this->getDoctrine()->getManager();
        $Rendezvous = new Rendezvous();
        
        $user=$this->getUser();
        $Rendezvous->setNomRendezvous($req->get('nomRendezvous'));
        $Rendezvous->setPrenomRendezvous($req->get('prenomRendezvous'));
        $Rendezvous->setLieuRendezvous($req->get('lieuRendezvous'));
        $Rendezvous->setEmailRendezvous($req->get('emailRendezvous'));
        $Rendezvous->setPrixRendezvous($req->get('prixRendezvous')); 
        $Rendezvous->setnb($req->get('nb')); 
        $Rendezvous->setcolor($req->get('color')); 
        //$Rendezvous->setUser($user);
        //$Rendezvous->setUser($req->get('idUser')); 
       $Rendezvous->setUser($user->get('idUser')); 

       
        
        $em->persist($Rendezvous);
        $em->flush();

        $jsonContent = $Normalizer->normalize($Rendezvous, 'json', ['groups' => 'Rendezvous']);
        return new Response(json_encode($jsonContent));
    }*/
    
    #[Route("/rendez/{id}", name: "rendezvous")]
    public function rendezId($id, NormalizerInterface $normalizer, RendezvousRepository $repo)
    {
        $Rendezvous = $repo->find($id);
        $RendezvousNormalises = $normalizer->normalize($Rendezvous, 'json', ['groups' => "Rendezvous"]);
        return new Response(json_encode($RendezvousNormalises));
    }
    


    #[Route('/pdf', name: 'app_rendezvous_pdf', methods: ['GET'])]
    public function index_pdf(RendezvousRepository $rendezvousRepository)
    {
        $pdfoptions=new Options();
        $pdfoptions->get('defaultFont','Arial');
        $dompdf=new dompdf();
        $logo = file_get_contents("logo.png");
        $logobase64 = base64_encode($logo);
        $rendezvouses=$rendezvousRepository->findAll();

        $html=$this->renderView('rendezvous/rendezvoupdf.html.twig', [
            'rendezvouses' => $rendezvouses,
            'logobase64'=>$logobase64,
        ]);
       $dompdf->loadHtml($html);
       $dompdf->setPaper('A4','portrait');
       $dompdf->render();
       $dompdf->stream('Rendez-Vous List.pdf',["Attachement" => false]);

    }


    #[Route('/calendar/{id}', name: 'app_rendezvous_indexA' , methods: ['GET'])]
    public function indexA (RendezvousRepository $rendezvousRepository, User $idUser  ){
     $rendezvous = $rendezvousRepository->findBy(['id_user' => $idUser->getId()]);

     $reservs= [];
     foreach($rendezvous as $rendezvous){
        $reservs[] = [
            'id'=> $rendezvous->getid(),
            'id_user' => $rendezvous->getUser(),
            'start' => $rendezvous->getDateRendezvous()->format('Y-m-d H:i:s'),

        ];
    
     }
     $data = json_encode($reservs);
     return $this->render('rendezvous/calendar.html.twig', compact('data'));

    }

    #[Route('/calendar', name: 'app_rendezvous_indexB' , methods: ['GET'])]
    public function indexB (RendezvousRepository $rendezvousRepository ){
     $rendezvous = $rendezvousRepository->findAll();

     $reservs= [];
     foreach($rendezvous as $rendezvous){
        $reservs[] = [
            'id'=> $rendezvous->getid(),
            'id_user' => $rendezvous->getUser(),
            'start' => $rendezvous->getDateRendezvous()->format('Y-m-d H:i:s'),
            
            

        ];
    
     }
     $data = json_encode($reservs);
     return $this->render('rendezvous/calendar.html.twig', compact('data'));

    }














        /**
     * @Route("/stats", name="stats2")
     */
    public function statistiques(RendezvousRepository $Repo){
        // On va chercher toutes les catégories
        $rendezvous = $Repo->findAll();
        $rendezNom = [];
        $rendezColor = [];
        $rendezCount = [];
        foreach($rendezvous as $rendezvou ){
            $rendezNom[] = $rendezvou->getNomRendezvous();
            $rendezColor[] = $rendezvou->getcolor();
            $rendezCount[] = count($rendezvou->getFacture());

        } 
    return $this->render('rendezvous/stat.html.twig', [
        'rendezNom'=> json_encode($rendezNom),
        'rendezColor'=> json_encode($rendezColor),
        'rendezCount'=> json_encode($rendezCount)
    ]);
    }

    //affichage back
    #[Route('/', name: 'app_rendezvous_index', methods: ['GET','POST'])]
    public function index(EntityManagerInterface $entityManager , RendezvousRepository $rendezvousRepository,Request $request): Response
    {
        $rendezvouses = $entityManager
            ->getRepository(Rendezvous::class)
            ->findAll();
            $back = null;
            
            if($request->isMethod("POST")){
                if ( $request->request->get('optionsRadios')){
                    $SortKey = $request->request->get('optionsRadios');
                    switch ($SortKey){
                        case 'nomRendezvous':
                            $rendezvouses = $rendezvousRepository->SortByNomRendezvous();
                            break;
    
                        case 'emailRendezvous':
                            $rendezvouses = $rendezvousRepository->SortByEmailRendezvous();
                            break;

                        case 'prenomRendezvous':
                            $rendezvouses = $rendezvousRepository->SortByPrenomRendezvous();
                            break;
    
    
                    }
                }
                else
                {
                    $type = $request->request->get('optionsearch');
                    $value = $request->request->get('Search');
                    switch ($type){
                        case 'nomRendezvous':
                            $rendezvouses = $rendezvousRepository->findBynomRendezvous($value);
                            break;
    
                        case 'emailRendezvous':
                            $rendezvouses = $rendezvousRepository->findByemailRendezvous($value);
                            break;
    
                        case 'prenomRendezvous':
                            $rendezvouses = $rendezvousRepository->findByprenomRendezvous($value);
                            break;
    
    
                    }
                }

                if ( $rendezvouses){
                    $back = "success";
                }else{
                    $back = "failure";
                }
            }

        return $this->render('rendezvous/index.html.twig', [
            'rendezvouses' => $rendezvouses, 'back' =>$back
        ]);
    }
    //affichage front
    #[Route('/front', name: 'app_rendezvous_front', methods: ['GET'])]
    public function front(EntityManagerInterface $entityManager): Response
    {
        $rendezvouses = $entityManager
            ->getRepository(Rendezvous::class)
            ->findAll();

        return $this->render('rendezvous/front.html.twig', [
            'rendezvouses' => $rendezvouses,
        ]);
    }
    //affichage
        //affichage front
        #[Route('/rendez', name: 'app_rendezvous_rendez', methods: ['GET'])]
        public function rendez(EntityManagerInterface $entityManager): Response
        {
            $rendezvouses = $entityManager
                ->getRepository(Rendezvous::class)
                ->findAll();
    
            return $this->render('rendezvous/rendez.html.twig', [
                'rendezvouses' => $rendezvouses,
            ]);
        }
//new back
    #[Route('/new', name: 'app_rendezvous_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user=$this->getUser();
        $rendezvou = new Rendezvous();
        $rendezvou->setUser($user);
        $form = $this->createForm(RendezvousType::class, $rendezvou);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($rendezvou);
            $entityManager->flush();

            return $this->redirectToRoute('app_rendezvous_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('rendezvous/new.html.twig', [
            'rendezvou' => $rendezvou,
            'form' => $form,
            'user' => $user,
           
        ]);
    }
//new front 
#[Route('/newfront', name: 'app_rendezvous_newfront', methods: ['GET', 'POST'])]
public function newfront(Request $request, EntityManagerInterface $entityManager, MailerServiceRendezvous $mailer): Response
{
    $user=$this->getUser();
    $rendezvou = new Rendezvous();
    $rendezvou->setUser($user);
    $form = $this->createForm(RendezvousType::class, $rendezvou);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->persist($rendezvou);
       
        $entityManager->flush();
        $id_rendezvous=$rendezvou->getid();
        $to=$rendezvou->getEmailRendezvous();
        $mailer->sendEmail($to);

        return $this->redirectToRoute('app_facture_newFront',array ('id'=>$id_rendezvous) , Response::HTTP_SEE_OTHER);
    }

    return $this->renderForm('rendezvous/newFront.html.twig', [
        'rendezvou' => $rendezvou,
        'form' => $form,
        'user' => $user,
       
    ]);
}
//show front
    #[Route('/{id}', name: 'app_rendezvous_show', methods: ['GET'])]
    public function show(Rendezvous $rendezvou): Response
    {
        return $this->render('rendezvous/show.html.twig', [
            'rendezvou' => $rendezvou,
        ]);
    }
    #[Route('/{id}/Front', name: 'app_rendezvous_showFront', methods: ['GET'])]
    public function showFront(Rendezvous $rendezvou): Response
    {
        return $this->render('rendezvous/showFront.html.twig', [
            'rendezvou' => $rendezvou,
        ]);
    }
//edit back
    #[Route('/{id}/edit', name: 'app_rendezvous_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Rendezvous $rendezvou, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RendezvousType::class, $rendezvou);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_rendezvous_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('rendezvous/edit.html.twig', [
            'rendezvou' => $rendezvou,
            'form' => $form,
        ]);
    }
    //edit front
    #[Route('/{id}/editfront', name: 'app_rendezvous_editfront', methods: ['GET', 'POST'])]
    public function editFront(Request $request, Rendezvous $rendezvou, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RendezvousType::class, $rendezvou);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_rendezvous_front', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('rendezvous/editFront.html.twig', [
            'rendezvou' => $rendezvou,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_rendezvous_delete', methods: ['POST'])]
    public function delete(Request $request, Rendezvous $rendezvou, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$rendezvou->getid(), $request->request->get('_token'))) {
            $entityManager->remove($rendezvou);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_rendezvous_index', [], Response::HTTP_SEE_OTHER);
    }
}
