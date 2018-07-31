<?php
/**
 * Created by PhpStorm.
 * User: Wei.Cheng
 * Date: 2018/7/30
 * Time: 下午 02:14
 */

// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;

//Load Composer's autoloader
require 'vendor/autoload.php';

$mail = null;

function init()
{
    global $mail;
    $mail = new PHPMailer(true);                              // Passing `true` enables exceptions

    //Server settings
    $mail->SMTPDebug = 2;                                 // Enable verbose debug output
    $mail->isSMTP();                                      // Set mailer to use SMTP
    $mail->Host = 'Relay.advantech.com.tw';  // Specify main and backup SMTP servers
    $mail->Port = 25;                                    // TCP port to connect to
}

function sendMail($to, $cc, $subject, $body)
{
    init();
    global $mail;
    //Recipients
    $mail->setFrom('php@example.com');

    foreach ($to as $user) {
        if (isset($user["email"])) {
            $mail->addAddress($user["email"]);     // Add a recipient
        }
    }

    foreach ($cc as $user2) {
        if (isset($user2["email"])) {
            $mail->addCC($user2["email"]);     // Add a recipient
        }
    }

    //Content
    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = $subject;
    $mail->Body = $body;
    $mail->AltBody = 'Sorry, your mail don\'t support html content.';
    $mail->CharSet = 'UTF-8';

    var_dump($mail);

    $mail->send();
}
