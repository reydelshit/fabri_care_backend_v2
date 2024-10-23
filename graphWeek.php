<?php


include 'DBconnect.php';
$objDB = new DbConnect();
$conn = $objDB->connect();
date_default_timezone_set('Asia/Manila');
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case "GET":


        if (!isset($_GET['user_Id'])) {
            $sql = "SELECT 
                    days.day_name AS name,
                    COALESCE(COUNT(image.image_id), 0) AS total
                FROM
                    (SELECT 'Monday' AS day_name
                    UNION ALL SELECT 'Tuesday'
                    UNION ALL SELECT 'Wednesday'
                    UNION ALL SELECT 'Thursday'
                    UNION ALL SELECT 'Friday'
                    UNION ALL SELECT 'Saturday'
                    UNION ALL SELECT 'Sunday') AS days
                LEFT JOIN image 
                    ON days.day_name = DAYNAME(image.image_uploadDate)
                GROUP BY 
                    days.day_name
                ORDER BY 
                    FIELD(days.day_name, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');";
        }



        if (isset($sql)) {
            $stmt = $conn->prepare($sql);



            $stmt->execute();
            $student = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode($student);
        }


        break;
}
