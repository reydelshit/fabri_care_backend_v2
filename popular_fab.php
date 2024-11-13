<?php


include 'DBconnect.php';
$objDB = new DbConnect();
$conn = $objDB->connect();
date_default_timezone_set('Asia/Manila');
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case "GET":
        $sql = "SELECT 
                    image.fabric, 
                    image.stain, 
                    COUNT(*) AS combination_count,
                    instructions.washing_instructions
                FROM 
                    image
                LEFT JOIN 
                    instructions ON image.fabric = instructions.fabric_type";

        // Initialize an array to build conditional WHERE clauses
        $conditions = [];

        // Check if startDate and endDate are set and add them to the conditions
        if (isset($_GET['startDate']) && !empty($_GET['startDate'])) {
            $conditions[] = "image.image_uploadDate >= :startDate";
        }
        if (isset($_GET['endDate']) && !empty($_GET['endDate'])) {
            $conditions[] = "image.image_uploadDate <= :endDate";
        }

        // Append conditions to the SQL query if any exist
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        // Group by and order clauses
        $sql .= " GROUP BY image.fabric ORDER BY combination_count DESC";

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
        break;
}
