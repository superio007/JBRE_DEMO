<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous" defer></script>
    <script src="https://kit.fontawesome.com/74e6741759.js" crossorigin="anonymous" defer></script>
</head>
<body>
<?php
session_start();
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


if(isset($_GET['Date'])){
    $Date = $_GET['Date'];
    $select_month_number = date('m', strtotime($Date));
    $select_year = date('Y', strtotime($Date));
    $formatted_date = "$select_year-$select_month_number";
}
// echo $formatted_date;
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

$filteredPackages = [];
if(isset($_GET['packageId'])) {
    $packageId = htmlspecialchars($_GET['packageId']);
    foreach($data['packageResponses'] as $item) {
        if($item['packageId'] == $packageId) {
            $filteredPackages[] = $item;
            break;
        }
    }
}

$departureId = htmlspecialchars($_GET['departureId'] ?? '');
$cabinId = htmlspecialchars($_GET['cabinId'] ?? '');

$filteredDepartures = [];
if (!empty($filteredPackages)) {
    foreach ($filteredPackages as $package) {
        if (isset($package['departures']) && is_array($package['departures'])) {
            foreach ($package['departures'] as $departure) {
                if ($departure['departureId'] == (int)$departureId) {
                    $filteredDepartures[] = $departure;
                }
            }
        }
    }
}

$filteredCabins = [];
if (!empty($filteredDepartures)) {
    foreach ($filteredDepartures as $departure) {
        if (isset($departure['cabins']) && is_array($departure['cabins'])) {
            foreach ($departure['cabins'] as $cabin) {
                if ($cabin['elementId'] == $cabinId) {
                    $filteredCabins[] = $cabin;
                }
            }
        }
    }
}
?>
<div style="background-color:#154782; display:flex; align-items:center; justify-content:space-around;padding:1rem 0;margin-bottom: 6rem;">
        <a href="index.php"><img src="logo.svg" id="logo" style="width:345px" alt="logo" class="offset-1"></a>
        <div id="nav-menu">
        <a href="index.php" style="color:white; font-size:20px;font-weight:bold; text-decoration:none; " target="_blank" rel="noopener noreferrer">Home</a>
        <a href="#" style="color:white; font-size:20px;font-weight:bold; text-decoration:none; " target="_blank" rel="noopener noreferrer">About Us</a>
        <a href="#" style="color:white; font-size:20px;font-weight:bold; text-decoration:none; " target="_blank" rel="noopener noreferrer">Contact Us</a>
        </div>
    </div>
