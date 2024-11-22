<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\PieChart;
use App\Entity\PdfGeneratorService;
use App\Entity\Pdfuser;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/', name: 'app_user_index', methods: ['GET','POST'])]
    public function index(EntityManagerInterface $entityManager,Request $request,UserRepository $userRepository): Response
    {
        $users = $entityManager
        ->getRepository(User::class)
        ->findAll();
        $back = null;
            
        if($request->isMethod("POST")){
            if ( $request->request->get('optionsRadios')){
                $SortKey = $request->request->get('optionsRadios');
                switch ($SortKey){
                    case 'id':
                        $users = $userRepository->SortByid();
                        break;

                    case 'email':
                        $users = $userRepository->SortByemail();
                        break;

                    case 'telephone':
                        $users = $userRepository->SortBytelephone();
                        break;


                }
            }
            else
            {
                $type = $request->request->get('optionsearch');
                $value = $request->request->get('Search');
                switch ($type){
                    case 'id':
                        $users = $userRepository->findByid($value);
                        break;

                    case 'email':
                        $users = $userRepository->findByemail($value);
                        break;

                    case 'telephone':
                        $users = $userRepository->findBytelephone($value);
                        break;

                


                }
            }

            if ( $users){
                $back = "success";
            }else{
                $back = "failure";
            }
        }
        return $this->render('user/index.html.twig', [
            'users' => $users,'back'=>$back
        ]);
    }

    #[Route('/statistique', name: 'stats')]
    public function stat()
        {
    
            $repository = $this->getDoctrine()->getRepository(User::class);
            $user= $repository->findAll();
    
            $em = $this->getDoctrine()->getManager();
    
    
            $pr1 = 0;
            $pr2 = 0;
    
    
            foreach ($user as $user) {
                if ($user->getRoles() == ["client"])  :
    
                    $pr1 += 1;
                else:
    
                    $pr2 += 1;
    
                endif;
    
            }
            $pr2-=1 ;
            $pieChart = new PieChart();
            $pieChart->getData()->setArrayToDataTable(
                [['medecin', 'client'],
                    ['client ', $pr1],
                    ['medecin', $pr2],
                ]
            );
            $pieChart->getOptions()->setTitle('statistique a partir des ROLE');
            $pieChart->getOptions()->setHeight(1000);
            $pieChart->getOptions()->setWidth(1400);
            $pieChart->getOptions()->getTitleTextStyle()->setBold(true);
            $pieChart->getOptions()->getTitleTextStyle()->setColor('green');
            $pieChart->getOptions()->getTitleTextStyle()->setItalic(true);
            $pieChart->getOptions()->getTitleTextStyle()->setFontName('Arial');
            $pieChart->getOptions()->getTitleTextStyle()->setFontSize(30);
    
           
    
            return $this->render('user/stat.html.twig', array('piechart' => $pieChart));
        }
    
    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserRepository $userRepository): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->save($user, true);

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, UserRepository $userRepository): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->save($user, true);

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, UserRepository $userRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $userRepository->remove($user, true);
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/pdf/user', name: 'generator_service')]
    public function pdfService(): Response
    { 
        $user= $this->getDoctrine()
        ->getRepository(user::class)
        ->findAll();

   

        $html =$this->renderView('pdf/indexuser.html.twig', ['user' => $user]);
        $pdfuser=new Pdfuser();
        $pdf = $pdfuser->generatePdf($html);

        return new Response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="document.pdf"',
        ]);
       
    }


}
