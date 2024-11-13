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
            fabric_types.fabric_type AS name,
            COALESCE(COUNT(img.fabric), 0) AS value
        FROM 
            (SELECT DISTINCT fabric_type 
             FROM instructions 
             WHERE fabric_type IS NOT NULL) AS fabric_types
        LEFT JOIN 
            image img ON fabric_types.fabric_type = img.fabric 
            AND CASE 
                WHEN :filterByWhat = 'Daily' THEN DAYNAME(img.image_uploadDate) = :filterValue
                WHEN :filterByWhat = 'Monthly' THEN DATE_FORMAT(img.image_uploadDate, '%M') = :filterValue
                WHEN :filterByWhat = 'Yearly' THEN YEAR(img.image_uploadDate) = :filterValue
            END
        GROUP BY 
            fabric_types.fabric_type
        ORDER BY 
            value DESC";

        if (isset($sql)) {
            try {
                $stmt = $conn->prepare($sql);

                $stmt->bindParam(':filterByWhat', $filterByWhat);
                $stmt->bindParam(':filterValue', $filterValue);

                $stmt->execute();
                $fabrics = $stmt->fetchAll(PDO::FETCH_ASSOC);

                echo json_encode($fabrics);
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(['error' => $e->getMessage()]);
            }
        }
        break;
}
