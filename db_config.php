<?php

$servername = "localhost";
$username = "root";
$db_password = "12345678";
$dbname = "react-commerce";

      try {
          $conn = new mysqli($servername, $username, $db_password, $dbname);
      }
      catch(mysqli_sql_exception $e) 
        { 
          echo json_encode(['status' => 'error', 'message' => "Connection failed: " . $e]);
        }
// Check connection
 if ($conn->connect_error)
 {   //Cant authenticate, error 
  echo json_encode(['status' => "error", 'message' => $conn->connect_error]);
  exit();
 }
