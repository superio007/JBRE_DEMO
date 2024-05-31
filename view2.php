<?php
session_start();
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View - Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous" defer></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" defer></script>
    <script src="https://kit.fontawesome.com/74e6741759.js" crossorigin="anonymous" defer></script>
    <style>
        #nav-menu{
            display:flex; 
            justify-content:center;
            align-items:center; 
            gap:12rem;
        }
        #desk .col-1{
            text-align: center;
            font-size: small;
            flex: 0 0 auto;
            width: 13.333333%;
        }
        .row{
            padding-left: 0;
        }
        .right-cabin{
            margin-right:0;
        }
        #row {
            padding: 10px 0;
            border-top: 2px solid #00000012;
        }
        #row-departures {
            padding: 5px 0;
            border-top: 2px solid #00000012;
        }
        .selected {
            padding: 10px 0;
            background-color: #b0b0b0;
            color: black;
        }
        body {
            background-color: #f0f0f0;
        }
        .date {
            font-size: 18px;
            font-weight: bold;
        }
        .month {
            font-size: 12px;
            text-transform: uppercase;
            line-height: 1;
        }
        .time {
            font-size: 18px;
        }
        .region {
            font-size: 16px;
            text-transform: uppercase;
        }
        .arrow-icon {
            font-size: 24px;
        }
        .sold-out {
            font-size: 14px;
            text-transform: uppercase;
            text-align: center;
        }
        .container{
            width: 90%;
        }
        .price {
            font-size: 16px;
            text-align: center;
        }
        .col{
            font-size: 7px;
            text-align: center;
        }
        @media screen and (max-width: 769px) {
            #nav-menu {
                display: none !important;
            }
            #logo {
                margin: 0;
            }
            .sold-out {
                font-size: 10px;
                text-transform: uppercase;
                text-align: center;
            }
            .price {
                font-size: 10px;
                text-align: center;
            }
        }
    </style>
</head>
<body>
<?php
$sessionData = $_SESSION['selectedDataArray'] ?? null;
if(isset($_GET['Date'])){
    $Date = $_GET['Date'];
    $select_month_number = date('m', strtotime($Date));
    $select_year = date('Y', strtotime($Date));
    $formatted_date = "$select_year-$select_month_number";
}

function getToken(){
    $url = "https://jb-auth-uat.azurewebsites.net/Client";
    $postData = [
        "claims" => ["userName" => "stuart@geelongtravel.com.au"],
        "clientId" => "515909a2-c9a6-46ee-a923-6cb1170e3571",
        "secret" => "oF7QWRUYYUmISudsRgixrg=="
    ];
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => ["Content-Type: application/json"],
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($postData)
    ]);
    $response = curl_exec($ch);
    if ($response === false) {
        die("Error: Curl request failed: " . curl_error($ch));
    }
    curl_close($ch);
    return json_decode($response, true)['accessToken'];
}
if(isset($_GET['select_type'])){
    $select_type = $_GET['select_type'];
}
if(isset($_COOKIE['token'])){
    $token = $_COOKIE['token'];
}else{
    $token = getToken();
}
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

$packageId = $_GET['packageId'] ?? null;
$filteredPackage = null;
foreach ($data['packageResponses'] as $item) {
    if ($item['packageId'] == $packageId) {
        $filteredPackage = $item;
        break;
    }
}

$dates = array_column($filteredPackage['departures'], 'departureId');
?>
<div style="background-color:#154782; display:flex; align-items:center; justify-content:space-around;padding:1rem 0">
    <a href="index.php"><img src="logo.svg" id="logo" style="width:345px" alt="logo" class="offset-1"></a>
    <div id="nav-menu">
        <a href="index.php" style="color:white; font-size:20px;font-weight:bold; text-decoration:none;" target="_blank" rel="noopener noreferrer">Home</a>
        <a href="#" style="color:white; font-size:20px;font-weight:bold; text-decoration:none;" target="_blank" rel="noopener noreferrer">About Us</a>
        <a href="#" style="color:white; font-size:20px;font-weight:bold; text-decoration:none;" target="_blank" rel="noopener noreferrer">Contact Us</a>
    </div>
</div>
<h1 class="text-center mt-5 my-3" style="color:#004585;">JOURNEY</h1>
<p class="text-center mb-5"><?php echo htmlspecialchars($filteredPackage['name']); ?></p>

