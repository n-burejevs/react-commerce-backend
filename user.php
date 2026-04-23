<?php
require("db_config.php");
require ("headers.php");

$method = $_SERVER['REQUEST_METHOD'];

//send the user key values
//to do: send user_parameters back

//a while back some test task asked for an ability to add custom user attributes?
//here is what they wanted???
    if ($method == 'GET')
{   
    
    //GET THE STUFF FROM THE user_key from user_parameters
       $get_user_attributes = "SELECT DISTINCT user_key FROM user_parameters";
        $ask_user_attributes = $conn->query($get_user_attributes);
        
        if ($ask_user_attributes->num_rows > 0)
        {
            $attributes = [];
           while($row = $ask_user_attributes->fetch_assoc())
           {
               $attributes[] = $row['user_key'];
           }
        
            echo json_encode($attributes);//['omg', 'nothing', 'is done']);

        }
     
}

elseif ($method == 'POST'){

$json_data = file_get_contents('php://input');
$request_data = json_decode($json_data, true);

if ($request_data !== null) 
{
    $user_value= isset($request_data['user_val']) ? $request_data['user_val'] : null;
        $user_key = isset($request_data['user_key']) ? $request_data['user_key'] : null; 
          $user_email = isset($request_data['email']) ? $request_data['email'] : null;
       
    if ($user_value !=null && $user_key !=null && $user_email !=null)
    {
        //get user id!
        $user_id;

       $find_user_id = "SELECT id FROM users WHERE email = '$user_email'";
        $ask_for_user_id = $conn->query($find_user_id);
                
       /* $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $$user_email);
        $stmt->execute();
        $ask_for_user_id = $stmt->get_result();*/
   
        if ($ask_for_user_id->num_rows > 0)
        {
           while($row = $ask_for_user_id->fetch_assoc())
           {
               $user_id = $row['id'];
           }
           
            //now final insert for users data...
         /*   $save_user_values = "INSERT INTO user_parameters (user_id, user_key, user_value) VALUES ('$user_id', '$user_key', '$user_value')";

            if ($conn->query($save_user_values) === true) 
                {
                echo json_encode(['status' => "Success", 'message' => "Records inserted successfully"]);
                } 
                else
                {
                    echo json_encode(['status' => "Error", 'message' => "Could not able to execute"]);
                }*/

//To DO: check before insert if that key exists, if so update the value
        $stmt = $conn->prepare("INSERT INTO user_parameters (user_id, user_key, user_value) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $user_id, $user_key, $user_value);
        $stmt->execute();
         
        /*if ($stmt->get_result() === true) 
            {
            echo json_encode(['status' => "Success", 'message' => "Records inserted successfully"]);
            } 
            else
            {
                echo json_encode(['status' => "Error", 'message' => "Could not able to execute"]);
            }*/
            echo json_encode(['status' => "Success", 'message' => "Records inserted successfully"]);
           


        }
         else {
                  echo json_encode(['status'=> 'Error', 'message'=> 'cant find the user, cant proced']);
              }



    }
      else
        {
            echo json_encode(['status' => "Error", 'message' => "somethings is null"]);
        }

}
   else
    {
        echo json_encode(['status' => "Error", 'message' => "request was bad"]);
    }

}
 $conn->close();
