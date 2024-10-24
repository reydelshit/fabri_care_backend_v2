<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['image']) && isset($_POST['userId'])) {
        $userId = $_POST['userId'];
        $fileTmpPath = $_FILES['image']['tmp_name'];
        $fileName = $_FILES['image']['name'];
        $fileSize = $_FILES['image']['size'];
        $fileType = $_FILES['image']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
        $allowedExtensions = array('jpg', 'jpeg', 'png');

        if (in_array($fileExtension, $allowedExtensions)) {
            $uploadFileDir = './uploads/';
            if (!file_exists($uploadFileDir)) {
                mkdir($uploadFileDir, 0777, true);
            }
            $dest_path = $uploadFileDir . $fileName;

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                // Database 

                $host = 'mysql-fabricare.alwaysdata.net';
                $dbname = 'fabricare_fabri_care';
                $username = 'fabricare';
                $password = 'fabri123';

                $conn = new mysqli($host, $username, $password, $dbname);
                // $conn = new mysqli('localhost', 'root', '', 'fabri-care');

                // Check connection
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // Insert the image details into the database
                $uploadDate = date('Y-m-d');
                $sql = "INSERT INTO image (image_path, image_uploadDate, user_id) VALUES ('$dest_path', '$uploadDate', '$userId')";

                if ($conn->query($sql) === TRUE) {
                    echo "File is successfully uploaded and details saved to the database.";
                } else {
                    echo "Error: " . $sql . "<br>" . $conn->error;
                }

                $conn->close();
            } else {
                echo "There was some error moving the file to upload directory.";
            }
        } else {
            echo "Upload failed. Allowed file types: " . implode(', ', $allowedExtensions);
        }
    } else {
        echo "No file uploaded or user ID missing.";
    }
} else {
    echo "Invalid request method.";
}
