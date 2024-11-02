<?php


include 'DBconnect.php';
$objDB = new DbConnect();
$conn = $objDB->connect();
date_default_timezone_set('Asia/Manila');
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case "GET":


        $month = $_GET['month'];

        $sql = "SELECT 
            image.*, 
            CONCAT(users.fname, ' ', users.lname) AS full_name
            FROM 
                image
            JOIN 
                users ON image.user_id = users.id
            WHERE 
                DATE_FORMAT(image.image_uploadDate, '%M') = :month;
            ;
            ";



        if (isset($sql)) {
            $stmt = $conn->prepare($sql);

            $stmt->bindParam(':month', $month);


            $stmt->execute();
            $fabric = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode($fabric);
        }


        break;
}
