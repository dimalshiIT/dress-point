
-- Dress Point Basic - Database Schema & Sample Records
-- Database Name: dresspoint_basic_db
-- Import directly in phpMyAdmin at http://localhost/phpmyadmin


CREATE DATABASE IF NOT EXISTS `dresspoint_basic_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `dresspoint_basic_db`;

-- Drop existing tables in reverse dependency order
DROP TABLE IF EXISTS `order_items`;
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `messages`;
DROP TABLE IF EXISTS `admins`;

-- 1. Categories Table
CREATE TABLE `categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(100) NOT NULL UNIQUE,
  `image` VARCHAR(255) DEFAULT '',
  `description` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Products Table
CREATE TABLE `products` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `category_id` INT NOT NULL,
  `name` VARCHAR(200) NOT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  `sizes` VARCHAR(150) NOT NULL DEFAULT 'Free Size',
  `image` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `is_featured` TINYINT(1) DEFAULT 0,
  `stock` INT DEFAULT 15,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Orders Table
CREATE TABLE `orders` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_number` VARCHAR(50) NOT NULL UNIQUE,
  `customer_name` VARCHAR(150) NOT NULL,
  `customer_phone` VARCHAR(50) NOT NULL,
  `shipping_address` TEXT NOT NULL,
  `customer_email` VARCHAR(150) DEFAULT '',
  `city` VARCHAR(100) DEFAULT '',
  `postal_code` VARCHAR(20) DEFAULT '',
  `payment_method` VARCHAR(50) NOT NULL DEFAULT 'Cash on Delivery',
  `total_amount` DECIMAL(10,2) NOT NULL,
  `status` ENUM('Pending', 'Processing', 'Shipped', 'Delivered') NOT NULL DEFAULT 'Pending',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Order Items Table
CREATE TABLE `order_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT NOT NULL,
  `product_id` INT DEFAULT NULL,
  `product_name` VARCHAR(200) NOT NULL,
  `size` VARCHAR(50) DEFAULT 'Standard',
  `quantity` INT NOT NULL DEFAULT 1,
  `unit_price` DECIMAL(10,2) NOT NULL,
  `subtotal` DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Customer Messages Table
CREATE TABLE `messages` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `phone` VARCHAR(50) DEFAULT '',
  `subject` VARCHAR(200) NOT NULL,
  `message` TEXT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Admins Table
CREATE TABLE `admins` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `full_name` VARCHAR(100) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert Default Admin (Username: admin | Password: admin123)
INSERT INTO `admins` (`id`, `username`, `password_hash`, `full_name`) VALUES
(1, 'admin', '$2y$10$b0Yhl64zGZNMW1BrcycqnOrvIuqDr99TjNr7TmA.u3LcSgTmhNDra', 'Store Administrator');

-- Insert 12 Categories
INSERT INTO `categories` (`id`, `name`, `slug`, `image`, `description`) VALUES
(1, 'Heels', 'heels', 'images/heels/heels-1.jpg', 'Stunning designer stilettos, transparent pumps and elegant party heels.'),
(2, 'Shoes', 'shoes', 'images/shoes/shoes-1.jpg', 'Premium leather loafers, casual sneakers, and chic flat sandals.'),
(3, 'Gents Wear', 'gents-wear', 'images/gents-wear/gents-wear-1.jpg', 'Tailored dress shirts, crisp pinstripe button-downs and resort linen shirts.'),
(4, 'Gents T-Shirts', 'gents-tshirts', 'images/gents-tshirts/gents-tshirts-1.jpg', 'Knit polos, retro colorblock tees and streetwear raglan t-shirts.'),
(5, 'Gents Trousers', 'gents-trousers', 'images/gents-trousers/gents-trousers-1.jpg', 'Pleated formal trousers, washed grey denim and relaxed drawstring slacks.'),
(6, 'Ladies Trousers', 'ladies-trousers', 'images/ladies-trousers/ladies-trousers-1.jpg', 'High-waist belted wide-leg trousers, floral embroidered denim and flare jeans.'),
(7, 'Ladies Blouses', 'ladies-blouses', 'images/ladies-blouses/ladies-blouses-1.jpg', 'Elegant split-sleeve tops, peplum blouses, corset shirts and milkmaid tops.'),
(8, 'T-Shirts', 'tshirts', 'images/tshirts/tshirts-1.jpg', 'Trendy unisex graphic t-shirts and daily casual comfort tees.'),
(9, 'Accessories', 'accessories', 'images/accessories/accessories-1.jpg', 'Gold chokers, butterfly ear cuffs, designer sunglasses, bags and rings.'),
(10, 'Dresses & Gowns', 'dresses-gowns', 'images/dresses-gowns/dresses-gowns-1.jpg', 'Breathtaking corset evening gowns, tiered lace dresses and summer maxis.'),
(11, 'Sarees', 'sarees', 'images/sarees/sarees-1.jpg', 'Luxurious party sarees, crystal embroidered drapes and organza designer sarees.'),
(12, 'Kids Wear', 'kids-wear', 'images/kids-wear/kids-wear-1.jpg', 'Adorable toddler suspender sets, denim dresses, overalls and party wear.');

