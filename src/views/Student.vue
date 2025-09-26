<template lang="">
   <div class="container mt-4">
    <h2 class="mb-3">รายชื่อนักเรียน</h2>
    
    <div class="mb-3">
      <a class="btn btn-primary" href="/addstudent" role="button">Add+</a>
    </div>

    <!-- ตารางแสดงข้อมูลลูกค้า -->
    <table class="table table-bordered table-striped">
      <thead class="table-primary">
        <tr>
          <th>ID</th>
          <th>ชื่อ</th>
          <th>นามสกุล</th>
          <th>อีเมล</th>
          <th>เบอร์โทร</th>
          <th>ลบ</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="student in students" :key="student.student_id">
          <td>{{ student.student_id }}</td>
          <!-- แก้ไขชื่อและนามสกุล -->
          <td>{{ student.first_name }}</td>
          <td>{{ student.last_name }}</td>
          <td>{{ student.email }}</td>
          <td>{{ student.phone }}</td>
          <td>  
            <button class="btn btn-danger btn-sm" @click="deletestudent(student.student_id)">ลบ</button>
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Loading -->
    <div v-if="loading" class="text-center">
      <p>กำลังโหลดข้อมูล...</p>
    </div>

    <!-- Error -->
    <div v-if="error" class="alert alert-danger">
      {{ error }}
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from "vue";

export default {
  name: "studentsList",
  setup() {
    const students = ref([]);
    const loading = ref(true);
    const error = ref(null);

    // ฟังก์ชันดึงข้อมูลจาก API ด้วย GET
    const fetchstudents = async () => {
      try {
        const response = await fetch("http://localhost:8081/project_67712277/api_php/show_student.php", {
          method: "GET",
          headers: {
            "Content-Type": "application/json"
          }
        });

        if (!response.ok) {
          throw new Error("ไม่สามารถดึงข้อมูลได้");
        }

        const result = await response.json();
        if (result.success) {
          students.value = result.data;
        } else {
          error.value = result.message;
        }

      } catch (err) {
        error.value = err.message;
      } finally {
        loading.value = false;
      }
    };

    onMounted(() => {
      fetchstudents();
    });

    // ฟังก์ชั่นการลบข้อมูล
    const deletestudent = async (id) => {
      if (!confirm("คุณต้องการลบข้อมูลนี้ใช่หรือไม่?")) return;

      loading.value = true; // แสดง loading ขณะลบข้อมูล

      try {
        const response = await fetch("http://localhost:8081/project_67712277/api_php/api_student.php", {
          method: "DELETE",
          headers: {
            "Content-Type": "application/json"
          },
          body: JSON.stringify({ student_id: id })
        });

        const result = await response.json();

        if (result.success) {
          // ลบออกจาก students ทันที (ไม่ต้องโหลดใหม่)
          students.value = students.value.filter(student => student.student_id !== id);
          alert(result.message);
        } else {
          alert(result.message);
        }

      } catch (err) {
        alert("เกิดข้อผิดพลาด: " + err.message);
      } finally {
        loading.value = false; // ซ่อน loading
      }
    };

    return {
      students,
      loading,
      error,
      deletestudent
    };
  }
};
</script>
