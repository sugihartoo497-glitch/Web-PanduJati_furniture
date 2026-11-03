/**
 * ============================================================================
 * TYPESCRIPT API CLIENT: PANDU JATI FURNITURE
 * ============================================================================
 * Modul ini menangani semua komunikasi HTTP (fetch) dari frontend ke backend PHP.
 */

import {
  ApiResponse,
  Product,
  Category,
  Order,
  ProductionStage,
  OrderStatusHistory,
  CustomRequest,
  DashboardStats,
  OrderStatus,
  PaymentStatus
} from './types';

const BASE_URL = 'api';

async function fetchJson<T>(url: string, options: RequestInit = {}): Promise<ApiResponse<T>> {
  try {
    const res = await fetch(url, {
      headers: {
        'Content-Type': 'application/json',
        ...options.headers,
      },
      ...options,
    });

    const data: ApiResponse<T> = await res.json();
    return data;
  } catch (error) {
    console.error(`Error requesting ${url}:`, error);
    return {
      success: false,
      message: error instanceof Error ? error.message : 'Terjadi gangguan koneksi ke server.'
    };
  }
}

export const PanduApi = {
  // 1. Ambil daftar produk dengan filter
  async getProducts(params: {
    search?: string;
    category?: string;
    sort?: string;
    featured?: number;
  } = {}): Promise<ApiResponse<{ total: number; products: Product[]; categories: Category[] }>> {
    const query = new URLSearchParams();
    if (params.search) query.append('search', params.search);
    if (params.category && params.category !== 'all') query.append('category', params.category);
    if (params.sort) query.append('sort', params.sort);
    if (params.featured !== undefined) query.append('featured', String(params.featured));

    return fetchJson(`${BASE_URL}/products.php?${query.toString()}`);
  },

  // 2. Ambil detail produk berdasarkan ID
  async getProductById(id: number): Promise<ApiResponse<{ product: Product; related: Product[] }>> {
    return fetchJson(`${BASE_URL}/products.php?id=${id}`);
  },

  // 3. Buat pesanan baru
  async createOrder(orderData: {
    customer_name: string;
    customer_phone: string;
    customer_email?: string;
    address: string;
    city: string;
    province: string;
    postal_code?: string;
    product_id: number;
    quantity: number;
    finishing_choice?: string;
    shipping_option_index?: number;
    payment_term?: string;
    customer_notes?: string;
  }): Promise<ApiResponse<any>> {
    return fetchJson(`${BASE_URL}/orders.php`, {
      method: 'POST',
      body: JSON.stringify(orderData)
    });
  },

  // 4. Lacak status pesanan (8-tahap)
  async trackOrder(orderNumber: string): Promise<ApiResponse<{
    order: Order;
    items: any[];
    history: OrderStatusHistory[];
    stages: ProductionStage[];
    current_stage_index: number;
    whatsapp_cs_url: string;
  }>> {
    return fetchJson(`${BASE_URL}/track.php?order_number=${encodeURIComponent(orderNumber)}`);
  },

  // 5. Kirim permohonan custom mebel
  async submitCustomRequest(data: {
    customer_name: string;
    customer_phone: string;
    customer_email?: string;
    furniture_type: string;
    dimensions?: string;
    wood_type?: string;
    finishing?: string;
    budget_range?: string;
    description: string;
    reference_image?: string;
  }): Promise<ApiResponse<any>> {
    return fetchJson(`${BASE_URL}/custom.php`, {
      method: 'POST',
      body: JSON.stringify(data)
    });
  },

  // 6. Login Admin
  async loginAdmin(credentials: { email: string; password: string }): Promise<ApiResponse<any>> {
    return fetchJson(`${BASE_URL}/auth.php`, {
      method: 'POST',
      body: JSON.stringify(credentials)
    });
  },

  // 7. Cek Sesi Admin
  async checkAdminAuth(): Promise<ApiResponse<{ logged_in: boolean; user?: any }>> {
    return fetchJson(`${BASE_URL}/auth.php`);
  },

  // 8. Logout Admin
  async logoutAdmin(): Promise<ApiResponse<any>> {
    return fetchJson(`${BASE_URL}/auth.php?action=logout`);
  },

  // 9. Ambil Statistik Admin (KPI)
  async getAdminStats(): Promise<ApiResponse<DashboardStats>> {
    return fetchJson(`${BASE_URL}/stats.php`);
  },

  // 10. Ambil Semua Pesanan untuk Admin
  async getAdminOrders(): Promise<ApiResponse<{ total: number; orders: Order[] }>> {
    return fetchJson(`${BASE_URL}/orders.php`);
  },

  // 11. Update Status Pesanan & Pembayaran
  async updateOrderStatus(data: {
    order_id: number;
    order_status: OrderStatus;
    payment_status: PaymentStatus;
    tracking_number?: string;
    admin_notes?: string;
  }): Promise<ApiResponse<any>> {
    return fetchJson(`${BASE_URL}/orders.php`, {
      method: 'PUT',
      body: JSON.stringify(data)
    });
  },

  // 12. Tambah Produk Mebel
  async createProduct(product: Partial<Product>): Promise<ApiResponse<any>> {
    return fetchJson(`${BASE_URL}/products.php`, {
      method: 'POST',
      body: JSON.stringify(product)
    });
  },

  // 13. Hapus Produk Mebel
  async deleteProduct(id: number): Promise<ApiResponse<any>> {
    return fetchJson(`${BASE_URL}/products.php?id=${id}`, {
      method: 'DELETE'
    });
  },

  // 14. Ambil Daftar Permohonan Custom
  async getAdminCustomRequests(): Promise<ApiResponse<{ total: number; requests: CustomRequest[] }>> {
    return fetchJson(`${BASE_URL}/custom.php`);
  }
};
