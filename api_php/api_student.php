<?php
// เชื่อมต่อฐานข้อมูล
include 'condb.php';

header("Content-Type: application/json; charset=UTF-8");

try {
    $method = $_SERVER['REQUEST_METHOD'];

    if ($method === "GET") {
        // ✅ ดึงข้อมูลลูกค้าทั้งหมด
        $stmt = $conn->prepare("SELECT * FROM students ORDER BY student_id DESC");
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // ตรวจสอบว่ามีข้อมูลหรือไม่
        if (empty($result)) {
            echo json_encode(["success" => true, "message" => "ไม่พบข้อมูล"]);
        } else {
            echo json_encode(["success" => true, "data" => $result]);
        }
    }

    elseif ($method === "DELETE") {
        // ✅ ลบข้อมูลลูกค้าตาม student_id
        $data = json_decode(file_get_contents("php://input"), true);

        // ตรวจสอบการส่ง student_id
        if (!isset($data["student_id"])) {
            echo json_encode(["success" => false, "message" => "ไม่พบค่า student_id"]);
            exit;
        }

        $student_id = intval($data["student_id"]);

        // ตรวจสอบว่า student_id มีอยู่ในฐานข้อมูล
        $stmt_check = $conn->prepare("SELECT 1 FROM students WHERE student_id = :id");
        $stmt_check->bindParam(":id", $student_id, PDO::PARAM_INT);
        $stmt_check->execute();

        if ($stmt_check->rowCount() == 0) {
            echo json_encode(["success" => false, "message" => "ไม่พบข้อมูล student_id ที่ต้องการลบ"]);
            exit;
        }

        // ลบข้อมูล
        $stmt = $conn->prepare("DELETE FROM students WHERE student_id = :id");
        $stmt->bindParam(":id", $student_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "ลบข้อมูลเรียบร้อย"]);
        } else {
            echo json_encode(["success" => false, "message" => "ไม่สามารถลบข้อมูลได้"]);
        }
    }

    else {
        echo json_encode(["success" => false, "message" => "Method ไม่ถูกต้อง"]);
    }

} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "เกิดข้อผิดพลาด: " . $e->getMessage()]);
}
?>
