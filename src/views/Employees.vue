<template>
  <div class="container mt-4">
    <h2 class="mb-3">รายชื่อพนักงาน</h2>

    <div class="mb-3">
      <button class="btn btn-primary" @click="openAddModal">Add+</button>
    </div>

    <table class="table table-bordered table-striped">
      <thead class="table-primary">
        <tr>
          <th>#</th> <!-- ลำดับแถว -->
          <th @click="sortBy('first_name')" style="cursor: pointer">
            ชื่อ <span v-if="sortKey === 'first_name'">{{ sortAsc ? '▲' : '▼' }}</span>
          </th>
          <th>นามสกุล</th>
          <th @click="sortBy('username')" style="cursor: pointer">
            ชื่อผู้ใช้ <span v-if="sortKey === 'username'">{{ sortAsc ? '▲' : '▼' }}</span>
          </th>
          <th>รูปภาพ</th>
          <th>การจัดการ</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(employees, index) in sortedEmployees" :key="employees.employees_id">
          <td>{{ index + 1 }}</td> <!-- ลำดับ -->
          <td>{{ employees.first_name }}</td>
          <td>{{ employees.last_name }}</td>
          <td>{{ employees.username }}</td>
          <td>
            <img
              v-if="employees.image"
              :src="'http://localhost:8081/project_67712277/api_php/uploads/' + employees.image"
              width="100"
            />
          </td>
          <td>
            <button class="btn btn-warning btn-sm me-2" @click="openEditModal(employees)">แก้ไข</button>
            <button class="btn btn-danger btn-sm" @click="deleteEmployees(employees.employees_id)">ลบ</button>
          </td>
        </tr>
      </tbody>
    </table>

    <div v-if="loading" class="text-center"><p>กำลังโหลดข้อมูล...</p></div>
    <div v-if="error" class="alert alert-danger">{{ error }}</div>

    <!-- Modal สำหรับเพิ่ม/แก้ไข -->
    <div class="modal fade" id="editModal" tabindex="-1">
      <div class="modal-dialog modal-md">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ isEditMode ? "แก้ไขข้อมูลพนักงาน" : "เพิ่มพนักงานใหม่" }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="saveEmployees">
              <div class="mb-3">
                <label class="form-label">ชื่อ</label>
                <input v-model="editForm.first_name" type="text" class="form-control" required />
              </div>
              <div class="mb-3">
                <label class="form-label">นามสกุล</label>
                <input v-model="editForm.last_name" type="text" class="form-control" required />
              </div>
              <div class="mb-3">
                <label class="form-label">ชื่อผู้ใช้</label>
                <input v-model="editForm.username" type="text" class="form-control" required />
              </div>
              <div class="mb-3">
                <label class="form-label">รหัสผ่าน</label>
                <input v-model="editForm.password" type="password" class="form-control" :required="!isEditMode" placeholder="กรอกเฉพาะเมื่อเพิ่มใหม่หรือเปลี่ยนรหัสผ่าน" />
              </div>
              <div class="mb-3">
                <label class="form-label">รูปภาพ</label>
                <input type="file" @change="handleFileUpload" class="form-control" :required="!isEditMode" />
                <div v-if="isEditMode && editForm.image">
                  <p class="mt-2">รูปเดิม:</p>
                  <img :src="'http://localhost:8081/project_67712277/api_php/uploads/' + editForm.image" width="100" />
                </div>
              </div>

              <button type="submit" class="btn btn-success">
                {{ isEditMode ? "บันทึกการแก้ไข" : "บันทึกพนักงานใหม่" }}
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, computed } from "vue";

