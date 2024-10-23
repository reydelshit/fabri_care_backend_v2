<?php


include 'DBconnect.php';
$objDB = new DbConnect();
$conn = $objDB->connect();
date_default_timezone_set('Asia/Manila');
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case "GET":

        $sql = "SELECT 
                CONCAT(users.fname, ' ', users.lname) AS fullname,
                (SELECT COUNT(*) 
                FROM image 
                WHERE image.user_id = users.id) AS uploaded_image,
                (SELECT DAYNAME(image_uploadDate) 
                FROM image 
                WHERE image.user_id = users.id 
                GROUP BY DAYNAME(image_uploadDate) 
                ORDER BY COUNT(*) DESC 
                LIMIT 1) AS day_most_used
            FROM 
                users
            GROUP BY 
                fullname;";

        if (isset($sql)) {
            $stmt = $conn->prepare($sql);



            $stmt->execute();
            $student = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode($student);
        }


        break;
}
