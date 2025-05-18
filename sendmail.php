<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

$mail = new PHPMailer(true);

try {
    // SMTP Configuration
    $mail->isSMTP();
    $mail->Host       = 'mail.nwssusjclibrary.com';  // From your screenshot
    $mail->SMTPAuth   = true;
    $mail->Username   = 'library@nwssusjclibrary.com'; // Your email
    $mail->Password   = 'nwssusjclibrary';         // Replace with your real password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port       = 465;

    // Email setup
    $mail->setFrom('library@nwssusjclibrary.com', 'Library Notification'); // Sender
    $mail->addAddress('modestoelizalde1@gmail.com', 'Modesto Elizalde');   // Receiver

    // Message
    $mail->isHTML(true);
    $mail->Subject = 'Library Notification';
    $mail->Body    = 'Hello Modesto,<br><br>This is an automated notification from the library system.';

    // Send
    $mail->send();
    echo 'Email sent successfully to Modesto!';
} catch (Exception $e) {
    echo "Failed to send email. Error: {$mail->ErrorInfo}";
}