-- Insert Products (96 Catalog Items)
INSERT INTO `products` (`id`, `category_id`, `name`, `price`, `sizes`, `image`, `description`, `is_featured`, `stock`, `created_at`) VALUES
(1, 1, 'Cinderella Crystal Bow Stiletto Heels', 8900.00, '36, 37, 38, 39, 40', 'images/heels/heels-1.jpg', 'Transparent stiletto pumps adorned with an exquisite sparkling crystal bow and pointed toe.', 1, 13, NOW()),
(2, 1, 'Red-Sole White Slingback Pointed Heels', 9500.00, '36, 37, 38, 39, 40', 'images/heels/heels-2.jpg', 'Iconic white patent slingback pumps featuring signature red contrast soles and sleek stiletto heel.', 1, 24, NOW()),
(3, 1, 'Ivory Pointed Pumps with Pearl Collar', 8400.00, '36, 37, 38, 39, 40', 'images/heels/heels-3.jpg', 'Chic cream bridal heels featuring an elegant gold ankle bangle with pearl drop accents.', 0, 10, NOW()),
(4, 1, 'Daffodil Yellow Ruffled Ankle Wrap Heels', 7800.00, '36, 37, 38, 39', 'images/heels/heels-4.jpg', 'Eye-catching pastel yellow stiletto sandals with delicate chiffon flower ruffles and wrap straps.', 0, 22, NOW()),
(5, 1, 'Midnight Black Double-Bow Satin Heels', 10200.00, '36, 37, 38, 39, 40', 'images/heels/heels-5.jpg', 'Luxury black satin pointed-toe stilettos wrapped in glittering crystal double-bow embellishments.', 1, 11, NOW()),
(6, 2, 'Classic Mahogany Brown Horsebit Loafers', 7200.00, '40, 41, 42, 43, 44', 'images/shoes/shoes-1.jpg', 'Handcrafted burnished brown leather penny loafers finished with vintage brass horsebit hardware.', 1, 25, NOW()),
(7, 2, 'Urban Cream & Mocha Chunky Platform Sneakers', 6800.00, '38, 39, 40, 41, 42', 'images/shoes/shoes-2.jpg', 'Trendy two-tone street sneaker with cushioned platform sole and breathable layered upper.', 1, 11, NOW()),
(8, 2, 'Beige All-Terrain Chunky Trainer Shoes', 6900.00, '38, 39, 40, 41, 42', 'images/shoes/shoes-3.jpg', 'Sporty trail-inspired running sneakers with sculpted grip soles and neutral monochrome finish.', 0, 16, NOW()),
(9, 2, 'Burnished Walnut Double Monk-Strap Lug Sole Shoes', 8500.00, '40, 41, 42, 43, 44', 'images/shoes/shoes-4.jpg', 'Modern formal monk shoes with dual silver buckles and heavy-duty lugged commando outsole.', 1, 21, NOW()),
(10, 2, 'Suede Taupe Buckled Boston Clogs', 5400.00, '37, 38, 39, 40, 41', 'images/shoes/shoes-5.jpg', 'Effortless casual slip-on clogs crafted from soft brushed suede on ergonomic cork footbeds.', 0, 19, NOW()),
(11, 2, 'White & Crimson Retro Low-Top Skate Sneakers', 5900.00, '40, 41, 42, 43, 44', 'images/shoes/shoes-6.jpg', 'Classic retro streetwear court sneakers in clean white leather with dark wine red accents.', 0, 12, NOW()),
(12, 2, 'Rose Gold Crystal Toe-Ring Flat Slides', 3900.00, '36, 37, 38, 39, 40', 'images/shoes/shoes-7.jpg', 'Comfortable summer slide sandals with shimmering crystal floral brooch and metallic strap.', 0, 10, NOW()),
(13, 2, 'Blush Pink Floral Appliqué Flat Sandals', 3600.00, '36, 37, 38, 39', 'images/shoes/shoes-8.jpg', 'Dainty summer slip-ons decorated with handcrafted crochet blossom petals and slim straps.', 0, 11, NOW()),
(14, 2, 'Cyan Orchid Flower Thong Sandals', 3700.00, '36, 37, 38, 39, 40', 'images/shoes/shoes-9.jpg', 'Tropical beach thong sandals in vibrant turquoise blue featuring realistic 3D orchid flowers.', 0, 17, NOW()),
(15, 3, 'Midnight Navy Slim-Fit Tailored Dress Shirt', 4800.00, 'S, M, L, XL, XXL', 'images/gents-wear/gents-wear-1.jpg', 'Crisp wrinkle-resistant cotton formal shirt in deep navy with semi-spread collar.', 1, 20, NOW()),
(16, 3, 'Pitch Black Formal Muscle-Fit Button Down', 4800.00, 'S, M, L, XL, XXL', 'images/gents-wear/gents-wear-2.jpg', 'Sophisticated pure black formal shirt with tapered waist and breathable stretch cotton blend.', 1, 8, NOW()),
(17, 3, 'Black Pinstripe Formal Long-Sleeve Shirt', 5200.00, 'M, L, XL, XXL', 'images/gents-wear/gents-wear-3.jpg', 'Italian style vertical pinstripe dress shirt with embroidered chest crest and double-button cuffs.', 0, 21, NOW()),
(18, 3, 'Warm Sand Striped Italian Business Shirt', 5200.00, 'S, M, L, XL', 'images/gents-wear/gents-wear-4.jpg', 'Beige and white fine stripe formal shirt tailored for business wear and warm evening dinners.', 0, 24, NOW()),
(19, 3, 'Natural Grey Linen Striped Resort Shirt', 4500.00, 'M, L, XL', 'images/gents-wear/gents-wear-5.jpg', 'Relaxed breathable pure linen long-sleeve shirt with vintage stripe texture and chest pocket.', 0, 9, NOW()),
(20, 3, 'Sage Green Bold Stripe Vacation Linen Shirt', 4600.00, 'M, L, XL, XXL', 'images/gents-wear/gents-wear-6.jpg', 'Wide striped summer beach shirt in olive sage green with button front and roll-tab sleeves.', 0, 12, NOW()),
(21, 3, 'Earth-Tone Relaxed Fit Striped Linen Shirt', 4700.00, 'M, L, XL, XXL', 'images/gents-wear/gents-wear-7.jpg', 'Casual luxury olive-khaki vertical stripe linen shirt perfect for outdoor events and resort trips.', 0, 25, NOW()),
(22, 3, 'Burgundy Ribbed Knit Long-Sleeve Polo', 5600.00, 'S, M, L, XL', 'images/gents-wear/gents-wear-8.jpg', 'Luxury open-collar ribbed knit polo sweater in rich wine red, ideal for semi-formal styling.', 1, 11, NOW()),
(23, 4, 'Almond Cream Textured Knit Polo T-Shirt', 4200.00, 'S, M, L, XL', 'images/gents-tshirts/gents-tshirts-1.jpg', 'Premium short-sleeve knit Johnny collar polo in warm cream with ribbed hem and cuffs.', 1, 23, NOW()),
(24, 4, 'Retro Colorblock Half-Zip Ribbed Knit Polo', 4500.00, 'M, L, XL', 'images/gents-tshirts/gents-tshirts-2.jpg', 'Vintage-inspired vertical stripe ribbed polo with silver zip closure and contrast collar.', 1, 24, NOW()),
(25, 4, 'Mocha & Cream Oversized Raglan Long-Sleeve Tee', 3800.00, 'S, M, L, XL', 'images/gents-tshirts/gents-tshirts-3.jpg', 'Relaxed streetwear heavyweight cotton t-shirt with two-tone raglan sleeves and typography print.', 0, 19, NOW()),
(26, 5, 'Navy Pinstripe Pleated Gurkha Trousers', 5900.00, '30, 32, 34, 36', 'images/gents-trousers/gents-trousers-1.jpg', 'Bespoke high-waisted pleated dress trousers with extended buckle tab and tapered leg.', 1, 15, NOW()),
(27, 5, 'Chocolate Brown Baggy Wide-Leg Sweatpants', 4200.00, 'S, M, L, XL', 'images/gents-trousers/gents-trousers-2.jpg', 'Ultra-cozy heavyweight fleece track pants with elastic drawstring waist and street drape.', 0, 12, NOW()),
(28, 5, 'Washed Charcoal Grey Skater Baggy Jeans', 5400.00, '30, 32, 34, 36', 'images/gents-trousers/gents-trousers-3.jpg', '90s vintage wash relaxed straight-leg denim jeans with white drawstring cord.', 0, 16, NOW()),
(29, 5, 'Espresso Brown Double-Pleated Tailored Slacks', 6200.00, '30, 32, 34, 36', 'images/gents-trousers/gents-trousers-4.jpg', 'Modern tailoring trousers in rich mocha brown with side adjusters and pressed creases.', 1, 22, NOW()),
(30, 6, 'Charcoal Heather Slim-Fit Office Trousers', 4600.00, 'S, M, L, XL', 'images/ladies-trousers/ladies-trousers-1.jpg', 'Professional wool-blend slim tailored dress trousers for women with clean front crease.', 1, 9, NOW()),
(31, 6, 'High-Rise Super-Wide Bell Bottom Denim Jeans', 5800.00, '26, 28, 30, 32', 'images/ladies-trousers/ladies-trousers-2.jpg', 'Vintage medium blue wash flare jeans with fitted hips and exaggerated wide leg drape.', 1, 13, NOW()),
(32, 6, 'Jet Black Belted Palazzo Wide-Leg Trousers', 5400.00, 'S, M, L, XL', 'images/ladies-trousers/ladies-trousers-3.jpg', 'High-waist pleated formal trousers complete with gold buckle belt and dramatic palazzo cut.', 0, 17, NOW()),
(33, 6, 'Chocolate Brown High-Waisted Belted Slacks', 5600.00, 'S, M, L, XL', 'images/ladies-trousers/ladies-trousers-4.jpg', 'Elegantly draped wide trousers in rich brown featuring dual front pleats and matching belt.', 0, 19, NOW()),
(34, 6, 'Light Indigo Raw-Hem Extra Wide Flare Jeans', 5500.00, '26, 28, 30, 32', 'images/ladies-trousers/ladies-trousers-5.jpg', 'Retro sky blue wash flare denim with frayed raw hemline and high-rise waistband.', 0, 13, NOW()),
(35, 6, 'Cherry Blossom Embroidered White Denim Jeans', 6200.00, '26, 28, 30, 32', 'images/ladies-trousers/ladies-trousers-6.jpg', 'Pure white relaxed wide-leg jeans decorated with exquisite pink cherry blossom embroidery.', 1, 16, NOW()),
(36, 7, 'Navy Cut-Out Split-Sleeve Designer Blouse', 4200.00, 'S, M, L, XL', 'images/ladies-blouses/ladies-blouses-1.jpg', 'Elegantly tailored dark blue blouse featuring laser-cut eyelet embroidery on split cape sleeves.', 1, 19, NOW()),
(37, 7, 'Burgundy Fine-Stripe Peplum Collared Blouse', 4400.00, 'S, M, L, XL', 'images/ladies-blouses/ladies-blouses-2.jpg', 'Hourglass fitted pinstripe blouse with fluted peplum hem and button-front collared neckline.', 1, 17, NOW()),
(38, 7, 'Fuchsia Pink Pleated Ruffle Tiered Peplum Top', 3900.00, 'S, M, L', 'images/ladies-blouses/ladies-blouses-3.jpg', 'Dramatic pleated chiffon party top in vivid magenta pink with tiered flutter sleeves.', 0, 15, NOW()),
(39, 7, 'Cornflower Blue Milkmaid Lantern-Sleeve Blouse', 4100.00, 'S, M, L, XL', 'images/ladies-blouses/ladies-blouses-4.jpg', 'Romantic textured crinkle blouse with front button detail, tie string and smocked cuffs.', 0, 22, NOW()),
(40, 7, 'Midnight Black Sheer-Sleeve Tailored Waistcoat Top', 4600.00, 'S, M, L, XL', 'images/ladies-blouses/ladies-blouses-5.jpg', 'Sharp tailored pointed-hem vest blouse accented with romantic sheer chiffon bishop sleeves.', 0, 25, NOW()),
(41, 7, 'Blush Pink Striped Corset-Back Tailored Shirt', 4500.00, 'S, M, L', 'images/ladies-blouses/ladies-blouses-6.jpg', 'Slim feminine striped button-up shirt with adjustable corset lace-up detailing at the back.', 0, 25, NOW()),
(42, 8, 'Urban Minimalist Oversized Crewneck Tee', 2800.00, 'S, M, L, XL', 'images/tshirts/tshirts-1.jpg', 'Classic heavy-cotton daily essential t-shirt designed for all-day comfort and casual style.', 0, 12, NOW()),
(43, 8, 'Vintage Washed Acid Drop Shoulder T-Shirt', 3200.00, 'M, L, XL', 'images/tshirts/tshirts-2.jpg', 'Distressed soft cotton unisex crewneck with raw finished cuffs and streetwear boxy fit.', 0, 22, NOW()),
(44, 8, 'Dress Point Signature Monogram Cotton Tee', 3100.00, 'S, M, L, XL', 'images/tshirts/tshirts-3.jpg', 'Signature premium 240GSM cotton t-shirt with subtle tonal chest logo embroidery.', 1, 11, NOW()),
(45, 8, 'Waffle-Knit Breathable Everyday T-Shirt', 3400.00, 'S, M, L, XL', 'images/tshirts/tshirts-4.jpg', 'Lightweight waffle textured t-shirt with ribbed collar, ideal for casual layering.', 0, 19, NOW()),
(46, 9, 'Gold Triple-Layer Butterfly Charm Anklet', 1800.00, 'Free Size', 'images/accessories/accessories-1.jpg', 'Delicate gold-plated ankle chain set featuring dangling butterfly charms and figaro links.', 1, 14, NOW()),
(47, 9, 'Dainty Bow Crystal Bead Gold Anklet', 1600.00, 'Free Size', 'images/accessories/accessories-2.jpg', 'Minimalist gold beaded satellite chain anklet with sparkling pavé crystal bow centerpiece.', 0, 14, NOW()),
(48, 9, 'Vintage Oval Gold-Tone Sunglasses', 2800.00, 'Standard', 'images/accessories/accessories-3.jpg', 'Retro 90s aesthetic oval sunglasses with polished gold metal rims and dark UV lenses.', 0, 15, NOW()),
(49, 9, 'Pavé Crystal Serpent Ear Wrap Cuff', 2200.00, 'Free Size', 'images/accessories/accessories-4.jpg', 'Intricate winding snake ear wrap encrusted with micro-pavé zirconia crystals in rose gold.', 0, 16, NOW()),
(50, 9, 'Modernist Gold Teardrop Choker & Ring Set', 3600.00, 'Free Size', 'images/accessories/accessories-5.jpg', 'Statement sculpted gold-tone open collar necklace with matching chunky teardrop ring.', 1, 11, NOW()),
(51, 9, 'Spiral Gold Ribbon Dangle Earrings', 1900.00, 'Free Size', 'images/accessories/accessories-6.jpg', 'Flowing architectural spiral ribbon drop earrings with delicate chain fringe.', 0, 15, NOW()),
(52, 9, 'Golden Butterfly Piercing Chain Earring Set', 2100.00, 'Free Size', 'images/accessories/accessories-7.jpg', 'Multi-level butterfly stud earrings connected by draped dainty gold curb chains.', 0, 10, NOW()),
(53, 9, 'Silver Crystal Flutter Butterfly Ear Wrap', 2400.00, 'Free Size', 'images/accessories/accessories-8.jpg', 'Non-pierced cuff wrap featuring brilliant marquise butterflies and dangling chain tassels.', 0, 25, NOW()),
(54, 9, 'Open Heart Torc Choker & Stud Earrings Set', 3200.00, 'Free Size', 'images/accessories/accessories-9.jpg', 'Minimalist polished gold open neck torc featuring heart contour and matching heart studs.', 1, 22, NOW()),
(55, 9, '4-Tier Gold Butterfly & Initial Medallion Necklace', 2900.00, 'Adjustable', 'images/accessories/accessories-10.jpg', 'Layered Cuban and figaro chain necklace with sparkling butterfly and crystal tag pendants.', 0, 20, NOW()),
(56, 9, 'Pearlescent Marble Heart Minaudière Evening Bag', 6500.00, 'One Size', 'images/accessories/accessories-11.jpg', 'Sculpted pearl swirl heart-shaped hard shell clutch with detachable pearl crossbody strap.', 1, 12, NOW()),
(57, 9, 'Glitter Cherry Charm Crescent Shoulder Bag', 5200.00, 'One Size', 'images/accessories/accessories-12.jpg', 'Chic off-white half-moon bag with chunky gold hardware and ruby glitter double cherries.', 0, 20, NOW()),
(58, 9, 'Ivory Braided Handle Bow Charm Handbag', 5800.00, 'One Size', 'images/accessories/accessories-13.jpg', 'Soft cream vegan leather purse featuring braided top handle and satin ribbon bow accent.', 0, 10, NOW()),
(59, 9, 'Crystal Butterfly Connected Double-Finger Ring', 1700.00, 'Adjustable', 'images/accessories/accessories-14.jpg', 'Silver-tone double ring with sparkling marquise crystal butterfly joined by a dainty chain.', 0, 11, NOW()),
(60, 9, 'Sculptural Abstract Gold Open Ring Trio', 2300.00, 'Adjustable', 'images/accessories/accessories-15.jpg', 'Set of three chunky teardrop wrap rings in 18k gold plating with modern organic shapes.', 0, 23, NOW()),
(61, 9, '22-Piece Bohemian Gold & Crystal Midi Ring Stack', 2600.00, 'Mixed Sizes', 'images/accessories/accessories-16.jpg', 'Extensive set of celestial, heart, chevron and crystal-encrusted knuckle stacking rings.', 0, 18, NOW()),
(62, 9, 'Enamel Lily of the Valley Pearl Hair Claw', 2100.00, 'One Size', 'images/accessories/accessories-17.jpg', 'Handmade pastel pink bellflower hair clip adorned with gold vine accents and pearl tassels.', 0, 21, NOW()),
(63, 9, 'Neutral Matte Rectangle Hair Claw 4-Pack', 2200.00, 'Large', 'images/accessories/accessories-18.jpg', 'Non-slip strong grip hair claw clips in matte cream, latte, espresso and black.', 0, 12, NOW()),
(64, 9, 'Aesthetic Korean Hair Claw & Clip Set', 2800.00, 'Assorted', 'images/accessories/accessories-19.jpg', 'Assorted floral, butterfly and plush shearling hair accessories in warm neutral tones.', 0, 20, NOW()),
(65, 9, 'Mirror-Gold Sculpted Loop Handle Clutch', 6800.00, 'One Size', 'images/accessories/accessories-20.jpg', 'High-fashion metallic golden statement clutch with circular integrated handle.', 1, 12, NOW()),
(66, 10, 'Mediterranean Blue Floral Cold-Shoulder Maxi Dress', 6900.00, 'S, M, L, XL', 'images/dresses-gowns/dresses-gowns-1.jpg', 'Vibrant cobalt floral print chiffon dress with ruffle cold shoulders and high-low ruffle hem.', 1, 10, NOW()),
(67, 10, 'Sage Green Boned Corset Slit Evening Gown', 14500.00, 'S, M, L', 'images/dresses-gowns/dresses-gowns-2.jpg', 'Luxurious satin formal gown featuring crystal beaded corset bodice and thigh-high side split.', 1, 24, NOW()),
(68, 10, 'Dark Denim Long-Sleeve Belted Skater Dress', 6500.00, 'S, M, L', 'images/dresses-gowns/dresses-gowns-3.jpg', 'Fitted indigo denim shirt dress with flared skater skirt, button front and waist cinching belt.', 0, 19, NOW()),
(69, 10, 'Victorian Tiered Lace Corset Prairie Dress', 8900.00, 'S, M, L', 'images/dresses-gowns/dresses-gowns-4.jpg', 'Romantic cream tiered lace maxi dress with brown leatherette lace-up waist corset.', 1, 10, NOW()),
(70, 10, 'Red Gingham Cottagecore Corset Midi Dress', 6200.00, 'S, M, L', 'images/dresses-gowns/dresses-gowns-5.jpg', 'Charming vintage red and white check prairie dress with puff sleeves and lace-up bodice.', 0, 9, NOW()),
(71, 10, 'Polka Dot Flared Bell-Sleeve Babydoll Dress', 5900.00, 'S, M, L', 'images/dresses-gowns/dresses-gowns-6.jpg', 'Cream chiffon mini dress with playful black polka dots, bell cuffs and velvet waist bow.', 0, 13, NOW()),
(72, 10, 'Pure White Corset-Waist Long Sleeve Mini Dress', 6400.00, 'S, M, L', 'images/dresses-gowns/dresses-gowns-7.jpg', 'Architectural structured white cotton shirt dress with ruched corset waist and pleated skirt.', 0, 8, NOW()),
(73, 11, 'Silver-Woven Steel Blue Silk Designer Saree', 15800.00, 'Free Size', 'images/sarees/sarees-1.jpg', 'Heavy Banarasi silver zari woven silk saree with matching high-neck sleeveless blouse piece.', 1, 15, NOW()),
(74, 11, 'Pastel Rainbow Ombre Pearl-Border Organza Saree', 14200.00, 'Free Size', 'images/sarees/sarees-2.jpg', 'Whisper-light pleated organza saree in rainbow sorbet hues with hand-sewn pearl trim.', 1, 23, NOW()),
(75, 11, 'Crimson & Champagne Dual-Tone Embroidered Saree', 16500.00, 'Free Size', 'images/sarees/sarees-3.jpg', 'Ombre gradient georgette saree heavily embroidered with antique gold gota work borders.', 1, 15, NOW()),
(76, 11, 'Hot Magenta All-Over Sequin Glamour Saree', 18500.00, 'Free Size', 'images/sarees/sarees-4.jpg', 'Dazzling celebrity red-carpet saree covered in micro-shimmer sequins with jewel bustier.', 1, 25, NOW()),
(77, 11, 'Chocolate Brown Cutdana Crystal Party Saree', 17200.00, 'Free Size', 'images/sarees/sarees-5.jpg', 'Rich espresso sheer net saree featuring intricate cutdana stone embroidery and full-sleeve blouse.', 0, 19, NOW()),
(78, 11, 'Blush Rose Floral Chikankari Net Saree', 13800.00, 'Free Size', 'images/sarees/sarees-6.jpg', 'Delicate pastel pink net saree with romantic floral threadwork borders and matching blouse.', 0, 8, NOW()),
(79, 11, 'Cream & Royal Blue Traditional Temple Border Saree', 12500.00, 'Free Size', 'images/sarees/sarees-7.jpg', 'Handloom raw silk saree with contrasting royal navy blue pallu and woven gold motifs.', 0, 25, NOW()),
(80, 12, 'Toddler Boy Maroon Suspender & Bowtie Set', 4200.00, '1-2Y, 2-3Y, 3-4Y', 'images/kids-wear/kids-wear-1.jpg', 'Gentleman 4-piece formal party outfit including white button shirt, suspenders, shorts and bow.', 1, 23, NOW()),
(81, 12, 'Toddler Blue Palm Hawaiian Resort 2-Piece Set', 3400.00, '6-12M, 1-2Y, 2-3Y', 'images/kids-wear/kids-wear-2.jpg', 'Vacation camp collar palm printed short-sleeve shirt paired with white cotton shorts.', 0, 9, NOW()),
(82, 12, 'Boys All-Black Minimalist Linen 3-Piece Outfit', 3600.00, '1-2Y, 2-3Y, 3-4Y', 'images/kids-wear/kids-wear-3.jpg', 'Chic streetwear monochrome set with black short-sleeve overshirt, white tee and black shorts.', 0, 10, NOW()),
(83, 12, 'Infant Teddy Pocket Bubble Denim Overalls', 3800.00, '6-12M, 12-18M, 18-24M', 'images/kids-wear/kids-wear-4.jpg', 'Adorable soft denim bubble romper dungarees featuring a cute plush teddy bear in the bib pocket.', 1, 16, NOW()),
(84, 12, 'Cartoon Friends Denim Overalls & Tee Set', 3500.00, '1-2Y, 2-3Y, 3-4Y', 'images/kids-wear/kids-wear-5.jpg', 'Playful graphic print short overalls with matching white crewneck baby tee.', 0, 25, NOW()),
(85, 12, 'Baby Girl Blue Corduroy Floral Pinafore & Beret', 4400.00, '6-12M, 1-2Y, 2-3Y', 'images/kids-wear/kids-wear-6.jpg', 'Vintage blue corduroy dress decorated with 3D yarn flowers, ruffled straps and matching beret hat.', 1, 15, NOW()),
(86, 12, 'Toddler Girl Puff Sleeve Daisy Denim Dress', 3900.00, '1-2Y, 2-3Y, 3-4Y', 'images/kids-wear/kids-wear-7.jpg', 'Sweet square-neck blue denim A-line dress with white puff sleeves and organza daisy accents.', 0, 17, NOW()),
(87, 12, 'Girls 3D Organza Floral Crop & Flared Pant Set', 4600.00, '2-3Y, 3-4Y, 4-5Y', 'images/kids-wear/kids-wear-8.jpg', 'Stylish denim blue party co-ord featuring oversized sheer flowers on crop cami and flared pants.', 0, 16, NOW()),
(88, 12, 'Girls Frayed Butterfly Tee & Cargo Jeans Set', 4800.00, '3-4Y, 4-5Y, 5-6Y', 'images/kids-wear/kids-wear-9.jpg', 'Trendsetting streetwear outfit with distressed denim butterfly t-shirt and multi-pocket cargo jeans.', 0, 14, NOW()),
(89, 12, 'Girls Asymmetric Two-Tone Denim Cami Dress', 3800.00, '2-3Y, 3-4Y, 4-5Y', 'images/kids-wear/kids-wear-10.jpg', 'Creative half-and-half white cotton and denim button-down dress with tie belt.', 0, 25, NOW()),
(90, 12, 'Little Girls Striped Halter Top & Skater Skirt', 3600.00, '2-3Y, 3-4Y, 4-5Y', 'images/kids-wear/kids-wear-11.jpg', 'Classic pinstripe collared sleeveless peplum top paired with matching pleated twirl skirt.', 0, 15, NOW()),
(91, 12, 'Little Girls Sage Olive Puff Sleeve Skirt Set', 3900.00, '2-3Y, 3-4Y, 4-5Y', 'images/kids-wear/kids-wear-12.jpg', 'Elegantly styled olive green co-ord with bubble hem top and flared ruffle skirt.', 0, 22, NOW()),
(92, 12, 'Toddler Layered Shirt Dress & Knit Vest Set', 4100.00, '1-2Y, 2-3Y, 3-4Y', 'images/kids-wear/kids-wear-13.jpg', 'Preppy white pleated collared dress layered with pastel pink ribbed sweater vest.', 0, 14, NOW()),
(93, 12, 'Boys Heather Grey Ribbed Streetwear Set', 4300.00, '3-4Y, 4-5Y, 5-6Y', 'images/kids-wear/kids-wear-14.jpg', 'Contemporary urban oversized drop-shoulder ribbed tee with matching wide-leg trousers.', 0, 15, NOW()),
(94, 12, 'Girls Sky Blue Striped Bow Top & Palazzo Set', 4200.00, '2-3Y, 3-4Y, 4-5Y', 'images/kids-wear/kids-wear-15.jpg', 'Breezy striped summer set with bow shoulder straps, peplum hem and wide flowy palazzo pants.', 0, 14, NOW()),
(95, 12, 'Boys Preppy Striped Rugby Polo & Denim Shorts', 4400.00, '2-3Y, 3-4Y, 4-5Y', 'images/kids-wear/kids-wear-16.jpg', 'Long-sleeve striped collar polo with varsity patch paired with light-wash denim jean shorts.', 1, 10, NOW()),
(96, 12, 'Boys Washed Black Denim Overshirt & Pants Set', 4900.00, '3-4Y, 4-5Y, 5-6Y', 'images/kids-wear/kids-wear-17.jpg', 'Cool urban streetwear button-front black denim overshirt with relaxed beige wide-leg cargo pants.', 0, 17, NOW());

