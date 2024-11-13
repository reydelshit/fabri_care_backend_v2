<?php


include 'DBconnect.php';
$objDB = new DbConnect();
$conn = $objDB->connect();
date_default_timezone_set('Asia/Manila');
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case "GET":
        $sql = "SELECT 
        i.image_id,
        CONCAT(u.fname, ' ', u.lname) AS fullname,
        i.fabric,
        i.stain,
        i.image_uploadDate
    FROM
        image i
    JOIN
        users u ON i.user_id = u.id";

        // Initialize an empty conditions array
        $conditions = [];

        if (isset($_GET['startDate']) && !empty($_GET['startDate'])) {
            $conditions[] = "i.image_uploadDate >= :startDate";
        }
        if (isset($_GET['endDate']) && !empty($_GET['endDate'])) {
            $conditions[] = "i.image_uploadDate <= :endDate";
        }

        // Add WHERE clause if conditions are set
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        try {
            $stmt = $conn->prepare($sql);

            // Bind parameters only if they are set
            if (isset($_GET['startDate']) && !empty($_GET['startDate'])) {
                $stmt->bindParam(':startDate', $_GET['startDate']);
            }
            if (isset($_GET['endDate']) && !empty($_GET['endDate'])) {
                $stmt->bindParam(':endDate', $_GET['endDate']);
            }

            $stmt->execute();
            $history = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode($history);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
}
