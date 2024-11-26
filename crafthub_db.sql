-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 17, 2024 at 05:53 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `crafthub_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_color` varchar(200) NOT NULL,
  `product_size` varchar(200) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `status` varchar(55) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`cart_id`, `user_id`, `product_id`, `product_color`, `product_size`, `quantity`, `price`, `status`) VALUES
(181, 1, 95, 'Khaki', 'small', 1, '100.00', 'cart'),
(182, 1, 41, 'White ', 'small', 1, '299.00', 'cart'),
(187, 14, 41, 'White ', 'small', 1, '299.00', 'ordered'),
(188, 14, 53, 'White ', 'Standard', 1, '49.00', 'ordered'),
(189, 14, 53, 'White ', 'Standard', 1, '49.00', 'ordered');

-- --------------------------------------------------------

--
-- Table structure for table `crafthub_users`
--

CREATE TABLE `crafthub_users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `middle_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) NOT NULL,
  `birthday` date DEFAULT NULL,
  `contact_no` varchar(15) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `user_profile` varchar(255) DEFAULT NULL,
  `role` varchar(50) NOT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `token` tinyint(1) DEFAULT NULL,
  `token_created_at` datetime DEFAULT NULL,
  `last_activity` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `crafthub_users`
--

INSERT INTO `crafthub_users` (`user_id`, `username`, `email`, `password`, `first_name`, `middle_name`, `last_name`, `birthday`, `contact_no`, `address`, `user_profile`, `role`, `is_verified`, `created_at`, `token`, `token_created_at`, `last_activity`) VALUES
(1, 'Kyra', 'mlgquindoy@bpsu.edu.ph', 'maluwisa123', 'Kyra', 'Pusod', 'Cruz', '2024-11-15', '09815720881', 'Tabing Ilog, Samal, Bataan', NULL, 'merchant', 0, '2024-11-11 19:19:07', NULL, NULL, '2024-11-16 23:19:05'),
(2, 'likhangkamayc', 'ghcruz@gmail.com', '123', 'Gulpo', 'Hany', 'Cruz', '1990-05-12', '9574621587', NULL, '../uploads/likhang kamay logo.png', 'merchant', 1, '2023-08-15 06:32:00', NULL, NULL, '2024-11-17 12:39:36'),
(3, 'craftwoodcreations', 'tkpillas@gmail.com', 'craftwoodcreations', 'Tavisora', 'Kobe', 'Pillas', '1995-07-22', '09181234567', 'Bnas, Abucay, Bataan', '../uploads/craftwood creations logo.png', 'merchant', 1, '2023-06-10 03:25:00', NULL, NULL, '2024-11-17 10:33:57'),
(4, 'seashellandcapizdelight', 'afariola@gmail.com', 'seashellandcapizdelight', 'Aday Francine', NULL, 'Ariola', '1998-03-18', '09191234567', 'San Ramon, Dinalupihan, Bataan', '../uploads/seashell and capiz delight.png', 'merchant', 1, '2023-09-20 08:50:00', NULL, NULL, '2024-11-17 00:02:26'),
(5, 'ecocraftcreations', 'hasalud@gmail.com', 'ecocraftcreations', 'Hanami', 'Ahmed', 'Salud', '1995-06-14', '09194567812', 'Palihan, Hermosa, Bataan', '../uploads/eco craft creations.png', 'merchant', 1, '2023-08-13 06:20:00', NULL, NULL, '2024-11-16 23:19:05'),
(6, 'bambooblisshandicrafts', 'sdSendaydiego@gmail.com', 'bambooblisshandicrafts', 'Sean', 'Domingo', 'Sendaydiego', '1992-12-07', '09182345678', 'Tagumpay, Orani, Bataan', '../uploads/bamboo bliss handicrafts.png', 'merchant', 1, '2023-04-25 01:35:00', NULL, NULL, '2024-11-16 23:19:05'),
(7, 'shorelineabanikocrafts', 'rsroxas@gmail.com', 'shorelineabanikocrafts', 'Rain', 'Sy', 'Roxas', '1993-07-21', '09195678345', 'Pantingan, Pilar, Bataan', '../uploads/shoreline abaniko crafts.png', 'merchant', 1, '2023-09-02 02:45:00', NULL, NULL, '2024-11-16 23:19:05'),
(8, 'coconutgrovehandicrafts', 'retenorio@gmail.com', 'coconutgrovehandicrafts', 'Renz', 'Espiritu', 'Tenorio', '1994-05-14', '09191234567', 'Bilolo, Orani, Bataan', '../uploads/coconut grove handicrafts.png', 'merchant', 1, '2023-08-20 06:30:00', NULL, NULL, '2024-11-16 23:19:05'),
(9, 'liliweavehandicrafts', 'hvmanahan@gmail.com', 'liliweavehandicrafts', 'Huns', 'Villas', 'Manahan', '1992-09-12', '09271234567', 'Laon, Abucay, Bataan', '../uploads/lily weave handicrafts.png', 'merchant', 1, '2023-11-05 02:25:00', NULL, NULL, '2024-11-17 12:44:27'),
(13, 'haha123334', 'tallorinluisa2@gmail.com', '123456789', 'asdasd', 'asdasd', 'asdfsadsa', '2024-10-28', '0912343241', 'sadasDFsad', NULL, 'customer', 0, '2024-11-11 22:45:36', NULL, NULL, '2024-11-17 12:44:12'),
(20, 'haha123', 'tallorinluisa1@gmail.com', '123456789', 'lusia', 'jajaj', 'gagdsf', '2024-10-28', '09985262989', 'haha heheh hihihi', NULL, 'customer', 0, '2024-11-12 03:17:12', 127, '2024-11-12 04:17:12', '2024-11-16 23:19:05');

-- --------------------------------------------------------

--
-- Table structure for table `merchant_applications`
--

