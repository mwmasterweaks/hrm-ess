<?php

namespace App\Helpers;

//composer require phpmailer/phpmailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Logger
{
    public function log($date, $userID, $userName, $ControllerName, $functionName, $logType, $message)
    {
        $filenameDate = date("mY");
        $myfile = fopen("logs/log" . $filenameDate . ".txt", "a") or die("Unable to open file!");
        // $txt = $date . "\t--\t" . $userID . "\t--\t" . $userName . "\t--\t" . $ControllerName .
        //     "\t--\t" . $functionName . "\t--\t" . $logType . "\t--\t" . $message . "\n\n";
        $txt = $date . "    |    User: (" . $userID . ") " . $userName . "    |    " . $ControllerName .
            " (" . $functionName . ")    |    " . $logType . "    |    " . $message . "\n\n";
        fwrite($myfile, $txt);
        fclose($myfile);
        return "ok";
    }

    public function logError($date, $userID, $userName, $ControllerName, $functionName, $logType, $message)
    {
        $filenameDate = date("mY");
        $myfile = fopen("logs/Errorlog" . $filenameDate . ".txt", "a") or die("Unable to open file!");
        // $txt = $date . "\t--\t" . $userID . "\t--\t" . $userName . "\t--\t" . $ControllerName .
        //     "\t--\t" . $functionName . "\t--\t" . $logType . "\t--\t" . $message . "\n\n";
        $txt = $date . "    |    User: (" . $userID . ") " . $userName . "    |    " . $ControllerName .
            " (" . $functionName . ")    |    " . $logType . "    |    " . $message . "\n\n";
        fwrite($myfile, $txt);
        fclose($myfile);
        return "ok";
    }

    public function mailer($subject, $message, $sender, $senderName, $sendTo, $CCTO)
    {

        $mail = new PHPMailer(true);
        // Server settings
        $mail->SMTPDebug = 0;
        //$mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $mail->isSMTP();

        $mail->Host = 'mail.dctechmicro.com'; //'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'customeractivation@dctechmicro.com';
        $mail->Password = 'pyzhet-cyxSo2-kokpec';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 25;
        $mail->CharSet = 'utf-8';
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );


        //any suggestion about the sender??
        $mail->setFrom('customeractivation@dctechmicro.com', $senderName . " " . $sender);
        if ($sendTo != null)
            foreach ($sendTo as $item) {
                $item = (object) $item;
                $mail->addAddress($item->email, $item->name);
            }
        if ($CCTO != null)
            foreach ($CCTO as $item) {
                $item = (object) $item;
                $mail->addCC($item->email, $item->name);
            }

        //$mail->addReplyTo($request->email, 'Mailer');
        //$mail->addCC('pbismonte@dctechmicro.com');
        //$mail->addBCC('his-her-email@gmail.com');

        //Attachments (optional)
        // $mail->addAttachment('/var/tmp/file.tar.gz');			// Add attachments
        // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');	// Optional name

        $mail->isHTML(true);                                                                     // Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = $message;

        if (!$mail->send()) {
            return 'Mailer Error: ' . $mail->ErrorInfo;
        } else {
            return 'ok';
        }
    }

    public function mailerGmail($subject, $message, $sender, $senderName, $sendTo, $CCTO)
    {

        $mail = new PHPMailer();
        // Server settings
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $mail->isSMTP();

        $mail->Host = 'smtp.gmail.com';
        $mail->Port = 587;
        $mail->SMTPSecure = "tls";
        $mail->SMTPAuth = true;
        $mail->Username = 'r11cnc.dctech@gmail.com';
        $mail->Password = 'r11accounting';

        // $mail->CharSet = 'utf-8';
        // $mail->SMTPOptions = array(
        //     'ssl' => array(
        //         'verify_peer' => false,
        //         'verify_peer_name' => false,
        //         'allow_self_signed' => true
        //     )
        // );


        //any suggestion about the sender??
        $mail->setFrom('r11cnc.dctech@gmail.com', $senderName . " " . $sender);
        if ($sendTo != null)
            foreach ($sendTo as $item) {
                $item = (object) $item;
                $mail->addAddress($item->email, $item->name);
            }
        if ($CCTO != null)
            foreach ($CCTO as $item) {
                $item = (object) $item;
                $mail->addCC($item->email, $item->name);
            }

        //$mail->addReplyTo($request->email, 'Mailer');
        //$mail->addCC('pbismonte@dctechmicro.com');
        //$mail->addBCC('his-her-email@gmail.com');

        //Attachments (optional)
        // $mail->addAttachment('/var/tmp/file.tar.gz');			// Add attachments
        // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');	// Optional name

        $mail->isHTML(true);                                                                     // Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = $message;

        if (!$mail->send()) {
            return 'Mailer Error: ' . $mail->ErrorInfo;
        } else {
            return 'Message sent!';
            //Section 2: IMAP
            //Uncomment these to save your message in the 'Sent Mail' folder.
            #if (save_mail($mail)) {
            #    echo "Message saved!";
            #}
        }
    }

    public function send_text($number, $msg)
    {
        //2488
        $url = "http://sms.dctechmicro.com:2488/index.php?" .
            "app=ws&u=marketing&h=7d6190ad6afde3a9f45683d9600d5dc8&op=pv&to=" .
            $number . "&msg=" . $msg;
        //7d6190ad6afde3a9f45683d9600d5dc8
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //$ret = "";
        $ret = curl_exec($ch); //get error
        curl_close($ch);
        return $ret;
    }

    public static function instance()
    {
        return new Logger();
    }
}
