<?php

// use PHPMailer\PHPMailer\PHPMailer;
// use PHPMailer\PHPMailer\Exception;
// require_once 'vendor/phpmailer/vendor/autoload.php';

// if (!function_exists('send_phpmailer_email')) {
//     function send_phpmailer_email($to, $subject, $htmlBody, $altBody = '', $name = 'Matrimony Service') {
//         $mail = new PHPMailer(true);

//         try {
//             // Server settings
//             $mail->isSMTP();
//             $mail->Host       = env('MAIL_HOST');
//             $mail->SMTPAuth   = true;
//             $mail->Username   = env('MAIL_USERNAME');
//             $mail->Password   = env('MAIL_PASSWORD');
//             $mail->SMTPSecure = env('MAIL_ENCRYPTION', 'tls');
//             $mail->Port       = env('MAIL_PORT', 465);

//             // Sender & recipient
//             $mail->setFrom(env('MAIL_FROM_ADDRESS'), $name);
//             $mail->addAddress($to);

//             // Content
//             $mail->isHTML(true);
//             $mail->Subject = $subject;
//             $mail->Body    = $htmlBody;
//             $mail->AltBody = $altBody;

//             $mail->send();
//             return true;
//         } catch (Exception $e) {
//             // Log or return error if needed
//             return 'Mailer Error: ' . $mail->ErrorInfo;
//         }
//     }
// }
