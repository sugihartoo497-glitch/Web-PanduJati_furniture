/**
 * ============================================================================
 * TYPESCRIPT DEFINITIONS: PANDU JATI FURNITURE
 * ============================================================================
 * File ini mendefinisikan tipe data dan antarmuka (interfaces) untuk seluruh
 * entitas di platform Pandu Jati Furniture agar kodingan terstruktur, aman (type-safe),
 * dan mudah dipahami.
 */

// 1. Status 8-Tahap Produksi Mebel
export type OrderStatus =
  | 'PENDING'         // 1. Pesanan Dibuat
  | 'CONFIRMED'       // 2. Pesanan Dikonfirmasi (DP Masuk)
  | 'PROCESSING'      // 3. Sedang Diproses (Pemilihan Kayu Jati & Oven)
  | 'PRODUCTION'      // 4. Sedang Dibuat (Konstruksi Purus-Pasak)
  | 'QUALITY_CHECK'   // 5. Quality Check
  | 'READY_TO_SHIP'   // 6. Siap Dikirim (Packing Peti Kayu)
  | 'SHIPPED'         // 7. Dalam Pengiriman (Truk Ekspedisi Jepara)
  | 'COMPLETED'       // 8. Pesanan Selesai
  | 'CANCELLED';      // Dibatalkan

// 2. Status Pembayaran
export type PaymentStatus =
  | 'UNPAID'          // Belum Dibayar
  | 'DP'              // DP 50% Diterima
  | 'PAID'            // Lunas
  | 'REFUNDED';       // Dikembalikan

// 3. Entitas Kategori
export interface Category {
  id: number;
  name: string;
  slug: string;
  description?: string;
}

// 4. Entitas Produk Mebel
export interface Product {
  id: number;
  category_id: number;
  category_name?: string;
  category_slug?: string;
  name: string;
  slug: string;
  price: number;
  original_price?: number | null;
  description: string;
  material: string;
  dimensions: string;
  weight: string;
  finishing: string;
  lead_time: string;
  stock: number;
  image_url: string;
  is_featured: boolean | number;
  is_active: boolean | number;
  created_at?: string;
  updated_at?: string;
}

// 5. Entitas Item Pesanan
export interface OrderItem {
  id: number;
  order_id: number;
  product_id: number;
  product_name: string;
  product_price: number;
  quantity: number;
  finishing_choice: string;
  subtotal: number;
  image_url?: string;
}

// 6. Entitas Pesanan (Order)
export interface Order {
  id: number;
  order_number: string;        // Contoh: PJF-20261008-0001
  customer_name: string;
  customer_phone: string;
  customer_email?: string;
  address: string;
  city: string;
  province: string;
  postal_code?: string;
  shipping_method: string;
  shipping_cost: number;
  payment_method: string;
  subtotal: number;
  total_amount: number;
  order_status: OrderStatus;
  payment_status: PaymentStatus;
  customer_notes?: string;
  tracking_number?: string | null;
  admin_notes?: string | null;
  created_at: string;
  updated_at: string;
  items?: OrderItem[];
}

// 7. Riwayat Status Pesanan
export interface OrderStatusHistory {
  id: number;
  order_id: number;
  status: OrderStatus;
  note?: string;
  created_at: string;
}

// 8. Tahapan Progress 8-Step
export interface ProductionStage {
  step: number;
  code: OrderStatus;
  title: string;
  description: string;
}

// 9. Entitas Permintaan Custom Furniture
export interface CustomRequest {
  id: number;
  request_number: string;     // Contoh: PJF-CST-20261008-0001
  customer_name: string;
  customer_phone: string;
  customer_email?: string;
  furniture_type: string;
  dimensions?: string;
  wood_type: string;
  finishing: string;
  description: string;
  reference_image?: string;
  budget_range?: string;
  status: 'PENDING' | 'REVIEWING' | 'QUOTED' | 'APPROVED' | 'CANCELLED';
  estimated_price?: number | null;
  admin_reply?: string | null;
  created_at: string;
}

// 10. Statistik KPI Admin
export interface DashboardStats {
  total_orders: number;
  new_orders: number;
  prod_orders: number;
  completed_orders: number;
  total_revenue: number;
  recent_orders: Order[];
}

// 11. Format Standar Respon API dari Backend PHP
export interface ApiResponse<T> {
  success: boolean;
  data?: T;
  message?: string;
}
