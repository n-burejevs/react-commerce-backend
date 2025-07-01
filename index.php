<?php
require("db_config.php");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
  //how much do i need??????
  header('Access-Control-Allow-Origin: http://localhost:5173');
   header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    //header("Content-Type: application/json; charset=UTF-8");
    header('Content-Type: application/json');

//Processing input from Sign up form

$nameErr = $lastnameErr = $emailErr = "";

   // 1. Get the raw JSON data from the request body
    $json_data = file_get_contents('php://input');

    // 2. Decode the JSON data into a PHP associative array or object
    $request_data = json_decode($json_data, true); // 'true' for associative array, omit for object

     if ($request_data !== null) 
{
        $firstname = isset($request_data['firstname']) ? $request_data['firstname'] : null;
        //$firstname = test_input($_POST["firstname"]);
        $lastname = isset($request_data['lastname']) ? $request_data['lastname'] : null;
        $email = isset($request_data['email']) ? $request_data['email'] : null;
        $password = isset($request_data['password']) ? $request_data['password'] : null;
        $response = array('message' => 'Data received!', 'data' => $request_data);
        //var_dump($response);

              if (!preg_match("/^[a-zA-Z0-9 ]*$/",$firstname)) 
              {
                 $nameErr = "Only letters, numbers and white space allowed";
              }
             if (!preg_match("/^[a-zA-Z0-9 ]*$/",$lastname))
             {
                $lastnameErr = "Only letters, numbers and white space allowed";
             }
              if (!filter_var($email, FILTER_VALIDATE_EMAIL))
              {
                $emailErr = "Invalid email format";
              }
    //check password?

    if ($nameErr != "" || $lastnameErr != "" || $emailErr != "") {
        //there were errors
      echo json_encode(['status' => 'error', 'message' =>  $emailErr." ". $lastnameErr." ". $nameErr]);
      exit();
    }

        if ($firstname !=null && $lastname !=null && $email !=null && $password !=null)
    {
        
         // Create connection
        try {$conn = new mysqli($servername, $username, $db_password, $dbname);}
        catch(mysqli_sql_exception $e) 
        { echo json_encode(['status' => 'error', 'message' => "Connection failed: " . $e]);  
          exit();}
        
       // Check connection
        if ($conn->connect_error)
        {
         echo json_encode(['status' => 'error', 'message' => "Connection failed: " . $conn->connect_error]);
         exit();
        }
          //check if the same email exist
          $check_uniq_email = "SELECT email FROM users WHERE email = '$email'";

        $email_test = $conn->query($check_uniq_email);

        if ($email_test->num_rows > 0)
        { 
          echo json_encode(['status' => 'error', 'message' => 'email is taken']);
        }
        else 
        {
            $secure_pass = password_hash($password, PASSWORD_BCRYPT);

            $sql = "INSERT INTO users (firstname, lastname, email, password) VALUES ( '$firstname', '$lastname', '$email', '$secure_pass')";

          if ($conn->query($sql) === TRUE) {
              echo json_encode(['status' => 'success', 'message' => "Added user to the database"]);
            }
          else
          {
            echo json_encode(['status' => 'error', 'message' => 'Error: ' . $conn->error] );
          }

        }
        $conn->close();       
    }
        
} else
      {
        // Handle JSON decoding error
        http_response_code(400); // Bad Request
        $error_response = array('status' => 'error', 'message' => 'Invalid JSON data received.');
        echo json_encode($error_response);
      }

?>