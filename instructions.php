<?php

include 'DBconnect.php';
$objDB = new DbConnect();
$conn = $objDB->connect();
date_default_timezone_set('Asia/Manila');
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case "GET":
        // GET all records or a specific one by id
        if (isset($_GET['id'])) {
            $sql = "SELECT * FROM instructions WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id', $_GET['id']);
        } else {
            $sql = "SELECT * FROM instructions ORDER BY id DESC";
            $stmt = $conn->prepare($sql);
        }

        $stmt->execute();
        $instructions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($instructions);
        break;

    case "POST":
        // POST (create new record)
        $data = json_decode(file_get_contents('php://input'));
        $sql = "INSERT INTO instructions (fabric_type, stain_type, washing_instructions, blood_instructions, coffee_instructions, 
                grass_instructions, grease_instructions, marker_instructions, ketchup_instructions, chocolate_instructions) 
                VALUES (:fabric_type, :stain_type, :washing_instructions, :blood_instructions, :coffee_instructions, :grass_instructions, 
                :grease_instructions, :marker_instructions, :ketchup_instructions, :chocolate_instructions)";
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':fabric_type', $data->fabric_type);
        $stmt->bindParam(':stain_type', $data->stain_type);
        $stmt->bindParam(':washing_instructions', $data->washing_instructions);
        $stmt->bindParam(':blood_instructions', $data->blood_instructions);
        $stmt->bindParam(':coffee_instructions', $data->coffee_instructions);
        $stmt->bindParam(':grass_instructions', $data->grass_instructions);
        $stmt->bindParam(':grease_instructions', $data->grease_instructions);
        $stmt->bindParam(':marker_instructions', $data->marker_instructions);
        $stmt->bindParam(':ketchup_instructions', $data->ketchup_instructions);
        $stmt->bindParam(':chocolate_instructions', $data->chocolate_instructions);

        if ($stmt->execute()) {
            $response = [
                "status" => "success",
                "message" => "Instruction created successfully"
            ];
        } else {
            $response = [
                "status" => "error",
                "message" => "Failed to create instruction"
            ];
        }

        echo json_encode($response);
        break;

    case "PUT":
        // PUT (update existing record)
        $data = json_decode(file_get_contents('php://input'));
        $sql = "UPDATE instructions SET fabric_type = :fabric_type, stain_type = :stain_type, washing_instructions = :washing_instructions, 
                blood_instructions = :blood_instructions, coffee_instructions = :coffee_instructions, grass_instructions = :grass_instructions, 
                grease_instructions = :grease_instructions, marker_instructions = :marker_instructions, ketchup_instructions = :ketchup_instructions, 
                chocolate_instructions = :chocolate_instructions WHERE id = :id";
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':fabric_type', $data->fabric_type);
        $stmt->bindParam(':stain_type', $data->stain_type);
        $stmt->bindParam(':washing_instructions', $data->washing_instructions);
        $stmt->bindParam(':blood_instructions', $data->blood_instructions);
        $stmt->bindParam(':coffee_instructions', $data->coffee_instructions);
        $stmt->bindParam(':grass_instructions', $data->grass_instructions);
        $stmt->bindParam(':grease_instructions', $data->grease_instructions);
        $stmt->bindParam(':marker_instructions', $data->marker_instructions);
        $stmt->bindParam(':ketchup_instructions', $data->ketchup_instructions);
        $stmt->bindParam(':chocolate_instructions', $data->chocolate_instructions);
        $stmt->bindParam(':id', $data->id);

        if ($stmt->execute()) {
            $response = [
                "status" => "success",
                "message" => "Instruction updated successfully"
            ];
        } else {
            $response = [
                "status" => "error",
                "message" => "Failed to update instruction"
            ];
        }

        echo json_encode($response);
        break;

    case "DELETE":
        // DELETE a specific record by id
        $data = json_decode(file_get_contents('php://input'));
        $sql = "DELETE FROM instructions WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $data->id);

        if ($stmt->execute()) {
            $response = [
                "status" => "success",
                "message" => "Instruction deleted successfully"
            ];
        } else {
            $response = [
                "status" => "error",
                "message" => "Failed to delete instruction"
            ];
        }

        echo json_encode($response);
        break;
}
