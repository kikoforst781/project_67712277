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
        if (isset($data['first_name'], $data['last_name'], $data['username'], $data['password'])) {
            
            // อัพโหลดไฟล์รูปภาพ
            $filename = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                $targetDir = "uploads/"; 
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }
                $filename = time() . '_' . basename($_FILES['image']['name']);
                $targetFile = $targetDir . $filename;
                move_uploaded_file($_FILES['image']['tmp_name'], $targetFile);
            }

            // เพิ่มข้อมูลพนักงานใหม่
            $stmt = $conn->prepare("INSERT INTO employees (first_name, last_name, username, password, image) 
                                   VALUES (:first_name, :last_name, :username, :password, :image)");

            // ผูกค่าตัวแปรกับ SQL
            $stmt->bindParam(':first_name', $data['first_name']);
            $stmt->bindParam(':last_name', $data['last_name']);
            $stmt->bindParam(':username', $data['username']);
            
            // การเข้ารหัสรหัสผ่าน
            $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);
            $stmt->bindParam(':password', $hashedPassword);
            
            // ผูกค่ารูปภาพ
            $stmt->bindParam(':image', $filename);

            // ตรวจสอบว่าการเพิ่มข้อมูลสำเร็จหรือไม่
            if ($stmt->execute()) {
                echo json_encode(["success" => true, "message" => "Employee added successfully"]);
            } else {
                echo json_encode(["success" => false, "message" => "Error adding employee"]);
            }
        } else {
            echo json_encode(["success" => false, "message" => "Missing required fields"]);
        }
    }
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
}
?>
