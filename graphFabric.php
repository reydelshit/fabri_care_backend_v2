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
            ins.fabric_type AS name,
            COALESCE(COUNT(img.fabric), 0) AS value
        FROM 
            instructions ins
        LEFT JOIN 
            image img ON ins.fabric_type = img.fabric AND DATE_FORMAT(img.image_uploadDate, '%M') = :month
            GROUP BY 
                ins.fabric_type
            ORDER BY 
                value DESC;
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
