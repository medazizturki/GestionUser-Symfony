<?php
namespace App\Service;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class Asma
{
    
    private $mailer;
    
    
    public function __construct( MailerInterface $mailer)
     {
        
        $this->mailer=$mailer;
     }
    
    public function sendEmail(    $post,$to): void
    {
        
        $email = (new Email())
            ->from('asmaf2408@gmail.com')
            ->to($to)
            ->subject('entretien')
            ->text($post);
             
            $this->mailer->send($email);
      
        // ...
    }
}
?>