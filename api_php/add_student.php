<?php
header('Content-Type: application/json');

// เชื่อมต่อฐานข้อมูล
include 'condb.php';

try {
    // ตรวจสอบคำขอที่ได้รับจาก Client ตามประเภทของคำ ว่าเป็น GET หรือ POST
    $method = $_SERVER['REQUEST_METHOD'];

    if ($method == 'POST') {
        // รับข้อมูลจาก Client
        $data = json_decode(file_get_contents("php://input"), true);

        // ตรวจสอบค่าที่จำเป็น
        if (isset($data['first_name'], $data['last_name'], $data['email'], $data['phone'])) {
            // เพิ่มข้อมูลลูกค้าใหม่
            $stmt = $conn->prepare("INSERT INTO students (first_name, last_name, email, phone) VALUES (:first_name, :last_name, :email, :phone)");
            $stmt->bindParam(':first_name', $data['first_name']);
            $stmt->bindParam(':last_name', $data['last_name']);
            $stmt->bindParam(':email', $data['email']);
            $stmt->bindParam(':phone', $data['phone']);

            if ($stmt->execute()) {
                echo json_encode(["success" => true, "message" => "Student added successfully"]);
            } else {
                echo json_encode(["success" => false, "message" => "Error adding student"]);
            }
        } else {
            echo json_encode(["success" => false, "message" => "Missing required fields"]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Invalid request method"]);
    }

} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
}
?>