<?php foreach($filteredCabins as $cabin): ?>
    <div class="container" id="Main-div">
        <h1><?php echo htmlspecialchars($cabin['name']); ?></h1>
        <p class="mb-4">How many guests in this cabin?</p>
        <div style="display: flex; justify-content: center; align-items:baseline; gap: 35px;" class="mb-4">
            <input type="hidden" name="occupancy" id="occupancyLimit" value="<?php echo htmlspecialchars($cabin['occupancyId']); ?>">
            <div class="d-grid justify-content-center">
                <div style="display: flex; align-items: center; margin-bottom: 8px; gap: 25px; height: 70px;">
                    <div>
                        <p id="adults-count" style="font-size: 5rem; font-weight: bolder;">1</p>
                    </div>
                    <div>
                        <div>
                            <button data-target="adults-count" style="margin-bottom:7px; border:1px solid #8a8b90; border-radius: 54%; width: 32px;" class="btn btn-circle btn-sm">+</button>
                        </div>
                        <div>
                            <button data-target="adults-count" style="margin-bottom:7px; border:1px solid #8a8b90; border-radius: 54%; width: 32px;" class="btn btn-circle btn-sm">-</button>
                        </div>
                    </div>
                </div>
                <div>
                    <p style="font-size:1em; font-weight: 700; text-align: start;">ADULTS</p>
                </div>
            </div>
            <div>
                <div style="display: flex; align-items: center; margin-bottom: 8px; gap: 25px; height: 70px;">
                    <div>
                        <p id="adults-under-16-count" style="font-size: 5rem; font-weight: bolder;">0</p>
                    </div>
                    <div>
                        <div>
                            <button data-target="adults-under-16-count" style="margin-bottom:7px; border:1px solid #8a8b90; border-radius: 54%; width: 32px;" class="btn btn-circle btn-sm">+</button>
                        </div>
                        <div>
                            <button data-target="adults-under-16-count" style="margin-bottom:7px; border:1px solid #8a8b90; border-radius: 54%; width: 32px;" class="btn btn-circle btn-sm">-</button>
                        </div>
                    </div>
                </div>
                <div>
                    <p style="font-size:1em; font-weight: 700; text-align: start; margin: 0; line-height: 1.8em;">CHILD</p>
                    <p style="font-size:1em; font-weight: 400; text-align: start; margin: 0; line-height: 1.8em;">UNDER 16</p>
                </div>
            </div>
            <div>
                <div style="display: flex; align-items: center; margin-bottom: 8px; gap: 25px; height: 70px;">
                    <div>
                        <p id="adults-under-4-count" style="font-size: 5rem; font-weight: bolder;">0</p>
                    </div>
                    <div>
                        <div>
                            <button data-target="adults-under-4-count" style="margin-bottom:7px; border:1px solid #8a8b90; border-radius: 54%; width: 32px;" class="btn btn-circle btn-sm">+</button>
                        </div>
                        <div>
                            <button data-target="adults-under-4-count" style="margin-bottom:7px; border:1px solid #8a8b90; border-radius: 54%; width: 32px;" class="btn btn-circle btn-sm">-</button>
                        </div>
                    </div>
                </div>
                <div>
                    <p style="font-size:1em; font-weight: 700; text-align: start; line-height: 1.8em; margin: 0;">INFANTS</p>
                    <p style="font-size:1em; font-weight: 400; text-align: start; margin: 0; line-height: 1.8em;">UNDER 4</p>
                </div>
            </div>
        </div>
        <div style="display: flex; gap: 35px;">
            <?php foreach($cabin['priceTypes'] as $priceType): ?>
                <div class="priceTypeDiv" id="priceType_<?php echo htmlspecialchars($priceType['priceTypeId']); ?>" style="position:relative; border: 1px solid #8a8b90; padding:20px 0; width: 150px; display: grid; justify-content: center; align-items: center;">
                    <p class="text-dark" style="margin:0;"><?php echo htmlspecialchars($priceType['priceTypeName']); ?></p>
                    <p class="text-secondary" style="margin:0;"><?php echo htmlspecialchars($priceType['adultPrice']); ?></p>
                    <input type="hidden" name="date" id="date" value="<?php echo $Date; ?>">
                    <input type="hidden" name="packageId" id="packageId" value="<?php echo $packageId; ?>">
                    <input type="hidden" name="priceTypeId" id="priceTypeId" value="<?php echo htmlspecialchars($priceType['priceTypeId']); ?>">
                    <input type="hidden" name="departureId" id="departureId" value="<?php echo htmlspecialchars($departureId); ?>">
                    <input type="hidden" name="elementIdSole" id="elementIdSole" value="<?php echo htmlspecialchars($cabin['elementIdSole']); ?>">
                    <input type="hidden" name="cabinId" id="cabinId" value="<?php echo htmlspecialchars($cabinId); ?>">
                    <?php foreach($filteredDepartures as $departure): ?>
                        <input type="hidden" name="startDate" id="startDate" value="<?php echo htmlspecialchars($departure['startDate']); ?>">
                    <?php endforeach; ?>
                    <input type="hidden" name="occupancy" id="occupancyLimit" value="<?php echo htmlspecialchars($cabin['occupancyId']); ?>">
                    <input type="hidden" name="cabinName" id="cabinName" value="<?php echo htmlspecialchars($cabin['name']); ?>">
                    <input type="hidden" name="Type" id="Type" value="<?php echo $select_type; ?>">
                    <div class="pop-show" style="position: absolute; top: 5px; right: 8px; background-color: black; width: 24px; border-radius: 54%;">
                        <i class="fa-solid fa-question fa-2xs" style="color: #ffffff;"></i>
                        <div class="popup"><?php echo htmlspecialchars($priceType['terms']); ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endforeach; ?>
</body>
</html>
<style>
#nav-menu{
        display:flex; 
        Justify-content:center;
        align-items:center; 
        gap:12rem;
    }
