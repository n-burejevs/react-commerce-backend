<?php
require("db_config.php");
require ("headers.php");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
 
     // 1. Get the raw JSON data from the request body
$json_data = file_get_contents('php://input');

// 2. Decode the JSON data into a PHP associative array or object
$request_data = json_decode($json_data, true); // 'true' for associative array, omit for object

if ($request_data !== null) 
{
    $user_token = isset($request_data['user_token']) ? $request_data['user_token'] : null;
         
    if ($user_token !=null)
    {
         // Create connection
        $conn = new mysqli($servername, $username, $db_password, $dbname);
        
       // Check connection
        if ($conn->connect_error)
        {   //Cant authenticate, error 
         echo json_encode(['status' => "error", 'message' => $conn->connect_error]);
         exit();
        }
        
          //find users cart and wishlist
       $find_carts_wishlists = "SELECT cart_items, wished_items FROM user_carts JOIN users on user_carts.user_id=users.id WHERE token = '$user_token'";
       
        $cart_wishlist = $conn->query($find_carts_wishlists);
        //data found, send it back to frond end?
        if ($cart_wishlist->num_rows > 0)
        {
           $cart_items = $wishlist_items = "";
           
             while($row = $cart_wishlist->fetch_assoc())
             {
                 //var_dump($row['cart_items']);
                $cart_items = $row['cart_items'];
                $wishlist_items = $row['wished_items'];
             }
                echo json_encode(['status' => 'success', 'message' => 
               ['cart_items' => $cart_items,'wished_items' => $wishlist_items] ]);
               
              }
              else {
                  echo json_encode(['status'=> 'Error', 'message'=> 0]);
              }
              
               
              $conn->close(); 
    }
    else 
    { //no request was sent?
        echo json_encode(['status'=> 'Error', 'message'=> 'nothing requested?']);
    }
         
                  
} else
      {
        // Handle JSON decoding error
        http_response_code(400); // Bad Request
        //: Invalid JSON data received.
        $error_response = ['status' => 'Error', 'message' => ''];
        echo json_encode($error_response);
      }
      