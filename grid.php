<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grid-Rail-now</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" media="screen">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous" defer></script>
</head>
<body>
<?php
    session_start();
    unset($_SESSION['usersData']);
    unset($_SESSION['leadformData']);
    unset($_SESSION['selectedDataArray']);

    function getToken(){
        $url = "https://jb-auth-uat.azurewebsites.net/Client";
        $postData = [
            "claims" => ["userName" => "stuart@geelongtravel.com.au"],
            "clientId" => "515909a2-c9a6-46ee-a923-6cb1170e3571",
            "secret" => "oF7QWRUYYUmISudsRgixrg=="
        ];
        $postDataJson = json_encode($postData);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postDataJson);
        $response = curl_exec($ch);
        if ($response === false) {
            die("Error: Curl request failed: " . curl_error($ch));
        }
        curl_close($ch);
        $data = json_decode($response, true);
        $token = $data['accessToken'];
        return $token;
    }
    // echo $token;

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $select_date = $_POST['select_date'] ?? '';
        $select_type = $_POST['select_type'] ?? '';
        if (!empty($select_date) && !empty($select_type)) {
            header("Location: grid.php?select_date=" . urlencode($select_date) . "&select_type=" . urlencode($select_type));
            exit();
        }
    }

    if(isset($_COOKIE['token'])){
        $token = $_COOKIE['token'];
    }else{
        $token = getToken();
    }
    if (isset($_GET['select_date'], $_GET['select_type'])) {
        $select_type = $_GET['select_type'];
        $select_date = $_GET['select_date'];
        $select_month_number = date('m', strtotime($select_date));
        $select_year = date('Y', strtotime($select_date));
        $formatted_date = "$select_year-$select_month_number";
        
    $url = "https://jb-b2b-api-test.azurewebsites.net/api/search/agent/search-packages?package=$select_type&from=$formatted_date";
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
        $token = getToken();
        echo "Error: Kindly check the authentication token";
        exit;
    }
        $filteredData = $data['packageResponses'];

        $finalFilteredData = array_filter($filteredData, function($package) use ($formatted_date) {
            foreach ($package['departures'] as $departure) {
                if (strpos($departure['startDate'], $formatted_date) !== false) {
                    return true;
                }
            }
            return false;
        });
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
<div class="w-100 p-3" id="searchDiv">
    <form method="post" id="booking-form" class="form-inline justify-content-center">
        <div class="gap-5 d-flex flex-column flex-md-row">
            <div class="form-group mb-2 mx-sm-3">
                <select name="select_type" class="form-control" id="travelOn" required>
                    <option value="" selected disabled hidden>Select</option>
                    <?php foreach($filteredData as $filterDrop): ?>
                        <option class="text-black" value="<?php echo htmlspecialchars($filterDrop['type']); ?>"><?php echo htmlspecialchars($filterDrop['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group mb-3 mx-sm-3">
                <input name="select_date" type="text" class="form-control" id="travelMonth" placeholder="Travel Month" required>
            </div>
            <button type="submit" class="mb-2 btn-disabled" id="search-link" role="button" style="pointer-events: none;">Search</button>
        </div>
    </form>
</div>
<div class="container mt-5">
    <h1 class="text-center mb-5">SELECT A JOURNEY</h1>
    <?php if (!empty($finalFilteredData)): ?>
        <div class="row">
            <?php foreach($finalFilteredData as $filter): ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div style="position:relative;">
                            <img src="<?php echo htmlspecialchars($filter['packageImageUrl']); ?>" class="card-img-top" alt="The Ghan Adelaide to Darwin">
                            <div style="position:absolute; bottom:0;">
                                <h5 class="text-center text-white p-3 card-title"><?php echo htmlspecialchars($filter['name']); ?></h5>
                            </div>
                        </div>
                        <div class="card-body">
                            <p class="card-text text-start"><?php echo htmlspecialchars($filter['duration']) . " DAYS"; ?> | <?php echo htmlspecialchars($filter['duration'] - 1) . " NIGHTS"; ?></p>
                            <p class="card-text text-start description" data-full-description="<?php echo htmlspecialchars($filter['description']); ?>">
                                <?php
                                    $description = $filter['description'];
                                    $truncatedDescription = (strlen($description) > 540) ? substr($description, 0, 540) . '...' : $description;
                                    echo htmlspecialchars($truncatedDescription);
                                ?>
                            </p>
                        </div>
                        <div style="border-top:2px solid #00000038" class="py-3">
                            <a href="view2.php?packageId=<?php echo htmlspecialchars($filter['packageId']); ?>&Date=<?php echo urlencode($select_date); ?>&select_type=<?php echo urlencode($select_type); ?>" class="btn text-center">View Dates</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <h2 class="text-center">Packages are not available</h2>
    <?php endif; ?>
</div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        $('#travelMonth').datepicker({
            format: "MM yyyy",
            startView: "months",
            minViewMode: "months",
            autoclose: true,
            todayHighlight: true
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
        $('[data-full-description]').each(function () {
            $(this).attr('title', $(this).data('full-description'));
        }).tooltip({
            trigger: 'hover',
            placement: 'top',
            html: true
        });
    });
</script>
<style>
    #nav-menu{
        display:flex; 
        Justify-content:center;
        align-items:center; 
        gap:12rem;
    }
    .card:hover {
        border: 8px solid #004585;
    }
    #searchDiv {
        background-color: #004585;
    }
    .container {
        text-align: center;
    }
    #search-link {
        width: 250px;
        height: 49px;
        text-align: center;
        background-color: #ffe3e38f;
        color: white;
        text-decoration: none;
        border: none;
    }
    .btn-active {
        color: white;
        font-size: larger;
        font-weight: 600;
        background-color: #b85b73;
        border-color: #b85b73;
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
    .description {
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }
    .tooltip-inner {
        max-width: 400px;
        text-align: left;
    }
    .tooltip {
        white-space: pre-line;
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
</body>
</html>
