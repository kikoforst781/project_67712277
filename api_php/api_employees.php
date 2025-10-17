<?php

include 'condb.php';

$action = $_POST['action'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action) {
    // เพิ่ม / แก้ไข / ลบ
    switch($action) {

        case 'add':
            $first_name = $_POST['first_name'];
            $last_name = $_POST['last_name'];
            $username = $_POST['username'];
            $password = $_POST['password'];

            // อัพโหลดไฟล์รูป
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

            $sql = "INSERT INTO employees (first_name, last_name, username, password, image)
                    VALUES (:first_name, :last_name, :username, :password, :image)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':first_name', $first_name);
            $stmt->bindParam(':last_name', $last_name);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $password);
            $stmt->bindParam(':image', $filename);

            if ($stmt->execute()) {
                echo json_encode(["message" => "เพิ่มข้อมูลพนักงานสำเร็จ"]);
            } else {
                echo json_encode(["error" => "เพิ่มข้อมูลพนักงานล้มเหลว"]);
            }
            break;

        case 'update':
            $employees_id = $_POST['employees_id'];
            $first_name = $_POST['first_name'];
            $last_name = $_POST['last_name'];
            $username = $_POST['username'];
            $password = $_POST['password'];

            // อัพโหลดไฟล์รูป
            if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                $targetDir = "uploads/"; 
                $filename = time() . '_' . basename($_FILES['image']['name']);
                $targetFile = $targetDir . $filename;
                move_uploaded_file($_FILES['image']['tmp_name'], $targetFile);
                $imageSQL = ", image = :image";
            } else {
                $imageSQL = "";
            }

            $sql = "UPDATE employees SET 
                        first_name = :first_name,
                        last_name = :last_name,
                        username = :username,
                        password = :password
                        $imageSQL
                    WHERE employees_id = :employees_id";
            $stmt = $conn->prepare($sql);

            $stmt->bindParam(':first_name', $first_name);
            $stmt->bindParam(':last_name', $last_name);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $password);
            $stmt->bindParam(':employees_id', $employees_id);
            if (isset($filename)) $stmt->bindParam(':image', $filename);

            if ($stmt->execute()) {
                echo json_encode(["message" => "แก้ไขข้อมูลพนักงานสำเร็จ"]);
            } else {
                echo json_encode(["error" => "แก้ไขข้อมูลพนักงานล้มเหลว"]);
            }
            break;

        case 'delete':
            $employees_id = $_POST['employees_id'];
            $stmt = $conn->prepare("DELETE FROM employees WHERE employees_id = :employees_id");
            $stmt->bindParam(':employees_id', $employees_id);
            if ($stmt->execute()) {
                echo json_encode(["message" => "ลบข้อมูลพนักงานสำเร็จ"]);
            } else {
                echo json_encode(["error" => "ลบข้อมูลพนักงานล้มเหลว"]);
            }
            break;

        default:
            echo json_encode(["error" => "Action ไม่ถูกต้อง"]);
            break;
    }

} else {
    // GET: ดึงข้อมูลพนักงาน
    $stmt = $conn->prepare("SELECT * FROM employees ORDER BY employees_id DESC");
    if ($stmt->execute()) {
        $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(["success" => true, "data" => $employees]);
    } else {
        echo json_encode(["success" => false, "data" => []]);
    }
}
?>
