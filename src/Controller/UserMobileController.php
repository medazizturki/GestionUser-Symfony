<?php

namespace App\Controller;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\Persistence\ManagerRegistry; 
use Symfony\Component\HttpFoundation\Request;

class UserMobileController extends AbstractController
{
    #[Route('/user/mobile', name: 'app_user_mobile')]
    public function index(): Response
    {
        return $this->render('user_mobile/index.html.twig', [
            'controller_name' => 'UserMobileController',
        ]);
    }

    #[Route("/usermobile/list", name: "list")]
    //* Dans cette fonction, nous utilisons les services NormlizeInterface et StudentRepository, 
    //* avec la méthode d'injection de dépendances.
    public function getUsers(UserRepository $repo, SerializerInterface $serializer)
    {
        $users = $repo->findAll();
        //* Nous utilisons la fonction normalize qui transforme le tableau d'objets 
        //* students en  tableau associatif simple.
        // $studentsNormalises = $normalizer->normalize($students, 'json', ['groups' => "students"]);


        
        // //* Nous utilisons la fonction json_encode pour transformer un tableau associatif en format JSON
        // $json = json_encode($studentsNormalises);

        $json = $serializer->serialize($users, 'json', ['groups' => "users"]);

        //* Nous renvoyons une réponse Http qui prend en paramètre un tableau en format JSON
        return new Response($json);
    }

    #[Route("/usermobile/{id}", name: "user")]
    public function userId($id, NormalizerInterface $normalizer, UserRepository $repo)
    {
        $user = $repo->find($id);
        $userNormalises = $normalizer->normalize($user, 'json', ['groups' => "users"]);
        return new Response(json_encode($userNormalises));
    }

    #[Route("addUserJSON/new", name: "addUserJSON")]
    public function addUserJSON(ManagerRegistry $doctrine, Request $req,   NormalizerInterface $Normalizer)
    {

        $em = $doctrine->getManager();
        $user = new User();
        $user ->setEmail($req->get('email'));
        $user ->setName($req->get('name'));
        $user ->setTelephone($req->get('telephone'));
       
        $user ->setPassword($req->get('password'));
        $em->persist($user );
        $em->flush();

        $jsonContent = $Normalizer->normalize($user, 'json', ['groups' => 'users']);
        return new Response(json_encode($jsonContent));
    }
    #[Route("updateUserJSON/{id}", name: "updateUserJSON")]
    public function updateUserJSON(ManagerRegistry $doctrine, Request $req, $id, NormalizerInterface $Normalizer)
    {

        $em = $doctrine->getManager();
        $user = $em->getRepository(User::class)->find($id);
             $user->setName($req->get('name'));
        $user->setTelephone($req->get('telephone'));
       
        $user->setPassword($req->get('password'));
       


        $em->flush();

        $jsonContent = $Normalizer->normalize($user, 'json', ['groups' => 'users']);
        return new Response("User updated successfully " . json_encode($jsonContent));
    }

    #[Route("deleteUserJSON/{id}", name: "deleteUserJSON")]
    public function deleteUserJSON(ManagerRegistry $doctrine, Request $req, $id, NormalizerInterface $Normalizer)
    {

        $em = $doctrine->getManager();
        $user = $em->getRepository(User::class)->find($id);
        $em->remove($user);
        $em->flush();
        $jsonContent = $Normalizer->normalize($user, 'json', ['groups' => 'users']);
        return new Response("user deleted successfully " . json_encode($jsonContent));
    }



}
