<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\User;

use App\Repository\ReservationRepository;
use App\Form\ReservationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Service\MailerService;
use Symfony\Component\Mime\Email;
#[Route('/reservation')]
class ReservationController extends AbstractController
{


   
    


#[Route('/calendar/{id}', name: 'app_reservation_indexA' , methods: ['GET'])]
    public function indexA (ReservationRepository $reservationRepository, User $idUser  ){
     $reservations = $reservationRepository->findBy(['id_user' => $idUser->getId()]);

     $reservs= [];
     foreach($reservations as $reservation){
        $reservs[] = [
            'id'=> $reservation->getId(),
            'id_user' => $reservation->getUser(),
            'start' => $reservation->getDateDebut()->format('Y-m-d H:i:s'),
            'end' => $reservation->getDateFin()->format('Y-m-d H:i:s'),
            'descp' => $reservation->getDescriptionReservation(),

        ];
    
     }
     $data = json_encode($reservs);
     return $this->render('reservation/calendar.html.twig', compact('data'));

    }

    #[Route('/calendar', name: 'app_reservation_indexB' , methods: ['GET'])]
    public function indexB (ReservationRepository $reservationRepository ){
     $reservations = $reservationRepository->findAll();

     $reservs= [];
     foreach($reservations as $reservation){
        $reservs[] = [
            'id'=> $reservation->getId(),
            'id_user' => $reservation->getUser(),
            'start' => $reservation->getDateDebut()->format('Y-m-d H:i:s'),
            'end' => $reservation->getDateFin()->format('Y-m-d H:i:s'),
            'descp' => $reservation->getDescriptionReservation(),

        ];
    
     }
     $data = json_encode($reservs);
     return $this->render('reservation/calendar.html.twig', compact('data'));

    }


    #[Route('/trier', name: 'app_reservation_trier', methods: ['GET'])]
    public function trier (EntityManagerInterface $entityManager): Response
    { $reservations = $entityManager
        ->getRepository(Reservation::class)
        ->findBy(array(),array('dateDebut' =>'ASC'));;

    return $this->render('reservation/index.html.twig', [
        'reservations' => $reservations,
        
        
        
        ]);
    }




    #[Route('/', name: 'app_reservation_index', methods: ['GET','POST'])]
    public function index(EntityManagerInterface $entityManager,Request $request,ReservationRepository $reservationRepository): Response
    {
        $reservations = $entityManager
            ->getRepository(Reservation::class)
            ->findAll();

            /////////
            $back = null;
            
            if($request->isMethod("POST")){
                if ( $request->request->get('optionsRadios')){
                    $SortKey = $request->request->get('optionsRadios');
                    switch ($SortKey){
                        case 'id':
                            $reservations = $reservationRepository->SortByidReservation();
                            break;
    
                        case 'dateDebut':
                            $reservations = $reservationRepository->SortBydateDebut();
                            break;

                        case 'descriptionReservation':
                            $reservations = $reservationRepository->SortBydescriptionReservation();
                            break;
    
    
                    }
                }
                else
                {
                    $type = $request->request->get('optionsearch');
                    $value = $request->request->get('Search');
                    switch ($type){
                        case 'id':
                            $reservations = $reservationRepository->findByidReservation($value);
                            break;
    
                        case 'dateDebut':
                            $reservations = $reservationRepository->findBydateDebut($value);
                            break;
    
                        case 'descriptionReservation':
                            $reservations = $reservationRepository->findBydescriptionReservation($value);
                            break;
    
    
                    }
                }

                if ( $reservations){
                    $back = "success";
                }else{
                    $back = "failure";
                }
            }
                ////////

        return $this->render('reservation/index.html.twig', [
            'reservations' => $reservations,
        ]);
    }

    


    #[Route('/front', name: 'app_reservation_Front', methods: ['GET'])]
    
    public function Front(EntityManagerInterface $entityManager): Response
    {
        $reservations = $entityManager
            ->getRepository(Reservation::class)
            ->findAll();

        return $this->render('reservation/indexFront.html.twig', [
            'reservations' => $reservations,
        ]);
    }


    #[Route('/new', name: 'app_reservation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager,MailerService $mailer): Response
    {
        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entityManager->persist($reservation);
            $id=$reservation->getUser();
            $medecin=$entityManager->getRepository(User::class)->find($id);
          $email=  $medecin->getEmail();
          $nom=$medecin->getName();
          
            $mailer->sendEmail($email,$nom);
            $entityManager->flush();
        




            return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reservation/new.html.twig', [
            'reservation' => $reservation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reservation_show', methods: ['GET'])]
    public function show(Reservation $reservation): Response
    {
        return $this->render('reservation/show.html.twig', [
            'reservation' => $reservation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_reservation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reservation/edit.html.twig', [
            'reservation' => $reservation,
            'form' => $form,
        ]);
    }

    

    #[Route('/{id}', name: 'app_reservation_delete', methods: ['POST'])]
    public function delete(Request $request, Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reservation->getId(), $request->request->get('_token'))) {
            $entityManager->remove($reservation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
    }
    
 
    
    
    
}
