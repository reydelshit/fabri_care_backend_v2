<?php
header("Content-Type: application/json");

// Get JSON input from the request body
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($data['userId'])) {
        $userId = $data['userId'];

        // Debugging: log userId
        error_log("Received userId: " . $userId);

        // Database connection
        $conn = new mysqli('mysql-fabricare.alwaysdata.net', 'fabricare', 'fabri123', 'fabricare_fabri_care');

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Query to retrieve the user’s upload history
        $sql = "SELECT image_path, fabric, stain, image_uploadDate FROM image WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        $history = array();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $history[] = array(
                    'image_path' => $row['image_path'],
                    'fabric' => $row['fabric'],
                    'stain' => $row['stain'],
                    'image_uploadDate' => $row['image_uploadDate']
                );
            }
            echo json_encode(array('status' => 'success', 'history' => $history));
        } else {
            echo json_encode(array('status' => 'no_data', 'message' => 'No upload history found.'));
        }

        // Close connections
        $stmt->close();
        $conn->close();
    } else {
        echo json_encode(array('status' => 'error', 'message' => 'User ID is missing.'));
    }
}
