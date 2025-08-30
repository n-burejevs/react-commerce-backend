<?php
require("db_config.php");
require ("headers.php");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


$user_name = $user_lastname = $user_email = $user_id  = "";

// 1. Get the raw JSON data from the request body
$json_data = file_get_contents('php://input');

// 2. Decode the JSON data into a PHP associative array or object
$request_data = json_decode($json_data, true); // 'true' for associative array, omit for object

if ($request_data !== null) 
{
    $token = isset($request_data['token']) ? $request_data['token'] : null;
        
        
    if ($token !=null)
    {
         // Create connection
        $conn = new mysqli($servername, $username, $db_password, $dbname);
        
       // Check connection
        if ($conn->connect_error)
        {   //Cant authenticate, error 
         echo json_encode(['status' => "error" . $conn->connect_error, 'message' => '']);
         exit();
        }
        
        
          //find the user
          $find_user = "SELECT * FROM users WHERE token = '$token'";
      
        $user_test = $conn->query($find_user);
        //user found
        if ($user_test->num_rows > 0)
        { 
           
             while($row = $user_test->fetch_assoc())
             {
                 //i hope there is only one row, lol
                
                //that will be the response back to front end
                $user_name = $row['firstname'];
                $user_email = $row['email'];
                $user_id = $row['id'];
                $user_lastname = $row['lastname'];
             }


              }
               echo json_encode(['status' => 'success', 'message' => 
               ['name' => $user_name,'email' => $user_email,'lastname' => $user_lastname,'id' => $user_id] ]);
        $conn->close();         
    }
    else 
    { //User was not yet logged in in this browser? No token?'
        echo json_encode(['status'=> 'Error', 'message'=> '']);
    }
         
                  
} else
      {
        // Handle JSON decoding error
        http_response_code(400); // Bad Request
        //: Invalid JSON data received.
        $error_response = ['status' => 'Error', 'message' => ''];
        echo json_encode($error_response);
      }


  
