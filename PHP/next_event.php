<?php

header('Content-Type: application/json');

// Database connection
$host = 'mi-linux.wlv.ac.uk'; 
$db   = 'db2378831';
$user = '2378831';
$pass = 'shawn162024';
$charset = 'utf8mb4';


//Creates data source name string for PHP Data Objects (PDO) connection 
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// PDO options for better error handeling 
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

//Database connection useing PDO since it is secure and prevents database injections
try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    //SQL query to get the next event
    $stmt = $pdo->prepare("SELECT title, start_time FROM Events WHERE start_time > NOW() ORDER BY start_time ASC LIMIT 1");
    $stmt->execute();
    //Fetches the result 
    $event = $stmt->fetch();

    //Returns JSON if there is an event. IF not, displays no upcoming event
    if ($event) {
        echo json_encode($event);
    } else {
        http_response_code(404);
        echo json_encode(['message' => 'No upcoming events']);
        
     //Error handlet for debugging   
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error', 'details' => $e->getMessage()]);
}
?>