.pop-show {
    position: relative;
    display: inline-block;
}
.info {
    padding: 10px 20px;
    background-color: #007BFF;
    color: white;
    border-radius: 5px;
    cursor: pointer;
}
.popup {
    color: black;
    display: none;
    position: absolute;
    top: 40px;
    left: 0;
    width: 160px;
    padding: 10px;
    background-color: white;
    border: 1px solid #ccc;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    z-index: 10;
    border-radius: 5px;
    font-size: 9px;
}
.pop-show:hover .popup {
    display: block;
}
.selected {
    padding: 10px 0;
    background-color: #b0b0b0;
    color: black;
}
#Main-div{
    display: grid;
    justify-content: center;
    text-align: center;
}
@media screen and (max-width: 769px) {
        #nav-menu {
            display: none !important;
        }
        #logo {
            margin: 0;
        }
        #Main-div{
            display: block;
        }
    }
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // var type = document.getElementById('Type').value;
    var packageId = document.getElementById('packageId').value;
    var dateurl = document.getElementById('date').value;
    var priceTypeDivs = document.querySelectorAll('.priceTypeDiv');
    priceTypeDivs.forEach(function(priceTypeDiv) {
        priceTypeDiv.addEventListener('click', function() {
            priceTypeDivs.forEach(function(div) {
                div.classList.remove('selected');
            });
            this.classList.add('selected');
            var priceTypeId = this.querySelector('input[name="priceTypeId"]').value;
            var cabinName = document.getElementById('cabinName').value;
            var cabinId = document.getElementById('cabinId').value;
            var startDate = document.getElementById('startDate').value;
            var occupancyLimit = parseInt(document.getElementById('occupancyLimit').value);
            var adultsCount = parseInt(document.getElementById('adults-count').textContent);
            var under16Count = parseInt(document.getElementById('adults-under-16-count').textContent);
            var under4Count = parseInt(document.getElementById('adults-under-4-count').textContent);
            var departureId = document.getElementById('departureId').value;
            var elementIdSole = document.getElementById('elementIdSole').value;
            var total = adultsCount + under16Count + under4Count;
            var selectedData = {
                cabinName: cabinName,
                departureId: departureId,
                startDate: startDate,
                priceTypeId: priceTypeId,
                elementIdSole: elementIdSole,
                cabinId: cabinId,
                occupancyLimit: occupancyLimit,
                Adult: adultsCount,
                Child: under16Count,
                Infants: under4Count,
                quantity: total
            };
            var selectedDataArray = JSON.parse(sessionStorage.getItem('selectedDataArray')) || [];
            var existingIndex = selectedDataArray.findIndex(function(item) {
                return item.cabinId === cabinId && item.departureId === departureId;
            });
            if (existingIndex !== -1) {
                selectedDataArray[existingIndex] = selectedData;
            } else {
                selectedDataArray.push(selectedData);
            }
            sessionStorage.setItem('selectedDataArray', JSON.stringify(selectedDataArray));
            console.log("Selected Data Array:", selectedDataArray);
            storeSessionAndRedirect(packageId, dateurl);
        });
    });
});
function storeSessionAndRedirect(packageId, dateurl) {
    var type = document.getElementById('Type').value; // Ensure 'type' is properly defined here
    var url = `view2.php?packageId=${packageId}&Date=${dateurl}&select_type=${type}`;
    var selectedDataArray = sessionStorage.getItem('selectedDataArray');

    if (selectedDataArray) {
        fetch('storeviewsession.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ selectedData: selectedDataArray })
        })
        .then(response => response.text())
        .then(data => {
            console.log(data);
            // Only redirect if the fetch call is successful
            window.location.href = url;
        })
        .catch(error => {
            console.error('Error:', error);
            // You can handle the error here, for example, showing an error message to the user
        });
    } else {
        window.location.href = url;
    }
}

document.querySelectorAll('.btn-circle').forEach(button => {
    button.addEventListener('click', function() {
        const occupancyLimit = parseInt(document.getElementById('occupancyLimit').value);
        const targetId = this.getAttribute('data-target');
        const targetElement = document.getElementById(targetId);
        let currentValue = parseInt(targetElement.textContent);
        const adultsCountElement = document.getElementById('adults-count');
        const under16CountElement = document.getElementById('adults-under-16-count');
        const under4CountElement = document.getElementById('adults-under-4-count');
        const adultsCount = parseInt(adultsCountElement.textContent);
        const under16Count = parseInt(under16CountElement.textContent);
        const under4Count = parseInt(under4CountElement.textContent);
        let totalCountWithoutCurrent = adultsCount + under16Count + under4Count - currentValue;
        if (occupancyLimit === 1) {
            if (targetId === 'adults-count') {
                if (this.textContent === '+' && currentValue < occupancyLimit) {
                    currentValue++;
                } else if (this.textContent === '-' && currentValue > 0) {
                    currentValue--;
                }
            } else {
                return;
            }
        } else {
            if (this.textContent === '+') {
                if (totalCountWithoutCurrent < occupancyLimit) {
                    if (targetId === 'adults-count' && currentValue < 3) {
                        currentValue++;
                    } else if (targetId === 'adults-under-16-count' && under16Count < adultsCount) {
                        currentValue++;
                    } else if (targetId === 'adults-under-4-count' && under4Count < adultsCount) {
                        currentValue++;
                    }
                }
            } else if (this.textContent === '-') {
                if (currentValue > 0) {
                    if (targetId === 'adults-count' && currentValue === 1 && (under16Count > 0 || under4Count > 0)) {
                    } else {
                        currentValue--;
                    }
                }
            }
        }
        targetElement.textContent = currentValue;
        const newAdultsCount = parseInt(adultsCountElement.textContent);
        const newUnder16Count = parseInt(under16CountElement.textContent);
        const newUnder4Count = parseInt(under4CountElement.textContent);
        if (newUnder16Count > newAdultsCount) {
            under16CountElement.textContent = newAdultsCount;
        }
        if (newUnder4Count > newAdultsCount) {
            under4CountElement.textContent = newAdultsCount;
        }
        const totalGuests = newAdultsCount + newUnder16Count + newUnder4Count;
        if (totalGuests > occupancyLimit) {
            const diff = totalGuests - occupancyLimit;
            let adjustUnder16 = Math.min(diff, newUnder16Count);
            let adjustUnder4 = Math.min(diff - adjustUnder16, newUnder4Count);
            under16CountElement.textContent = newUnder16Count - adjustUnder16;
            under4CountElement.textContent = newUnder4Count - adjustUnder4;
        }
    });
});
</script>