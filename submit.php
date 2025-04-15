<?php
header('Content-Type: text/plain');

// getting the values from the form
$firstname = isset($_POST['firstname']) ? $_POST['firstname'] : '';
$lastname = isset($_POST['lastname']) ? $_POST['lastname'] : '';
$region = isset($_POST['region']) ? $_POST['region'] : '';
$province = isset($_POST['province']) ? $_POST['province'] : '';
$city = isset($_POST['city']) ? $_POST['city'] : '';
$barangay = isset($_POST['barangay']) ? $_POST['barangay'] : '';

// if some fields are empty, it will display a message
if (empty($firstname) || empty($lastname) || empty($region) || empty($province) || empty($city) || empty($barangay)) {
    echo "Please complete all fields.";
    exit; 
}

// the message that will be swapped
echo "Hello $firstname $lastname, you live at $barangay, $city, $province, $region, Philippines.";
