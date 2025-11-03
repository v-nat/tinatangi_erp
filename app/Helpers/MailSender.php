<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Mail;

class MailSender
{
    public static function sendEmployeeEmail($content){
        try {
            $user_email = $content['email'];
            Mail::send($content['blade_file'], $content, function($message) use ($user_email, $content){
                $message->to($user_email, 'EMPLOYEE OF Tinatangi Cafe')->subject
                ( $content['title'] );
                $message->from('v.nathaniel.8213@gmail.com', 'Tinatangi Cafe');
            });

        } catch (\Exception $e) {
            throw new \Exception("Failed to send email: " . $e->getMessage());
        }
    }
}