<div class="d-none d-md-block" id="desktop">
    <?php if ($sessionData): ?>
        <div id="session_window" style="background-color:#004585; margin:25px 0;">
            <div class="row offset-1 g-3">
                <?php foreach ($sessionData as $sess): ?>
                    <div style="display: flex;gap: 10px;" class="col-2 text-light">
                        <div>
                            <p style="margin:0;"><?php echo htmlspecialchars($sess['quantity']) . ' adults'; ?></p>
                            <p><b><?php echo htmlspecialchars($sess['cabinName']); ?></b></p>
                        </div>
                        <div>
                            <button class=" btn remove-session" value="<?php echo htmlspecialchars($sess['cabinId']); ?>" class="btn" type="submit"><i class="fa-solid fa-x fa-lg" style="color: #ffffff;"></i></button>
                        </div>
                    </div>
                <?php endforeach; ?>
                <div style="margin-right:50px;" class="col-auto ms-auto">
                    <a style="border:1px solid white;color:white;" class="btn" href="leadform.php?packageId=<?php echo htmlspecialchars($packageId); ?>&cabinId=<?php echo htmlspecialchars($sess['cabinId']); ?>&select_type=<?php echo $select_type;?>&Date=<?php echo $Date; ?>">Continue</a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<div class="d-md-none">
    <?php if ($sessionData): ?>
        <div id="session_window" style="background-color:#004585; margin:25px 0;">
            <div class="row offset-1 g-3">
                <?php foreach ($sessionData as $sess): ?>
                    <div style="display: flex;gap: 10px;" class="col-2 text-light">
                        <div style="display: flex; gap:12px;align-items:center;">
                            <p style="margin:0;font-size:xx-small;"><?php echo htmlspecialchars($sess['quantity']) . ' adults'; ?></p>
                            <p style="font-size:10px"><b><?php echo htmlspecialchars($sess['cabinName']); ?></b></p>
                        </div>
                        <div>
                            <button class="btn remove-session" value="<?php echo htmlspecialchars($sess['cabinId']); ?>" type="submit"><i class="fa-solid fa-x fa-lg" style="color: #ffffff;"></i></button>
                        </div>
                    </div>
                <?php endforeach; ?>
                <div style="margin-right:50px;" class="col-auto ms-auto">
                    <a style="border:1px solid white;color:white;" class="btn" href="leadform.php?packageId=<?php echo htmlspecialchars($packageId); ?>&cabinId=<?php echo htmlspecialchars($sess['cabinId']); ?>&select_type=<?php echo $select_type;?>&Date=<?php echo $Date; ?>">Continue</a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<div class="d-md-none container">
    <div class="row cabinName text-center mb-4">
        <?php
        $maxCabinCount = 0;
        $maxCabinDeparture = null;

        foreach ($filteredPackage['departures'] as $departure) {
            $cabinNames = array_map(function($cabin) {
                return $cabin['name'];
            }, $departure['cabins']);
            
            $uniqueCabinNames = array_unique($cabinNames);
            $cabinCount = count($uniqueCabinNames);
            
            if ($cabinCount > $maxCabinCount) {
                $maxCabinCount = $cabinCount;
                $maxCabinDeparture = $departure;
            }
        }

        if ($maxCabinDeparture) {
            foreach ($maxCabinDeparture['cabins'] as $cabin) {
                echo '<p class="col">' . htmlspecialchars($cabin['name']) . '</p>';
            }
        }
        ?>
    </div>
</div>

<div id="desk">
    <div class="d-none d-md-block row offset-6">
        <div class="row cabinName">
            <?php
            // Initialize variables to track the departure with the most unique cabin types
            $maxCabinCount = 0;
            $maxCabinDeparture = null;

            // Iterate through each departure to find the one with the most unique cabin types
            foreach ($filteredPackage['departures'] as $departure) {
                $cabinNames = array_map(function($cabin) {
                    return $cabin['name'];
                }, $departure['cabins']);
                
                $uniqueCabinNames = array_unique($cabinNames);
                $cabinCount = count($uniqueCabinNames);
                
                if ($cabinCount > $maxCabinCount) {
                    $maxCabinCount = $cabinCount;
                    $maxCabinDeparture = $departure;
                }
            }

            // Display the cabin names of the departure with the most unique cabin types
            if ($maxCabinDeparture) {
                foreach ($maxCabinDeparture['cabins'] as $cabin) {
                    echo '<p class="col-1">' . htmlspecialchars($cabin['name']) . '</p>';
                }
            }
            ?>
        </div>
    </div>
