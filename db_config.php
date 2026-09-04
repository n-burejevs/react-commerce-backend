<?php

$servername = "localhost";
$username = "root";
$db_password = "12345678";
$dbname = "react-commerce";

$conn = new mysqli($servername, $username, $db_password, $dbname);

// Check connection
 if ($conn->connect_error)
 {   //Cant authenticate, error 
  echo json_encode(['status' => "error", 'message' => $conn->connect_error]);
  exit();
 }