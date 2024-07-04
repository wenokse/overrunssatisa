-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 23, 2024 at 07:53 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ecomm`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `size` varchar(50) NOT NULL,
  `color` varchar(10) NOT NULL,
  `quantity` int(11) NOT NULL,
  `shipping` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `product_id`, `size`, `color`, `quantity`, `shipping`) VALUES
(522, 165, 28, 'Small', 'Red', 1, '100'),
(523, 165, 28, 'Medium', 'Red', 1, '0'),
(524, 165, 28, 'Large', 'Red', 1, '0'),
(525, 165, 72, 'Small', 'Red', 1, '100'),
(526, 165, 72, 'Medium', 'Red', 1, '0');

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `cat_slug` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`id`, `name`, `cat_slug`) VALUES
(3, 'Accessories', 'accessories'),
(4, 'Pants', 'pants'),
(5, 'Shorts', 'shorts'),
(7, 'Bags', 'bags'),
(8, 'Shoes', 'shoes'),
(9, 'T-shirts', 't-shirts'),
(10, 'Sandals', 'sandals');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `msg` text NOT NULL,
  `commented_on` date DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `user_id`, `msg`, `commented_on`, `created_at`) VALUES
(1, 14, 'nice', '2022-04-24', '2022-04-24 06:27:49'),
(2, 22, 'kdkkd', '2022-04-24', '2022-04-24 06:44:26'),
(4, 22, 'dfdf', '2022-04-24', '2022-04-24 06:47:09'),
(5, 13, 'awesome', '2022-04-28', '2022-04-27 23:48:33'),
(6, 17, 'dfhhdj', '2022-05-02', '2022-05-02 02:09:38'),
(7, 16, 'wow', '2022-05-02', '2022-05-02 02:12:21'),
(8, 163, 'so cute', '2024-04-01', '2024-04-01 07:49:59');

-- --------------------------------------------------------

--
-- Table structure for table `comment_replies`
--

CREATE TABLE `comment_replies` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment_id` int(11) NOT NULL,
  `reply_msg` text NOT NULL,
  `commented_on` date DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comment_replies`
--

INSERT INTO `comment_replies` (`id`, `user_id`, `comment_id`, `reply_msg`, `commented_on`, `created_at`) VALUES
(3, 22, 1, 'dfda', '2023-04-20', '2023-04-20 06:44:07'),
(4, 22, 1, 'dfdf', '2023-04-20', '2023-04-20 06:44:55'),
(5, 22, 2, 'jjdjd', '2023-04-20', '2023-04-20 06:57:02'),
(6, 22, 1, 'cxcx', '2023-04-20', '2023-04-20 06:57:22'),
(7, 16, 4, 'dsffsf', '2023-04-20', '2023-04-20 06:57:37'),
(8, 17, 1, 'dfsadf', '2023-04-20', '2023-04-20 06:57:52'),
(9, 14, 2, '@Rowen Secuya fdfdf', '2023-04-20', '2023-04-20 06:58:10'),
(10, 13, 1, '@Alyzza Jyen Garcia jdjjdh', '2023-04-20', '2023-04-20 08:00:25'),
(11, 22, 1, '', '2023-04-20', '2023-04-20 02:36:45'),
(12, 22, 1, '@Rosie Jean Nunez ', '2023-04-20', '2023-04-20 02:37:00'),
(13, 68, 5, 'yeah', '2023-04-20', '2023-04-20 02:04:31'),
(14, 17, 5, '@Armelyn Escala nice', '2023-04-28', '2023-04-20 02:05:14'),
(15, 22, 6, 'yeah', '2023-04-20', '2023-04-20 02:12:41');

-- --------------------------------------------------------

--
-- Table structure for table `details`
--

CREATE TABLE `details` (
  `id` int(11) NOT NULL,
  `sales_id` int(11) NOT NULL,
  `size` varchar(50) NOT NULL,
  `color` varchar(10) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `shipping` int(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `details`
--

INSERT INTO `details` (`id`, `sales_id`, `size`, `color`, `product_id`, `quantity`, `shipping`) VALUES
(373, 208, 'null', 'Red', 27, 1, 100),
(374, 208, 'null', 'Green', 27, 1, 0),
(375, 209, 'Small', 'Red', 28, 1, 100),
(376, 209, 'Medium', 'Red', 28, 1, 0),
(377, 210, 'Small', 'Red', 28, 1, 100),
(378, 210, 'Medium', 'Red', 28, 1, 0),
(379, 210, 'XLarge', 'Red', 28, 1, 0),
(380, 211, 'Small', 'Red', 36, 1, 100),
(381, 211, 'Large', 'Red', 36, 1, 0),
(382, 212, '24', 'Blue', 30, 1, 100),
(383, 212, '27', 'Blue', 30, 1, 0),
(384, 213, '24', 'Blue', 30, 1, 100),
(385, 213, '25', 'Blue', 30, 1, 0),
(386, 213, '24', 'Blue', 29, 1, 100),
(387, 213, '25', 'Blue', 29, 1, 0),
(388, 213, '27', 'Blue', 30, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `name` text NOT NULL,
  `description` text NOT NULL,
  `slug` varchar(200) NOT NULL,
  `price` double NOT NULL,
  `stock` int(11) NOT NULL,
  `photo` varchar(200) NOT NULL,
  `date_view` date NOT NULL,
  `counter` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `description`, `slug`, `price`, `stock`, `photo`, `date_view`, `counter`) VALUES
(27, 3, 'Anti Rad Eye Glass', '<p>Anti Rad</p>\r\n', 'anti-rad-eye-glass', 80, 9, 'anti-rad-eye-glass.jpg', '2024-04-23', 1),
(28, 5, 'Taslan Short', '', 'taslan-short', 150, 5, 'taslan-short.jpg', '2024-04-23', 3),
(29, 4, 'Long Pants LANSITE Fashion', '', 'long-pants-lansite-fashion', 180, 1, 'long-pants-lansite-fashion.jpg', '2024-04-20', 2),
(30, 4, 'DS Unisex Pants Adidas', '', 'ds-unisex-pants-adidas', 350, 91, 'ds-unisex-pants-adidas.jpg', '2024-04-21', 3),
(31, 4, 'Jeans Fashionable Basic Casual Skinny Denim Pants for Men', '<ul>\r\n	<li>Brand: No Brand</li>\r\n	<li>98% Cotton, 2% Spandex</li>\r\n	<li>Imported</li>\r\n	<li>Zipper Closure</li>\r\n	<li>Hand / Machine Wash</li>\r\n	<li>Comfortable Stretch Span Pants</li>\r\n	<li>Slim Fit Style</li>\r\n	<li>Zip Fly and Button Closure</li>\r\n	<li>Available Size: 27</li>\r\n</ul>\r\n', 'jeans-fashionable-basic-casual-skinny-denim-pants-men', 200, 9, 'jeans-fashionable-basic-casual-skinny-denim-pants-men.jpg', '2024-04-18', 3),
(33, 4, 'ADIDAS PREMIUM JOGGER', '<p>Adidas Overruns Jogger Makapal</p>\r\n\r\n<p>* premium quality 100%</p>\r\n\r\n<p>* cotton super nice ang tela and naka embroide po yung adidas brand</p>\r\n\r\n<p>stretch and comfy 100% cotton</p>\r\n', 'adidas-premium-jogger', 350, 97, 'adidas-premium-jogger.jpg', '2024-04-04', 1),
(34, 4, 'Women Plus Size Ripped Stretch Skinny Jeans', '<p>PREMIUM JEANS: Constructed with high quality jeans with a brilliant curve hug while creating a slim look. The fabric which contains mostly Cotton and an amount of Elastane creates the perfect thickness helps hiding all your physical flaws while embracing your curve.</p>\r\n', 'women-plus-size-ripped-stretch-skinny-jeans', 200, 100, 'women-plus-size-ripped-stretch-skinny-jeans-high-rise-distressed-denim-jegging.jpg', '2022-04-28', 1),
(35, 4, 'Light Blue Jeans For Men Skinny Stretchable Pants', '<ul>\r\n	<li>Brand : No Brand</li>\r\n	<li>SKU : 281624829_PH-431496112</li>\r\n	<li>Wash Color : Light</li>\r\n	<li>Clothing Material : DENIM</li>\r\n</ul>\r\n', 'light-blue-jeans-men-skinny-stretchable-pants', 500, 100, 'light-blue-jeans-men-skinny-stretchable-pants.jpg', '2024-04-03', 2),
(36, 5, 'Cargo Short Above the knee', '<ul>\r\n	<li>Brand : No Brand</li>\r\n	<li>Pattern : Plain</li>\r\n	<li>Clothing Material : Shorts</li>\r\n	<li>Fit Type : Regular</li>\r\n	<li>Pants Fly : Zip</li>\r\n	<li>fa_season : Summer</li>\r\n	<li>Length : Cropped</li>\r\n</ul>\r\n', 'cargo-short-above-knee', 100, 7, 'cargo-short-above-knee.jpg', '2024-04-18', 1),
(37, 5, 'Taslan Shorts Quick-Drying Shorts Best Seller Board Short', '', 'taslan-shorts-quick-drying-shorts-best-seller-board-short', 300, 97, 'taslan-shorts-quick-drying-shorts-best-seller-board-short.jpg', '2024-04-05', 2),
(38, 5, 'PRINCE APPAREL  Taslan Vans Checkered Shorts for Men', '<ul>\r\n	<li>Brand : No Brand</li>\r\n	<li>16 inches length</li>\r\n	<li>26-35 waistline</li>\r\n	<li>Unisex</li>\r\n	<li>Material : Taslan</li>\r\n	<li>Occasion : Casual or Swimming</li>\r\n	<li>Summer Shorts Type</li>\r\n	<li>Comfortable to wear</li>\r\n	<li>Proper Packaging</li>\r\n	<li>Disinfected before shipping (safe to wear)</li>\r\n</ul>\r\n', 'prince-apparel-taslan-vans-checkered-shorts-men', 129, 100, 'prince-apparel-taslan-vans-checkered-shorts-men-checkboard-shorts-skateboard-sweatshorts-skating-shorts.jpg', '2022-05-02', 1),
(39, 8, 'FILA Shoes', '', 'fila-shoes', 450, 2, 'fila-shoes.jpg', '2024-04-21', 5),
(40, 7, 'Shoulder Bag', '', 'shoulder-bag', 350, 95, 'shoulder-bag.jpg', '2024-04-05', 1),
(41, 9, 'Women Shirt', '<p>Botton Spandex</p>\r\n', 'women-shirt', 480, 98, 'women-shirt.jpg', '2024-04-02', 2),
(42, 9, 'Nike Men T-shirt', '<p>Nike Men T-shirt</p>\r\n', 'nike-men-t-shirt', 250, 1, 'nike-men-t-shirt.jpg', '2022-05-17', 38),
(43, 10, 'Sandals', '', 'sandals', 100, 7, 'sandals.jpg', '2024-04-03', 1),
(44, 7, 'bag', '<p>bag</p>\r\n', 'bag', 150, 100, 'bag.jpg', '2023-05-08', 1),
(45, 9, 'T-shirt', '', 't-shirt', 350, 100, 't-shirt.jpg', '2024-04-04', 1),
(46, 3, 'Eye Glass 2', '', 'eye-glass-2', 500, 100, 'eye-glass-2.jpg', '2022-05-17', 1),
(47, 8, 'Shoes 1', '<p>shoes</p>\r\n', 'shoes-1', 350, 9, 'shoes-1.jpg', '2022-05-21', 1),
(64, 9, 'CropTops', '', 'croptops', 65, 99, 'croptops.jpg', '2024-04-09', 1),
(65, 3, 'Watch', '<p>Watch for women</p>\r\n', 'watch', 100.5, 100, 'watch.jpg', '2023-05-06', 1),
(66, 3, 'Eye Glass', '', 'eye-glass', 199.5, 100, 'eye-glass.png', '0000-00-00', 0),
(67, 3, 'Bracelet', '', 'bracelet', 99, 100, 'bracelet.jpg', '0000-00-00', 0),
(68, 4, 'Pants', '', 'pants', 399, 100, 'pants.jpg', '0000-00-00', 0),
(69, 4, 'Jogger Pants', '', 'jogger-pants', 170, 100, 'jogger-pants.jpg', '0000-00-00', 0),
(70, 4, 'Square Pants', '', 'square-pants', 130, 100, 'square-pants.jpg', '0000-00-00', 0),
(71, 5, 'Tiktok Shorts', '', 'tiktok-shorts', 50, 100, 'tiktok-shorts.jpg', '0000-00-00', 0),
(72, 5, 'Sexy Shorts', '', 'sexy-shorts', 95, 100, 'sexy-shorts.png', '2024-04-23', 1),
(73, 5, 'Sexy Short', '', 'sexy-short', 150, 97, 'sexy-short.jpg', '2024-04-17', 2),
(74, 5, 'Cargo Short', '', 'cargo-short', 295, 100, 'cargo-short.jpg', '0000-00-00', 0),
(75, 9, 'Cotton Blouse', '', 'cotton-blouse', 70, 99, 'cotton-blouse.jpg', '2024-04-04', 1),
(76, 9, 'Sexy Tops', '', 'sexy-tops', 80, 100, 'sexy-tops.jpg', '0000-00-00', 0),
(77, 9, 'Branded T-shirts', '', 'branded-t-shirts', 230, 100, 'branded-t-shirts.jpg', '0000-00-00', 0),
(78, 8, 'Shoes', '', 'shoes', 350, 100, 'shoes.jpg', '0000-00-00', 0),
(79, 7, 'Mens Bag', '', 'men-s-bag', 170, 99, 'men-s-bag.png', '2023-05-08', 1),
(80, 7, 'Bag Pack', '', 'bag-pack', 200, 100, 'bag-pack.jpg', '0000-00-00', 0),
(81, 7, '3n\'1 Bag', '', '3n-1-bag', 330, 100, '3n-1-bag.jpg', '0000-00-00', 0),
(82, 3, 'Headband', '', 'headband', 70, 100, 'headband.jpg', '0000-00-00', 0),
(83, 3, 'Clip', '', 'clip', 30, 100, 'clip.jpg', '0000-00-00', 0),
(84, 3, 'Plastic Earing\'s', '', 'plastic-earing-s', 20, 100, 'plastic-earing-s.jpg', '0000-00-00', 0);

-- --------------------------------------------------------

--
-- Table structure for table `return_details`
--

CREATE TABLE `return_details` (
  `id` int(11) NOT NULL,
  `sales_id` int(20) NOT NULL,
  `size` varchar(20) NOT NULL,
  `color` varchar(20) NOT NULL,
  `product_id` int(20) NOT NULL,
  `quantity` int(20) NOT NULL,
  `shipping` int(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `return_details`
--

INSERT INTO `return_details` (`id`, `sales_id`, `size`, `color`, `product_id`, `quantity`, `shipping`) VALUES
(2, 82, 'Small', 'Red', 73, 1, 100),
(3, 82, 'Large', 'Green', 73, 1, 100),
(4, 82, 'Small', 'Red', 37, 1, 100),
(5, 90, '36', 'White', 39, 1, 100),
(6, 72, '24', 'Blue', 29, 1, 100),
(7, 73, 'Small', 'Red', 28, 1, 100),
(8, 73, '24', 'Blue', 30, 1, 100),
(9, 73, '25', 'Blue', 30, 1, 100),
(10, 97, '24', 'Blue', 29, 1, 100),
(11, 74, '36', 'White', 43, 1, 100),
(12, 100, '36', 'White', 39, 1, 100),
(13, 100, '38', 'White', 39, 1, 100),
(14, 101, '36', 'White', 39, 1, 100),
(15, 101, '38', 'White', 39, 1, 100),
(16, 105, '36', 'White', 39, 1, 100),
(17, 105, '38', 'White', 39, 1, 100),
(18, 105, '37', 'White', 39, 1, 100),
(19, 106, '36', 'White', 39, 1, 100),
(20, 106, '37', 'White', 39, 1, 100),
(21, 107, '36', 'White', 39, 1, 100),
(22, 107, '38', 'White', 39, 1, 100),
(23, 108, '36', 'White', 39, 1, 100),
(24, 108, '37', 'White', 39, 1, 100),
(25, 109, '36', 'White', 39, 1, 100),
(26, 109, '38', 'White', 39, 1, 100),
(27, 111, 'Small', 'Red', 28, 1, 100),
(28, 111, 'Medium', 'Red', 28, 1, 100),
(29, 111, 'Large', 'Red', 28, 1, 100),
(30, 116, '36', 'White', 39, 1, 100),
(31, 116, '37', 'White', 39, 1, 100),
(32, 116, '38', 'White', 39, 1, 100),
(33, 122, 'Small', 'Red', 28, 1, 100),
(34, 122, 'Medium', 'Red', 28, 2, 100),
(35, 209, 'Small', 'Red', 28, 1, 100),
(36, 209, 'Medium', 'Red', 28, 1, 0),
(37, 211, 'Small', 'Red', 36, 1, 100),
(38, 211, 'Large', 'Red', 36, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `return_products`
--

CREATE TABLE `return_products` (
  `id` int(10) NOT NULL,
  `sales_id` int(10) NOT NULL,
  `pay_id` varchar(50) NOT NULL,
  `user_id` int(10) NOT NULL,
  `shipping` varchar(20) NOT NULL,
  `return_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `return_products`
