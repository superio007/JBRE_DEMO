<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Other Traveller's Details Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>
<body>
    <?php
    if(isset($_GET['packageId'])) {
        $packageId= $_GET['packageId'];
    }
    if(isset($_GET['cabinId'])) {
        $cabinId = $_GET['cabinId'];
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
    ?>
    <div style="background-color:#154782; display:flex; align-items:center; justify-content:space-around;padding:1rem 0">
        <a href="index.php"><img src="logo.svg" id="logo" style="width:345px" alt="logo" class="offset-1"></a>
        <div id="nav-menu" style="display:flex; Justify-content:center; align-items:center; gap:12rem;">
        <a href="index.php" style="color:white; font-size:20px;font-weight:bold; text-decoration:none; " target="_blank" rel="noopener noreferrer">Home</a>
        <a href="#" style="color:white; font-size:20px;font-weight:bold; text-decoration:none; " target="_blank" rel="noopener noreferrer">About Us</a>
        <a href="#" style="color:white; font-size:20px;font-weight:bold; text-decoration:none; " target="_blank" rel="noopener noreferrer">Contact Us</a>
        </div>
    </div>
    <form method="post" id="dataForm">
        <div class="container">
            <h1 class="text-center my-3" style="color:#154782">Other Traveller's Details Form</h1>
            <div class="form-group">
                <label class="mb-3" for="options">Select title:</label>
                <select name="title" id="options" class="form-control">
                    <option value="" selected disabled hidden>Select Title:</option>
                    <option value="Mr">Mr</option>
                    <option value="Dr">Dr</option>
                    <option value="Father">Father</option>
                    <option value="Hon">Hon</option>
                    <option value="Lady">Lady</option>
                    <option value="Miss">Miss</option>
                    <option value="Mrs">Mrs</option>
                    <option value="Ms">Ms</option>
                    <option value="Mstr">Mstr</option>
                    <option value="Prof">Prof</option>
                    <option value="Rev">Rev</option>
                    <option value="Sir">Sir</option>
                    <option value="Sister">Sister</option>
                </select>
            </div>
            <div class="form-group">
                <label class="mb-3" for="fname">Enter First Name:</label>
                <input class="mb-3 form-control" type="text" id="fname" name="fname" placeholder="Enter First name">
                <input type="text" id="packageId" name="packageId" value="<?php echo $packageId; ?>" hidden>
                <input type="text" id="cabinId" name="cabinId" value="<?php echo $cabinId; ?>" hidden>
                <input type="text" id="select_type" hidden value="<?php echo $select_type;?>">
                <input type="text" id="Date" hidden value="<?php echo $Date;?>">
            </div>
            <div class="form-group">
                <label class="mb-3" for="lname">Enter Last Name:</label>
                <input class="mb-3 form-control" type="text" id="lname" name="lname" placeholder="Enter Last name">
            </div>
            <div class="form-group">
                <label class="mb-3" for="address1">Address Line:</label>
                <input class="mb-3 form-control" type="text" id="address1" name="address1" placeholder="Enter Address">
            </div>
            <div class="form-group">
                <label class="mb-3" for="address2">Additional Address Line:</label>
                <input class="mb-3 form-control" type="text" id="address2" name="address2" placeholder="Enter Address">
            </div>
            <div class="form-group">
                <label class="mb-3" for="postno">Enter Post code:</label>
                <input class="mb-3 form-control" type="text" id="postno" name="postno" placeholder="Enter Post code">
            </div>
            <div class="form-group">
                <label class="mb-3" for="city">Enter City:</label>
                <input class="mb-3 form-control" type="text" id="city" name="city" placeholder="Enter City">
            </div>
            <div class="form-group">
                <label class="mb-3" for="state">Enter State:</label>
                <input class="mb-3 form-control" type="text" id="state" name="state" placeholder="Enter State">
            </div>
            <div class="form-group">
                <label class="mb-3" for="country">Enter Country:</label>
                <input class="mb-3 form-control" type="text" id="country" name="country" placeholder="Enter Country">
            </div>
            <div class="form-group">
                <label class="mb-3" for="phone">Enter Phone no:</label>
                <input class="mb-3 form-control" type="text" id="phone" name="phone" placeholder="Enter Phone no">
            </div>
            <div class="form-group">
                <label class="mb-3" for="email">Enter Email:</label>
                <input class="mb-3 form-control" type="email" id="email" name="email" placeholder="Enter Email">
            </div>
            <div class="form-group mb-3">
                <input type="checkbox" class="form-check-input" id="veg" name="veg">
                <label class="form-check-label" for="veg">Vegetarian</label>
                <input type="checkbox" class="form-check-input" id="lactose" name="lactose">
                <label class="form-check-label" for="lactose">Lactose Free</label>
                <input type="checkbox" class="form-check-input" id="vegan" name="vegan">
                <label class="form-check-label" for="vegan">Vegan</label>
                <input type="checkbox" class="form-check-input" id="gluten" name="gluten">
                <label class="form-check-label" for="gluten">Gluten Free</label>
            </div>
            <div class="form-group">
                <label class="mb-3" for="infants_details">Enter Infants Details:</label>
                <textarea class="mb-3 form-control" name="infants_details" id="infants_details"></textarea>
            </div>
            <div class="form-group">
                <label class="mb-3" for="other_request">Enter Other Request:</label>
                <textarea class="mb-3 form-control" name="other_request" id="other_request"></textarea>
            </div>
            <button type="submit" id="submitBtn" style="color:#48C7F4; background-color:#004585;" class="btn btn-primary">Submit</button>
        </div>
    </form>

    <script>
        document.getElementById('submitBtn').addEventListener('click', function(event) {
            event.preventDefault();
            var title = document.getElementById('options').value;
            var fname = document.getElementById('fname').value;
            var lname = document.getElementById('lname').value;
            var address1 = document.getElementById('address1').value;
            var address2 = document.getElementById('address2').value;
            var postno = document.getElementById('postno').value;
            var city = document.getElementById('city').value;
            var state = document.getElementById('state').value;
            var country = document.getElementById('country').value;
            var phone = document.getElementById('phone').value;
            var email = document.getElementById('email').value;
            var veg = document.getElementById('veg').checked;
            var lactose = document.getElementById('lactose').checked;
            var vegan = document.getElementById('vegan').checked;
            var gluten = document.getElementById('gluten').checked;
            var infants_details = document.getElementById('infants_details').value;
            var other_request = document.getElementById('other_request').value;
            var cabinId = document.getElementById('cabinId').value;
            var packageId = document.getElementById('packageId').value;
            var newEntry = {
                cabinId: parseInt(cabinId),
                title: title,
                fname: fname,
                lname: lname,
                address1: address1,
                address2: address2,
                postno: postno,
                city: city,
                state: state,
                country: country,
                phone: phone,
                email: email,
                veg: veg,
                lactose: lactose,
                vegan: vegan,
                gluten: gluten,
                infants_details: infants_details,
                other_request: other_request
            };
            var existingEntries = JSON.parse(sessionStorage.getItem('usersData')) || [];
            existingEntries.push(newEntry);
            sessionStorage.setItem('usersData', JSON.stringify(existingEntries));
            console.log('Form data saved to session storage:', existingEntries);
            storeSessionAndRedirect(packageId, cabinId);
        });
        function storeSessionAndRedirect(packageId, cabinId) {
            var select_type = document.getElementById('select_type').value;
            var Dateurl = document.getElementById('Date').value;
            var url = `selectGuest.php?packageId=${packageId}&cabinId=${cabinId}&select_type=${select_type}&Date=${Dateurl}`;
            var usersData = sessionStorage.getItem('usersData');
            if (usersData) {
                fetch('storeusersformsession.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ usersData: usersData })
                })
                .then(response => response.text())
                .then(data => {
                    console.log(data);
                    window.location.href = url;
                })
                .catch(error => console.error('Error:', error));
            } else {
                window.location.href = url;
            }
        }
    </script>
</body>
</html>
<style>
    @media screen and (min-width: 769px){
            #nav-menu{
                display:none;
            }
            #logo{
                margin:0;
            }
        }
</style>