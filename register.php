<?php
session_start();

// Include necessary files
require_once 'vendor/autoload.php';
require_once 'db.php';
require_once 'email.php';


use Twig\Environment;
use Twig\Loader\FilesystemLoader;

// Twig setup
$loader = new FilesystemLoader('templates');
$twig = new Environment($loader);

//  A function for email section that makes sure the code is unique by looping through the existing ones in db
// Produces a shoter code
function smallCode($db, $length = 10) {
    do {
        $randomBytes = random_bytes(5); 
        $registrationCode = strtoupper(base_convert(bin2hex($randomBytes), 16, 36));

        $stmt = $db->prepare("SELECT id FROM members WHERE registration_code = ?");
        $stmt->bind_param("s", $registrationCode);
        $stmt->execute();
        $stmt->store_result();
        $exists = $stmt->num_rows > 0;
        $stmt->close();

    } while ($exists);

    return $registrationCode;
}


// Helper to build consistent template variables
function buildTemplateVars($extras = []) {
    $vars = $extras;

    if (isset($_SESSION['delete_success'])) {
        $vars['delete_success'] = $_SESSION['delete_success'];
    }

    return $vars;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $studyLevel = $_POST['study_level'];
    $subjectInterest = $_POST['subject_interest'];
    $numberGuests = $_POST['guests'];

    $errors = [];

    // Validation
    if (empty($fullName)) {
        $errors['full_name'] = 'Full name is required.';
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email address.';
    }

    if (!empty($errors)) {
        echo $twig->render('register.twig', buildTemplateVars([
            'errors' => $errors,
            'fullName' => $fullName,
            'email' => $email
        ]));
        unset($_SESSION['delete_success']);
        exit;
    }

    // Check for duplicate email
    $stmt = $db->prepare("SELECT id FROM members WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $errors['email'] = 'Email address already registered.';
        echo $twig->render('register.twig', buildTemplateVars([
            'errors' => $errors,
            'fullName' => $fullName,
            'email' => $email
        ]));
        $stmt->close();
        unset($_SESSION['delete_success']);
        exit;
    }
    $stmt->close();

    // Generate registration code
    $registrationCode = smallCode($db);

    // Store in DB
    $stmt = $db->prepare("INSERT INTO members (full_name, email, study_level, subject_interest, number_of_guests, registration_code) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $fullName, $email, $studyLevel, $subjectInterest, $numberGuests, $registrationCode);

if ($stmt->execute()) {
    if (sendVerificationEmail($email, $fullName, $registrationCode)) {
        $_SESSION['registration_email'] = $email;
        header("Location: success.php");
        exit;
    } else {
        echo "Error sending verification email.";
    }
} else {
        echo "Database error: " . $stmt->error;
    }

    $stmt->close();
    $db->close();
} else {
    // Show form
    echo $twig->render('register.twig', buildTemplateVars());
    unset($_SESSION['delete_success']);
}
?>
