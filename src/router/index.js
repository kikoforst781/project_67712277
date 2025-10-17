import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'

const routes = [
  {
    path: '/home',
    name: 'home',
    component: HomeView
  },
  {
    path: '/about',
    name: 'about',
    // route level code-splitting
    // this generates a separate chunk (about.[hash].js) for this route
    // which is lazy-loaded when the route is visited.
    component: () => import(/* webpackChunkName: "about" */ '../views/AboutView.vue')
  },
  {
    path: '/showproduct',
    name: 'showproduct',
    component: () => import(/* webpackChunkName: "about" */ '../views/ShowProduct.vue')
  },
  {
    path: '/product',
    name: 'product',
    component: () => import(/* webpackChunkName: "about" */ '../views/Product.vue')
  },
  {
    path: '/addproduct',
    name: 'addproduct',
    component: () => import(/* webpackChunkName: "about" */ '../views/Add_Product.vue')
  },
  {
    path: '/editproduct',
    name: 'editproduct',
    component: () => import(/* webpackChunkName: "about" */ '../views/Edit_Product.vue')
  },
  {
    path: '/customer',
    name: 'customer',
    component: () => import(/* webpackChunkName: "about" */ '../views/Customer.vue')
  },
  {
    path: '/editcustomer',
    name: 'editcustomer',
    component: () => import(/* webpackChunkName: "about" */ '../views/Edit_Customer.vue')
  },
  {
    path: '/addcustomer',
    name: 'addcustomer',
    component: () => import(/* webpackChunkName: "about" */ '../views/Add_Customer.vue')
  },
  {
    path: '/student',
    name: 'student',
    component: () => import(/* webpackChunkName: "about" */ '../views/Student.vue')
  },
  {
    path: '/editstudent',
    name: 'editstudent',
    component: () => import(/* webpackChunkName: "about" */ '../views/Edit_Student.vue')
  },
  {
    path: '/addstudent',
    name: 'addstudent',
    component: () => import(/* webpackChunkName: "about" */ '../views/Add_Student.vue')
  },
  {
    path: '/employees',
    name: 'employees',
    component: () => import(/* webpackChunkName: "about" */ '../views/Employees.vue')
  },
]

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes
})

export default router
