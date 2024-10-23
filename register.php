<?php
$host = 'mysql-fabricare.alwaysdata.net';
$dbname = 'fabricare_fabri_care';
$username = 'fabricare';
$password = 'fabri123';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $fname = $_POST['firstname']; // Added
    $lname = $_POST['lastname'];  // Added
    $created_at = date('Y-m-d');

    // Updated SQL query to include fname and lname
    $sql = "INSERT INTO users (username, email, password, fname, lname, created_at) VALUES ('$username', '$email', '$password', '$fname', '$lname', '$created_at')";

    if ($conn->query($sql) === TRUE) {
        echo "Registration successful";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
$conn->close();
