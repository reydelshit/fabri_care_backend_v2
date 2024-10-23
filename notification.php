<?php


include 'DBconnect.php';
$objDB = new DbConnect();
$conn = $objDB->connect();
date_default_timezone_set('Asia/Manila');
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case "GET":



        $sql = "SELECT * FROM notifications";




        if (isset($sql)) {
            $stmt = $conn->prepare($sql);



            $stmt->execute();
            $notif = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode($notif);
        }


        break;
}