</div>

<?php foreach ($filteredPackage['departures'] as $departure): ?>
    <div class="container py-2" id="row-departures">
        <div class="row color-departure align-items-center">
            <div class="col-md-6 col-sm-12">
                <div id="departures_<?php echo htmlspecialchars($departure['departureId']); ?>" class="row mb-3" data-departure-id="<?php echo htmlspecialchars($departure['departureId']); ?>">
                    <div class="col-2">
                        <div class="date"><?php echo htmlspecialchars(substr($departure['startDate'], 8, 2)); ?></div>
                        <div class="month"><?php echo htmlspecialchars(explode(' ', $Date)[0]); ?></div>
                    </div>
                    <div class="col-3">
                        <div class="time"><?php echo htmlspecialchars(substr($departure['startDate'], 11, 5)); ?></div>
                        <div class="region"><?php echo htmlspecialchars($departure['startRegion']); ?></div>
                    </div>
                    <div class="col-1 d-flex justify-content-center align-items-center">
                        <i class="bi bi-chevron-compact-right arrow-icon"></i>
                    </div>
                    <div class="col-2">
                        <div class="date"><?php echo htmlspecialchars(substr($departure['endDate'], 8, 2)); ?></div>
                        <div class="month"><?php echo htmlspecialchars(explode(' ', $Date)[0]); ?></div>
                    </div>
                    <div class="col-4">
                        <div class="time"><?php echo htmlspecialchars(substr($departure['endDate'], 11, 5)); ?></div>
                        <div class="region"><?php echo htmlspecialchars($departure['endRegion']); ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-sm-12">
                <div class="row right-cabin">
                    <?php
                    if ($maxCabinDeparture && isset($maxCabinDeparture['cabins'])) {
                        foreach($maxCabinDeparture['cabins'] as $maxCabin) {
                            $cabinDisplayed = false;
                            foreach ($departure['cabins'] as $cabin) {
                                if ($maxCabin['name'] == $cabin['name']) {
                                    echo '<div class="col d-flex justify-content-center align-items-center">';
                                    if ($cabin['roomsLeft'] == 0) {
                                        echo '<p class="sold-out">Sold Out</p>';
                                    } else {
                                        echo '<p class="price">' . htmlspecialchars($cabin['startingPrice']) . '</p>';
                                    }
                                    echo '</div>';
                                    $cabinDisplayed = true;
                                    break;
                                }
                            }
                            if (!$cabinDisplayed) {
                                echo '<div class="col d-flex justify-content-center align-items-center"><p class="price">-</p></div>';
                            }
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
        <?php 
        $anyCabinAvailable = false;
        foreach ($departure['cabins'] as $departCabin) {
            if ($departCabin['roomsLeft'] != 0) {
                $anyCabinAvailable = true;
                break;
            }
        }
        if ($anyCabinAvailable): ?>
        <div id="cabins_<?php echo htmlspecialchars($departure['departureId']); ?>" class="mb-2 cabin_dive" style="display: none;">
            <div class="container">
                <h2 class="my-5 text-center">SELECT A CABIN</h2>
                <div id="cabindiv" class="row justify-content-center">
                    <?php $counter = 0; ?>
                    <?php foreach ($departure['cabins'] as $departCabin): ?>
                        <?php if ($departCabin['roomsLeft'] != 0): ?>
                            <div class="col-md-4 mb-4">
                                <div class="card" data-href="book2.php?packageId=<?php echo htmlspecialchars($packageId); ?>&departureId=<?php echo htmlspecialchars($departure['departureId']); ?>&cabinId=<?php echo htmlspecialchars($departCabin['elementId']); ?>&Date=<?php echo $Date; ?>&select_type=<?php echo $select_type; ?>">
                                    <div style="position:relative;">
                                        <img src="https://dev.yourbestwayhome.com.au/afltravel/wp-content/uploads/2024/05/gold-premium-twin.jpg" class="card-img-top" alt="<?php echo htmlspecialchars($departCabin['name']); ?>">
                                        <div style="position:absolute; bottom:0; width:100%; background:rgba(0,0,0,0.5);">
                                            <h5 class="text-center text-white p-3 card-title"><?php echo htmlspecialchars($departCabin['name']); ?></h5>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <p class="card-text text-start"><?php echo htmlspecialchars($departCabin['roomsLeft']) . " CABINS LEFT"; ?></p>
                                        <p>PAX: <?php echo htmlspecialchars($departCabin['occupancyId']); ?></p>
                                        <p class="card-text text-start"><?php echo htmlspecialchars($departCabin['serviceType']); ?></p>
                                    </div>
                                </div>
                            </div>
                            <?php $counter++; ?>
                            <?php if ($counter % 3 == 0): ?>
                                </div>
                                <div class="row justify-content-center">
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php else: ?>
            <div id="cabins_<?php echo htmlspecialchars($departure['departureId']); ?>" class="mb-2 cabin_dive" style="display: none;">
                <div class="text-center" style="margin-top:3rem;">
                    <h3>Cabins are not available</h3>
                    <p>Please select a different departure</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
<?php endforeach; ?>

<script>
    let lastSelectedDepartureId = null;
    let sessionData = <?php echo json_encode($sessionData); ?>;

    function handleDepartureClick(departureId) {
        const currentlyVisible = $('.cabin_dive:visible');
        const targetDiv = $('#cabins_' + departureId);

        if (currentlyVisible.length && currentlyVisible.attr('id') === targetDiv.attr('id')) {
            targetDiv.hide();
            lastSelectedDepartureId = null;
        } else {
            if (sessionData && Object.keys(sessionData).length > 0) {
                if (confirm('Changing departures will clear the selected cabins. Do you want to continue?')) {
                    // Remove all sessions
                    $('.remove-session').each(function() {
                        const cabinId = $(this).val();
                        removeSession(cabinId, $(this).closest('.col-2'));
                    });
                    currentlyVisible.hide();
                    targetDiv.show();
                    lastSelectedDepartureId = departureId;
                } else {
                    return;
                }
            } else {
                currentlyVisible.hide();
                targetDiv.show();
                lastSelectedDepartureId = departureId;
            }
        }
    }

    function removeSession(cabinId, sessionElement) {
        $.ajax({
            url: 'remove_session.php',
            type: 'POST',
            data: { cabinId: cabinId },
            success: function(response) {
                const result = JSON.parse(response);
                if (result.status === 'success') {
                    sessionElement.remove();
                    sessionData = sessionData.filter(sess => sess.cabinId !== cabinId); // Update sessionData

                    if (sessionData.length === 0) {
                        $('#session_window').hide();
                    }

                    // Check if there are any sessions left for the departure
                    const departureId = sessionElement.closest('[data-departure-id]').data('departure-id');
                    const remainingSessionsForDeparture = sessionData.filter(sess => sess.departureId === departureId);

                    if (remainingSessionsForDeparture.length === 0) {
                        $('#departures_' + departureId).closest('.color-departure').removeClass('selected');
                    }
                } else {
                    alert('Failed to remove session: ' + result.message);
                }
            },
            error: function() {
                alert('An error occurred while trying to remove the session.');
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Apply the selected class based on session data
        if (sessionData && sessionData.length > 0) {
            sessionData.forEach(function(sess) {
                const departureId = sess.departureId;
                $('#departures_' + departureId).closest('.color-departure').addClass('selected');
                $('#cabins_' + departureId).show();
                lastSelectedDepartureId = departureId;
            });
        }

        $('[data-departure-id]').click(function() {
            var departureId = $(this).data('departure-id');
            
            // Remove .selected class from the previously selected row
            if (lastSelectedDepartureId !== null && lastSelectedDepartureId !== departureId) {
                $('#departures_' + lastSelectedDepartureId).closest('.color-departure').removeClass('selected');
            }

            // Add .selected class to the clicked row
            $('#departures_' + departureId).closest('.color-departure').toggleClass('selected');

            handleDepartureClick(departureId);
        });

        $(document).on('click', '.card', function() {
            const href = $(this).data('href');
            window.location.href = href;
        });

        $(document).on('click', '.remove-session', function() {
            const cabinId = $(this).val();
            const sessionElement = $(this).closest('.col-2');
            removeSession(cabinId, sessionElement);
        });

        $(document).ready(function() {
            if ($('.remove-session').length === 0) {
                $('#session_window').hide();
            }
        });
    });
</script>
</body>
</html>