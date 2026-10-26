<?php

namespace app\helpers;

use PHPMailer\PHPMailer\PHPMailer;

require __DIR__ . '/../bibliotecas/PHPMailer/src/Exception.php';
require __DIR__ . '/../bibliotecas/PHPMailer/src/PHPMailer.php';
require __DIR__ . '/../bibliotecas/PHPMailer/src/SMTP.php';

class SendMailHelper
{
    public function sendMail(string $to, string $subject, string $message): bool
    {
        $mail = new PHPMailer(true);

        try {
            $usuario = trim((string) getenv('MAIL_USERNAME'));
            $senha = (string) getenv('MAIL_PASSWORD');
            $remetente = trim((string) (getenv('MAIL_FROM_ADDRESS') ?: $usuario));

            if ($usuario === '' || $senha === '' || $remetente === '') {
                throw new \RuntimeException('As configurações de e-mail não foram informadas.');
            }

            $mail->isSMTP();
            $mail->Host = getenv('MAIL_HOST') ?: 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = $usuario;
            $mail->Password = $senha;
            $mail->SMTPSecure = strtolower((string) getenv('MAIL_ENCRYPTION')) === 'ssl'
                ? PHPMailer::ENCRYPTION_SMTPS
                : PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = (int) (getenv('MAIL_PORT') ?: 587);
            $mail->CharSet = 'UTF-8';

            $mail->setFrom($remetente, getenv('MAIL_FROM_NAME') ?: APP_NAME);
            $mail->addAddress($to);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $message;
            $mail->AltBody = trim(html_entity_decode(strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $message)), ENT_QUOTES | ENT_HTML5, 'UTF-8'));

            $mail->send();
            return true;
        } catch (\Throwable $e) {
            error_log('Falha ao enviar e-mail: ' . ($mail->ErrorInfo ?: $e->getMessage()));
            return false;
        }
    }
}
