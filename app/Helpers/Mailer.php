<?php 

namespace App\Helpers;

use Mail;
use PHPMailer\PHPMailer\PHPMailer;
use Illuminate\Support\Facades\View;

class Mailer
{
    public static function sendMail($subject, $receiver_email, $receiver_name, $template, $data)
    {
        /* A mail problem (SMTP down, bad address) must never break the visitor's submit: the enquiry is already saved,
           so log the failure and report false instead of throwing a 500 page that makes people submit twice. */
        try {
            Mail::send($template, $data, function($message) use ($receiver_email, $receiver_name, $subject) {
                 $message->to($receiver_email, $receiver_name)->subject
                    ($subject);
                 $message->from(env('MAIL_FROM_ADDRESS'),'DataMySite Marketing');
              });
        } catch (\Throwable $e) {
            \Log::error('Mailer: could not send "' . $subject . '": ' . $e->getMessage());
            return false;
        }

        return true;
    }
}