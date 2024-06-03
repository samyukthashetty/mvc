<?php


require_once '../Database/envloader.php';

require_once '../phpmailer/src/Exception.php';
require_once '../phpmailer/src/PHPMailer.php';
require_once '../phpmailer/src/SMTP.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class UserHelper {
    public static function generateRandomPassword($length = 8) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $password .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $password;
    }

    public static function sendRegistrationEmail($email, $password) {
        EnvLoader::loadEnv(__DIR__ . '/../../fdapp/.env');
        $mail = new PHPMailer(true);

        try {
           
            $mail->isSMTP();
            $mail->Host       = $_ENV['MAIL_HOST'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $_ENV['MAIL_USERNAME'];
            $mail->Password   = $_ENV['MAIL_PASSWORD'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = $_ENV['MAIL_PORT'];

           
            $mail->setFrom($_ENV['MAIL_USERNAME'], 'Samyuktha'); 
            $mail->addAddress($email);

           
            $mail->isHTML(true);
            $mail->Subject = 'Registration Confirmation';
            $mail->Body    = "Hello,<br><br>Your account has been successfully registered.<br><br>Your password is: $password<br><br>Please use this password to log in.";

           
            $mail->send();
            return true;
        } catch (Exception $e) {
            throw new Exception("Failed to send email. Mailer Error: {$mail->ErrorInfo}");
        }
    }
}

?>
