<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['image']) && isset($_POST['userId']) && isset($_POST['fabric']) && isset($_POST['stain'])) {
        $userId = $_POST['userId'];
        $fabric = $_POST['fabric'];
        $stain = $_POST['stain'];
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

                $host = 'mysql-fabricare.alwaysdata.net';
                $dbname = 'fabricare_fabri_care';
                $username = 'fabricare';
                $password = 'fabri123';
                // Database connection
                $conn = new mysqli($host, $username, $password, $dbname);

                // Check connection
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // Insert the image details into the `image` table, along with fabric and stain details
                $uploadDate = date('Y-m-d');
                $sql_image = "INSERT INTO image (image_path, image_uploadDate, user_id, fabric, stain) 
                              VALUES ('$dest_path', '$uploadDate', '$userId', '$fabric', '$stain')";
                if ($conn->query($sql_image) === TRUE) {
                    echo "File uploaded successfully. Image, fabric, and stain data saved.";
                } else {
                    echo "Error inserting image data: " . $conn->error;
                }

                // Close connection
                $conn->close();
            } else {
                echo "Error moving the uploaded file.";
            }
        } else {
            echo "Invalid file type. Only JPG, JPEG, and PNG are allowed.";
        }
    } else {
        echo "Required parameters are missing.";
    }
}
