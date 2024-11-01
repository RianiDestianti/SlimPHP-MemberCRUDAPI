<?php

namespace App\Repository;

class Email {

    public function sendMail($email, $name, $subject, $message)
    {
        $mail = new \SendGrid\Mail\Mail();
        $mail->setFrom("sendermail@example.com", "SENDER_NAME");
        $mail->setSubject($subject);
        $mail->addTo($email, $name);
        $mail->addContent(
            "text/html", $message
        );
        
        $sendgrid = new \SendGrid($_ENV['SENDGRID_KEY']);
        $sendgrid->send($mail);

        return 'success';
    }

    public function style($greeting, $content, $button, $url, $closing)
    {
        $message = '
        <style type="text/css">
            a:hover {text-decoration: underline !important;}
            .button {
                -webkit-text-size-adjust: none;
                border-radius: 4px;
                color: #fff;
                display: inline-block;
                overflow: hidden;
                text-decoration: none;
            }
            
            .button-blue,
            .button-primary {
                background-color: #2d3748;
                border-bottom: 8px solid #2d3748;
                border-left: 18px solid #2d3748;
                border-right: 18px solid #2d3748;
                border-top: 8px solid #2d3748;
            }
        </style>
        <table cellspacing="0" border="0" cellpadding="0" width="100%" bgcolor="#f2f3f8"
            style="@import url(https://fonts.googleapis.com/css?family=Rubik:300,400,500,700|Open+Sans:300,400,600,700); font-family: Open Sans, sans-serif;">
            <tr>
                <td>
                    <table style="background-color: #f2f3f8; max-width:670px; margin:0 auto;" width="100%" border="0"
                        align="center" cellpadding="0" cellspacing="0">
                        <tr>
                            <td style="height:50px;">&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="text-align:center;">
                                <a href="'.$_ENV['FRONTEND_URL'].'" target="_blank">
                                    <img width="80" src="https://sobat.sgp1.cdn.digitaloceanspaces.com/images%2Fsobat-logo%20(1).png" title="logo" alt="logo">
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td style="height:20px;">&nbsp;</td>
                        </tr>
                        <tr>
                            <td>
                                <table width="95%" border="0" cellpadding="0" cellspacing="0"
                                    style="max-width:670px; background:#fff; border-radius:3px;-webkit-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);-moz-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);box-shadow:0 6px 18px 0 rgba(0,0,0,.06);">
                                    <tr>
                                        <td style="height:40px;">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:0 35px;">
                                            <p style="font-size:15px; color:#455056; margin:8px 0 0; line-height:24px;"><b>'.$greeting.'</b></p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding:0 35px;">
                                            <p style="font-size:15px; color:#455056; margin:8px 0 0; line-height:24px;">'.$content.'</p>
                                        </td>
                                    </tr>'.($button ? '
                                    <tr>
                                        <td style="padding: 0 35px;
                                            text-align: center;
                                            padding-top: 40px;
                                            padding-bottom: 40px;">
                                            '.($button ? '
                                            <a href="'.$url.'" class="button button-primary" style="-webkit-text-size-adjust: none;
                                                border-radius: 4px;
                                                color: #fff;
                                                padding: 6px;
                                                text-align: center;
                                                overflow: hidden;
                                                text-decoration: none;
                                                background-color: #2d3748;
                                                border-bottom: 8px solid #2d3748;
                                                border-left: 18px solid #2d3748;
                                                border-right: 18px solid #2d3748;
                                                width: 20%;
                                                margin-left: auto;
                                                margin-right: auto;
                                                border-top: 8px solid #2d3748;" 
                                            target="_blank" rel="noopener">'.$button.'</a>' : '').'
                                        </td>
                                    </tr>' : '').'
                                    <tr>
                                        <td style="padding:0 35px;">
                                            <p style="font-size:15px; color:#455056; margin:8px 0 0; line-height:24px;">'.$closing.'</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding:0 35px;">
                                        <br><br>
                                        <hr>
                                            <p style="font-size:15px; color:#455056; margin:8px 0 0; line-height:24px;text-align:center">
                                                Have questions or comments? Contact us! <br>
                                                We’ll do everything we can do to make sure you have a good experience with us. <br><br>
                                                Email us at: hello@peopleofhappiness.com<br><br>
                                                <a href="https://twitter.com/pplofhappiness"><img src="https://sobat.sgp1.cdn.digitaloceanspaces.com/norman/Jq4aBmmnL9WfxS2Kqknx8FRRmQqaaiZYNWJE2pkW.png" style="height: 20px;"></a>                              
                                                <a href="https://www.youtube.com/channel/UCi5rwhosY1SssQhRyk805Tw"><img src="https://sobat.sgp1.cdn.digitaloceanspaces.com/norman/w1pTQqNrP9yqvUyFvMVvNDyBiXHkN5ZXPkn92Gbl.png" style="height: 20px;"></a>                              
                                                <a href="https://www.linkedin.com/company/people-of-happiness/"><img src="https://sobat.sgp1.cdn.digitaloceanspaces.com/norman/QwCzgNEYoSNSbEPMBZ8QenRddRE8mkPynEmp2tF7.png" style="height: 20px;"></a>                              
                                                <a href="https://www.instagram.com/people.of.happiness/?hl=en"><img src="https://sobat.sgp1.cdn.digitaloceanspaces.com/norman/XINSAlAsBLVRP0BAkwkVPvJhRXMF4yEadDJGKchz.png" style="height: 20px;"></a>                              
                                            </p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="height:40px;">&nbsp;</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td style="height:20px;">&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="text-align:center;">
                                <p style="font-size:14px; color:rgba(69, 80, 86, 0.7411764705882353); line-height:18px; margin:0 0 0;">&copy; <strong>SOBAT</strong> </p>
                            </td>
                        </tr>
                        <tr>
                            <td style="height:80px;">&nbsp;</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>';
        return $message;
    }

}
