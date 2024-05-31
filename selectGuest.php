<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Guests</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<style>
    body{
        background-color: #f0f0f0;
    }
#myDiv{
    padding-top:6rem;
    margin: 0;
    font-family: Arial, sans-serif;
    display: flex;
    flex-direction: column;
    align-items: center;
}
.container {
    height: 130px;
    padding: 10px;
    text-align: center;
    display: flex;
    flex-direction: column;
    justify-content: center;
    border-radius: 8px;
}
.content {
    display: grid;
    justify-content: center;
    align-items: center;
}
.content p {
    margin: 0;
    font-size: 22px;
}
.content .button {
    margin-left: 20px;
    padding: 5px 10px;
    font-size: 16px;
    background-color: #154782;
    color: #48C7F4;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}
.content .button:hover {
    background-color: #3E5462;
}
hr {
    width: 100%;
    border: 1px solid #3E5462;
    margin-top: 20px;
}
.center-box {
    margin-top: 20px;
    padding: 20px;
    border-radius: 8px;
    text-align: center;
}
.blue-div {
    height: 40px;
    background-color: #154782;
    margin-top: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px;
}
#nav-menu{
    display:flex; 
    Justify-content:center;
    align-items:center; 
    gap:12rem;
}
.blue-div p {
    text-align: center;
    width: 350px;
    height: 20px;
    margin: 0;
    color: #48C7F4;
    font-size: 14px;
}
.center-text {
    padding-top: 20px;
    text-align: center;
}
.dropdown {
    margin-top: 20px;
    width: 100%;
    height: 60px;
}
.dropdown select {
    width: 100%;
    height: 100%;
    padding: 10px;
    font-size: 14px;
    background-color: #154782;
    color: #48C7F4;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}
.dropdown option.add {
    padding-left: 40px;
}
.pink-div {
    background-color: #154782;
}
.pink-div p {
    text-align: center;
    width: 350px;
    height: 20px;
    margin: 0;
    color: white;
    font-size: 14px;
}
@media screen and (max-width: 769px) {
    #nav-menu {
        display: none !important;
    }
    #logo {
        margin: 0;
    }
    #myDiv{
        display: block;
    }
}
</style>
<body>
<?php
    // session_start();
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    use PHPMailer\PHPMailer\SMTP;
    require 'vendor/autoload.php';
    $sessionData = $_SESSION['selectedDataArray'];
    $leadData = isset($_SESSION['leadformData']) ? $_SESSION['leadformData'] : null;
    $usersData = $_SESSION['usersData'];
    foreach($sessionData as $session){
        $start = $session['startDate'];
        break;
    }
    if(isset($_GET['packageId'])) {
        $packageId= $_GET['packageId'];
    }
    if(isset($_GET['cabinId'])) {
        $cabinId= $_GET['cabinId'];
    }
    if(isset($_GET['select_type'])){
        $select_type = $_GET['select_type'];
    }
    if(isset($_GET['Date'])){
        $Date = $_GET['Date'];
        $select_month_number = date('m', strtotime($Date));
        $select_year = date('Y', strtotime($Date));
        $formatted_date = "$select_year-$select_month_number";
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
        $tokenData = json_decode($response, true);
        if ($response === false) {
            die("Error: Curl request failed: " . curl_error($ch));
        }
        curl_close($ch);
        $token = $tokenData['accessToken'];
        return $token;
    }
    if(isset($_COOKIE['token'])){
        $token = $_COOKIE['token'];
    }else{
        $token = getToken();
    }
    if(isset($_GET['packageId'])){
        $url = "https://jb-b2b-api-test.azurewebsites.net/api/search/agent/search-packages?package=$select_type&from=$formatted_date";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
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
        $filteredData = $data['packageResponses'];
        foreach($filteredData as $filter){
            $packagePriceTypeId = $filter['priceTypeId']; 
            $bookingTypeId = $filter['bookingTypeId'];
            break;
        }
        $filteredData = array_filter($filteredData, function($item) use ($packageId) {
            return $item['packageId'] == $packageId;
        });
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $arrData = [
            "packageId" => $packageId,
            "packageDepartureDate" => $start . ".480Z",
            "packageBookingDate" => "2024-05-07T02:59:14.480Z",
            "packagePriceTypeId" => $packagePriceTypeId,
            "bookingTypeId" => $bookingTypeId,
            "employeeId" => "yrz7m8i3",
            "cabins" => [],
            "passengers" => [],
            "agentReference" => "kiran"
        ];
        $cabinId2 = 1;
        $isLead = true;
        foreach ($_SESSION['selectedDataArray'] as $sessionData) {
            $quantity = $sessionData['quantity'];
            $occupancyId = (int) $sessionData['occupancyLimit'];
            $arrData['cabins'][] = [
                "cabinId" => $cabinId2,
                "elementId" => (int) $sessionData['cabinId'],
                "elementIdSole" => (int) $sessionData['elementIdSole'],
                "pax" => $quantity,
                "occupancyType" => $occupancyId,
                "priceTypeId" => (int) $sessionData['priceTypeId']
            ];
            $pax =1;
            foreach ($usersData as $users) {
                if ($users['cabinId'] == $sessionData['cabinId']) {
                    $arrData['passengers'][] = [
                        "cabinId" => $cabinId2,
                        "isAdult" => true,
                        "passengerNumberInRoom" => $pax,
                        "isLeadPassenger" => $isLead,
                        "firstName" => $users['fname'],
                        "lastName" => $users['lname'],
                        "title" => $users['title'],
                        "addressLine1" => $users['address1'],
                        "addressLine2" => $users['address2'],
                        "addressPostCode" => $users['postno'],
                        "addressCity" => $users['city'],
                        "addressState" => $users['state'],
                        "addressCountry" => $users['country'],
                        "phoneNumber" => $users['phone'],
                        "emailAddress" => $users['email'],
                        "isVegetarian" => $users['veg'],
                        "isLactoseFree" => $users['lactose'],
                        "isGlutenFree" => $users['gluten'],
                        "isVegan" => $users['vegan'],
                        "travellingWithInfantDetails" => $users['infants_details'],
                        "otherRequestsValue" => $users['other_request']
                    ];
                    $pax++;
                    $isLead = false;
                }
            }
            $cabinId2++;
        }
        $apiToken = getToken();
        $url = "https://jb-b2b-api-test.azurewebsites.net/api/Booking/create-and-confirm";
        $postApiJson = json_encode($arrData);
        // echo $postApiJson;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Content-Type: application/json",
                "Authorization: Bearer $apiToken"
            ));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postApiJson);
        $response = curl_exec($ch);
        $apiData = json_decode($response, true);
        if ($response === false) {
            die("Error: Curl request failed: " . curl_error($ch));
        } else {
            $mail = new PHPMailer(true);
        try {
            // $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'dhokekiran98@gmail.com';
            $mail->Password   = 'aznwuwgxybwyqqyx';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            $mail->setFrom('dhokekiran98@gmail.com', 'AussiTrains');
            $mail->addAddress($leadData['email'], $leadData['fname']);
            $mail->addCC('SriharshanS@gauratravel.com.au');
            $mail->isHTML(true);
            $mail->Subject = 'Booking Reference Number: ' . $apiData['bookingReferenceNumber'];
            $mail->Body    = "Thanks for your booking. For more details, visit: https://wpt.yourbestwayhome.com.au/rail_v2/show?referenceNo={$apiData['bookingReferenceNumber']}";
            $mail->AltBody = 'Thanks for your booking. For more details, visit: https://wpt.yourbestwayhome.com.au/rail_v2/show?referenceNo=' . $apiData['bookingReferenceNumber'];
            $mail->send();
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
        }
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
    <div id="myDiv">
    <input type="text" id="Date" hidden value="<?php echo $Date;?>">
    <?php if (!isset($apiData['bookingReferenceNumber'])) : ?>
        <div>
            <div class="container">
                <h1 style="margin: 0; font-size: 36px;color:#154782;">LEAD GUEST</h1>
                <div class="content my-3">
                    <p style="color:#48C7F4;"><?php echo htmlspecialchars($leadData['title']) . ' ' . htmlspecialchars($leadData['fname']) . ' ' . htmlspecialchars($leadData['lname']) ?></p>
                    <a class="btn button" href="leadform.php?packageId=<?php echo $packageId;?>&cabinId=<?php echo $cabinId;?>&select_type=<?php echo $select_type;?>">Change</a>
                </div>
            </div>
            <hr>
            <form id="guestForm" action="" method="post">
    <input type="hidden" name="packageId" value="<?php echo $packageId; ?>">
    <?php $addguestCount = 0; $cabin_count = 1; foreach($sessionData as $session_code): ?>
    <div class="center-box">
        <div class="blue-div">
            <p>CABIN <?php echo $cabin_count?></p>
            <p><?php echo $session_code['cabinName']?></p>
        </div>
        <div class="center-text">
            <p style="color:#48C7F4">Add Guests</p>
        </div>
        <?php for ($i = 0; $i < $session_code['quantity']; $i++): ?>
        <div class="dropdown">
            <input type="text" id="select_type" hidden value="<?php echo $select_type; ?>">
            <select class="guest-select" data-cabin-id="<?php echo $session_code['cabinId']; ?>">
                <option>Select or add a guest</option>
                <?php foreach ($usersData as $user): ?>
                    <option value="<?php echo $user['fname']; ?>">
                        <?php echo $user['fname'] . ' ' . $user['lname']; ?>
                    </option>
                <?php endforeach; ?>
                <?php if (count($usersData) <= $addguestCount): ?>
                    <option class="add-guest-option" value="add">+ Add a guest</option>
                <?php endif; ?>
            </select>
        </div>
        <?php $addguestCount++; ?>
        <?php endfor; ?>
    </div>
    <?php $cabin_count++; ?>
    <?php endforeach; ?>
    <div class="pink-div d-grid gap-2 col-6 mx-auto">
        <button class="btn" style="color:#48C7F4;" type="submit">Submit</button>
    </div>
</form>

        </div>
        <?php elseif (($apiData['bookingReferenceNumber'] == null)): ?>
        <div class="d-grid justify-content-center align-items-center h-100">
            <h3 class="text-center">Sorry for the inconvenience</h3>
            <p class="text-center">Contact on:</p>
            <p class="text-center">CALL 1800 703 357</p>
            <?php
                unset($_SESSION['usersData']);
                unset($_SESSION['leadformData']);
                unset($_SESSION['selectedDataArray']);
            ?>
        </div>
        <?php else: ?>
        <div class="d-flex justify-content text-center align-items-center" style="height: 80vh;">
            <div>
                <p>Your booking has been confirmed. Your booking reference number is: <?php echo $apiData['bookingReferenceNumber']; ?></p>
                <p>Thank you for booking with us!</p>
                <a href="show.php?referenceNo=<?php echo $apiData['bookingReferenceNumber']; ?>">Show Details</a>
                <?php
                    unset($_SESSION['usersData']);
                    unset($_SESSION['leadformData']);
                    unset($_SESSION['selectedDataArray']);
                    $cookie_name = "token";
                    $cookie_expiration = time() - 3600;
                    setcookie($cookie_name,"",$cookie_expiration, "/");
                ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selects = document.querySelectorAll('.guest-select');
    var select_type = document.getElementById('select_type').value;
    var Dateurl = document.getElementById('Date').value;
    selects.forEach(select => {
        select.addEventListener('change', function() {
            const selectedOption = this.value;
            if (selectedOption === 'add') {
                const packageIdElement = document.querySelector('input[name="packageId"]');
                const cabinId = this.getAttribute('data-cabin-id');
                if (packageIdElement && cabinId) {
                    const packageId = packageIdElement.value;
                    window.location.href = `userform.php?packageId=${packageId}&cabinId=${cabinId}&select_type=${select_type}&Date=${Dateurl}`;
                }
            } else {
                selects.forEach(otherSelect => {
                    if (otherSelect !== select) {
                        const options = otherSelect.querySelectorAll('option');
                        options.forEach(option => {
                            if (option.value === selectedOption) {
                                option.disabled = true;
                            }
                        });
                    }
                });
            }
        });
    });
});
</script>
</body>
</html>