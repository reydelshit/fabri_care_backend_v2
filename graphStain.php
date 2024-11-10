<?php


include 'DBconnect.php';
$objDB = new DbConnect();
$conn = $objDB->connect();
date_default_timezone_set('Asia/Manila');
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case "GET":
        $filterByWhat = $_GET['filterByWhat'];
        $filterValue = $_GET['filterValue'];

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
            AND CASE 
                WHEN :filterByWhat = 'Daily' THEN DAYNAME(img.image_uploadDate) = :filterValue
                WHEN :filterByWhat = 'Monthly' THEN DATE_FORMAT(img.image_uploadDate, '%M') = :filterValue
                WHEN :filterByWhat = 'Yearly' THEN YEAR(img.image_uploadDate) = :filterValue
            END
        GROUP BY 
            stains.stain_type
        ORDER BY 
            value DESC";

        if (isset($sql)) {
            try {
                $stmt = $conn->prepare($sql);

                $stmt->bindParam(':filterByWhat', $filterByWhat);
                $stmt->bindParam(':filterValue', $filterValue);

                $stmt->execute();
                $stains = $stmt->fetchAll(PDO::FETCH_ASSOC);

                echo json_encode($stains);
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(['error' => $e->getMessage()]);
            }
        }
        break;
}
