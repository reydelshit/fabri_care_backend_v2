<?php

$host = 'mysql-fabricare.alwaysdata.net';
$dbname = 'fabricare_fabri_care';
$username = 'fabricare';
$password = 'fabri123';

$conn = new mysqli($host, $username, $password, $dbname);

$fabric_type = $_GET['fabric_type']; // Received from Android app

if ($fabric_type === "all") {
    // Query to get unique fabric types
    $query = "SELECT DISTINCT fabric_type FROM instructions";
    $result = $conn->query($query);

    $fabrics = array();
    while ($row = $result->fetch_assoc()) {
        $fabrics[] = $row['fabric_type'];
    }

    echo json_encode($fabrics);
} else {
    // Query to get washing and stain instructions for a specific fabric type
    $sql = "SELECT washing_instructions, blood_instructions, coffee_instructions, grass_instructions, grease_instructions, marker_instructions, ketchup_instructions, chocolate_instructions 
            FROM instructions 
            WHERE fabric_type = '$fabric_type' AND stain_type = 'General'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode($row);
    } else {
        // Return empty instructions if no match is found
        echo json_encode([
            'washing_instructions' => '',
            'blood_instructions' => '',
            'coffee_instructions' => '',
            'grass_instructions' => '',
            'grease_instructions' => '',
            'marker_instructions' => '',
            'ketchup_instructions' => '',
            'chocolate_instructions' => ''
        ]);
    }
}

$conn->close();
