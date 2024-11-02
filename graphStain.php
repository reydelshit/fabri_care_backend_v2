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
            stains.stain_type AS name,
            COALESCE(COUNT(img.stain), 0) AS value
        FROM 
            (SELECT 'blood' AS stain_type
            UNION ALL SELECT 'Chocolate'
            UNION ALL SELECT 'Coffee'
            UNION ALL SELECT 'Grass'
            UNION ALL SELECT 'Grease'
            UNION ALL SELECT 'Ketchup'
            UNION ALL SELECT 'Marker') AS stains
        LEFT JOIN 
            image img ON stains.stain_type = img.stain 
            AND DATE_FORMAT(img.image_uploadDate, '%M') = :month
        GROUP BY 
            stains.stain_type
        ORDER BY 
            value DESC;;
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
