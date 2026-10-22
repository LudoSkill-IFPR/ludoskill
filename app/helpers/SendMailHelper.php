<?php

namespace app\helpers;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../bibliotecas/PHPMailer/src/Exception.php';
require __DIR__ . '/../bibliotecas/PHPMailer/src/PHPMailer.php';
require __DIR__ . '/../bibliotecas/PHPMailer/src/SMTP.php';

class SendMailHelper
{
    public static function sendMail($to, $subject, $message)
    {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;

            $mail->Username = 'ludoskill26@gmail.com';
            $mail->Password = 'projetoIntegrador2026'; //app password gerada no gmail

            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            $mail->CharSet = 'UTF-8';

            $mail->setFrom('ludoskill26@gmail.com', 'LudoSkill');
            $mail->addAddress($to);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $message;
            $mail->AltBody = 'E-mail enviado pelo PHP!';

            $mail->send();

            echo 'E-mail enviado com sucesso!';
        } catch (Exception $e) {
            error_log($mail->ErrorInfo);
            echo 'Não foi possível enviar o e-mail.';
        }
    }
}
