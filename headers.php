<?php
/* Old stuff
  header('Access-Control-Allow-Origin: http://localhost:5173');
  
  header('Access-Control-Allow-Methods: POST');
  
  header('Access-Control-Allow-Headers: Content-Type, Authorization');
 
  header('Content-Type: application/json');
*/
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
/*
//Gemini code

/*
// Get the origin sending the request
$origin = $_SERVER['HTTP_ORIGIN'] ?? '*';

// Allow the requesting origin
header("Access-Control-Allow-Origin: $origin");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// CRITICAL FOR PREFLIGHT (OPTIONS) REQUESTS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

header('Content-Type: application/json');
  
  */