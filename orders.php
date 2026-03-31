<?php
require("db_config.php");
require ("headers.php");


$user_error = $order_error = "";

$method = $_SERVER['REQUEST_METHOD'];

//sanitize inputs??

$input = json_decode(file_get_contents('php://input'), true);

$json_data = json_decode(file_get_contents('https://dummyjson.com/products'), true);

$products = $json_data['products'];

//var_dump( $products);

function universal_isset($input_field, $input)
{   $var ='';
    if (isset($input[$input_field]))
    {
    $var = $input[$input_field];
    }
    else {
     $var = '';
    }
    return $var;
}

switch ($method) {
     //all orders displayed in a browser
    case 'GET': 
            $orders_items = [];
            $result = $conn->query("SELECT * FROM orders;");

            $orders = [];
            
            while ($row = $result->fetch_assoc()) {
                //list of details about orderds -> data about shipment adress, date, customer name and so on
                $orders[] = $row;
                //item ids and quantities for each order
                $orders_items[] = $row['items'];

            }
           
           
            //var_dump($orders[0]);//['items']);
            //trying to get a price for every item and make a total order amount/cost
            //$total = 0;
            for($i = 0; $i< sizeof($orders); $i++)
            {
             $data = json_decode($orders[$i]['items']);
             $total = 0;
                foreach ($data as $item) {
                    //works
                    //echo "ID: " . $item->id . " | Quantity: " . $item->quantity . "<br>";

                   // var_dump($item->id); //find_price_by_id($item->id, $products);
                  $total = $total + $item->quantity * find_price_by_id($item->id, $products);
                  //var_dump($item->id);
                     
                }
                $orders[$i]['total'] = round($total, 2);
                  
            }
               // var_dump($orders);
                 //this should be uncommented, to send list of all orders
            echo json_encode($orders);                                          //[0]['id'] );

        break;
        
     case 'POST':
        //submitting/making an order
         
         
          //check each with isset()?

         //if user is registred and logged in
        //$email = $input['user_email'];
        $email = universal_isset('user_email', $input);
         $items = json_encode($input['order_items']);
            $order_email = universal_isset('order_email', $input);
            //$order_email = $input['order_email'];
               $fullname = universal_isset('fullname', $input);
                //$fullname =  $input['fullname'];
         $adress =  universal_isset('adress', $input);//$input['adress'];
            $city = universal_isset('city', $input);//$input['city'];
                $zipCode = universal_isset('zipCode', $input);//$input['zipCode'];
                    $paymentMethod = universal_isset('paymentMethod', $input);//$input['paymentMethod'];
        $user_id = null;
          
         //get user id from email
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email); // "s" - variable is a string
        $stmt->execute();
        
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
          $user_id = $row['id'];
            } else {
          $user_id = null; // No user found // annon. user?
            }
        $stmt->close();
        
        //if($user_id == null)
        //{
           // echo json_encode(["status" =>"error", "message" => "No user found for that order"]);
       // }
        /*else
        {*/
       
       
        $date = date("Y-m-d H:i:s");
        $status = "Processing";
        
        $stmt_insert = $conn->prepare("INSERT INTO orders (fullName, email, address, city, zipCode, paymentMethod, user_id, date, status, items) "
                . "VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $stmt_insert->bind_param("ssssssssss", $fullname, $order_email, $adress, $city, $zipCode, $paymentMethod, $user_id, $date, $status, $items);
        $stmt_insert->execute();
        echo json_encode(["status" =>"success", "message" => "Order placed successfully!"]);
        $stmt_insert->close();
      
        
        /*}*/

        break;

    default:
        echo json_encode(["status" =>"error", "message" => "Invalid request method"]);
        break;
}
//what if products are empty or null?
//what if id are not found?
function find_price_by_id($id, $products)
{   
    foreach ($products as $item) {
        //var_dump ($items);
     
           //var_dump($item['id']);
           if($item['id'] == $id)
           {
               //return $item['price'];
               return $item['price'];
           }
           
        //if ($value->id == $id)
      //  {
        //     echo json_encode($value->price);
           // return $value->price;
       // }
    }

  /* foreach ($products as $items) {
        var_dump($items);
   }    */
}

$conn->close();