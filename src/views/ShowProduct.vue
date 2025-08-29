<template>
  <div class="container my-5">
    <h2 class="text-center mb-4">รายการสินค้า</h2>
    <div class="row">
      <div class="col-md-4" v-for="product in products" :key="product.id">
        <div class="card shadow-sm mb-4">
          <img :src="product.image" class="card-img-top" :alt="product.name">
          <div class="card-body text-center">
            <h5 class="card-title">{{ product.name }}</h5>
            <p class="card-text">{{ product.price }} บาท</p>
            <button class="btn btn-primary">รายละเอียด</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "ShowProduct",
  data() {
    return {
      products: [
        { id: 1, name: "เตาไฟฟ้า", price: 1200, image: "https://imarflex.co.th/wp-content/uploads/2020/10/IF-404-4-scaled.jpg" },
        { id: 2, name: "หม้อชาบู", price: 800, image: "https://shopmania.mcot.net/wp-content/uploads/2024/07/100.jpg" },
        { id: 3, name: "ชุดช้อนส้อม", price: 250, image: "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRmtcpzwY3B518F9NECXbdOzR_nF9XL1ldh7Q&s" },
        { id: 4, name: "เตาไฟฟ้า", price: 1200, image: "https://picsum.photos/400/200?7" },
        { id: 5, name: "หม้อชาบู", price: 800, image: "https://picsum.photos/400/200?8" },
        { id: 6, name: "ชุดช้อนส้อม", price: 250, image: "https://picsum.photos/400/200?8" },
      ]
    }
  }
}
</script>

<style lang="">
    
</style>