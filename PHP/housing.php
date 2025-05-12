<?php
session_start();

// Include necessary files
require_once 'vendor/autoload.php';
require_once 'db.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

// Twig setup
$loader = new FilesystemLoader('templates');
$twig = new Environment($loader);

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
    $fullName = trim($_POST['student_name']);
    $email = trim($_POST['email']);
    $accType = $_POST['accommodation_type'];
    $moveDate = $_POST['move_in_date'];
    $addInfo = $_POST['additional_info'];

    $errors = [];

    // Validation
    if (empty($fullName)) {
        $errors['student_name'] = 'Full name is required.';
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email address.';
    }

    if (!empty($errors)) {
        // Store errors and form data in session to preserve the input
        $_SESSION['errors'] = $errors;
        $_SESSION['form_data'] = $_POST;
        // Redirect to form to display errors
        header("Location: housing.php");
        exit;
    }

    // Check for duplicate email
    $stmt = $db->prepare("SELECT id FROM applications WHERE email_address = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $errors['email'] = 'Email address already registered.';
        // Store errors and form data in session to preserve the input
        $_SESSION['errors'] = $errors;
        $_SESSION['form_data'] = $_POST;
        $stmt->close();
        header("Location: housing.php");
        exit;
    }
    $stmt->close();

    // Store in DB
    $stmt = $db->prepare("INSERT INTO applications (full_name, email_address, accommodation_type, preferred_move_in_date, additional_comments) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $fullName, $email, $accType, $moveDate, $addInfo);

    if ($stmt->execute()) {
        // Redirect to success page
        header("Location: success.php");
        exit;
    } else {
        echo "Database error: " . $stmt->error;
    }

    $stmt->close();
    $db->close();
} else {
    // Show form
    echo $twig->render('housing.twig', buildTemplateVars());
}

?>
