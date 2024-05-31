<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cancel Booking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>
<body>
<?php
function getToken(){
    $url = "https://jb-auth-uat.azurewebsites.net/Client";
    $postData = array(
        "claims" => array(
            "userName" => "stuart@geelongtravel.com.au"
        ),
        "clientId" => "515909a2-c9a6-46ee-a923-6cb1170e3571",
        "secret" => "oF7QWRUYYUmISudsRgixrg=="
    );
    
    // Convert the data to JSON format
    $postDataJson = json_encode($postData);
    // Initialize curl
    $ch = curl_init();
    // Set curl options
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Content-Type: application/json" // Set the Content-Type header to JSON
    ));
    curl_setopt($ch, CURLOPT_POST, true); // Set the request method to POST
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postDataJson); // Set the request body data

    // Execute curl request
    $response = curl_exec($ch);

    // Decode JSON response
    $data = json_decode($response, true);

    // Check for curl errors
    if ($response === false) {
        die("Error: Curl request failed: " . curl_error($ch));
    }
    // Close curl
    curl_close($ch);

    $token = $data['accessToken'];
    return $token;
}
$delete = false;
if(isset($_COOKIE['token'])){
    $token = $_COOKIE['token'];
}else{
    $token = getToken();
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $bookingReference = $_POST['bookingReference'];
    // echo $bookingReference;

    // Prepare POST data for API request
    $postData = array(
        "bookingReferenceNumber" => $bookingReference
    );

    // Convert the data to JSON format
    $postDataJson = json_encode($postData);

    // API endpoint
    $url = "https://jb-b2b-api-test.azurewebsites.net/api/booking/cancel";

    // Initialize curl
    $ch = curl_init();

    // Set curl options
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Authorization: Bearer $token",
        "Content-Type: application/json" // Specify content type
    ));
    curl_setopt($ch, CURLOPT_POST, true); // Set the request method to POST
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postDataJson);

    // Execute curl request
    $response = curl_exec($ch);

    // Check for curl errors
    if ($response === false) {
        echo "Error: Curl request failed: " . curl_error($ch);
    }else{
        $delete = true;
    }
    // Close curl
    curl_close($ch);
} 
?>
    
    <div style="background-color:#154782; display:flex; align-items:center; justify-content:space-around;padding:1rem 0">
        <a href="index.php"><img src="logo.svg" id="logo" style="width:345px" alt="logo" class="offset-1"></a>
        <div id="nav-menu">
        <a href="index.php" style="color:white; font-size:20px;font-weight:bold; text-decoration:none; " target="_blank" rel="noopener noreferrer">Home</a>
        <a href="#" style="color:white; font-size:20px;font-weight:bold; text-decoration:none; " target="_blank" rel="noopener noreferrer">About Us</a>
        <a href="#" style="color:white; font-size:20px;font-weight:bold; text-decoration:none; " target="_blank" rel="noopener noreferrer">Contact Us</a>
        </div>
    </div>
<div class="container">

    <div>
        <h1 class="text-center my-4">Cancel <span class="text-primary">Booking</span></h1>
    </div>
    <?php 
    if(!$delete){
    ?>
    <div class="d-flex justify-content-center">
        <form class="w-50" action="" method="post">
            <div class="form-group">
                <label class="mb-2" for="exampleInputEmail1">Booking Reference Number</label>
                <input type="text" class="form-control mb-2" name="bookingReference" placeholder="Enter Reference Number">
            </div>
            <div class="d-flex justify-content-center">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>
    <?php
    }else{
    ?>
    <div class="d-flex justify-content-center text-center">
        <div>
            <p>Your booking has been canceled. Your booking reference number is: <?php echo $bookingReference; ?></p>
            <p>Feel free to booking with us again!</p>
        </div>
    </div>
    <?php
    }
    ?>
</div>
</body>
</html>
<style>
    #nav-menu{
        display:flex; 
        Justify-content:center;
        align-items:center; 
        gap:12rem;
    }
    @media screen and (max-width: 769px) {
        #nav-menu {
            display: none !important;
        }
        #logo {
            margin: 0;
        }
    }
</style>