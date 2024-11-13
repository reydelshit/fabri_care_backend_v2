<?php


include 'DBconnect.php';
$objDB = new DbConnect();
$conn = $objDB->connect();
date_default_timezone_set('Asia/Manila');
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case "GET":
        $sql = "SELECT 
                    LOWER(i.fabric) AS fabric_type, 
                    LOWER(i.stain) AS stain_type, 
                    COUNT(*) AS combination_count,
                    CASE 
                        WHEN LOWER(i.stain) = 'blood' THEN ins.blood_instructions
                        WHEN LOWER(i.stain) = 'coffee' THEN ins.coffee_instructions
                        WHEN LOWER(i.stain) = 'grass' THEN ins.grass_instructions
                        WHEN LOWER(i.stain) = 'grease' THEN ins.grease_instructions
                        WHEN LOWER(i.stain) = 'marker' THEN ins.marker_instructions
                        WHEN LOWER(i.stain) = 'ketchup' THEN ins.ketchup_instructions
                        WHEN LOWER(i.stain) = 'chocolate' THEN ins.chocolate_instructions
                        ELSE ins.washing_instructions
                    END AS specific_instructions
                FROM 
                    image i
                LEFT JOIN 
                    instructions ins ON LOWER(i.fabric) = LOWER(ins.fabric_type)";

        // Initialize an array to store conditional WHERE clauses
        $conditions = [];

        // Add conditions for startDate and endDate if they are set
        if (isset($_GET['startDate']) && !empty($_GET['startDate'])) {
            $conditions[] = "i.image_uploadDate >= :startDate";
        }
        if (isset($_GET['endDate']) && !empty($_GET['endDate'])) {
            $conditions[] = "i.image_uploadDate <= :endDate";
        }

        // Append conditions to the SQL query if any are set
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        // Group by, order, and limit clause
        $sql .= " GROUP BY LOWER(i.fabric), LOWER(i.stain) ORDER BY combination_count DESC LIMIT 3";

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
