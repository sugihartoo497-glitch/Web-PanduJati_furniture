/**
 * ============================================================================
 * JAVASCRIPT: PORTAL ADMIN DASHBOARD (admin.html)
 * ============================================================================
 */

document.addEventListener('DOMContentLoaded', async () => {
    const loginSection = document.getElementById('admin-login-section');
    const dashboardSection = document.getElementById('admin-dashboard-section');
    const loginForm = document.getElementById('admin-login-form');
    const loginError = document.getElementById('login-error');
    const logoutBtn = document.getElementById('logout-btn');

    // Tab Containers
    const tabOrders = document.getElementById('tab-orders-content');
    const tabProducts = document.getElementById('tab-products-content');
    const tabCustom = document.getElementById('tab-custom-content');
    const tabBtns = document.querySelectorAll('.admin-tab-btn');

    // Status Update Modal
    const orderModal = document.getElementById('orderModal');
    const updateOrderForm = document.getElementById('update-order-form');

    // Check Auth Status
    async function checkAuth() {
        const res = await PanduApi.checkAdminAuth();
        if (res.success && res.data && res.data.logged_in) {
            showDashboard();
        } else {
            showLogin();
        }
    }

    function showLogin() {
        if (loginSection) loginSection.classList.remove('hidden');
        if (dashboardSection) dashboardSection.classList.add('hidden');
        if (window.lucide) window.lucide.createIcons();
    }

    function showDashboard() {
        if (loginSection) loginSection.classList.add('hidden');
        if (dashboardSection) dashboardSection.classList.remove('hidden');
        loadDashboardData();
        if (window.lucide) window.lucide.createIcons();
    }

    // 1. Handle Login
    if (loginForm) {
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            if (loginError) loginError.classList.add('hidden');

            const email = loginForm.email.value.trim();
            const password = loginForm.password.value.trim();

            const res = await PanduApi.loginAdmin({ email, password });
            if (res.success) {
                showDashboard();
            } else {
                if (loginError) {
                    loginError.innerText = res.message || 'Login gagal. Periksa kembali email dan password.';
                    loginError.classList.remove('hidden');
                }
            }
        });
    }

    // 2. Handle Logout
    if (logoutBtn) {
        logoutBtn.addEventListener('click', async (e) => {
            e.preventDefault();
            await PanduApi.logoutAdmin();
            showLogin();
        });
    }

    // 3. Tab Switching
    tabBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const tabName = btn.getAttribute('data-tab');
            tabBtns.forEach(b => {
                b.className = 'admin-tab-btn px-5 py-2.5 rounded-xl transition ' + 
                    (b.getAttribute('data-tab') === tabName 
                        ? 'bg-amber-800 text-white font-bold' 
                        : 'bg-white text-stone-700 hover:bg-stone-100 border border-stone-200');
            });

            if (tabOrders) tabOrders.classList.toggle('hidden', tabName !== 'orders');
            if (tabProducts) tabProducts.classList.toggle('hidden', tabName !== 'products');
            if (tabCustom) tabCustom.classList.toggle('hidden', tabName !== 'custom');
        });
    });

    // 4. Load Dashboard Data (KPI, Orders, Products, Custom)
    async function loadDashboardData() {
        // Load Stats KPI
        try {
            const statsRes = await PanduApi.getAdminStats();
            if (statsRes.success && statsRes.data) {
                const s = statsRes.data;
                document.getElementById('stat-total-orders').innerText = s.total_orders;
                document.getElementById('stat-new-orders').innerText = s.new_orders;
                document.getElementById('stat-prod-orders').innerText = s.prod_orders;
                document.getElementById('stat-completed-orders').innerText = s.completed_orders;
                document.getElementById('stat-revenue').innerText = PanduApp.formatRupiah(s.total_revenue);
            }
        } catch (e) {
            console.error('Error loading stats:', e);
        }

        // Load Orders
        loadOrdersTable();
        // Load Products
        loadProductsTable();
        // Load Custom Requests
        loadCustomRequestsTable();
    }

    // Load Orders Table
    async function loadOrdersTable() {
        const tbody = document.getElementById('orders-table-body');
        if (!tbody) return;

        const res = await PanduApi.getAdminOrders();
        if (!res.success || !res.data) return;

        const orders = res.data.orders;
        document.getElementById('tab-orders-count').innerText = orders.length;

        if (orders.length === 0) {
            tbody.innerHTML = `<tr><td colspan="6" class="p-8 text-center text-stone-400">Belum ada pesanan masuk.</td></tr>`;
            return;
        }

        tbody.innerHTML = orders.map(o => {
            const sb = PanduApp.getStatusBadge(o.order_status);
            const pb = PanduApp.getPaymentBadge(o.payment_status);

            return `
                <tr class="hover:bg-stone-50/80 transition">
                    <td class="p-4">
                        <span class="font-mono font-bold text-stone-900 block">${o.order_number}</span>
                        <span class="text-[10px] text-stone-400">${o.created_at}</span>
                    </td>
                    <td class="p-4">
                        <span class="font-semibold text-stone-900 block">${o.customer_name}</span>
                        <span class="text-stone-500 block">${o.customer_phone}</span>
                        <span class="text-[11px] text-stone-400 block">${o.city}</span>
                    </td>
                    <td class="p-4">
                        <span class="font-bold text-stone-900 block">${PanduApp.formatRupiah(o.total_amount)}</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border ${pb.class} inline-block mt-1">
                            ${pb.label}
                        </span>
                    </td>
                    <td class="p-4">
                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold border ${sb.class} inline-block">
                            ${sb.label}
                        </span>
                    </td>
                    <td class="p-4">
                        <span class="font-mono text-stone-700 block">${o.tracking_number || '-'}</span>
                        <span class="text-[10px] text-stone-400 block">${o.shipping_method}</span>
                    </td>
                    <td class="p-4 text-center">
                        <button type="button" class="btn-edit-order bg-stone-900 hover:bg-amber-800 text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition"
                            data-order='${JSON.stringify(o).replace(/'/g, "&apos;")}'>
                            Ubah Status
                        </button>
                    </td>
                </tr>
            `;
        }).join('');

        // Attach modal triggers
        tbody.querySelectorAll('.btn-edit-order').forEach(btn => {
            btn.addEventListener('click', () => {
                const orderData = JSON.parse(btn.getAttribute('data-order'));
                openOrderModal(orderData);
            });
        });

        if (window.lucide) window.lucide.createIcons();
    }

    function openOrderModal(o) {
        if (!orderModal) return;
        document.getElementById('modal_order_id').value = o.id;
        document.getElementById('modal_order_number').value = o.order_number;
        document.getElementById('modal_order_status').value = o.order_status;
        document.getElementById('modal_payment_status').value = o.payment_status;
        document.getElementById('modal_tracking_number').value = o.tracking_number || '';
        document.getElementById('modal_admin_notes').value = o.admin_notes || '';
        orderModal.classList.remove('hidden');
    }

    window.closeEditModal = function() {
        if (orderModal) orderModal.classList.add('hidden');
    };

    // Handle update status form
    if (updateOrderForm) {
        updateOrderForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const payload = {
                order_id: parseInt(document.getElementById('modal_order_id').value, 10),
                order_status: document.getElementById('modal_order_status').value,
                payment_status: document.getElementById('modal_payment_status').value,
                tracking_number: document.getElementById('modal_tracking_number').value.trim(),
                admin_notes: document.getElementById('modal_admin_notes').value.trim()
            };

            const res = await PanduApi.updateOrderStatus(payload);
            if (res.success) {
                window.closeEditModal();
                loadOrdersTable();
                loadDashboardData();
            } else {
                alert(res.message || 'Gagal memperbarui status');
            }
        });
    }

    // Load Products Table
    async function loadProductsTable() {
        const tbody = document.getElementById('products-table-body');
        if (!tbody) return;

        const res = await PanduApi.getProducts();
        if (!res.success || !res.data) return;

        const products = res.data.products;
        document.getElementById('tab-products-count').innerText = products.length;

        tbody.innerHTML = products.map(pr => `
            <tr class="hover:bg-stone-50 transition">
                <td class="p-4 flex items-center gap-3">
                    <img src="${pr.image_url}" alt="" class="w-10 h-10 rounded-lg object-cover">
                    <div>
                        <span class="font-bold text-stone-900 block">${pr.name}</span>
                        <span class="text-[10px] text-stone-400">${pr.dimensions}</span>
                    </div>
                </td>
                <td class="p-4 text-stone-600">${pr.category_name || '-'}</td>
                <td class="p-4 font-bold text-stone-900">${PanduApp.formatRupiah(pr.price)}</td>
                <td class="p-4 font-semibold text-emerald-600">${pr.stock} unit</td>
                <td class="p-4 text-center">
                    <button type="button" class="btn-del-prod text-rose-600 hover:text-rose-800 p-1.5 rounded-lg hover:bg-rose-50 transition" data-id="${pr.id}">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </button>
                </td>
            </tr>
        `).join('');

        tbody.querySelectorAll('.btn-del-prod').forEach(btn => {
            btn.addEventListener('click', async () => {
                const id = btn.getAttribute('data-id');
                if (confirm('Yakin ingin menghapus produk ini?')) {
                    await PanduApi.deleteProduct(id);
                    loadProductsTable();
                }
            });
        });

        if (window.lucide) window.lucide.createIcons();
    }

    // Form Tambah Produk Baru
    const addProductForm = document.getElementById('add-product-form');
    if (addProductForm) {
        addProductForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const payload = {
                name: addProductForm.name.value.trim(),
                category_id: parseInt(addProductForm.category_id.value, 10),
                price: parseFloat(addProductForm.price.value),
                stock: parseInt(addProductForm.stock.value, 10),
                material: addProductForm.material.value.trim(),
                dimensions: addProductForm.dimensions.value.trim(),
                finishing: addProductForm.finishing.value.trim(),
                image_url: addProductForm.image_url.value.trim(),
                description: addProductForm.description.value.trim()
            };

            const res = await PanduApi.createProduct(payload);
            if (res.success) {
                addProductForm.reset();
                loadProductsTable();
                alert('Produk berhasil ditambahkan!');
            } else {
                alert(res.message || 'Gagal menambahkan produk');
            }
        });
    }

    // Load Custom Requests Table
    async function loadCustomRequestsTable() {
        const tbody = document.getElementById('custom-table-body');
        if (!tbody) return;

        const res = await PanduApi.getAdminCustomRequests();
        if (!res.success || !res.data) return;

        const requests = res.data.requests;
        document.getElementById('tab-custom-count').innerText = requests.length;

        if (requests.length === 0) {
            tbody.innerHTML = `<tr><td colspan="6" class="p-8 text-center text-stone-400">Belum ada request custom baru.</td></tr>`;
            return;
        }

        tbody.innerHTML = requests.map(cr => `
            <tr class="hover:bg-stone-50 transition">
                <td class="p-4 font-mono font-bold text-stone-900">${cr.request_number}</td>
                <td class="p-4">
                    <span class="font-bold text-stone-900 block">${cr.customer_name}</span>
                    <span class="text-stone-500 block">${cr.customer_phone}</span>
                </td>
                <td class="p-4">
                    <span class="font-semibold text-stone-900 block">${cr.furniture_type}</span>
                    <span class="text-stone-500 text-[10px]">${cr.wood_type}</span>
                </td>
                <td class="p-4">
                    <span class="text-stone-700 block">${cr.dimensions || '-'}</span>
                    <span class="text-amber-700 font-bold text-[11px]">${cr.budget_range || '-'}</span>
                </td>
                <td class="p-4 max-w-xs text-stone-600 truncate">${cr.description}</td>
                <td class="p-4 text-center">
                    <a href="https://wa.me/${cr.customer_phone.replace(/[^0-9]/g, '')}?text=Halo%20${encodeURIComponent(cr.customer_name)},%20kami%20dari%20Pandu%20Jati%20Furniture%20menanggapi%20request%20custom%20mebel%20Anda" target="_blank" class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded-lg font-semibold inline-flex items-center gap-1">
                        <i data-lucide="message-circle" class="w-3.5 h-3.5"></i> Balas WA
                    </a>
                </td>
            </tr>
        `).join('');

        if (window.lucide) window.lucide.createIcons();
    }

    // Inisialisasi Auth Check
    await checkAuth();
});
