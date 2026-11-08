<?php

$servername = "mysql-service";
$username = "root";
$db_password = "rootpassword";
$dbname = "react-commerce";

$conn = new mysqli($servername, $username, $db_password, $dbname);

// Check connection
 if ($conn->connect_error)
 {   //Cant authenticate, error 
  echo json_encode(['status' => "error", 'message' => $conn->connect_error]);
  exit();
 }