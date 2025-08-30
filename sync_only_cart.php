<?php
require("db_config.php");
require ("headers.php");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
  
$json_data = file_get_contents('php://input');

 $user_id = "";
 
$request_data = json_decode($json_data, true);

if ($request_data !== null) 
{
    $cart_items = isset($request_data['cart_items']) ? $request_data['cart_items'] : null;
        //$wishlist = isset($request_data['wish_list']) ? $request_data['wish_list'] : null; 
            $user_token = isset($request_data['user_token']) ? $request_data['user_token'] : null;
       
    if ($cart_items !=null && $user_token !=null)
    {
                //whats the difference between json.stringify from JS and json_encode????3
                
         //  $wishlist_json = json_encode($wishlist);
            $cart_items_json = json_encode($cart_items);
        
         $conn = new mysqli($servername, $username, $db_password, $dbname);
         
        if ($conn->connect_error)
        {   //Cant authenticate, error 
         echo json_encode(['status' => "Error", 'message' => $conn->connect_error]);
         exit();
        }
       $find_user_id = "SELECT id FROM users WHERE token = '$user_token'";
       
               $ask_for_user_id = $conn->query($find_user_id);
        
        if ($ask_for_user_id->num_rows > 0)
        {
           while($row = $ask_for_user_id->fetch_assoc())
           {
               $user_id = $row['id'];
           }       
        }
         else {
                  echo json_encode(['status'=> 'Error', 'message'=> 'cant find the user, cant proced']);
                   exit();
              }
              
              
        
        $save_items="INSERT INTO user_carts (user_id, cart_items) VALUES ('$user_id', '$cart_items_json')";
        
        $update_carts="UPDATE user_carts SET cart_items='$cart_items_json' WHERE user_id='$user_id'";
        
        $carts_exist_already = "SELECT * FROM user_carts WHERE user_id='$user_id'";
     
        
        
        
        $check_if_carts_ex = $conn->query($carts_exist_already);
        if ($check_if_carts_ex->num_rows > 0)
        {
             //update if exists
                if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }
                if ($conn->query($update_carts) === true)
                {
                  echo json_encode(['status' => "Success", 'message' => "Record updated successfully"]);
                } else 
                {
                  echo json_encode(['status' => "Error", 'message' => "Error updating record: " . $conn->error]);
                }
               
        }
         else //insert a fresh record of cart and wishlist
         {
          if ($conn->query($save_items) === true) 
            {
              echo json_encode(['status' => "Success", 'message' => "Records inserted successfully"]);
            } 
            else
            {
                echo json_encode(['status' => "Error", 'message' => "Could not able to execute"]);
            }
         }     
        
        
       
                  //echo json_encode(['status'=> 'Error', 'message'=> 'sth went wrong']);
                 
        
        

        
         $conn->close();
    }
    else 
    { //no request was sent?
        echo json_encode(['status'=> "Error", 'message'=> 'nothing requested?']);
    }
} else
      {
        // Handle JSON decoding error
        http_response_code(400); // Bad Request
        //: Invalid JSON data received.
        $error_response = ['status' => 'Error', 'message' => "Bad Request"];
        echo json_encode($error_response);
      }
     