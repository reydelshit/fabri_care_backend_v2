<?php


include 'DBconnect.php';
$objDB = new DbConnect();
$conn = $objDB->connect();
date_default_timezone_set('Asia/Manila');
$method = $_SERVER['REQUEST_METHOD'];


switch ($method) {
    case "GET":
        // Base SQL query with optional date filtering
        $sql = "SELECT 
                    fabric_types.fabric_type AS name,
                    COALESCE(COUNT(img.fabric), 0) AS value
                FROM 
                    (SELECT DISTINCT fabric_type 
                    FROM instructions 
                    WHERE fabric_type IS NOT NULL) AS fabric_types
                LEFT JOIN 
                    image img ON fabric_types.fabric_type = img.fabric";

        // Initialize an empty conditions array for the WHERE clause
        $conditions = [];

        // Add date conditions if startDate and/or endDate are set
        if (isset($_GET['startDate']) && !empty($_GET['startDate'])) {
            $conditions[] = "img.image_uploadDate >= :startDate";
        }
        if (isset($_GET['endDate']) && !empty($_GET['endDate'])) {
            $conditions[] = "img.image_uploadDate <= :endDate";
        }

        // Append conditions to the SQL query if any exist
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        // Group by and order clause
        $sql .= " GROUP BY fabric_types.fabric_type ORDER BY value DESC";

        try {
            $stmt = $conn->prepare($sql);

            // Bind the date parameters if present
            if (isset($_GET['startDate']) && !empty($_GET['startDate'])) {
                $stmt->bindParam(':startDate', $_GET['startDate']);
            }
            if (isset($_GET['endDate']) && !empty($_GET['endDate'])) {
                $stmt->bindParam(':endDate', $_GET['endDate']);
            }

            $stmt->execute();
            $fabricTypes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode($fabricTypes);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
        break;
}
