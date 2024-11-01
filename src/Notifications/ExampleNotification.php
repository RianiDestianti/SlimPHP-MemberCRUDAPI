<?php

namespace App\Notifications;

use App\Repository\Email;
use App\Repository\URL;
use Pimple\Psr11\Container;

class ExampleNotification {

    public function sendMail($email, $name, $point)
    {
        $url                = $this->url();
        $button             = $this->button();
        $greeting           = $this->greeting($name);
        $content            = $this->content($point);
        $closing            = $this->closing();

        $mail               = new Email();
    
        $mail->sendMail($email, $name, 'SURPRISE!', $mail->style($greeting, $content, $button, $url, $closing));
    }

    private function url()
    {
        $URL                = new URL();

        return $URL->frontendUrl().'/user/point';
    }

    private function button()
    {
        return 'CLAIM MY REWARD';
    }

    private function greeting($name)
    {
        return "<b>Hello, {$name}!<b>";
    }

    private function content($minimum_point)
    {
        $content = "Congrats! You’ve earned {$minimum_point} points, which means you have enough to redeem a new reward!<br>Simply sign into your account and select one of the below reward options:<br>";
    
        return $content;
    }

    private function closing()
    {
        return " ";
    }

    private function generateRandomString($length = 64) 
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}