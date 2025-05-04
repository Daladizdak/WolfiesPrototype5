<?php
function verificationEmail($toEmail, $fullName, $registrationCode) {
    $subject = "You’re Registered for Our University Open Day!";
    
    // Email content goes here
    $message = "
        <html>
        <head>
            <title>$subject</title>
        </head>
        <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
            <p>Dear " . htmlspecialchars($fullName) . ",</p>

            <p>Thank you for signing up for our upcoming <strong>University Open Day</strong>! We’re excited to welcome you to campus and help you explore everything we have to offer.</p>

            <p><strong>Your Registration ID:</strong> " . htmlspecialchars($registrationCode) . "<br>
            Please keep this code handy—you’ll need it to check in on the day.</p>

            <p>We’ll be sending you more details soon, including schedules, session info, and directions. In the meantime, feel free to reach out if you have any questions.</p>

            <p>We look forward to seeing you soon!</p>

            <p>Best regards,<br>
            Riyadh Alsalahi, Jamal Abdille<br>
            <strong>Wolfies</strong><br>
            <a href='mailto:R.alsalahi@wlv.ac.uk'>R.alsalahi@wlv.ac.uk</a>, 
            <a href='mailto:J.abdille@wlv.ac.uk'>J.abdille@wlv.ac.uk</a></p>
        </body>
        </html>
    ";

    // Email headers for HTML
    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: noreply@yourdomain.com\r\n";

    // sends then email
    return mail($toEmail, $subject, $message, $headers);
}
