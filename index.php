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
    $postDataJson = json_encode($postData);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
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
    $cookie_name = "token";
    $cookie_value = $token;
    $cookie_expiration = time() + 1140;
    setcookie($cookie_name, $cookie_value, $cookie_expiration, "/");
    return $token;
}
function getCurrentDate() {
    return date('Y-m');
}
$Date = getCurrentDate();
function getNextMonthDate($date) {
    $currentDate = new DateTime($date);
    $currentDate->modify('+1 month');
    return $currentDate->format('Y-m');
}

$formatted_date = getNextMonthDate($Date);
$token = getToken(); // Assumes getToken() is defined elsewhere
$url = "https://jb-b2b-api-test.azurewebsites.net/api/search/agent/search-packages?package=GHROW&from=$formatted_date";

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
if ($data === null) {
    echo "Error: Kindly check the authentication token";
    exit;
}

$filteredData = $data['packageResponses'];


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $select_date = $_POST['select_date'] ?? '';
    $select_type = $_POST['select_type'] ?? '';

    if (!empty($select_date) && !empty($select_type)) {
        $redirect_url = "grid.php?select_date=" . urlencode($select_date) . "&select_type=" . urlencode($select_type);
        header("Location: " . $redirect_url);
        exit();
    } else {
        echo "Error: Both date and type must be selected.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>JBRE - API</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" media="screen">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous" defer></script>
</head>
<body>
    <div style="background-color:#154782; display:flex; align-items:center; justify-content:space-around;padding:1rem 0">
        <a href="index.php"><img src="logo.svg" id="logo" style="width:345px" alt="logo" class="offset-1"></a>
        <div id="nav-menu">
            <a href="index.php" style="color:white; font-size:20px;font-weight:bold; text-decoration:none;" target="_blank" rel="noopener noreferrer">Home</a>
            <a href="#" style="color:white; font-size:20px;font-weight:bold; text-decoration:none;" target="_blank" rel="noopener noreferrer">About Us</a>
            <a href="#" style="color:white; font-size:20px;font-weight:bold; text-decoration:none;" target="_blank" rel="noopener noreferrer">Contact Us</a>
        </div>
    </div>
    <div id="myDiv">
        <div class="container">
            <h1 class="mb-4">READY TO BOOK?</h1>
            <form method="post" id="booking-form" style="margin:3rem 0" class="form-inline justify-content-center">
                <div class="gap-5 d-flex flex-column flex-md-row">
                    <div class="form-group mb-2 mx-sm-3">
                        <select name="select_type" class="form-control" id="travelOn" required>
                            <option value="" selected disabled hidden>Select</option>
                            <?php foreach($filteredData as $filterDrop): ?>
                                <option class="text-black" value="<?php echo $filterDrop['type']?>"><?php echo $filterDrop['name']?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group mb-3 mx-sm-3">
                        <input name="select_date" type="text" class="form-control" id="travelMonth" placeholder="Travel Month" required>
                    </div>
                </div>
                <button type="submit" class="mb-2 btn-disabled" id="search-link" role="button" style="pointer-events: none;">Search</button>
            </form>
            <p>OR CALL 1800 703 357</p>
        </div>
    </div>
    <script>
        
        document.addEventListener('DOMContentLoaded', function () {
    var today = new Date();
    var firstDayOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);

    $('#travelMonth').datepicker({
        format: "MM yyyy",
        startView: "months",
        minViewMode: "months",
        autoclose: true,
        todayHighlight: true,
        startDate: firstDayOfMonth,
        beforeShowMonth: function(date) {
            // Disable May
            if (date.getMonth() === 4) {
                return false;
            }
        }
    });

    $('#booking-form').on('input change', function () {
        const travelOn = $('#travelOn').val();
        const travelMonth = $('#travelMonth').val();
        const searchLink = $('#search-link');
        if (travelOn && travelMonth) {
            searchLink.prop('disabled', false).removeClass('btn-default btn-disabled').addClass('btn-active').css('pointer-events', 'auto');
        } else {
            searchLink.prop('disabled', true).removeClass('btn-active').addClass('btn-default btn-disabled').css('pointer-events', 'none');
        }
    });

    $('#search-link').on('click', function (e) {
        if ($(this).hasClass('btn-disabled')) {
            e.preventDefault();
        }
    });
});

    </script>
    <style>
        #myDiv {
            background: linear-gradient(to bottom, #004585, #999b9f);
            color: white;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .container {
            text-align: center;
        }
        #nav-menu{
            display:flex; 
            Justify-content:center;
            align-items:center; 
            gap:12rem;
        }
        #search-link {
            width: 250px;
            height: 49px;
            text-align: center;
            background-color: #ffffff14;
            color: white;
            text-decoration: none;
            border: none;
        }
        #search-link:active {
            color: white;
            font-size: larger;
            font-weight: 600;
            background-color: #ffe3e38f !important;
            border-color: #ffe3e38f !important;
            cursor: pointer;
        }
        .btn-active {
            color: white;
            font-size: larger;
            font-weight: 600;
            background-color: #ffe3e38f !important;
            border-color: #ffe3e38f !important;
            cursor: pointer;
        }
        .form-group {
            display: flex;
            flex-direction: column;
            align-items: center;
            background-color: transparent;
            border-bottom: 1px solid white;
            padding: 5px;
            margin-bottom: 10px;
        }
        .form-control {
            background-color: transparent;
            border: none;
            color: white;
        }
        .form-control::placeholder {
            color: white;
        }
        .form-control:focus {
            background-color: transparent;
            color: white;
            outline: none;
            box-shadow: none;
        }
        .form-control:not(:placeholder-shown) {
            background-color: transparent;
        }
        .form-inline {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 30px;
        }
        .form-group.mb-2 {
            margin-bottom: 0 !important;
        }
        .form-group.mb-3 {
            margin-bottom: 0 !important;
        }
        @media screen and (max-width: 769px) {
            #nav-menu {
                display: none;
            }
            #logo {
                margin: 0;
            }
        }
    </style>
</body>
</html>
