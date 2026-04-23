<?php
require("db_config.php");
require ("headers.php");

//Processing input from Sign up form

$nameErr = $lastnameErr = $emailErr = $passErr = "";

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
        //$response = array('message' => 'Data received!', 'data' => $request_data);
        //var_dump($response);

              if (!preg_match("/^[a-zA-Z0-9_\-]{3,16}$/",$firstname)) 
              {
                 $nameErr = "Only letters and numbers allowed";
              }
             if (!preg_match("/^[a-zA-Z0-9_\-]{3,16}$/",$lastname))
             {
                $lastnameErr = "Only letters, numbers allowed";
             }
              if (!filter_var($email, FILTER_VALIDATE_EMAIL))
              {
                $emailErr = "Invalid email format";
              }
              //remove tags and quote chars
              $password = filter_string_polyfill($password);
              // Check if password is at least 8 characters
              if (strlen($password) < 8) {
                 $passErr = "Password too short!";
}


    if ($nameErr != "" || $lastnameErr != "" || $emailErr != "" || $passErr != "") {
        //there were errors
      echo json_encode(['status' => 'error', 'message' =>  $emailErr+" "+$lastnameErr." "+$nameErr+" "+$passErr]);
      exit();
    }

        if ($firstname !=null && $lastname !=null && $email !=null && $password !=null)
    {        
          //check if the same email exist
          $check_uniq_email = "SELECT email FROM users WHERE email = '$email'";

        $email_test = $conn->query($check_uniq_email);

        if ($email_test->num_rows > 0)
        { 
          echo json_encode(['status' => 'error', 'message' => 'email is taken']);
        }
        else 
        {
            $secure_pass = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO users (firstname, lastname, email, password) VALUES ( '$firstname', '$lastname', '$email', '$secure_pass')";

          if ($conn->query($sql) === TRUE) {
              echo json_encode(['status' => 'success', 'message' => "Added user to the database"]);
            }
          else
          {
            echo json_encode(['status' => 'error', 'message' => 'Error: ' . $conn->error] );
          }

        }
       
    }
        
} else
      {
        // Handle JSON decoding error
        http_response_code(400); // Bad Request
        $error_response = array('status' => 'error', 'message' => 'Invalid JSON data received.');
        echo json_encode($error_response);
      }

 $conn->close();
 
//https://stackoverflow.com/questions/69207368/constant-filter-sanitize-string-is-deprecated/69207369
  function filter_string_polyfill(string $string): string
  {
      $str = preg_replace('/\x00|<[^>]*>?/', '', $string);
      return str_replace(["'", '"'], ['&#39;', '&#34;'], $str);
  }