<template>
  <div class="container mt-4 col-md-4 bg-body-secondary ">
    <h2 class="text-center mb-3">ลงทะเบียนพนักงาน</h2>
    <form @submit.prevent="addEmployee">
      <div class="mb-2">
        <input v-model="employee.first_name" class="form-control" placeholder="ชื่อ" required />
      </div>
      <div class="mb-2">
        <input v-model="employee.last_name" class="form-control" placeholder="นามสกุล" required />
      </div>
      <div class="mb-2">
        <input v-model="employee.username" class="form-control" placeholder="ชื่อผู้ใช้" required />
      </div>
      <div class="mb-2">
        <input type="password" v-model="employee.password" class="form-control" placeholder="รหัสผ่าน" required />
      </div>
      <div class="mb-2">
        <input type="file" @change="handleFileUpload" class="form-control" placeholder="เลือกรูปภาพ" />
      </div>
      <div class="text-center mt-4 ">
        <button type="submit" class="btn btn-primary mb-4">บันทึก</button> &nbsp;
        <button type="reset" class="btn btn-secondary mb-4">ยกเลิก</button>
      </div>
    </form>

    <div v-if="message" class="alert alert-info mt-3">
      {{ message }}
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      employee: {
        first_name: "",
        last_name: "",
        username: "",
        password: "",
        image: null
      },
      message: ""
    };
  },
  methods: {
    // ฟังก์ชันอัปโหลดไฟล์รูปภาพ
    handleFileUpload(event) {
      this.employee.image = event.target.files[0];
    },

    async addEmployee() {
      try {
        const formData = new FormData();
        formData.append("first_name", this.employee.first_name);
        formData.append("last_name", this.employee.last_name);
        formData.append("username", this.employee.username);
        formData.append("password", this.employee.password);
        if (this.employee.image) {
          formData.append("image", this.employee.image);
        }

        const res = await fetch("http://localhost:8081/project_67712277/api_php/add_employee.php", {
          method: "POST",
          body: formData
        });
        const data = await res.json();
        this.message = data.message;

        if (data.success) {
          // ✅ เคลียร์ข้อมูลใน textbox หลังบันทึกสำเร็จ
          this.employee = { first_name: "", last_name: "", username: "", password: "", image: null };
        }

      } catch (err) {
        this.message = "เกิดข้อผิดพลาด: " + err.message;
      }
    }
  }
}
</script>
