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
             users u ON i.user_id = u.id

             ";

        if (isset($sql)) {
            try {
                $stmt = $conn->prepare($sql);


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
