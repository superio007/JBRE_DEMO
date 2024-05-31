<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Document</title>
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />
   <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" media="screen">
   <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
   <style>
      .table-responsive {
         overflow-x: auto;
      }
      @media screen and (max-width: 769px) {
          #nav-menu {
              display: none;
          }
          #logo {
              margin: 0;
          }
          .table-responsive {
              overflow-x: auto;
          }
          .table-hover th, .table-hover td {
              display: block;
              width: 100%;
          }
          .table-hover thead {
              display: none;
          }
          .table-hover tr {
              display: flex;
              flex-direction: column;
              border-bottom: 1px solid #dee2e6;
              margin-bottom: 1rem;
              padding-bottom: 1rem;
          }
          .table-hover td {
              border: none;
          }
          .table-hover td::before {
              content: attr(data-label);
              font-weight: bold;
              width: 100%;
              display: inline-block;
          }
      }
   </style>
</head>
<body>
<?php
         if(isset($_GET['referenceNo'])){
           $referenceNo = $_GET['referenceNo'];
         }   
         function getToken(){ 
           $url = "https://jb-auth-uat.azurewebsites.net/Client";
           $postData = array(
             "claims" => array(
               "userName" => "stuart@geelongtravel.com.au"
             ),
             "clientId" => "515909a2-c9a6-46ee-a923-6cb1170e3571",
             "secret" => "oF7QWRUYYUmISudsRgixrg=="
           );
           $postDataJson = json_encode($postData);
           $ch = curl_init();
           curl_setopt($ch, CURLOPT_URL, $url);
           curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
           curl_setopt($ch, CURLOPT_HTTPHEADER, array(
             "Content-Type: application/json"
           ));
           curl_setopt($ch, CURLOPT_POST, true);
           curl_setopt($ch, CURLOPT_POSTFIELDS, $postDataJson);
           $response = curl_exec($ch);
           $data = json_decode($response, true);
           if ($response === false) {
             die("Error: Curl request failed: " . curl_error($ch));
           }
           curl_close($ch);
           $token = $data['accessToken'];
           return $token;
         }
         if(isset($_COOKIE['token'])){
            $token = $_COOKIE['token'];
         }else{
               $token = getToken();
         }
         $url = "https://jb-b2b-api-test.azurewebsites.net/api/booking?bookingReferenceNumber={$referenceNo}";
         $ch = curl_init();
         curl_setopt($ch, CURLOPT_URL, $url);
         curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
         curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
         curl_setopt($ch, CURLOPT_HTTPHEADER, array(
           "Authorization: Bearer $token"
         ));
         $response = curl_exec($ch);
         if ($response === false) {
           die("Error: Curl request failed: " . curl_error($ch));
         }
         curl_close($ch);
         $data = json_decode($response, true);
         $otherPassengers = $data['bookingDetails']['otherPassengers'];
