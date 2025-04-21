<?php

namespace App\Services;

use Illuminate\Http\Request;
#use Swift_Mailer;
#use Mail;

use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

class SendMail
{

    /*
    public static function send($to,$sujet,$contenu)
    {

        $swiftTransport =  new \Swift_SmtpTransport( env('MAIL_HOST'), env('MAIL_PORT'), env('MAIL_ENCRYPTION'));
        $swiftTransport->setUsername( env('MAIL_USERNAME')); //adresse email
        $swiftTransport->setPassword( env('MAIL_PASSWORD')); // mot de passe email

        $swiftMailer = new Swift_Mailer($swiftTransport);
        Mail::setSwiftMailer($swiftMailer);
        $from= env('MAIL_FROM_ADDRESS');
        $fromname= env('MAIL_FROM_NAME');

        Mail::send([], [], function ($message) use ($to,$sujet, $contenu,$from,$fromname   ) {
                $message
                  ->to($to)
                //->bcc($chunk ?: [])
                    ->subject($sujet)
                       ->setBody($contenu, 'text/html')
                    ->setFrom([$from => $fromname]);

        });
    }


    public static function send_pdf($to,$sujet,$contenu,$id)
    {
        try{
            $swiftTransport =  new \Swift_SmtpTransport( env('MAIL_HOST'), env('MAIL_PORT'), env('MAIL_ENCRYPTION'));
            $swiftTransport->setUsername( env('MAIL_USERNAME')); //adresse email
            $swiftTransport->setPassword( env('MAIL_PASSWORD')); // mot de passe email

            $swiftMailer = new Swift_Mailer($swiftTransport);
            Mail::setSwiftMailer($swiftMailer);
            $from= env('MAIL_FROM_ADDRESS');
            $fromname= env('MAIL_FROM_NAME');

            Mail::send([], [], function ($message) use ($to,$sujet, $contenu,$from,$fromname,$id   ) {
                    $message
                    ->to($to)
                        ->subject($sujet)
                        ->setBody($contenu, 'text/html')
                        ->setFrom([$from => $fromname]);

                $fullpath=storage_path().'\pdf\facture-'.$id.'.pdf';
                $name=basename($fullpath);
                $mime_content_type=mime_content_type ($fullpath);

                $message->attach($fullpath, array(
                        'as' => $name, // If you want you can chnage original name to custom name
                        'mime' => $mime_content_type)
                );

            });

            //return redirect()->route('invoices.index')
            //->with('success','Facture envoyée !');

        }catch(\Exception $e){
            dd($e->getMessage());
            return redirect()->route('invoices.index');
        }

    }

    */



    public static function send($to, $sujet, $contenu)
    {
        $transport = new EsmtpTransport(env('MAIL_HOST'), env('MAIL_PORT'));
        $transport->setUsername(env('MAIL_USERNAME'));
        $transport->setPassword(env('MAIL_PASSWORD'));

        $mailer = new Mailer($transport);
        $from = env('MAIL_FROM_ADDRESS');
        $fromName = env('MAIL_FROM_NAME');


        // Check if $to is an array, if not, make it an array
        $recipients = is_array($to) ? $to : [$to];

        $email = (new Email())
            ->from($from)
            //->bcc(...$recipients) // Spread operator to add all recipients
            ->to(...$recipients)
            ->replyTo($from)
            ->subject($sujet)
            ->html($contenu);

        $mailer->send($email);
    }


    public static function send_pdf($to, $sujet, $contenu, $id)
    {
        try {
            // Create the SMTP transport like in send()
            $transport = new EsmtpTransport(env('MAIL_HOST'), env('MAIL_PORT'));
            $transport->setUsername(env('MAIL_USERNAME'));
            $transport->setPassword(env('MAIL_PASSWORD'));

            // Create the Mailer using your SMTP transport
            $mailer = new Mailer($transport);
            $from = env('MAIL_FROM_ADDRESS');
            $fromName = env('MAIL_FROM_NAME');
            $recipients = is_array($to) ? $to : [$to];

            // Get file info
            //$fullpath=storage_path().'\pdf\facture-'.$id.'.pdf';
            $fullpath = storage_path('pdf' . DIRECTORY_SEPARATOR . 'facture-' . $id . '.pdf');

            $name = basename($fullpath);
            $mime_content_type = mime_content_type($fullpath);


            // Create the email with attachment
            $email = (new Email())
                ->from($from)
                ->to(...$recipients)
                //->cc($ccAddress)
                ->replyTo($from)
                ->subject($sujet)
                ->html($contenu)
                ->attachFromPath($fullpath, $name, $mime_content_type);

            // Send the email
            $mailer->send($email);

        } catch (\Exception $e) {
            // Handle exception
            //dd($e->getMessage());
        }
    }
}