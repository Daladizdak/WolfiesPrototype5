<?php

header('Content-Type: application/json');

// Database connection
$host = 'mi-linux.wlv.ac.uk'; // or your host
$db   = 'db2378831';
$user = '2378831';
$pass = 'shawn162024';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    $stmt = $pdo->prepare("SELECT title, start_time FROM Events WHERE start_time > NOW() ORDER BY start_time ASC LIMIT 1");
    $stmt->execute();
    $event = $stmt->fetch();

    if ($event) {
        echo json_encode($event);
    } else {
        http_response_code(404);
        echo json_encode(['message' => 'No upcoming events']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error', 'details' => $e->getMessage()]);
}
?>