?>
<div class="d-md-none">
   <div style="background-color:#154782; display:flex; align-items:center; justify-content:space-around;padding:1rem 0">
         <a href="index.php"><img src="logo.svg" id="logo" style="width:345px" alt="logo" class="offset-1"></a>
         <div id="nav-menu">
         <a href="index.php" style="color:white; font-size:20px;font-weight:bold; text-decoration:none; " target="_blank" rel="noopener noreferrer">Home</a>
         <a href="#" style="color:white; font-size:20px;font-weight:bold; text-decoration:none; " target="_blank" rel="noopener noreferrer">About Us</a>
         <a href="#" style="color:white; font-size:20px;font-weight:bold; text-decoration:none; " target="_blank" rel="noopener noreferrer">Contact Us</a>
         </div>
   </div>
   <div class="container">
      <h2 class="text-center my-5" style="text-transform:uppercase;">Booking Details</h2>
      <table class="table table-hover">
         <tbody>
               <tr>
                  <th scope="row">Booking ID</th>
                  <td><?php echo $data['bookingDetails']['bookingId']; ?></td>
               </tr>
               <tr>
                  <th scope="row">Name</th>
                  <td><?php echo $data['bookingDetails']['name']; ?></td>
               </tr>
               <tr>
                  <th scope="row">Reference Number</th>
                  <td><?php echo $data['bookingDetails']['referenceNumber']; ?></td>
               </tr>
               <tr>
                  <th scope="row">Total Sell Amount</th>
                  <td><?php echo '$' . number_format($data['bookingDetails']['totalSellAmount'], 2); ?></td>
               </tr>
         </tbody>
      </table>

      <h2 class="text-center my-4" style="text-transform:uppercase;">Lead <span style="color:#154782;">Passenger</span> Details</h2>
      <div class="table-responsive">
         <table class="table table-hover">
               <thead>
                  <tr>
                     <th>Sr/no</th>
                     <th>Title</th>
                     <th>First Name</th>
                     <th>Last Name</th>
                  </tr>
               </thead>
               <tbody>
                  <?php 
                     $passenger = 1;
                     $leadPassenger = $data['bookingDetails']['leadPassenger'];
                  ?>
                  <tr>
                     <td data-label="Sr/no"><?php echo $passenger; ?></td>
                     <td data-label="Title"><?php echo $leadPassenger['title']; ?></td>
                     <td data-label="First Name"><?php echo $leadPassenger['firstName']; ?></td>
                     <td data-label="Last Name"><?php echo $leadPassenger['lastName']; ?></td>
                  </tr>
                  <tr>
                     <th colspan="2">Email</th>
                     <th colspan="2">Telephone</th>
                  </tr>
                  <tr>
                     <td colspan="2" data-label="Email"><?php echo $leadPassenger['email']; ?></td>
                     <td colspan="2" data-label="Telephone"><?php echo $leadPassenger['telephone']; ?></td>
                  </tr>
                  <tr>
                     <td data-label="Vegetarian Meal"><?php echo $leadPassenger['vegetarianMeal'] ? 'Yes' : 'No'; ?></td>
                     <td data-label="Lactose-Free Meal"><?php echo $leadPassenger['lactoseFreeMeal'] ? 'Yes' : 'No'; ?></td>
                     <td data-label="Gluten-Free Meal"><?php echo $leadPassenger['glutenFreeMeal'] ? 'Yes' : 'No'; ?></td>
                     <td data-label="Vegan Meal"><?php echo $leadPassenger['veganMeal'] ? 'Yes' : 'No'; ?></td>
                  </tr>
               </tbody>
         </table>
      </div>

      <?php if($otherPassengers): ?>
      <h2 class="text-center my-4" style="text-transform:uppercase;">Other's <span style="color:#154782;">Passenger</span> Details</h2>
      <div class="table-responsive">
         <table class="table table-hover">
               <thead>
                  <tr>
                     <th>Sr/no</th>
                     <th>Title</th>
                     <th>First Name</th>
                     <th>Last Name</th>
                     <th>Email</th>
                     <th>Telephone</th>
                     <th>Vegetarian Meal</th>
                     <th>Lactose-Free Meal</th>
                     <th>Gluten-Free Meal</th>
                     <th>Vegan Meal</th>
                  </tr>
               </thead>
               <tbody>
                  <?php 
                     $passengerCount = 1;
                     foreach ($otherPassengers as $passenger): 
                  ?>
                  <tr>
                     <td data-label="Sr/no"><?php echo $passengerCount; ?></td>
                     <td data-label="Title"><?php echo $passenger['title']; ?></td>
                     <td data-label="First Name"><?php echo $passenger['firstName']; ?></td>
                     <td data-label="Last Name"><?php echo $passenger['lastName']; ?></td>
                     <td data-label="Email"><?php echo $passenger['email']; ?></td>
                     <td data-label="Telephone"><?php echo $passenger['telephone']; ?></td>
                     <td data-label="Vegetarian Meal"><?php echo $passenger['vegetarianMeal'] ? 'Yes' : 'No'; ?></td>
                     <td data-label="Lactose-Free Meal"><?php echo $passenger['lactoseFreeMeal'] ? 'Yes' : 'No'; ?></td>
                     <td data-label="Gluten-Free Meal"><?php echo $passenger['glutenFreeMeal'] ? 'Yes' : 'No'; ?></td>
                     <td data-label="Vegan Meal"><?php echo $passenger['veganMeal'] ? 'Yes' : 'No'; ?></td>
                  </tr>
                  <?php 
                     $passengerCount++;
                     endforeach; 
                  ?>
               </tbody>
         </table>
      </div>
      <?php endif; ?>
   </div>
