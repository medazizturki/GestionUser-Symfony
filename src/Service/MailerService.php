<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class MailerService
{
    
    private $mailer;
    
    
    public function __construct( MailerInterface $mailer)
     {
        
        $this->mailer=$mailer;
     }
    
    public function sendEmail(    $to , $nom ): void
    {
        
        $email = (new Email())
            ->from('azizturki111@gmail.com')
            ->to($to)
            ->subject('Ado-Doc')
            ->text( $nom.'  Votre Participation est bien validé !');
             
            $this->mailer->send($email);
      
        // ...
    }
}



