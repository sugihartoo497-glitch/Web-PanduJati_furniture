-- Database: pandu_jati_db
CREATE DATABASE IF NOT EXISTS `pandu_jati_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `pandu_jati_db`;

-- Table: admin_users
CREATE TABLE IF NOT EXISTS `admin_users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `full_name` VARCHAR(100) NOT NULL,
  `role` VARCHAR(20) DEFAULT 'admin',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: categories
CREATE TABLE IF NOT EXISTS `categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(50) NOT NULL,
  `slug` VARCHAR(50) NOT NULL UNIQUE,
  `description` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: products
CREATE TABLE IF NOT EXISTS `products` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `category_id` INT,
  `name` VARCHAR(150) NOT NULL,
  `slug` VARCHAR(150) NOT NULL UNIQUE,
  `price` DECIMAL(12,2) NOT NULL,
  `original_price` DECIMAL(12,2) DEFAULT NULL,
  `description` TEXT NOT NULL,
  `material` VARCHAR(100) DEFAULT 'Kayu Jati Solid TPK Perhutani',
  `dimensions` VARCHAR(100) DEFAULT 'P 180 x L 90 x T 75 cm',
  `weight` VARCHAR(50) DEFAULT '50 kg',
  `finishing` VARCHAR(100) DEFAULT 'Natural Teak Melamine Doff / Salak Brown',
  `lead_time` VARCHAR(50) DEFAULT '7 - 14 Hari Kerja',
  `stock` INT DEFAULT 5,
  `image_url` VARCHAR(255) NOT NULL,
  `is_featured` TINYINT(1) DEFAULT 0,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: orders
CREATE TABLE IF NOT EXISTS `orders` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_number` VARCHAR(30) NOT NULL UNIQUE,
  `customer_name` VARCHAR(100) NOT NULL,
  `customer_phone` VARCHAR(30) NOT NULL,
  `customer_email` VARCHAR(100) NOT NULL,
  `address` TEXT NOT NULL,
  `city` VARCHAR(100) NOT NULL,
  `province` VARCHAR(100) NOT NULL,
  `postal_code` VARCHAR(10),
  `shipping_method` VARCHAR(100) NOT NULL,
  `shipping_cost` DECIMAL(12,2) DEFAULT 0,
  `payment_method` VARCHAR(100) NOT NULL,
  `subtotal` DECIMAL(12,2) NOT NULL,
  `total_amount` DECIMAL(12,2) NOT NULL,
  `order_status` ENUM('PENDING','CONFIRMED','PROCESSING','PRODUCTION','QUALITY_CHECK','READY_TO_SHIP','SHIPPED','COMPLETED','CANCELLED') DEFAULT 'PENDING',
  `payment_status` ENUM('UNPAID','DP','PAID','REFUNDED') DEFAULT 'UNPAID',
  `customer_notes` TEXT,
  `tracking_number` VARCHAR(100) DEFAULT NULL,
  `admin_notes` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: order_items
