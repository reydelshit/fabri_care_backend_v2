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
            i.image_id,
            CONCAT(u.fname, ' ', u.lname) AS fullname,
            i.fabric,
            i.stain,
            i.image_uploadDate
        FROM 
            image i
        JOIN 
            users u ON i.user_id = u.id
        WHERE 
            CASE 
                WHEN :filterByWhat = 'Daily' THEN DAYNAME(i.image_uploadDate) = :filterValue
                WHEN :filterByWhat = 'Monthly' THEN DATE_FORMAT(i.image_uploadDate, '%M') = :filterValue
                WHEN :filterByWhat = 'Yearly' THEN YEAR(i.image_uploadDate) = :filterValue
            END
        ORDER BY 
            i.image_uploadDate DESC";

        if (isset($sql)) {
            try {
                $stmt = $conn->prepare($sql);

                $stmt->bindParam(':filterByWhat', $filterByWhat);
                $stmt->bindParam(':filterValue', $filterValue);

                $stmt->execute();
                $history = $stmt->fetchAll(PDO::FETCH_ASSOC);

                echo json_encode($history);
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(['error' => $e->getMessage()]);
            }
        }
        break;
}
