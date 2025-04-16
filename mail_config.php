<!-- filepath: c:\xampp\htdocs\mealconnect\mail_config.php -->
<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Manually include PHPMailer files (since Composer is not used)
require __DIR__ . '/PHPMailer/src/Exception.php';
require __DIR__ . '/PHPMailer/src/PHPMailer.php';
require __DIR__ . '/PHPMailer/src/SMTP.php';

function send_email($to, $subject, $body) {  
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Use Gmail SMTP or your own SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'mealconnect22@gmail.com'; // Your email
        $mail->Password = 'mnyd siyp mcxk ypsx'; // Use an App Password, NOT your Gmail password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('no-reply@mealconnect.com', 'MealConnect');
        $mail->addAddress($to);

        // Email Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->AltBody = strip_tags($body); // Plain text version of email

        // Send Email
        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
?>