CREATE TABLE IF NOT EXISTS `order_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT NOT NULL,
  `product_id` INT,
  `product_name` VARCHAR(150) NOT NULL,
  `product_price` DECIMAL(12,2) NOT NULL,
  `quantity` INT NOT NULL DEFAULT 1,
  `finishing_choice` VARCHAR(100) DEFAULT 'Natural Teak Doff',
  `subtotal` DECIMAL(12,2) NOT NULL,
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: order_status_history
CREATE TABLE IF NOT EXISTS `order_status_history` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT NOT NULL,
  `status` VARCHAR(50) NOT NULL,
  `note` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: custom_requests
CREATE TABLE IF NOT EXISTS `custom_requests` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `request_number` VARCHAR(30) NOT NULL UNIQUE,
  `customer_name` VARCHAR(100) NOT NULL,
  `customer_phone` VARCHAR(30) NOT NULL,
  `customer_email` VARCHAR(100) NOT NULL,
  `furniture_type` VARCHAR(100) NOT NULL,
  `dimensions` VARCHAR(100) NOT NULL,
  `wood_type` VARCHAR(100) NOT NULL,
  `finishing` VARCHAR(100) NOT NULL,
  `description` TEXT NOT NULL,
  `reference_image` VARCHAR(255) DEFAULT NULL,
  `budget_range` VARCHAR(100) DEFAULT NULL,
  `status` ENUM('PENDING','REVIEWING','QUOTED','APPROVED','CANCELLED') DEFAULT 'PENDING',
  `estimated_price` DECIMAL(12,2) DEFAULT NULL,
  `admin_reply` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed Admin User: admin@pandujati.com / admin123
INSERT INTO `admin_users` (`username`, `email`, `password`, `full_name`, `role`)
VALUES ('admin', 'admin@pandujati.com', '$2y$10$w3U69xG6vO9UoQ9i6xTzEO0aLp9G9b2Y7uN8oK.eP5A8B/qj1yK/O', 'Administrator Pandu Jati', 'admin')
ON DUPLICATE KEY UPDATE `full_name` = VALUES(`full_name`);

-- Seed Categories
INSERT INTO `categories` (`name`, `slug`, `description`) VALUES
('Meja', 'meja', 'Meja makan, meja tamu, meja kerja, dan console kayu jati solid'),
('Kursi', 'kursi', 'Kursi makan, kursi santai, sofa kayu, dan bangku teras jati'),
('Lemari', 'lemari', 'Lemari pakaian, buffet, lemari pajangan kaca, dan credenza'),
('Dipan', 'dipan', 'Tempat tidur kayu jati minimalis dan ukir dengan konstruksi kokoh'),
('Rak', 'rak', 'Rak buku, rak display, rak partisi ruangan, dan rak dinding kayu'),
('Kitchen Set', 'kitchen-set', 'Kitchen set kayu jati perhutani tahan rayap dan lembab'),
('Outdoor Furniture', 'outdoor-furniture', 'Meja dan kursi taman tahan cuaca dari kayu jati tebal')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- Seed Products
INSERT INTO `products` (`category_id`, `name`, `slug`, `price`, `original_price`, `description`, `material`, `dimensions`, `weight`, `finishing`, `lead_time`, `stock`, `image_url`, `is_featured`, `is_active`) VALUES
(1, 'Meja Makan Jati Minimalis Solid 6 Kursi', 'meja-makan-jati-minimalis-solid-6-kursi', 8500000, 9800000, 'Set meja makan kayu jati TPK Perhutani grade A dengan 6 kursi ergonomis. Top table tebal 4cm utuh tanpa sambungan berlebih, finishing melamine doff natural tahan gores dan air panas.', 'Kayu Jati Solid TPK Perhutani', 'P 180 x L 90 x T 75 cm', '75 kg', 'Natural Teak Melamine Doff', '7 - 14 Hari Kerja', 4, 'https://images.unsplash.com/photo-1615066390971-03e4e1c36ddf?auto=format&fit=crop&w=800&q=80', 1, 1),
(2, 'Kursi Santai Jati Retro Scandinavian', 'kursi-santai-jati-retro-scandinavian', 2450000, 2900000, 'Armchair kayu jati gaya modern scandinavian dengan bantalan busa royal foam dibalut kain woven premium tebal. Rangka purus pasak kokoh tidak mudah goyang.', 'Kayu Jati Solid Grade A', 'P 70 x L 75 x T 82 cm', '16 kg', 'Walnut Brown Matte', '5 - 10 Hari Kerja', 8, 'https://images.unsplash.com/photo-1580481077114-1e037b51b0f5?auto=format&fit=crop&w=800&q=80', 1, 1),
(3, 'Lemari Pakaian Jati 3 Pintu Sliding', 'lemari-pakaian-jati-3-pintu-sliding', 12500000, 14000000, 'Lemari baju 3 pintu model sliding minimalis hemat ruangan. Bagian dalam dilengkapi gantungan jas panjang, rak susun lapang, dan 2 laci perhiasan dengan kunci pengaman.', 'Kayu Jati Perhutani Pilihan', 'P 160 x L 58 x T 210 cm', '110 kg', 'Salak Brown Semi Gloss', '14 - 21 Hari Kerja', 3, 'https://images.unsplash.com/photo-1595428774223-ef52624120d2?auto=format&fit=crop&w=800&q=80', 1, 1),
(4, 'Dipan Tempat Tidur Jati King Size 180x200', 'dipan-tempat-tidur-jati-king-size-180x200', 7900000, 8900000, 'Rangka dipan kayu jati minimalis ukuran King Size (180x200 cm). Dilengkapi headboard empuk dan konstruksi galaran kayu jati rapat anti decit saat berbaring.', 'Kayu Jati Solid Anti Hama', 'P 210 x L 190 x T 100 cm', '85 kg', 'Bleaching Natural Teak Doff', '10 - 15 Hari Kerja', 5, 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=800&q=80', 1, 1),
(5, 'Rak Buku Partisi Jati Multifungsi', 'rak-buku-partisi-jati-multifungsi', 4200000, 4800000, 'Rak display kayu jati model asimetris modern yang berfungsi ganda sebagai penyekat ruangan (room divider). Cocok untuk pajangan buku, vas bunga, dan miniatur hias.', 'Kayu Jati Solid Pilihan', 'P 120 x L 35 x T 185 cm', '38 kg', 'Natural Doff', '7 - 12 Hari Kerja', 6, 'https://images.unsplash.com/photo-1594938298603-c8148c4dae35?auto=format&fit=crop&w=800&q=80', 1, 1),
(6, 'Set Kitchen Set Jati Custom Modular', 'set-kitchen-set-jati-custom-modular', 28500000, 32000000, 'Kitchen set dapur atas & bawah berbahan full kayu jati solid anti air dan anti rayap. Menggunakan engsel soft-close stainless steel dan rel laci tandem tahan karat.', 'Full Kayu Jati Solid & Top Table Granite', 'Sesuai Ukuran Ruang Dapur', '200 kg', 'Duco Kombinasi Natural Jati', '21 - 30 Hari Kerja', 2, 'https://images.unsplash.com/photo-1556911220-e15b29be8c8f?auto=format&fit=crop&w=800&q=80', 1, 1),
(1, 'Meja Tamu Jati Live Edge Resin River', 'meja-tamu-jati-live-edge-resin-river', 5800000, 6500000, 'Meja kopi kayu jati erosi alami dipadukan dengan sentuhan resin hitam elegan. Kaki besi hollow powder coating hitam doff industrial yang kokoh.', 'Kayu Jati Erosi Alami & Besi Baja', 'P 120 x L 65 x T 45 cm', '32 kg', 'Polyurethane Clear Doff', '10 - 14 Hari Kerja', 3, 'https://images.unsplash.com/photo-1533090161767-e6ffed986c88?auto=format&fit=crop&w=800&q=80', 1, 1),
(7, 'Set Meja Kursi Taman Outdoor Jati Lipat', 'set-meja-kursi-taman-outdoor-jati-lipat', 3600000, 4100000, 'Set outdoor meja bundar lipat diameter 100cm dengan 4 kursi lipat jati. Diberi lapisan Teak Oil khusus outdoor tahan sengatan matahari dan air hujan lebat.', 'Kayu Jati Perhutani Outdoor Grade', 'Meja Dia 100 x T 75 cm', '42 kg', 'Teak Oil Natural Protection', '5 - 10 Hari Kerja', 7, 'https://images.unsplash.com/photo-1519710164239-da123dc03ef4?auto=format&fit=crop&w=800&q=80', 1, 1)
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- Seed Orders
INSERT INTO `orders` (`order_number`, `customer_name`, `customer_phone`, `customer_email`, `address`, `city`, `province`, `postal_code`, `shipping_method`, `shipping_cost`, `payment_method`, `subtotal`, `total_amount`, `order_status`, `payment_status`, `customer_notes`, `tracking_number`, `admin_notes`)
VALUES ('PJF-20261008-0001', 'Bambang Sudirman', '081298765432', 'bambang.s@gmail.com', 'Jl. Kemang Raya No. 42, Bangka, Mampang Prapatan', 'Jakarta Selatan', 'DKI Jakarta', '12730', 'Truk Ekspedisi Khusus Mebel Jepara', 250000, 'Transfer Bank BCA (DP 50%)', 8500000, 8750000, 'PRODUCTION', 'DP', 'Tolong kayu jatinya dipilihkan yang serat lurus dan mulus ya Pak.', 'RESI-JEPARA-IND-88219', 'Kayu sudah selesai serut & potong presisi, saat ini masuk tahap perakitan purus pasak.')
ON DUPLICATE KEY UPDATE `customer_name` = VALUES(`customer_name`);

-- Seed Order Items
INSERT INTO `order_items` (`order_id`, `product_id`, `product_name`, `product_price`, `quantity`, `finishing_choice`, `subtotal`)
SELECT o.id, p.id, p.name, p.price, 1, 'Natural Teak Melamine Doff', 8500000
FROM `orders` o, `products` p
WHERE o.order_number = 'PJF-20261008-0001' AND p.slug = 'meja-makan-jati-minimalis-solid-6-kursi'
LIMIT 1;

-- Seed Order History
INSERT INTO `order_status_history` (`order_id`, `status`, `note`)
SELECT o.id, 'PENDING', 'Pesanan berhasil dibuat oleh pelanggan'
FROM `orders` o WHERE o.order_number = 'PJF-20261008-0001';

INSERT INTO `order_status_history` (`order_id`, `status`, `note`)
SELECT o.id, 'CONFIRMED', 'DP 50% berhasil diverifikasi via Bank BCA'
FROM `orders` o WHERE o.order_number = 'PJF-20261008-0001';

INSERT INTO `order_status_history` (`order_id`, `status`, `note`)
SELECT o.id, 'PRODUCTION', 'Material kayu jati grade A disiapkan, masuk tahap konstruksi perakitan pengrajin'
FROM `orders` o WHERE o.order_number = 'PJF-20261008-0001';
