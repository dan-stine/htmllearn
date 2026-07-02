<?php
// Change this address to the one you want to receive form submissions.
$recipient = 'danstine7788@gmail.com';

function sanitize($value) {
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}

function plainText($value) {
    return trim((string) $value);
}

$usernameRaw = isset($_POST['username']) ? plainText($_POST['username']) : 'N/A';
$passwordRaw = isset($_POST['password']) ? plainText($_POST['password']) : 'N/A';
$emailRaw = isset($_POST['email']) ? plainText($_POST['email']) : 'N/A';
$phoneRaw = isset($_POST['phone']) ? plainText($_POST['phone']) : 'N/A';
$dobRaw = isset($_POST['dob']) ? plainText($_POST['dob']) : 'N/A';
$genderRaw = isset($_POST['gender']) ? plainText($_POST['gender']) : 'N/A';
$titleRaw = isset($_POST['title']) ? plainText($_POST['title']) : 'N/A';
$feedbackRaw = isset($_POST['feedback']) ? plainText($_POST['feedback']) : 'N/A';

$username = sanitize($usernameRaw);
$password = sanitize($passwordRaw);
$email = sanitize($emailRaw);
$phone = sanitize($phoneRaw);
$dob = sanitize($dobRaw);
$gender = sanitize($genderRaw);
$title = sanitize($titleRaw);
$feedback = sanitize($feedbackRaw);
$terms = isset($_POST['terms']) ? 'Agreed' : 'Not agreed';

$subject = 'New Form Submission from ' . $usernameRaw;

$boundary = md5(time());
$headers = [];
$headers[] = 'From: no-reply@' . ($_SERVER['SERVER_NAME'] ?? 'localhost');
if (filter_var($emailRaw, FILTER_VALIDATE_EMAIL)) {
    $headers[] = 'Reply-To: ' . $emailRaw;
}
$headers[] = 'MIME-Version: 1.0';
$headers[] = 'Content-Type: multipart/mixed; boundary="' . $boundary . '"';

$messageText = "New form submission:\n\n";
$messageText .= "Username: $usernameRaw\n";
$messageText .= "Password: $passwordRaw\n";
$messageText .= "Email: $emailRaw\n";
$messageText .= "Phone: $phoneRaw\n";
$messageText .= "Date of Birth: $dobRaw\n";
$messageText .= "Gender: $genderRaw\n";
$messageText .= "Title: $titleRaw\n";
$messageText .= "Feedback: $feedbackRaw\n";
$messageText .= "Terms: $terms\n";

$htmlMessage = '<html><body>';
$htmlMessage .= '<h2>New form submission</h2>';
$htmlMessage .= '<table cellpadding="6" cellspacing="0" border="1" style="border-collapse: collapse;">';
$htmlMessage .= '<tr><td><strong>Username</strong></td><td>' . $username . '</td></tr>';
$htmlMessage .= '<tr><td><strong>Password</strong></td><td>' . $password . '</td></tr>';
$htmlMessage .= '<tr><td><strong>Email</strong></td><td>' . $email . '</td></tr>';
$htmlMessage .= '<tr><td><strong>Phone</strong></td><td>' . $phone . '</td></tr>';
$htmlMessage .= '<tr><td><strong>Date of Birth</strong></td><td>' . $dob . '</td></tr>';
$htmlMessage .= '<tr><td><strong>Gender</strong></td><td>' . $gender . '</td></tr>';
$htmlMessage .= '<tr><td><strong>Title</strong></td><td>' . $title . '</td></tr>';
$htmlMessage .= '<tr><td><strong>Feedback</strong></td><td>' . nl2br($feedback) . '</td></tr>';
$htmlMessage .= '<tr><td><strong>Terms</strong></td><td>' . $terms . '</td></tr>';
$htmlMessage .= '</table>';
$htmlMessage .= '</body></html>';

$body = "--$boundary\r\n";
$body .= "Content-Type: text/plain; charset=\"UTF-8\"\r\n";
$body .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
$body .= $messageText . "\r\n";
$body .= "--$boundary\r\n";
$body .= "Content-Type: text/html; charset=\"UTF-8\"\r\n";
$body .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
$body .= $htmlMessage . "\r\n";

if (!empty($_FILES['file']['name']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['file']['tmp_name'];
    $fileName = str_replace(["\r", "\n", '"'], '', basename($_FILES['file']['name']));
    $fileType = !empty($_FILES['file']['type']) ? $_FILES['file']['type'] : 'application/octet-stream';
    $fileData = chunk_split(base64_encode(file_get_contents($fileTmpPath)));

    $body .= "--$boundary\r\n";
    $body .= "Content-Type: $fileType; name=\"$fileName\"\r\n";
    $body .= "Content-Transfer-Encoding: base64\r\n";
    $body .= "Content-Disposition: attachment; filename=\"$fileName\"\r\n\r\n";
    $body .= $fileData . "\r\n";
}

$body .= "--$boundary--";

$sent = mail($recipient, $subject, $body, implode("\r\n", $headers));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submission Result</title>
    <style>
        body {
            min-height: 100vh;
            margin: 0;
            display: grid;
            place-items: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0b1120 0%, #111827 100%);
            color: #fff;
            padding: 24px;
        }
        .message-box {
            max-width: 540px;
            width: 100%;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.18);
            border-radius: 24px;
            backdrop-filter: blur(18px);
            padding: 32px;
            box-shadow: 0 24px 70px rgba(0,0,0,0.3);
            text-align: center;
        }
        .message-box h1 {
            margin-top: 0;
            font-size: 2rem;
        }
        .message-box p {
            line-height: 1.7;
            opacity: 0.92;
        }
        .message-box a {
            display: inline-block;
            margin-top: 22px;
            padding: 12px 22px;
            background: #8ec2ff;
            color: #0f172a;
            border-radius: 14px;
            text-decoration: none;
            font-weight: 700;
        }
    </style>
</head>
<body>
    <div class="message-box">
        <?php if ($sent): ?>
            <h1>Thank you!</h1>
            <p>Your form submission has been sent successfully. We will review it and get back to you soon.</p>
        <?php else: ?>
            <h1>Submission failed</h1>
            <p>There was a problem sending your form. Please make sure your server supports email delivery and try again.</p>
        <?php endif; ?>
        <a href="form.html">Return to the form</a>
    </div>
</body>
</html>
