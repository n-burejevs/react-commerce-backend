<?php
require("db_config.php");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
  //how much do i need??????
  header('Access-Control-Allow-Origin: http://localhost:5173');
  header('Access-Control-Allow-Methods: POST');
  header('Access-Control-Allow-Headers: Content-Type, Authorization');
  header('Content-Type: application/json');

       // 1. Get the raw JSON data from the request body
    $json_data = file_get_contents('php://input');

    // 2. Decode the JSON data into a PHP associative array or object
    $request_data = json_decode($json_data, true); // 'true' for associative array, omit for object
    
    $user_pw_in_db = "";

    $user_name = $user_lastname = $user_email = $user_id  = "";

         if ($request_data !== null) 
       {
        $email = isset($request_data['email']) ? $request_data['email'] : null;
        $password = isset($request_data['password']) ? $request_data['password'] : null;
       // $response = array('message' => 'Data received!', 'data' => $request_data);
        //var_dump($response);

        if ($email !=null && $password !=null)
    {
         // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);
        
       // Check connection
        if ($conn->connect_error)
        {
         echo json_encode(['status' => 'error', 'message' => "Connection failed: " . $conn->connect_error]);
         exit();
        }
        
        
          //find the user
          $find_user = "SELECT * FROM users WHERE email = '$email'";
      
        $user_test = $conn->query($find_user);
        //user found
        if ($user_test->num_rows > 0)
        { 
           
          //$user_data.id = $row["id"];
        //  $user_data.name =  $row["name"];
             while($row = $user_test->fetch_assoc())
              {
                //get the result in the object, pass it as response to front end?
                 //echo "id: " . $row["id"]. " - Name: " . $row["firstname"]. " " . $row["lastname"]. "<br>";
                 //i hope there is only one row, lol
                $user_pw_in_db = $row['password'];
                //this will be used later, after cookie is set, that will be the response back to front end
                $user_name = $row['firstname'];
                $user_email = $row['email'];
                $user_id = $row['id'];
                $user_lastname = $row['lastname'];

              }//https://www.phpmentoring.org/blog/php-password-verify-function#:~:text=The%20Password_Verify()%20function%20is,true%2C%20otherwise%20it%20returns%20false.
              if(password_verify($password, $user_pw_in_db))
              {
               //user pw's match -> login succes
               //echo json_encode(['status' => 'succes', 'message' => "passwords match"]);
               //make the token, compare it to something somewhere, idk, store it in the cookies? => dont need the react context then?!!?!?
              //https://www.dbestech.com/tutorials/how-to-generate-access-token-in-php
              $token = md5(uniqid().rand(1000000, 9999999));

              $update_user_acc_token = "UPDATE users SET token='$token' WHERE email='$email'";

               if ($conn->query($update_user_acc_token) === true) {

                 echo json_encode(['status' => 'success', 'message' => 
                 ['name' => $user_name, 'email' => $user_email,
                  'lastname' => $user_lastname, 'id' => $user_id, ], 'token' => $token]);
              }
              else  { echo json_encode(['status' => 'error', 'message' => "token was not created: "/*.$conn->error*/]); }
                 
          //genereate token then store it in the db also in the coookies, then compare then when needed to check auth???
              }
              else{
               echo json_encode(['status'=> 'error', 'message'=> 'Wrong password or email']);
               
              }

        }
        else 
        {
           echo json_encode(['status' => 'error', 'message' => 'User not found ' . $conn->error] );
        }
        
        $conn->close(); 
                  
} else
      {
        // Handle JSON decoding error
        http_response_code(400); // Bad Request
        $error_response = array('status' => 'error', 'message' => 'Invalid JSON data received.');
        echo json_encode($error_response);
      }

      }