<?php

namespace App\Controller;

use App\Entity\Demande;
use App\Repository\DemandeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\Material\BarChart;

class StatisticController extends AbstractController
{
    /**
     * @Route("/statistic", name="app_statistic")
     */
    public function index(): Response
    {
        return $this->render('statistic/stats.html.twig', [
            'controller_name' => 'StatisticController',
        ]);
    }


   /**
    * @Route("/stats", name="rec_stat")
    */
    public function statistiques (DemandeRepository $rep){

        //chercher les types de reclamation

        $demandes = $rep->countByNbr();

        $recType = [];
        $recCount = [];


        foreach($demandes as $demande){

            // $recType[] = $reclamation->getType();
            $recType[] = $demande ['nom'];
            $recCount[]= $demande ['nbr'];
            // $recCount[] = count($recType);
        }
        return $this->render('statistic/stats.html.twig', [
            'recType' => json_encode($recType),
            'recCount' => json_encode($recCount),


        ]);

        
    }
}
?>