</div>
<div class="d-none d-md-block">
   <div style="background-color:#154782; display:flex; align-items:center; justify-content:space-around;padding:1rem 0" id="desktop-nav">
      <a href="index.php"><img src="logo.svg" id="logo" style="width:345px" alt="logo" class="offset-1"></a>
      <div id="nav-menu" style="display:flex; justify-content:center; align-items:center; gap:12rem;">
      <a href="index.php" style="color:white; font-size:20px;font-weight:bold; text-decoration:none;">Home</a>
      <a href="#" style="color:white; font-size:20px;font-weight:bold; text-decoration:none;">About Us</a>
      <a href="#" style="color:white; font-size:20px;font-weight:bold; text-decoration:none;">Contact Us</a>
      </div>
   </div>
   <div id="mobile-nav" style="display:none;">
      <a href="index.php"><img src="logo.svg" id="logo" style="width:150px" alt="logo"></a>
      <button class="btn btn-light" data-bs-toggle="collapse" data-bs-target="#mobile-menu" aria-expanded="false" aria-controls="mobile-menu">Menu</button>
   </div>
   <div class="collapse" id="mobile-menu">
      <div class="bg-light p-4">
         <a href="index.php" class="d-block mb-2">Home</a>
         <a href="#" class="d-block mb-2">About Us</a>
         <a href="#" class="d-block">Contact Us</a>
      </div>
   </div>
   <div class="container">
      <h2 class="text-center my-5" style="text-transform:uppercase;">Booking Details</h2>
      <div class="table-responsive">
         <table class="table table-hover">
            <tbody>
               <tr>
                  <th scope="row">Booking ID</th>
                  <td><?php echo $data['bookingDetails']['bookingId']; ?></td>
               </tr>
               <tr>
                  <th scope="row">Name</th>
                  <td><?php echo $data['bookingDetails']['name']; ?></td>
               </tr>
               <tr>
                  <th scope="row">Reference Number</th>
                  <td><?php echo $data['bookingDetails']['referenceNumber']; ?></td>
               </tr>
               <tr>
                  <th scope="row">Total Sell Amount</th>
                  <td><?php echo '$' . number_format($data['bookingDetails']['totalSellAmount'], 2); ?></td>
               </tr>
            </tbody>
         </table>
      </div>
      <h2 class="text-center my-4" style="text-transform:uppercase;">Lead <span style="color:#154782;">Passenger</span> Details</h2>
      <div class="table-responsive">
         <table class="table table-hover">
            <thead>
               <tr>
                  <th>Sr/no</th>
                  <th>Title</th>
                  <th>First Name</th>
                  <th>Last Name</th>
                  <th>Email</th>
                  <th>Telephone</th>
                  <th>Vegetarian Meal</th>
                  <th>Lactose-Free Meal</th>
                  <th>Gluten-Free Meal</th>
                  <th>Vegan Meal</th>
               </tr>
            </thead>
            <tbody>
               <?php 
                  $passenger = 1;
                  $leadPassenger = $data['bookingDetails']['leadPassenger'];
                  ?>
               <tr>
                  <td><?php echo $passenger; ?></td>
                  <td><?php echo $leadPassenger['title']; ?></td>
                  <td><?php echo $leadPassenger['firstName']; ?></td>
                  <td><?php echo $leadPassenger['lastName']; ?></td>
                  <td><?php echo $leadPassenger['email']; ?></td>
                  <td><?php echo $leadPassenger['telephone']; ?></td>
                  <td><?php echo $leadPassenger['vegetarianMeal'] ? 'Yes' : 'No'; ?></td>
                  <td><?php echo $leadPassenger['lactoseFreeMeal'] ? 'Yes' : 'No'; ?></td>
                  <td><?php echo $leadPassenger['glutenFreeMeal'] ? 'Yes' : 'No'; ?></td>
                  <td><?php echo $leadPassenger['veganMeal'] ? 'Yes' : 'No'; ?></td>
               </tr>
            </tbody>
         </table>
      </div>
      <?php if($otherPassengers):?>
      <h2 class="text-center my-4"  style="text-transform:uppercase;">Other's <span style="color:#154782;">Passenger</span> Details</h2>
      <div class="table-responsive">
         <table class="table table-hover">
            <thead>
               <tr>
                  <th>Sr/no</th>
                  <th>Title</th>
                  <th>First Name</th>
                  <th>Last Name</th>
                  <th>Email</th>
                  <th>Telephone</th>
                  <th>Vegetarian Meal</th>
                  <th>Lactose-Free Meal</th>
                  <th>Gluten-Free Meal</th>
                  <th>Vegan Meal</th>
               </tr>
            </thead>
            <tbody>
               <?php 
                  $passengerCount = 1;
                  foreach ($otherPassengers as $passenger): 
                  ?>
               <tr>
                  <td><?php echo $passengerCount; ?></td>
                  <td><?php echo $passenger['title']; ?></td>
                  <td><?php echo $passenger['firstName']; ?></td>
                  <td><?php echo $passenger['lastName']; ?></td>
                  <td><?php echo $passenger['email']; ?></td>
                  <td><?php echo $passenger['telephone']; ?></td>
                  <td><?php echo $passenger['vegetarianMeal'] ? 'Yes' : 'No'; ?></td>
                  <td><?php echo $passenger['lactoseFreeMeal'] ? 'Yes' : 'No'; ?></td>
                  <td><?php echo $passenger['glutenFreeMeal'] ? 'Yes' : 'No'; ?></td>
                  <td><?php echo $passenger['veganMeal'] ? 'Yes' : 'No'; ?></td>
               </tr>
               <?php 
                  $passengerCount++;
                  endforeach; 
                  ?>
            </tbody>
         </table>
      </div>
      <?php endif;?>
   </div>
</div>
</body>
</html>
