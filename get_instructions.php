<?php

$host = 'mysql-fabricare.alwaysdata.net';
$dbname = 'fabricare_fabri_care';
$username = 'fabricare';
$password = 'fabri123';
$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$fabric_type = $_GET['fabric_type']; // From Android app, e.g., 'Cotton'

// Query to get the washing instructions for Cotton
$sql = "SELECT washing_instructions, blood_instructions, coffee_instructions, grass_instructions, grease_instructions, marker_instructions, ketchup_instructions, chocolate_instructions FROM instructions WHERE fabric_type = '$fabric_type' AND stain_type = 'General'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode($row);
} else {
    echo json_encode(['washing_instructions' => '', 'blood_instructions' => '', 'coffee_instructions' => '', 'grass_instructions' => '', 'grease_instructions' => '', 'marker_instructions' => '', 'ketchup_instructions' => '', 'chocolate_instructions' => '']);
}

$conn->close();
