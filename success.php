<?php
require_once 'vendor/autoload.php';

// Twig environment
$loader = new \Twig\Loader\FilesystemLoader('/home/stud/1/2337117/public_html/templates');
$twig = new \Twig\Environment($loader, [
    'debug' => true
]);


// Render the template
echo $twig->render('success.twig');
?>