-- Sample Orders
INSERT INTO `orders` (`id`, `order_number`, `customer_name`, `customer_phone`, `shipping_address`, `customer_email`, `city`, `postal_code`, `payment_method`, `total_amount`, `status`, `created_at`) VALUES
(1, 'ORD-20261108-7491', 'Nadeesha Perera', '0771234567', '45 Galle Road, Colombo 03', 'nadeesha@example.com', 'Colombo', '00300', 'Cash on Delivery', 14500.00, 'Processing', NOW() - INTERVAL 2 DAY),
(2, 'ORD-20261108-9823', 'Kasun Fernando', '0719876543', '12 Kandy Road, Peradeniya', 'kasun.f@example.com', 'Kandy', '20400', 'Cash on Delivery', 7200.00, 'Delivered', NOW() - INTERVAL 5 DAY);

INSERT INTO `order_items` (`order_id`, `product_id`, `product_name`, `size`, `quantity`, `unit_price`, `subtotal`) VALUES
(1, 72, 'Sage Green Boned Corset Slit Evening Gown', 'M', 1, 14500.00, 14500.00),
(2, 6, 'Classic Mahogany Brown Horsebit Loafers', '42', 1, 7200.00, 7200.00);

-- Sample Customer Inquiry Message
INSERT INTO `messages` (`name`, `email`, `phone`, `subject`, `message`, `created_at`) VALUES
('Ruwan Silva', 'ruwan.silva@example.com', '0765551234', 'Inquiry about Island-wide Delivery', 'Hello Dress Point team, do you offer cash on delivery to Kurunegala? How many business days does delivery take?', NOW() - INTERVAL 1 DAY);