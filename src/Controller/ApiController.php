<?php
 namespace App\Controller;

 use App\Entity\Calendar;
use App\Entity\Reservation;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController {

    /**
     * @Route("/api", name="api")
     */
    public function index()
    {
        return $this->render('api/index.html.twig', [
            'controller_name' => 'ApiController',
        ]);
    }
     /**
     * @Route("/api/{id}/edit", name="api_reservation_edit", methods={"PUT"})
     */
    public function majReservation(?Reservation $reservation, Request $request)
    {
        // On récupère les données
        $donnees = json_decode($request->getContent());

        if(
            isset($donnees->start) && !empty($donnees->start) &&
            isset($donnees->descp) && !empty($donnees->descp)&&
            isset($donnees->end) && !empty($donnees->end)
        ){
            // Les données sont complètes
            // On initialise un code
            $code = 200;

            // On vérifie si l'id existe
            if(!$reservation){
                // On instancie un rendez-vous
                $reservation = new Reservation();

                // On change le code
                $code = 201;
            }

            // On hydrate l'objet avec les données
        
            $reservation->setDescriptionReservation($donnees->descp);
            $reservation->setDateDebut(new DateTime($donnees->start));
            if($donnees->allDay){
                $reservation->setFin(new DateTime($donnees->start));
            }else{
                $reservation->setDateFin(new DateTime($donnees->end));
            }
         


            $em = $this->getDoctrine()->getManager();
            $em->persist($reservation);
            $em->flush();

            // On retourne le code  ;
            return new Response('Ok', $code);
        }else{
            // Les données sont incomplètes
            return new Response('Données incomplètes', 404);
        }


        return $this->redirectToRoute('reservation_index');
    }
}