--

INSERT INTO `return_products` (`id`, `sales_id`, `pay_id`, `user_id`, `shipping`, `return_date`) VALUES
(54, 209, '6620d64570563', 165, '0', '2024-04-18'),
(55, 211, '6620dc844c38e', 165, '0', '2024-04-18');

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `pay_id` varchar(50) NOT NULL,
  `sales_date` date NOT NULL,
  `status` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`id`, `user_id`, `pay_id`, `sales_date`, `status`) VALUES
(212, 165, '6620dd497f4c2', '2024-04-18', 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(200) NOT NULL,
  `password` varchar(60) NOT NULL,
  `type` int(1) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `address` text NOT NULL,
  `contact_info` varchar(100) NOT NULL,
  `photo` varchar(200) NOT NULL,
  `status` int(1) NOT NULL,
  `activate_code` varchar(15) NOT NULL,
  `reset_code` varchar(15) NOT NULL,
  `created_on` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `type`, `firstname`, `lastname`, `address`, `contact_info`, `photo`, `status`, `activate_code`, `reset_code`, `created_on`) VALUES
(1, 'admin@gmail.com', '$2y$10$u.3hXKCTs4QPXJ8Vb10TyeUXM1e7HxPiqYH7pap/dAyd9eNpjdDfS', 1, 'Overruns Sa Tisa', 'Online Shop', '', '', 'logo1.png', 1, '', 'ZXOSlN9fg7MoyLP', '2023-04-20'),
(13, 'garciaalyzzajyen04@gmail.com', '$2y$10$zF59FIRy9Y8jJuCSDQSwhO86m.NawJ.N0N3JyxVN5e5DML2RFhloG', 0, 'Alyzza Jyen', 'Garcia', 'Kabangbang, Bantayan, Cebu', '09382230605', 'alyzza.jpeg', 1, '', 'IOgrlpY7MFoitbT', '2023-04-10'),
(16, 'rosiejeannunez019@gmail.com', '$2y$10$tDNDOPrT3FjTmW1LBJ.B0uvv2463hYrQ/m1i00fELFZAK2T3Iq2Ry', 0, 'Rosie Jean', 'Nunez', 'Tabagak, Madridejos,Cebu', '09275370235', 'rosie.jpg', 1, '', 'xsYFPyJzZQ1Sfuc', '2023-04-20'),
(17, 'escalaarmelyn05@gmail.com', '$2y$10$qbK026ubDmMnfn6dggxEqek.8o4CkBVWaB.W9m.Owkl4uQGvSolzK', 0, 'Armelyn', 'Escala', 'Tugas, Madridejos Cebu', '09636541545', 'arme.jpg', 1, '', '', '2023-04-10'),
(99, 'staff@gmail.com', '$2y$10$HB7ktkUtVxRmo//2mVJA.uKBDwRbN10WXn6JW7D/cEi3gAcbKYBZ2', 2, 'Staff', '50', '', '', '', 1, '', '', '2023-04-20'),
(100, 'staff01@gmail.com', '$2y$10$e3OqiALQE/snMaVkXLfYcumQG2kaja3LmCLBdaUFx2QTw9ra2h0A.', 2, 'Staff', '49', '', '', '', 1, '', '', '2023-04-20'),
(101, 'staff02@gmail.com', '$2y$10$oB.JESpwJB0sE6gFDlLraOVdYhgOQ0dMmx5BxPii/6o/QIvgbtbDe', 2, 'Staff', '48', '', '', '', 1, '', '', '2023-04-20'),
(102, 'staff03@gmail.com', '$2y$10$glRPkSFhpfZgtspCQxV5/uKRUwlO..7KxOYCUszh3qhSGtgtea2NW', 2, 'Staff', '47', '', '', '', 1, '', '', '2023-04-20'),
(103, 'staff04@gmail.com', '$2y$10$qvHefkbupLaRTxK7Ls952um5LbDGwylCyag62jQzAdAGY4itTjp9W', 2, 'Staff', '46', '', '', '', 1, '', '', '2023-04-20'),
(163, 'secuya@gmail.com', '$2y$10$RNRsFJub3dDmqD73tFoH5.7N8oL5C50sOj0yS86InNsLFrYWL7fhW', 0, 'Rowen', 'Secuya', 'Kabangbang', '', 'gwapo.jpg', 1, '', '', '2024-04-01'),
(164, 'rowen@gmail.com', '$2y$10$4T4FzjG7pFhX7Ed2vBPIuuS753lMu346myl2NxgJQmGc1db0/.B/S', 0, 'Rowen', 'asedfs', 'Kabangbang', '09124547474', '', 1, '', '', '2024-04-01'),
(165, 'danilo@gmail.com', '$2y$10$kKEGs3hyuaI32gNMomzMZ.MAVhLVd1UcslxQaykBTma9JHXmpQoWO', 0, 'danilo', 'asedfs', 'Kabangbang', '09124547474', '', 1, '', '', '2024-04-02'),
(168, 'admin2@gmail.com', '$2y$10$To16WaPUEaA7GRacVjARQOua2TmzedJ5QOMgVRplPhzKH/.EahUMO', 0, 'Rowen', 'Secuya', 'Kabangbang', '09124547474', '', 1, '', '', '2024-04-04'),
(170, 'try@gmail.com', '$2y$10$8gcXxxWTqIQzLQV7Z591G.SWBoKLWVKF3y/itJTggI1BhGAP1fquK', 0, 'Rosie', 'Jean', 'Kabangbang', '', 'rosie.jpg', 1, '', '', '2024-04-08');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `comment_replies`
--
ALTER TABLE `comment_replies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `details`
--
ALTER TABLE `details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `return_details`
--
ALTER TABLE `return_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `return_products`
--
ALTER TABLE `return_products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=527;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `comment_replies`
--
ALTER TABLE `comment_replies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `details`
--
ALTER TABLE `details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=389;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=87;

--
-- AUTO_INCREMENT for table `return_details`
--
ALTER TABLE `return_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `return_products`
--
ALTER TABLE `return_products`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=214;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=171;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