CREATE TABLE `merchant_applications` (
  `application_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `shop_name` varchar(100) NOT NULL,
  `shop_contact_no` varchar(15) NOT NULL,
  `shop_email` varchar(100) NOT NULL,
  `shop_address` text DEFAULT NULL,
  `shop_municipality` varchar(50) DEFAULT NULL,
  `shop_barangay` varchar(50) DEFAULT NULL,
  `shop_street` varchar(50) DEFAULT NULL,
  `business_permit` varchar(255) DEFAULT NULL,
  `BIR_registration` varchar(255) DEFAULT NULL,
  `BIR_0605` varchar(255) DEFAULT NULL,
  `DTI_certificate` varchar(255) DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'pending',
  `applied_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `merchant_applications`
--

INSERT INTO `merchant_applications` (`application_id`, `user_id`, `shop_name`, `shop_contact_no`, `shop_email`, `shop_address`, `shop_municipality`, `shop_barangay`, `shop_street`, `business_permit`, `BIR_registration`, `BIR_0605`, `DTI_certificate`, `status`, `applied_at`) VALUES
(1, 1, 'Rekkagamiii Hub', '09912312324', 'mlgquindoy@bpsu.edu.ph', 'Mt. View, Mariveles, Bataan', 'Abucay', 'Capitangan', 'bautista', 'uploads/quindoymg-ass05.pdf', 'uploads/acenajyb-ass05.pdf', 'uploads/cearma-ass05.pdf', 'uploads/quindoymg-ass05.pdf', 'approved', '2024-11-11 19:19:07'),
(2, 2, 'LikhangKamay', '9574621587', 'likhangkamay@gmail.com', NULL, 'Limay', 'Townsite', 'San Isidro', NULL, NULL, NULL, NULL, 'approved', '2023-08-15 06:32:00'),
(3, 3, 'CraftWood Creations', '09197654321', 'craftwoodcreations@gmail.com', NULL, 'Balanga City', 'Dona Francisca', 'Dama de Noche', NULL, NULL, NULL, NULL, 'approved', '2023-06-10 03:25:00'),
(4, 4, 'Seashell and Capiz Delight', '09195678912', 'seashellandcapizdelight@gmail.com', NULL, 'Dinalupihan', 'Pentor', 'Basilio Street', NULL, NULL, NULL, NULL, 'approved', '2023-09-20 08:50:00'),
(5, 5, 'Eco Craft Creations', '09193456721', 'ecocraftcreations@gmail.com', NULL, 'Abucay', 'Bangkal', 'Bautista Street', NULL, NULL, NULL, NULL, 'approved', '2023-08-13 06:20:00'),
(6, 6, 'Bamboo Bliss Handicrafts', '09193456789', 'bambooblisshandicrafts@gmail.com', NULL, 'Balanga City', 'Dona Francisca', 'Carnation Street', NULL, NULL, NULL, NULL, 'approved', '2023-04-25 01:35:00'),
(7, 7, 'Shoreline Abaniko Crafts', '09193456234', 'shorelineabanikocrafts@gmail.com', NULL, 'Hermosa', 'Almacen', 'A.Rivera Street', NULL, NULL, NULL, NULL, 'approved', '2023-09-02 02:45:00'),
(8, 8, 'Coconut Grove Handicrafts', '09198765432', 'coconutgrovehandicrafts@gmail.com', NULL, 'Samal', 'Tabing Ilog', 'Lote', NULL, NULL, NULL, NULL, 'approved', '2023-08-20 06:30:00'),
(9, 9, 'LilyWeave Handicrafts', '09381234567', 'liliweavehandicrafts@gmail.com', NULL, 'Bagac', 'Pag-asa', 'J.P. Rizal Street', NULL, NULL, NULL, NULL, 'approved', '2023-11-05 02:25:00'),
(10, 13, 'Bayong ni Juls', '09927336336', 'tallorinluisa1@gmail.com', 'Sa tabi tabi hehi', 'Balanga City', 'Camacho', 'Purok 7', '', '', '', '', 'pending', '2024-11-11 22:45:36');

-- --------------------------------------------------------

--
-- Table structure for table `merchant_products`
--

CREATE TABLE `merchant_products` (
  `product_id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  `product_img` varchar(500) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `product_desc` varchar(300) NOT NULL,
  `category` varchar(200) NOT NULL,
  `material` varchar(100) DEFAULT NULL,
  `stock` int(11) NOT NULL,
  `price` int(11) DEFAULT NULL,
  `upload_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `merchant_products`
--

INSERT INTO `merchant_products` (`product_id`, `application_id`, `product_img`, `product_name`, `product_desc`, `category`, `material`, `stock`, `price`, `upload_date`) VALUES
(19, 2, 'Abaca Fiber Rugjpeg.jpeg', 'Abaca Fiber Rug', 'A durable and eco-friendly rug made from abaca fiber, adding a natural touch to any room.', 'Rugs', 'Abacca, Rug', 112, NULL, NULL),
(20, 2, 'Rattan Coffee Table.jpeg', 'Rattan Coffee Table', 'A stylish and sturdy coffee table handcrafted from natural rattan, perfect for any living room.', 'Tables', 'Rattan, Soft wood', 119, NULL, NULL),
(21, 2, 'Abaca Placemat Set.jpeg', 'Abaca Placemat Set', 'A set of elegant placemats made from abaca fiber, perfect for enhancing your dining table.', 'Tables', 'Abaca', 113, NULL, NULL),
(22, 2, '../uploads/0624ee707af6822d328f806a80bbb804.jpg_720x720q80.jpg', 'Rattan Pendant Light', 'A beautiful pendant light made from handwoven rattan, providing a warm and cozy illumination.', 'Lighting', 'Rattan, bulb', 115, NULL, NULL),
(23, 2, 'Abaca Storage Basket.jpeg', 'Abaca Storage Basket', 'A versatile and durable storage basket made from abaca fiber, ideal for organizing your home.', 'Storage', 'Abaca, Thick rope', 114, NULL, NULL),
(24, 2, 'Rattan Armchair.jpeg', 'Rattan Armchair', 'A comfortable and stylish armchair handcrafted from rattan, perfect for indoor or outdoor use.', 'Tables', 'Rattan, Wood', 119, NULL, NULL),
(25, 2, 'Abaca Lampshade.jpeg', 'Abaca Lampshade', 'A unique lampshade made from abaca fiber, adding a touch of elegance to your lighting.', 'Lighting', 'Abaca', 114, NULL, NULL),
(26, 2, 'Rattan Wall Mirror.jpeg', 'Rattan Wall Mirror', 'A decorative wall mirror framed with handwoven rattan, perfect for enhancing any room.', 'Decors', 'Rattan, Mirror', 115, NULL, NULL),
(27, 2, 'Abaca Table Runner.jpg', 'Abaca Table Runner', 'A handcrafted table runner made from abaca fiber, ideal for adding a natural touch to your dining table.', 'Tables', 'Abaca', 129, NULL, NULL),
(28, 2, 'Rattan Floor Lamp.jpeg', 'Rattan Floor Lamp', 'A beautiful floor lamp made from handwoven rattan, providing soft and warm lighting for any room.', 'Lighting', 'Rattan', 128, NULL, NULL),
(29, 2, 'Abaca Wall Decor.jpeg', 'Abaca Wall Decor', 'A unique wall decor piece made from abaca fiber, adding a touch of natural beauty to your home.', 'Decors', 'Abaca, and design', 115, NULL, NULL),
(30, 2, 'Rattan Magazine Rack.jpeg', 'Rattan Magazine Rack Basket', ' A stylish and functional magazine rack handcrafted from rattan, perfect for organizing your reading materials.', 'Storage', 'Rattan, Soft wood', 116, NULL, NULL),
(31, 3, 'Wooden Coasters Set.jpeg', 'Wooden Coasters Set', 'A set of beautifully crafted oak wood coasters, perfect for protecting your surfaces.', 'Coasters', 'Wood, thick rope', 119, NULL, NULL),
(32, 3, 'Hand-carved Wooden Bowl.jpeg', 'Hand-carved Wooden Bowl', 'A stunning hand-carved bowl made from durable teak wood, ideal for serving or decoration.', 'Dining', 'Wood', 116, NULL, NULL),
(33, 3, 'Wooden Jewelry Box.jpeg', 'Wooden Jewelry Box', 'An elegant mahogany jewelry box with intricate carvings, providing stylish storage for your treasures.', 'Storage', 'Wooden, box', 114, NULL, NULL),
(34, 3, 'Wooden Picture Frame.jpeg', 'Wooden Picture Frame', 'A sophisticated walnut wood frame that enhances the beauty of your photographs.', 'Decors', 'Wooden, sample picture size', 116, NULL, NULL),
(35, 3, 'Wooden Candle Holders.jpeg', 'Wooden Candle Holders', 'Charming pine wood candle holders that add a rustic touch to any setting.', 'Decors', 'Wood', 140, NULL, NULL),
(36, 3, 'Wooden Wall Clock.jpeg', 'Wooden Wall Clock', 'A stylish birch wood wall clock that combines functionality with aesthetic appeal', 'Decors', 'Wood, Clock accesories', 139, NULL, NULL),
(37, 3, 'Wooden Serving Tray.jpeg', ' Wooden Serving Tray', 'A beautiful cherry wood serving tray, perfect for entertaining guests.', 'Trays', 'Wood', 125, NULL, NULL),
(38, 3, 'Wooden Plant Stand.jpg', 'Wooden Plant Stand', ' A sturdy and stylish cedar wood plant stand that elevates your indoor greenery.', 'Decors', 'Wood', 133, NULL, NULL),
(39, 3, 'Wooden Desk Organizer.jpeg', 'Wooden Desk Organizer', 'An eco-friendly bamboo desk organizer that keeps your workspace neat and tidy.', 'Storage', 'Woods', 122, NULL, NULL),
(40, 3, 'Wooden Wine Rack.jpeg', 'Wooden Wine Rack', 'A handcrafted acacia wood wine rack that elegantly stores and displays your wine collection.', 'Dining', 'Wood', 111, NULL, NULL),
(41, 3, 'Wooden Bookends.jpeg', 'Wooden Bookends', 'Durable oak wood bookends that keep your books organized and add a touch of elegance to your shelves.', 'Accessories', 'Wood', 124, NULL, NULL),
(42, 4, 'Capiz Chandelier.jpeg', 'Capiz Chandelier', 'A stunning centerpiece that casts a warm, diffused light through the natural beauty of capiz shells.', 'Lighting', 'Capiz, thick rope', 119, NULL, NULL),
(43, 4, 'Capiz Window Panels.jpeg', 'Capiz Window Panels', 'Provides privacy and a touch of elegance while allowing natural light to filter through. ', 'Decors', 'Capiz, Wood', 129, NULL, NULL),
(44, 4, 'Shell Mobile.jpeg', ' Shell Mobile', 'A delightful addition to any room, bringing a touch of the ocean indoors.', 'Decor', 'Shell', 114, NULL, NULL),
(45, 4, 'Capiz Jewelry Box.jpeg', 'Capiz Jewelry Box', 'A beautiful and functional way to store your cherished jewelry.', 'Storage', 'Shell', 119, NULL, NULL),
(46, 4, 'Shell Picture Frame.jpeg', ' Shell Picture Frame', 'A unique way to showcase your favorite memories.', 'Decor', 'Shell, Sample picture size', 115, NULL, NULL),
(47, 4, 'Capiz Candle Holder.jpeg', 'Capiz Candle Holder', 'Creates a warm and inviting ambiance with the soft glow of candlelight filtering through the capiz. ', 'Decor', 'Shell', 119, NULL, NULL),
(48, 4, 'Shell Wind Chimejpeg.jpeg', 'Shell Wind Chime', 'Enjoys the calming melodies of the wind chimes as they gently chime.', 'Decor', 'Shell, thick rope', 115, NULL, NULL),
(49, 4, 'Capiz Coasters.jpeg', 'Capiz Coasters', 'Protects your furniture from water rings while adding a touch of coastal style.', 'Coasters', 'Capiz shell', 129, NULL, NULL),
(50, 4, 'Shell Trinket Dish.jpeg', 'Shell Trinket Dish', 'A beautiful and functional catch-all for your jewelry, keys, or other small items.', 'Kitchen', 'Shell', 133, NULL, NULL),
(51, 4, 'Seashell Mirror.jpeg', 'Seashell Mirror', ' A decorative mirror framed with an assortment of seashells, adding a coastal and natural aesthetic to any space.', 'Decor', 'seashell', 129, NULL, NULL),
(52, 5, 'Eco-Friendly Journal.jpeg', 'Eco-Friendly Journal', 'A beautifully crafted journal perfect for eco-conscious note-taking.', 'Accessories', 'Papers', 129, NULL, NULL),
(53, 5, 'Handmade Greeting Cards.jpeg', 'Handmade Greeting Cards', 'Unique greeting cards made with love and sustainable materials.', 'Accessories', 'Papers', 129, NULL, NULL),
(54, 5, 'Fabric-Covered Photo Album.jpeg', ' Fabric-Covered Photo Album', 'Preserve your memories in this charming, eco-friendly photo album.', 'Decor', 'Fabric', 114, NULL, NULL),
(55, 5, '../uploads/fb20ab6d-a4e7-4408-8d6d-e70e5387f513.jpeg', ' Decorative Paper Beads Necklace', ' A stylish and sustainable accessory made from recycled materials.', 'Accessories', '', 119, NULL, NULL),
(56, 5, 'Recycled Paper Gift Bags.jpeg', 'Recycled Paper Gift Bags', 'Eco-friendly gift bags perfect for any occasion.', 'Storage', 'Papers', 119, NULL, NULL),
(57, 5, 'Fabric-Paper Bookmarks.jpeg', ' Fabric-Paper Bookmarks', ' Keep your place in style with these handcrafted, eco-friendly bookmarks.', 'Accessories', 'Papers', 0, NULL, NULL),
(58, 5, 'Recycled Paper and Fabric Coasters.jpeg', 'Recycled Paper and Fabric Coasters', 'Protect your surfaces with these sustainable and stylish coasters.', 'Coasters', 'Papers, Fabric', 124, NULL, NULL),
(59, 5, 'Handmade Paper Lanterns.jpeg', 'Handmade Paper Lanterns', ' Illuminate your space with these beautiful, eco-friendly lanterns.', 'Lighting', 'Papers', 129, NULL, NULL),
(60, 5, 'Recycled Paper Envelopes.jpeg', 'Recycled Paper and Fabric Envelopes', ' Send your letters with these unique, eco-conscious envelopes.', 'Storage', 'Papers', 133, NULL, NULL),
(61, 5, 'Fabric and Paper Wall Art.jpeg', ' Fabric and Paper Wall Art', ' Enhance your home decor with this stunning, sustainable wall art.', 'Decor', 'Papers', 115, NULL, NULL),
(62, 5, 'Recyucled Notepads.jpg', 'Recycled Paper and Fabric Notepads', 'Perfect for jotting down thoughts, these notepads are eco-friendly and stylish.', 'Decor', 'Papers', 144, NULL, NULL),
(63, 5, 'Recycled Paper and Fabric Notepads.jpeg', ' Upcycled Paper and Fabric Earrings', ' Add a touch of sustainability to your look with these unique earrings.', 'Decor', 'Papers', 122, NULL, NULL),
(64, 6, 'Decorative lantern.jpeg', 'Decorative lantern', 'Elegant bamboo lanterns that create a warm, inviting glow. Ideal for outdoor and indoor decoration, available in various finishes to match any décor.', 'Lantern', 'Bamboo', 119, NULL, NULL),
(65, 6, 'Bamboo Utensil Set.jpeg', 'Bamboo Utensil Set', 'Eco-friendly, durable, and lightweight utensil set made from sustainably sourced bamboo, perfect for everyday kitchen use.', 'Kitchen', 'Bamboo', 119, NULL, NULL),
(66, 6, 'Bamboo Cutting Board.jpeg', 'Bamboo Cutting Board', ' Durable and knife-friendly bamboo cutting boards, available in different sizes with optional juice groove for convenient food preparation.', 'Kitchen', 'Bamboo', 115, NULL, NULL),
(68, 6, 'Bamboo Wind Chimes.jpg', 'Bamboo Wind Chimes', 'Handcrafted bamboo wind chimes that produce soothing sounds. Ideal for gardens, patios, or as a gift.', 'Decor', 'Bamboo, windchimes designs', 133, NULL, NULL),
(69, 6, 'Bamboo Serving Tray.jpeg', 'Bamboo Serving Tray', '', 'Trays', 'Bamboo', 129, NULL, NULL),
(70, 6, 'Bamboo Basket.jpeg', 'Bamboo Basket', ' Versatile bamboo baskets ideal for storage and organization. Handwoven and available in natural or dyed finishes.', 'Storage', 'Bamboo', 114, NULL, NULL),
(71, 6, 'Bamboo Coasters.jpg', 'Bamboo Coasters', 'Attractive and eco-friendly bamboo coasters, designed to protect your surfaces from heat and moisture. Available in plain or intricately carved designs.', 'Coasters', 'Bamboo', 129, NULL, NULL),
(72, 6, 'Bamboo Toothbrush.jpeg', 'Bamboo Toothbrush', 'Sustainable and biodegradable bamboo toothbrushes with various bristle options for comfortable and effective oral care.', 'Decor', 'Bamboo', 122, NULL, NULL),
(73, 6, 'Bamboo Photo Frame.jpg', 'Bamboo Photo Frame', ' Beautifully crafted bamboo photo frames that add a touch of natural elegance to your home or office. Available in multiple sizes and finishes.', 'Decor', 'Bamboo', 115, NULL, NULL),
(74, 7, 'Abaniko Hand Fan.jpeg', 'Abaniko Hand Fan', ' Beautifully woven bamboo hand fans, perfect for keeping cool in hot weather or as decorative pieces.', 'Decor', 'Abaniko, Bamboo', 0, NULL, NULL),
(75, 7, 'Abaniko Bamboo Bag.jpeg', 'Abaniko Bamboo Bag', ' Stylish and eco-friendly bamboo bags, ideal for carrying essentials while adding a touch of Filipino craftsmanship to your outfit.', 'Storage', 'Abaniko, Bamboo', 113, NULL, NULL),
(76, 7, 'Abaniko Bamboo Sculptures.jpeg', 'Abaniko Bamboo Sculptures', 'Artistic bamboo sculptures that capture the imagination and showcase the versatility of bamboo as a medium for creative expression.', 'Furniture', 'Abaniko, Bamboo', 0, NULL, NULL),
(77, 7, 'Abaniko Bamboo Trowel Rake Shovel Tool.jpeg', 'Abaniko Bamboo Trowel Rake Shovel Tool', ' Durable and eco-friendly bamboo gardening tools designed to make gardening tasks easier while reducing environmental impact.', 'Storage', 'Abaniko, Bamboo', 113, NULL, NULL),
(78, 7, 'Abaniko Bamboo Stationery.jpeg', 'Abaniko Bamboo Stationery', 'Sustainable bamboo stationery items that add a touch of natural elegance to your desk while promoting eco-friendly practices.', 'Storage', 'Abaniko, Bamboo', 133, NULL, NULL),
(79, 7, 'Bamboo Yoga Accessories.jpeg', 'Abaniko Bamboo Yoga Accessories', 'Eco-friendly bamboo yoga accessories designed to enhance your yoga practice while promoting sustainability and connection with nature.', 'Storage', 'Bamboo', 149, NULL, NULL),
(80, 7, 'Bamboo Musical Instruments.jpeg', 'Abaniko Bamboo Musical Instruments', 'Authentic bamboo musical instruments that produce beautiful sounds, perfect for music enthusiasts and cultural enthusiasts alike.', 'Decor', 'Bamboo', 114, NULL, NULL),
(81, 8, 'Coconut Cup.jpeg', 'Coconut Cup', 'Polished Coconut Shell', 'Dining', 'Coconut shell', 119, NULL, NULL),
(82, 8, 'conutbutton.jpg', 'Coconut Shell Button Set', 'Coconut Shell, Natural Dyes', 'Decor', 'Young coconut ', 114, NULL, NULL),
(83, 8, 'Coconut Frond Tote Bag.jpg', 'Coconut Frond Tote Bag', 'Dried Coconut Fronds, Fabric Lining', 'Storage', 'Young coconut ', 115, NULL, NULL),
(84, 8, 'Coconut Coir Doormat.jpeg', 'Coconut Coir Doormat', ' A natural and effective doormat made from coconut coir fibers. The coarse fibers trap dirt and debris, keeping your entryway clean and preventing moisture from entering your home.', 'Decor', 'Young coconut ', 119, NULL, NULL),
(85, 8, 'Coconut Shell Bird Feeder.jpg', 'Coconut Shell Bird Feeder', 'Coconut Shell, Metal Hanger. A sustainable and attractive bird feeder made from a halved coconut shell with a metal hanger. The natural opening allows birds easy access to food, while the shell provides shelter from the elements.', 'Decor', 'Coconut shell', 114, NULL, NULL),
(86, 8, 'Coconut Wood Coasterjpg.jpg', 'Coconut Wood Coasters', ' A set of elegant and durable coasters made from polished coconut wood. They protect your furniture from water stains and add a touch of natural beauty to your coffee table.', 'Coasters', 'coconut', 114, NULL, NULL),
(87, 8, 'Coconut Shell Bowl.jpeg', 'Coconut Shell Bowl', 'A natural and eco-friendly bowl made from a polished coconut shell. Perfect for serving snacks, salads, or acai bowls. Each bowl is unique due to the natural variations in the coconut shell.', 'Dining', 'Coconut shell ', 114, NULL, NULL),
(88, 8, '../uploads/af02c5ca-60a1-47e6-803e-8f18bd4b0814.jpeg', 'Coconut Shell Soap Dish', 'A unique and functional soap dish made from a coconut shell with drainage holes. The natural material allows soap to dry quickly and prevents it from becoming mushy.', 'Kitchen', 'Coconut', 114, NULL, NULL),
(89, 8, 'Coconut Frond Wall Hanging.jpg', 'Coconut Frond Wall Hanging', 'Dried Coconut Fronds, Natural Dyes', 'Decor', 'Coconut', 115, NULL, NULL),
(90, 8, 'Coconut Shell Wind Chime.jpeg', 'Coconut Shell Wind Chime', 'Coconut Shell Halves, Bamboo, Twine, Seashells ', 'Decor', 'Coconut shell', 119, NULL, NULL),
(91, 9, 'Water Lily Handbag.jpeg', 'Water Lily Handbag', 'Water Lily Fiber, Fabric Lining', 'Storage', 'Waterlily, Fabric', 129, NULL, NULL),
(92, 9, 'Water Lily Coasters.JPG', 'Water Lily Coasters', 'et of coasters made from dried water lily leaves, providing a natural and rustic touch to your tabletop while protecting surfaces from moisture and heat.', 'Coasters', 'Waterlily, Fabric', 111, NULL, NULL),
(93, 9, 'Water Lily Tea Light Holder.jpeg', 'Water Lily Tea Light Holder', 'Water Lily Fiber, Glass Votive', 'Decor', 'Waterlily', 129, NULL, NULL),
(94, 9, 'Water Lily Basket.jpeg', 'Water Lily Basket', 'Water Lily Stems, Jute', 'Storage', 'Waterlily', 114, NULL, NULL),
(95, 9, 'Water Lily Earrings.jpg', 'Water Lily Earrings', 'Dried Water Lily Petals, Metal Findings', 'Accessories', 'Waterlily', 119, NULL, NULL),
(96, 9, 'waterlilyfiber.jpg', 'Water Lily Table Runner', 'Handwoven table runner crafted from water lily fiber, adding texture and elegance to your dining table or sideboard. Perfect for special occasions or everyday use.', 'Accessories', 'Waterlily', 115, NULL, NULL),
(97, 9, 'Water Lily Keychain.jpeg', 'Water Lily Keychain', 'Dried Water Lily Petals, Key Ring', 'Accessories', 'Water Lily', 114, NULL, NULL),
(98, 9, 'Water Lily Dreamcatcher.jpg', 'Water Lily Dreamcatcher', 'Water Lily Leaves, Feathers, Wooden Hoop', 'Accessories', 'Waterlily', 114, NULL, NULL),
(141, 2, '6145455085514244460.jpg', 'bag ni badeng', 'asdasd', 'Tables', '', 3, NULL, NULL),
(142, 1, '462546887_1476828552981732_4693112051903019344_n.png', 'bag ni badeng', 'vfev', 'Rugs', '', 3, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `message_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `sender_type` enum('user','merchant') NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `receiver_type` enum('user','merchant') NOT NULL,
  `message` text DEFAULT NULL,
  `media_path` varchar(255) DEFAULT NULL,
  `message_type` enum('text','image','video') DEFAULT 'text',
  `created_at` datetime DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0,
  `read_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`message_id`, `sender_id`, `sender_type`, `receiver_id`, `receiver_type`, `message`, `media_path`, `message_type`, `created_at`, `is_read`, `read_date`) VALUES
(239, 13, 'user', 3, 'merchant', 'ddd', NULL, 'text', '2024-11-17 00:07:31', 1, '2024-11-17 00:08:36'),
(240, 3, 'merchant', 3, 'merchant', 'Hello', NULL, 'text', '2024-11-17 00:08:54', 1, '2024-11-17 00:08:56'),
(241, 3, 'merchant', 5, 'merchant', 'Friend', NULL, 'text', '2024-11-17 00:09:14', 0, '2024-11-17 00:09:14'),
(242, 3, 'merchant', 13, 'user', 'Hello', NULL, 'text', '2024-11-17 00:09:20', 1, '2024-11-17 00:09:27'),
(243, 13, 'user', 5, 'merchant', 'hey', NULL, 'text', '2024-11-17 01:05:20', 0, '2024-11-17 01:05:20'),
(244, 13, 'user', 3, 'merchant', 'f', NULL, 'text', '2024-11-17 01:05:25', 1, '2024-11-17 10:33:48'),
(245, 13, 'user', 3, 'merchant', '', 'uploads/1731810609_313130379_550633933736717_8829119533192071056_n.jpg', 'image', '2024-11-17 10:30:09', 1, '2024-11-17 10:33:48'),
(246, 3, 'merchant', 3, 'merchant', 'h', NULL, 'text', '2024-11-17 10:33:46', 1, '2024-11-17 10:33:47'),
(247, 13, 'user', 1000, 'user', 'gotham', NULL, 'text', '2024-11-17 12:37:30', 0, '2024-11-17 12:37:30'),
(248, 13, 'user', 5, 'merchant', 'gotham', NULL, 'text', '2024-11-17 12:37:40', 0, '2024-11-17 12:37:40'),
(249, 13, 'user', 3, 'merchant', 'nagloko?', NULL, 'text', '2024-11-17 12:37:49', 0, '2024-11-17 12:37:49'),
(250, 13, 'user', 4, 'merchant', 'I\'m Batman', NULL, 'text', '2024-11-17 12:38:03', 0, '2024-11-17 12:38:03'),
(251, 2, 'merchant', 4, 'merchant', 'For gotham', NULL, 'text', '2024-11-17 12:38:55', 0, '2024-11-17 12:38:55'),
(252, 13, 'user', 9, 'merchant', 'I\'m batman', NULL, 'text', '2024-11-17 12:39:17', 1, '2024-11-17 12:41:27'),
(253, 9, 'merchant', 1000, 'user', 'No, I\'m Batman', NULL, 'text', '2024-11-17 12:41:43', 0, '2024-11-17 12:41:43'),
(254, 9, 'merchant', 13, 'user', 'No. I\'m Batman', NULL, 'text', '2024-11-17 12:41:58', 0, '2024-11-17 12:41:58'),
(255, 9, 'merchant', 1000, 'user', 'Iam', NULL, 'text', '2024-11-17 12:42:08', 0, '2024-11-17 12:42:08'),
(256, 9, 'merchant', 13, 'user', 'I\'am', NULL, 'text', '2024-11-17 12:42:14', 0, '2024-11-17 12:42:14'),
(257, 9, 'merchant', 13, 'user', 'No i,am', NULL, 'text', '2024-11-17 12:42:28', 0, '2024-11-17 12:42:28'),
(258, 9, 'merchant', 1000, 'user', 'f', NULL, 'text', '2024-11-17 12:42:46', 0, '2024-11-17 12:42:46'),
(259, 9, 'merchant', 13, 'user', 'Noice', NULL, 'text', '2024-11-17 12:44:03', 0, '2024-11-17 12:44:03'),
(260, 9, 'merchant', 3, 'merchant', 'Goods?', NULL, 'text', '2024-11-17 12:44:15', 0, '2024-11-17 12:44:15'),
(261, 9, 'merchant', 3, 'merchant', 'yeah', NULL, 'text', '2024-11-17 12:44:25', 0, '2024-11-17 12:44:25'),
(262, 9, 'merchant', 13, 'user', '1st', NULL, 'text', '2024-11-17 12:44:30', 0, '2024-11-17 12:44:30');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(55) NOT NULL,
  `user_id` int(50) NOT NULL,
  `product_id` int(50) NOT NULL,
  `product_color` varchar(100) NOT NULL,
  `product_size` varchar(200) NOT NULL,
  `quantity` bigint(30) NOT NULL,
  `total` varchar(5000) NOT NULL,
  `payment_method` varchar(200) NOT NULL,
  `user_note` varchar(500) NOT NULL,
  `order_date` datetime NOT NULL DEFAULT current_timestamp(),
  `ship_date` datetime DEFAULT current_timestamp(),
  `received_date` datetime NOT NULL DEFAULT current_timestamp(),
  `returned_date` datetime NOT NULL DEFAULT current_timestamp(),
  `cancel_date` datetime NOT NULL DEFAULT current_timestamp(),
  `status` varchar(255) NOT NULL,
  `reason_for_return` varchar(500) NOT NULL,
  `additional_deets` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `product_id`, `product_color`, `product_size`, `quantity`, `total`, `payment_method`, `user_note`, `order_date`, `ship_date`, `received_date`, `returned_date`, `cancel_date`, `status`, `reason_for_return`, `additional_deets`) VALUES
(278, 1, 19, 'Brown', 'small', 1, '100', 'cash', '', '2024-11-13 01:51:04', '2024-11-13 01:51:04', '2024-11-13 11:50:28', '2024-11-13 01:51:04', '2024-11-13 01:51:04', 'already rated', '', ''),
(279, 2, 23, 'Brown', 'small', 1, '200', 'cash', '', '2024-11-13 07:05:38', '2024-11-13 07:05:38', '2024-11-13 07:05:38', '2024-11-13 07:05:38', '2024-11-13 07:05:38', 'returned', '', ''),
(280, 20, 19, 'Brown', 'small', 1, '100', 'cash', '', '2024-11-13 10:58:02', '2024-11-13 10:58:02', '2024-11-13 10:58:02', '2024-11-13 10:58:02', '2024-11-13 10:58:02', 'returned', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `reset_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_color`
--

CREATE TABLE `product_color` (
  `color_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  `color` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_color`
--

INSERT INTO `product_color` (`color_id`, `product_id`, `application_id`, `color`) VALUES
(22, 19, 2, 'Brown'),
(23, 19, 2, 'White '),
(24, 19, 2, 'Beige'),
(25, 20, 2, 'Brown'),
(26, 20, 2, 'Beige'),
(28, 21, 2, ''),
(29, 21, 2, 'violet'),
(30, 21, 2, 'Red'),
(31, 22, 2, 'Khaki'),
(32, 22, 2, 'Brown'),
(34, 23, 2, 'Brown'),
(35, 23, 2, 'Nude'),
(36, 23, 2, 'Brown'),
(37, 24, 2, 'Beige'),
(38, 24, 2, 'Khaki'),
(43, 26, 2, 'Beige'),
(44, 27, 2, 'Black'),
(45, 27, 2, 'Brown'),
(46, 27, 2, 'Red'),
(47, 28, 2, ''),
(48, 28, 2, 'Beige'),
(49, 28, 2, 'Black'),
(50, 29, 2, 'Multiple'),
(51, 30, 2, 'Brown'),
(52, 30, 2, 'White '),
(53, 30, 2, 'Khaki'),
(54, 31, 3, 'Brown'),
(55, 31, 3, 'White '),
(56, 31, 3, 'Khaki'),
(57, 32, 3, 'Brown'),
(58, 32, 3, 'White '),
(59, 33, 3, 'Brown'),
(60, 33, 3, 'Khaki'),
(61, 33, 3, 'Black'),
(62, 34, 3, 'Brown'),
(63, 34, 3, 'Khaki'),
(64, 35, 3, 'White '),
(65, 35, 3, 'Beige'),
(66, 35, 3, 'Pale'),
(67, 36, 3, 'White '),
(68, 36, 3, 'Beige'),
(69, 36, 3, 'Pale'),
(70, 37, 3, 'White '),
(71, 37, 3, 'Beige'),
(72, 37, 3, 'Pale'),
(73, 38, 3, 'Brown'),
(74, 38, 3, 'Khaki'),
(75, 39, 3, 'White '),
(76, 39, 3, 'Beige'),
(77, 39, 3, 'Pale'),
(78, 40, 3, 'Brown'),
(79, 40, 3, 'Khaki'),
(80, 41, 3, 'White '),
(81, 41, 3, 'Beige'),
(82, 41, 3, 'Pale'),
(83, 42, 4, 'White '),
(84, 42, 4, 'Beige'),
(85, 43, 4, 'Brown'),
(86, 43, 4, 'Nude'),
(87, 44, 4, 'Multiple'),
(88, 45, 4, 'White '),
(89, 45, 4, 'Beige'),
(90, 45, 4, 'Pale'),
(91, 46, 4, 'Multiple'),
(92, 47, 4, 'Red'),
(93, 47, 4, 'Blue'),
(94, 47, 4, 'Green'),
(95, 47, 4, 'Black'),
(96, 47, 4, 'White '),
(97, 48, 4, 'Multiple'),
(98, 49, 4, 'Red'),
(99, 49, 4, 'Blue'),
(100, 49, 4, 'Green'),
(101, 49, 4, 'Brown'),
(102, 49, 4, 'White '),
(103, 49, 4, 'Beige'),
(104, 50, 4, 'Red'),
(105, 50, 4, 'Blue'),
(106, 50, 4, 'Green'),
(107, 51, 4, 'White '),
(108, 51, 4, 'Beige'),
(109, 52, 5, 'White '),
(110, 52, 5, 'Beige'),
(111, 53, 5, 'White '),
(112, 53, 5, 'Beige'),
(113, 54, 5, 'Multiple'),
(114, 55, 5, 'Adjustable'),
(115, 56, 5, 'Multiple'),
(116, 57, 5, 'pink'),
(117, 57, 5, 'Red'),
(118, 57, 5, 'yellow'),
(119, 57, 5, 'Black'),
(120, 57, 5, 'purple'),
(121, 58, 5, 'Multiple'),
(122, 59, 5, 'Red'),
(123, 59, 5, 'Blue'),
(124, 59, 5, 'yellow'),
(125, 59, 5, 'Black'),
(126, 59, 5, 'purple'),
(127, 60, 5, 'Brown'),
(128, 60, 5, 'Beige'),
(129, 61, 5, 'Multiple'),
(130, 62, 5, 'pink'),
(131, 62, 5, 'Red'),
(132, 62, 5, 'yellow'),
(133, 62, 5, 'Black'),
(134, 63, 5, 'pink'),
(135, 63, 5, 'Red'),
(136, 63, 5, 'yellow'),
(137, 63, 5, 'Black'),
(138, 64, 5, 'Brown'),
(139, 64, 5, 'Beige'),
(140, 65, 5, 'Brown'),
(141, 65, 5, 'Beige'),
(142, 66, 5, 'Brown'),
(143, 66, 5, 'Beige'),
(144, 66, 5, 'pale'),
(149, 68, 5, 'Brown'),
(150, 68, 5, 'Beige'),
(151, 68, 5, 'pale'),
(152, 69, 5, 'Brown'),
(153, 69, 5, 'Beige'),
(154, 69, 5, 'pale'),
(155, 69, 5, 'White '),
(156, 70, 5, 'Brown'),
(157, 70, 5, 'Beige'),
(158, 70, 5, 'pale'),
(159, 71, 5, 'Brown'),
(160, 71, 5, 'Beige'),
(161, 71, 5, 'Khaki'),
(162, 72, 5, 'Brown'),
(163, 72, 5, 'Beige'),
(164, 73, 5, 'Brown'),
(165, 73, 5, 'Beige'),
(166, 74, 6, ''),
(167, 74, 6, ''),
(168, 74, 6, ''),
(169, 74, 6, ''),
(170, 74, 6, ''),
(171, 75, 6, 'Brown'),
(172, 75, 6, 'Beige'),
(173, 75, 6, 'Khaki'),
(174, 76, 6, ''),
(175, 76, 6, ''),
(176, 77, 6, 'Brown'),
(177, 77, 6, 'Beige'),
(178, 77, 6, 'Khaki'),
(179, 78, 6, 'White '),
(180, 78, 6, 'Red'),
(181, 78, 6, 'pink'),
(182, 79, 6, 'Khaki'),
(183, 79, 6, 'White '),
(184, 80, 6, 'White '),
(186, 80, 6, 'Pale'),
(187, 81, 8, 'Brown'),
(188, 81, 8, 'Khaki'),
(189, 82, 8, 'Brown'),
(190, 82, 8, 'Khaki'),
(191, 83, 8, 'Brown'),
(192, 83, 8, 'Khaki'),
(193, 83, 8, 'Black'),
(194, 84, 8, 'Brown'),
(195, 84, 8, 'Khaki'),
(196, 84, 8, 'Pale'),
(197, 85, 8, 'Brown'),
(198, 85, 8, 'Khaki'),
(199, 85, 8, 'Pale'),
(200, 86, 8, 'Brown'),
(201, 86, 8, 'Khaki'),
(202, 87, 8, 'Brown'),
(203, 87, 8, 'Khaki'),
(204, 87, 8, 'Pale'),
(205, 88, 8, 'Brown'),
(206, 88, 8, 'Khaki'),
(207, 88, 8, 'Pale'),
(208, 89, 8, 'Green'),
(209, 89, 8, 'Yellow'),
(210, 90, 8, 'Multiple'),
(211, 91, 9, 'Green'),
(212, 91, 9, 'Yellow'),
(213, 92, 9, 'Khaki'),
(214, 92, 9, 'Brown'),
(215, 92, 9, 'White '),
(216, 93, 9, 'Blue'),
(217, 93, 9, 'Red'),
(218, 93, 9, 'White '),
(219, 93, 9, 'Green'),
(220, 93, 9, 'Yellow'),
(221, 94, 9, 'Khaki'),
(222, 94, 9, 'Brown'),
(223, 94, 9, 'White '),
(224, 95, 9, 'Khaki'),
(225, 95, 9, 'Brown'),
(226, 95, 9, 'White '),
(227, 96, 9, 'Blue'),
(228, 96, 9, 'Red'),
(229, 96, 9, 'White '),
(230, 96, 9, 'Green'),
(231, 97, 9, 'Blue'),
(232, 97, 9, 'Red'),
(233, 97, 9, 'White '),
(234, 98, 9, 'Blue'),
(235, 98, 9, 'Red'),
(236, 98, 9, 'White '),
(237, 98, 9, 'Green'),
(359, 141, 2, 'blue'),
(360, 142, 1, ''),
(361, 142, 1, '');

-- --------------------------------------------------------

--
-- Table structure for table `product_feedback`
--

CREATE TABLE `product_feedback` (
  `feedback_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `quality_rating` int(11) NOT NULL,
  `price_rating` int(11) NOT NULL,
  `service_rating` int(11) NOT NULL,
  `feedback_notes` text NOT NULL,
  `feedback_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_feedback`
--

INSERT INTO `product_feedback` (`feedback_id`, `product_id`, `user_id`, `quality_rating`, `price_rating`, `service_rating`, `feedback_notes`, `feedback_date`) VALUES
(46, 53, 14, 5, 5, 5, 'The seller is very responsive while the item itself is superb!', '2024-11-12 10:36:59'),
(47, 19, 1, 5, 5, 5, 'very comfy, very demure, very mindful', '2024-11-13 11:50:48');

-- --------------------------------------------------------

--
-- Table structure for table `product_sizes`
--

CREATE TABLE `product_sizes` (
  `size_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  `sizes` varchar(200) NOT NULL,
  `price` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_sizes`
--

INSERT INTO `product_sizes` (`size_id`, `product_id`, `application_id`, `sizes`, `price`) VALUES
(24, 19, 2, 'small', '100'),
(25, 19, 2, 'Medium', '150'),
(26, 19, 2, 'large', '250'),
(27, 20, 2, 'small', '2500'),
(28, 20, 2, 'Medium', '2800'),
(29, 20, 2, 'large', '3000'),
(30, 21, 2, 'Standard', '250'),
(31, 22, 2, 'small', '849'),
(32, 22, 2, 'Medium', '949'),
(33, 22, 2, 'large', '1.49'),
(35, 23, 2, 'small', '200'),
(36, 23, 2, 'Medium', '249'),
(37, 23, 2, 'large', '300'),
(38, 24, 2, 'small', '400'),
(39, 24, 2, 'Medium', '500'),
(40, 25, 2, 'small', '799'),
(41, 25, 2, 'Medium', '849'),
(42, 25, 2, 'large', '899'),
(43, 26, 2, 'small', '2200'),
(44, 26, 2, 'Medium', '2500'),
(45, 26, 2, 'large', '2700'),
(46, 27, 2, 'Standard', '200'),
(47, 28, 2, 'Standard', '2000'),
(48, 29, 2, 'small', '90'),
(49, 29, 2, 'Medium', '189'),
(50, 29, 2, 'large', '249'),
(51, 30, 2, 'Standard', '650'),
(52, 31, 3, 'Standard', '1000'),
(53, 32, 3, 'small', '350'),
(54, 32, 3, 'Medium', '400'),
(55, 32, 3, 'large', '450'),
(56, 33, 3, 'small', '250'),
(57, 33, 3, 'Medium', '550'),
(58, 33, 3, 'large', '950'),
(59, 34, 3, 'small', '649'),
(60, 34, 3, 'Medium', '699'),
(61, 34, 3, 'large', '749'),
(62, 35, 3, 'small', '59'),
(63, 35, 3, 'Medium', '69'),
(64, 35, 3, 'large', '89'),
(65, 36, 3, 'standard', '1200'),
(68, 37, 3, 'small', '499'),
(69, 37, 3, 'Medium', '599'),
(70, 37, 3, 'large', '699'),
(71, 38, 3, 'small', '699'),
(72, 38, 3, 'Medium', '849'),
(73, 38, 3, 'large', '949'),
(74, 39, 3, 'small', '299'),
(75, 39, 3, 'Medium', '349'),
(76, 39, 3, 'large', '399'),
(77, 40, 3, 'small', '459'),
(78, 40, 3, 'Medium', '559'),
(79, 40, 3, 'large', '659'),
(80, 41, 3, 'small', '299'),
(81, 41, 3, 'Medium', '349'),
(84, 43, 4, 'small', '799'),
(85, 43, 4, 'Medium', '849'),
(86, 43, 4, 'large', '899'),
(87, 44, 4, 'Standard', '359'),
(88, 45, 4, 'small', '459'),
(89, 45, 4, 'Medium', '666'),
(90, 45, 4, 'large', '889'),
(91, 46, 4, 'small', '149'),
(92, 46, 4, 'Medium', '199'),
(93, 46, 4, 'large', '249'),
(94, 47, 4, 'small', '149'),
(95, 47, 4, 'Medium', '199'),
(96, 47, 4, 'large', '249'),
(97, 48, 4, 'Standard', '299'),
(98, 49, 4, 'small', '159'),
(99, 49, 4, 'Medium', '189'),
(100, 49, 4, 'large', '229'),
(101, 50, 4, 'small', '159'),
(102, 50, 4, 'Medium', '199'),
(103, 51, 4, 'small', '999'),
(106, 52, 5, 'Standard', '69'),
(107, 53, 5, 'Standard', '49'),
(108, 54, 5, 'Standard', '149'),
(109, 55, 5, 'Standard', '149'),
(110, 56, 5, 'Small', '59'),
(111, 56, 5, 'Medium', '69'),
(112, 56, 5, 'large', '79'),
(113, 57, 5, 'Standard', '49'),
(114, 58, 5, 'Small', '99'),
(115, 58, 5, 'Medium', '149'),
(116, 58, 5, 'large', '189'),
(117, 59, 5, 'Small', '89'),
(118, 59, 5, 'Medium', '129'),
(119, 60, 5, 'Standard', '79'),
(120, 61, 5, 'Standard', '99'),
(121, 62, 5, 'Standard', '59'),
(122, 63, 5, 'Standard', '99'),
(123, 64, 6, 'small', '199'),
(124, 64, 6, 'Medium', '249'),
(125, 64, 6, 'large', '299'),
(126, 65, 6, 'small', '199'),
(127, 65, 6, 'Medium', '299'),
(128, 66, 6, 'small', '400'),
(129, 66, 6, 'Medium', '499'),
(130, 66, 6, 'large', '549'),
(134, 68, 6, 'small', '350'),
(135, 68, 6, 'Medium', '450'),
(136, 69, 6, 'small', '450'),
(137, 69, 6, 'Medium', '499'),
(138, 69, 6, 'large', '549'),
(139, 70, 6, 'small', '300'),
(140, 70, 6, 'Medium', '549'),
(141, 70, 6, 'large', '700'),
(142, 71, 6, 'small', '199'),
(143, 71, 6, 'Medium', '249'),
(144, 71, 6, 'large', '299'),
(145, 72, 6, 'small', '110'),
(146, 72, 6, 'Medium', '210'),
(147, 73, 6, 'small', '250'),
(148, 73, 6, 'Medium', '300'),
(149, 73, 6, 'large', '350'),
(150, 74, 7, 'Standard', '350'),
(151, 75, 7, 'small', '199'),
(152, 75, 7, 'Medium', '249'),
(153, 75, 7, 'large', '299'),
(154, 76, 7, 'Standard', '999'),
(155, 77, 7, 'Standard', '199'),
(156, 78, 7, 'small', '100'),
(157, 78, 7, 'Medium', '300'),
(158, 79, 7, 'Standard', '399'),
(159, 80, 7, 'Angklung Set', '3000'),
(160, 81, 8, 'small', '149'),
(161, 81, 8, 'Medium', '169'),
(162, 81, 8, 'large', '189'),
(163, 82, 8, 'small', '400'),
(164, 83, 8, 'small', '500'),
(165, 83, 8, 'Medium', '650'),
(166, 83, 8, 'large', '850'),
(167, 84, 8, 'small', '750'),
(168, 84, 8, 'Medium', '950'),
(169, 84, 8, 'large', '1150'),
(170, 85, 8, 'small', '750'),
(171, 85, 8, 'Medium', '950'),
(172, 86, 8, 'small', '349'),
(173, 86, 8, 'Medium', '399'),
(174, 87, 8, 'small', '459'),
(175, 87, 8, 'Medium', '529'),
(176, 87, 8, 'large', '749'),
(177, 88, 8, 'small', '250'),
(178, 88, 8, 'Medium', '300'),
(179, 88, 8, 'large', '350'),
(180, 89, 8, 'small', '99'),
(181, 89, 8, 'Medium', '129'),
(182, 90, 8, 'Standard', '850'),
(183, 91, 9, 'small', '800'),
(184, 91, 9, 'Medium', '900'),
(185, 91, 9, 'large', '1000'),
(186, 92, 9, 'small', '150'),
(187, 92, 9, 'Medium', '200'),
(188, 92, 9, 'large', '250'),
(189, 93, 9, 'small', '199'),
(190, 93, 9, 'Medium', '249'),
(191, 93, 9, 'large', '299'),
(192, 94, 9, 'small', '400'),
(193, 94, 9, 'Medium', '500'),
(194, 94, 9, 'large', '600'),
(195, 95, 9, 'small', '100'),
(196, 95, 9, 'Medium', '200'),
(197, 95, 9, 'large', '300'),
(198, 96, 9, 'small', '400'),
(199, 96, 9, 'Medium', '500'),
(200, 96, 9, 'large', '600'),
(201, 97, 9, 'small', '49'),
(202, 97, 9, 'Medium', '99'),
(203, 98, 9, 'small', '599'),
(204, 98, 9, 'Medium', '899'),
(359, 141, 2, 'small', '100'),
(360, 141, 2, 'medium', '200'),
(361, 142, 1, '', ''),
(362, 142, 1, '', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `crafthub_users`
--
ALTER TABLE `crafthub_users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `merchant_applications`
--
ALTER TABLE `merchant_applications`
  ADD PRIMARY KEY (`application_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `merchant_products`
--
ALTER TABLE `merchant_products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `merchant_id` (`application_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`message_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`reset_id`);

--
-- Indexes for table `product_color`
--
ALTER TABLE `product_color`
  ADD PRIMARY KEY (`color_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `application_id` (`application_id`);

--
-- Indexes for table `product_feedback`
--
ALTER TABLE `product_feedback`
  ADD PRIMARY KEY (`feedback_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `product_sizes`
--
ALTER TABLE `product_sizes`
  ADD PRIMARY KEY (`size_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `application_id` (`application_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=190;

--
-- AUTO_INCREMENT for table `crafthub_users`
--
ALTER TABLE `crafthub_users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `merchant_applications`
--
ALTER TABLE `merchant_applications`
  MODIFY `application_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `merchant_products`
--
ALTER TABLE `merchant_products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=143;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=263;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(55) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=281;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `reset_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `product_color`
--
ALTER TABLE `product_color`
  MODIFY `color_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=362;

--
-- AUTO_INCREMENT for table `product_feedback`
--
ALTER TABLE `product_feedback`
  MODIFY `feedback_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `product_sizes`
--
ALTER TABLE `product_sizes`
  MODIFY `size_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=363;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `merchant_applications`
--
ALTER TABLE `merchant_applications`
  ADD CONSTRAINT `merchant_applications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `crafthub_users` (`user_id`);

--
-- Constraints for table `merchant_products`
--
ALTER TABLE `merchant_products`
  ADD CONSTRAINT `merchant_products_ibfk_1` FOREIGN KEY (`application_id`) REFERENCES `merchant_applications` (`application_id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `crafthub_users` (`user_id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `merchant_products` (`product_id`);

--
-- Constraints for table `product_color`
--
ALTER TABLE `product_color`
  ADD CONSTRAINT `product_color_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `merchant_products` (`product_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_color_ibfk_2` FOREIGN KEY (`application_id`) REFERENCES `merchant_applications` (`application_id`);

--
-- Constraints for table `product_sizes`
--
ALTER TABLE `product_sizes`
  ADD CONSTRAINT `product_sizes_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `merchant_products` (`product_id`),
  ADD CONSTRAINT `product_sizes_ibfk_2` FOREIGN KEY (`application_id`) REFERENCES `merchant_applications` (`application_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