export default {
  name: "EmployeesList", // เปลี่ยนชื่อ component เป็น EmployeesList
  setup() {
    const employees = ref([]); // เปลี่ยนจาก products เป็น employees
    const loading = ref(true);
    const error = ref(null);

    const isEditMode = ref(false);
    const editForm = ref({
      employees_id: null, // เปลี่ยนจาก product_id เป็น employee_id
      first_name: "",
      last_name: "",
      username: "",
      password: "",
      image: ""
    });

    const newImageFile = ref(null);
    let modalInstance = null;

    const sortKey = ref("employees_id"); // เปลี่ยนจาก product_id เป็น employee_id
    const sortAsc = ref(true);

    // โหลดข้อมูลพนักงาน
    const fetchEmployees = async () => {
      try {
        const res = await fetch("http://localhost:8081/project_67712277/api_php/api_employees.php"); // เปลี่ยน API เป็น api_employees.php
        const data = await res.json();
        employees.value = data.success ? data.data : [];
      } catch (err) {
        error.value = err.message;
      } finally {
        loading.value = false;
      }
    };

    // เรียงข้อมูล
    const sortedEmployees = computed(() => {
      return [...employees.value].sort((a, b) => {
        let valA = a[sortKey.value];
        let valB = b[sortKey.value];

        valA = isNaN(valA) ? valA : parseFloat(valA);
        valB = isNaN(valB) ? valB : parseFloat(valB);

        if (valA < valB) return sortAsc.value ? -1 : 1;
        if (valA > valB) return sortAsc.value ? 1 : -1;
        return 0;
      });
    });

    const sortBy = (key) => {
      if (sortKey.value === key) {
        sortAsc.value = !sortAsc.value;
      } else {
        sortKey.value = key;
        sortAsc.value = true;
      }
    };

    const openAddModal = () => {
      isEditMode.value = false;
      editForm.value = {
        employees_id: null, // เปลี่ยนจาก product_id เป็น employee_id
        first_name: "",
        last_name: "",
        username: "",
        password: "",
        image: ""
      };
      newImageFile.value = null;

      const modalEl = document.getElementById("editModal");
      modalInstance = new window.bootstrap.Modal(modalEl);
      modalInstance.show();

      const fileInput = modalEl.querySelector('input[type="file"]');
      if (fileInput) fileInput.value = "";
    };

    const openEditModal = (employees) => {
      isEditMode.value = true;
      editForm.value = { ...employees };
      newImageFile.value = null;

      const modalEl = document.getElementById("editModal");
      modalInstance = new window.bootstrap.Modal(modalEl);
      modalInstance.show();
    };

    const handleFileUpload = (event) => {
      newImageFile.value = event.target.files[0];
    };

    const saveEmployees = async () => {
      const formData = new FormData();
      formData.append("action", isEditMode.value ? "update" : "add");
      if (isEditMode.value) formData.append("employees_id", editForm.value.employees_id); // เปลี่ยนจาก product_id เป็น employee_id
      formData.append("first_name", editForm.value.first_name);
      formData.append("last_name", editForm.value.last_name);
      formData.append("username", editForm.value.username);
      formData.append("password", editForm.value.password);
      if (newImageFile.value) formData.append("image", newImageFile.value);

      try {
        const res = await fetch("http://localhost:8081/project_67712277/api_php/api_employees.php", { // เปลี่ยน API เป็น api_employees.php
          method: "POST",
          body: formData
        });
        const result = await res.json();
        if (result.message) {
          alert(result.message);
          fetchEmployees();
          modalInstance.hide();
        } else if (result.error) {
          alert(result.error);
        }
      } catch (err) {
        alert(err.message);
      }
    };

    const deleteEmployees = async (id) => {
      if (!confirm("คุณแน่ใจหรือไม่ที่จะลบพนักงานนี้?")) return;

      const formData = new FormData();
      formData.append("action", "delete");
      formData.append("employees_id", id); // เปลี่ยนจาก product_id เป็น employee_id

      try {
        const res = await fetch("http://localhost:8081/project_67712277/api_php/api_employees.php", { // เปลี่ยน API เป็น api_employees.php
          method: "POST",
          body: formData
        });
        const result = await res.json();
        if (result.message) {
          alert(result.message);
          employees.value = employees.value.filter((e) => e.employees_id !== id); // เปลี่ยนจาก product_id เป็น employee_id
        } else if (result.error) {
          alert(result.error);
        }
      } catch (err) {
        alert(err.message);
      }
    };

    onMounted(fetchEmployees);

    return {
      employees,
      sortedEmployees,
      loading,
      error,
      editForm,
      isEditMode,
      openAddModal,
      openEditModal,
      handleFileUpload,
      saveEmployees,
      deleteEmployees,
      sortBy,
      sortKey,
      sortAsc
    };
  }
};
</script>

<style scoped>
th {
  user-select: none;
}
</style>
