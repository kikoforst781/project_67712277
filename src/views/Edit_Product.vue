<template>
  <div class="container mt-4">
    <h2 class="mb-3">รายการสินค้า</h2>

    <div class="mb-3">
      <button class="btn btn-primary" @click="openAddModal">Add+</button>
    </div>

    <table class="table table-bordered table-striped">
      <thead class="table-primary">
        <tr>
          <th>#</th> <!-- ลำดับแถว -->
          <th @click="sortBy('product_name')" style="cursor: pointer">
            ชื่อสินค้า <span v-if="sortKey === 'product_name'">{{ sortAsc ? '▲' : '▼' }}</span>
          </th>
          <th>รายละเอียด</th>
          <th @click="sortBy('price')" style="cursor: pointer">
            ราคา <span v-if="sortKey === 'price'">{{ sortAsc ? '▲' : '▼' }}</span>
          </th>
          <th @click="sortBy('stock')" style="cursor: pointer">
            จำนวน <span v-if="sortKey === 'stock'">{{ sortAsc ? '▲' : '▼' }}</span>
          </th>
          <th>รูปภาพ</th>
          <th>การจัดการ</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(product, index) in sortedProducts" :key="product.product_id">
          <td>{{ index + 1 }}</td> <!-- ลำดับ -->
          <td>{{ product.product_name }}</td>
          <td>{{ product.description }}</td>
          <td>{{ product.price }}</td>
          <td>{{ product.stock }}</td>
          <td>
            <img
              v-if="product.image"
              :src="'http://localhost:8081/project_67712277/api_php/uploads/' + product.image"
              width="100"
            />
          </td>
          <td>
            <button class="btn btn-warning btn-sm me-2" @click="openEditModal(product)">แก้ไข</button>
            <button class="btn btn-danger btn-sm" @click="deleteProduct(product.product_id)">ลบ</button>
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
            <h5 class="modal-title">{{ isEditMode ? "แก้ไขสินค้า" : "เพิ่มสินค้าใหม่" }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="saveProduct">
              <div class="mb-3">
                <label class="form-label">ชื่อสินค้า</label>
                <input v-model="editForm.product_name" type="text" class="form-control" required />
              </div>
              <div class="mb-3">
                <label class="form-label">รายละเอียด</label>
                <textarea v-model="editForm.description" class="form-control"></textarea>
              </div>
              <div class="mb-3">
                <label class="form-label">ราคา</label>
                <input v-model="editForm.price" type="number" step="0.01" class="form-control" required />
              </div>
              <div class="mb-3">
                <label class="form-label">จำนวน</label>
                <input v-model="editForm.stock" type="number" class="form-control" required />
              </div>
              <div class="mb-3">
                <label class="form-label">รูปภาพ</label>
                <input
                  type="file"
                  @change="handleFileUpload"
                  class="form-control"
                  :required="!isEditMode"
                />
                <div v-if="isEditMode && editForm.image">
                  <p class="mt-2">รูปเดิม:</p>
                  <img
                    :src="'http://localhost:8081/project_67712277/api_php/uploads/' + editForm.image"
                    width="100"
                  />
                </div>
              </div>

              <button type="submit" class="btn btn-success">
                {{ isEditMode ? "บันทึกการแก้ไข" : "บันทึกสินค้าใหม่" }}
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
  name: "ProductList",
  setup() {
    const products = ref([]);
    const loading = ref(true);
    const error = ref(null);

    const isEditMode = ref(false);
    const editForm = ref({
      product_id: null,
      product_name: "",
      description: "",
      price: "",
      stock: "",
      image: ""
    });

    const newImageFile = ref(null);
    let modalInstance = null;

    const sortKey = ref("product_id");
    const sortAsc = ref(true);

    // โหลดข้อมูลสินค้า
    const fetchProducts = async () => {
      try {
        const res = await fetch("http://localhost:8081/project_67712277/api_php/api_product.php");
        const data = await res.json();
        products.value = data.success ? data.data : [];
      } catch (err) {
        error.value = err.message;
      } finally {
        loading.value = false;
      }
    };

    // เรียงข้อมูล
    const sortedProducts = computed(() => {
      return [...products.value].sort((a, b) => {
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
        product_id: null,
        product_name: "",
        description: "",
        price: "",
        stock: "",
        image: ""
      };
      newImageFile.value = null;

      const modalEl = document.getElementById("editModal");
      modalInstance = new window.bootstrap.Modal(modalEl);
      modalInstance.show();

      const fileInput = modalEl.querySelector('input[type="file"]');
      if (fileInput) fileInput.value = "";
    };

    const openEditModal = (product) => {
      isEditMode.value = true;
      editForm.value = { ...product };
      newImageFile.value = null;

      const modalEl = document.getElementById("editModal");
      modalInstance = new window.bootstrap.Modal(modalEl);
      modalInstance.show();
    };

    const handleFileUpload = (event) => {
      newImageFile.value = event.target.files[0];
    };

    const saveProduct = async () => {
      const formData = new FormData();
      formData.append("action", isEditMode.value ? "update" : "add");
      if (isEditMode.value) formData.append("product_id", editForm.value.product_id);
      formData.append("product_name", editForm.value.product_name);
      formData.append("description", editForm.value.description);
      formData.append("price", editForm.value.price);
      formData.append("stock", editForm.value.stock);
      if (newImageFile.value) formData.append("image", newImageFile.value);

      try {
        const res = await fetch("http://localhost:8081/project_67712277/api_php/api_product.php", {
          method: "POST",
          body: formData
        });
        const result = await res.json();
        if (result.message) {
          alert(result.message);
          fetchProducts();
          modalInstance.hide();
        } else if (result.error) {
          alert(result.error);
        }
      } catch (err) {
        alert(err.message);
      }
    };

    const deleteProduct = async (id) => {
      if (!confirm("คุณแน่ใจหรือไม่ที่จะลบสินค้านี้?")) return;

      const formData = new FormData();
      formData.append("action", "delete");
      formData.append("product_id", id);

      try {
        const res = await fetch("http://localhost:8081/project_67712277/api_php/api_product.php", {
          method: "POST",
          body: formData
        });
        const result = await res.json();
        if (result.message) {
          alert(result.message);
          products.value = products.value.filter((p) => p.product_id !== id);
        } else if (result.error) {
          alert(result.error);
        }
      } catch (err) {
        alert(err.message);
      }
    };

    onMounted(fetchProducts);

    return {
      products,
      sortedProducts,
      loading,
      error,
      editForm,
      isEditMode,
      openAddModal,
      openEditModal,
      handleFileUpload,
      saveProduct,
      deleteProduct,
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
