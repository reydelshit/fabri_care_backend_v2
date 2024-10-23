<?php

date_default_timezone_set('Asia/Manila');
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


include 'DBconnect.php';
$objDB = new DbConnect();
$conn = $objDB->connect();
date_default_timezone_set('Asia/Manila');
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case "GET":


        if (!isset($_GET['user_Id'])) {
            $sql = "SELECT CONCAT(users.fname, ' ', users.lname) AS fullname, users.*,
                users.id AS user_Id
                    FROM users
               ";
        }

        if (isset($_GET['user_Id'])) {
            $user_Id = $_GET['user_Id'];
            $sql = "SELECT CONCAT(users.fname, ' ', users.lname) AS fullname,
                image.image_uploadDate,
                users.id AS user_Id,
                image.image_path
                    FROM users
                    INNER JOIN image ON users.id = image.user_id
                    WHERE users.id = :user_Id;
                    ";
        }


        if (isset($sql)) {
            $stmt = $conn->prepare($sql);

            if (isset($user_Id)) {
                $stmt->bindParam(':user_Id', $user_Id);
            }

            $stmt->execute();
            $student = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode($student);
        }


        break;


    case "DELETE":
        $user = json_decode(file_get_contents('php://input'));
        $sql = "DELETE FROM users WHERE id = :user_Id";
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':user_Id', $user->user_Id);

        if ($stmt->execute()) {
            $response = [
                "status" => "success",
                "message" => "user_Id deleted successfully"
            ];
        } else {
            $response = [
                "status" => "error",
                "message" => "user_Id delete failed"
            ];
        }

        echo json_encode($response);
        break;
}
