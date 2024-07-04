-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 21, 2024 at 02:11 AM
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
(638, 163, 29, '24', 'Blue', 1, '100'),
(652, 13, 79, 'undefined', 'Red', 2, '100'),
(659, 178, 91, 'Small', 'Red', 1, '100');

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
(10, 'Sandals', 'sandals'),
(17, 'Terno for Kids', 'terno-kids');

-- --------------------------------------------------------

--
-- Table structure for table `comment`
--

CREATE TABLE `comment` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `likes` int(50) NOT NULL,
  `dislikes` int(50) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comment`
--

INSERT INTO `comment` (`id`, `product_id`, `user_id`, `comment`, `likes`, `dislikes`, `parent_id`, `created_at`) VALUES
(1, 29, 163, 'fbg', 0, 0, 0, '2024-05-19 00:00:00'),
(2, 31, 163, 'nice', 0, 0, 0, '2024-05-19 00:00:00'),
(3, 31, 163, 'dcd', 0, 0, 0, '2024-05-19 00:00:00'),
(4, 33, 163, 'great', 0, 0, 0, '2024-05-19 00:00:00'),
(5, 30, 163, 'vdfvd', 0, 0, 0, '2024-05-19 00:00:00'),
(6, 83, 180, 'I rate this 5 star, I like this system', 0, 0, 0, '2024-05-19 00:00:00'),
(7, 34, 180, 'Nice jeans Baga sya', 0, 0, 0, '2024-05-19 00:00:00'),
(8, 34, 180, 'sac', 0, 0, 0, '2024-05-19 00:00:00'),
(9, 34, 180, 'scsdcsd', 0, 0, 0, '2024-05-19 00:00:00'),
(10, 39, 163, 'Nindot, ug baga barato ra ', 3, 0, 0, '2024-05-20 00:00:00'),
(11, 39, 13, 'If you would like to buy product that affordable, just go to this shop.', 0, 0, 0, '2024-05-20 00:00:00'),
(12, 91, 178, 'My kids like this, It\'s perfect for nowadays climate. init man gud ', 0, 0, 0, '2024-05-21 00:00:00'),
(13, 91, 178, 'I love it', 0, 0, 0, '2024-05-21 00:00:00'),
(14, 88, 178, 'I love it', 0, 0, 0, '2024-05-21 07:28:05'),
(15, 88, 178, 'nice', 0, 0, 0, '2024-05-21 07:30:04'),
(16, 88, 178, 'cxvx', 0, 0, 0, '2024-05-21 07:34:15');

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
(388, 213, '27', 'Blue', 30, 1, 0),
(389, 214, 'Small', 'Red', 28, 1, 100),
(390, 214, 'Medium', 'Red', 28, 1, 0),
(391, 215, 'Small', 'Red', 28, 1, 100),
(392, 215, 'Medium', 'Red', 28, 1, 0),
(393, 215, 'Large', 'Red', 28, 1, 0),
(394, 216, 'Small', 'Red', 28, 1, 100),
(395, 216, 'Medium', 'Red', 28, 1, 0),
(396, 217, '24', 'Blue', 31, 1, 100),
(397, 217, '25', 'Blue', 31, 1, 0),
(398, 218, '24', 'Blue', 31, 1, 100),
(399, 218, '25', 'Blue', 31, 1, 0),
(400, 219, 'Small', 'Red', 36, 1, 100),
(401, 220, 'Small', 'Red', 36, 1, 100),
(402, 221, 'null', 'Red', 40, 1, 100),
(403, 221, 'null', 'Green', 40, 1, 0),
(404, 222, 'null', 'Red', 40, 1, 100),
(405, 222, 'null', 'Blue', 40, 1, 0),
(406, 223, 'null', 'Red', 40, 1, 100),
(407, 223, 'null', 'Green', 40, 1, 0),
(408, 224, 'null', 'Red', 40, 1, 100),
(409, 224, 'null', 'Green', 40, 1, 0),
(410, 225, 'null', 'Red', 40, 1, 100),
(411, 225, 'null', 'Green', 40, 1, 0),
(412, 226, 'null', 'Red', 40, 1, 100),
(413, 226, 'null', 'Green', 40, 1, 0),
(414, 227, 'null', 'Red', 40, 1, 100),
(415, 227, 'null', 'Green', 40, 1, 0),
(416, 228, 'null', 'Red', 40, 1, 100),
(417, 229, 'null', 'Red', 40, 1, 100),
(418, 230, 'null', 'Red', 40, 1, 100),
(419, 231, 'null', 'Red', 40, 1, 100),
(420, 232, 'null', 'Red', 40, 1, 100),
(421, 233, 'null', 'Red', 40, 1, 100),
(422, 234, 'null', 'Red', 40, 3, 100),
(423, 235, 'null', 'Red', 40, 1, 100),
(424, 236, 'null', 'Red', 40, 1, 100),
(425, 237, 'null', 'Red', 40, 1, 100),
(426, 238, 'null', 'Red', 40, 1, 100),
(427, 239, 'null', 'Red', 40, 1, 100),
(428, 240, 'null', 'Red', 80, 1, 100),
(429, 241, '24', 'Blue', 33, 1, 100),
(430, 242, 'Small', 'Red', 75, 2, 100),
(431, 242, 'Medium', 'Red', 75, 1, 0),
(432, 243, 'Small', 'Red', 75, 1, 100),
(433, 244, 'Small', 'Red', 28, 3, 100),
(434, 245, 'null', 'Red', 40, 1, 100),
(435, 246, 'Small', 'Red', 36, 2, 100),
(436, 247, '24', 'Blue', 31, 3, 100),
(437, 248, '24', 'Blue', 33, 1, 100),
(438, 248, '26', 'Blue', 33, 2, 0),
(439, 249, '24', 'Blue', 68, 2, 100),
(440, 250, 'Small', 'Red', 36, 1, 100),
(441, 250, 'Medium', 'Red', 36, 1, 0),
(442, 251, 'null', 'Red', 81, 2, 100),
(443, 252, '24', 'Blue', 31, 1, 100),
(444, 253, '24', 'Blue', 31, 1, 100),
(445, 254, '24', 'Blue', 31, 1, 100),
(446, 255, '24', 'Blue', 31, 1, 100),
(447, 256, '24', 'Blue', 31, 1, 100),
(448, 257, '24', 'Blue', 31, 1, 100),
(449, 258, 'Small', 'Red', 72, 1, 100),
(450, 259, 'undefined', 'Red', 82, 3, 100),
(451, 260, '24', 'Blue', 34, 3, 100),
(452, 262, '24', 'Blue', 31, 1, 100),
(453, 263, 'undefined', 'Red', 66, 1, 100),
(454, 264, 'Small', 'Red', 37, 1, 100),
(455, 265, '36', 'White', 39, 2, 100),
(456, 266, 'undefined', 'Red', 84, 1, 100),
(457, 267, '36', 'White', 47, 1, 100),
(458, 268, 'Small', 'Red', 45, 3, 100),
(459, 268, 'Small', 'Red', 88, 1, 100),
(460, 268, 'Small', 'Red', 87, 1, 100),
(461, 269, 'Small', 'Red', 42, 1, 100),
(462, 269, '24', 'Blue', 34, 2, 100),
(463, 270, 'Small', 'Red', 92, 3, 100),
(464, 271, 'Small', 'Red', 92, 8, 100),
(465, 272, 'Small', 'Red', 91, 2, 100),
(466, 272, 'Large', 'Red', 91, 1, 0),
(467, 273, '24', 'Blue', 34, 1, 100),
(468, 274, '24', 'Blue', 30, 1, 100),
(469, 274, '24', 'Blue', 31, 1, 100),
(470, 275, 'Small', 'Red', 88, 1, 100),
(471, 275, 'Small', 'Red', 87, 1, 100),
(472, 276, 'Small', 'Red', 92, 1, 100),
(473, 276, 'Small', 'Yellow', 92, 1, 0),
(474, 276, '24', 'Blue', 29, 1, 100),
(475, 277, 'Small', 'Red', 77, 1, 100),
(476, 277, 'Medium', 'Red', 77, 2, 0);

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
(27, 3, 'Anti Rad Eye Glass', '<p>Anti Rad</p>\r\n', 'anti-rad-eye-glass', 80, 0, 'anti-rad-eye-glass.jpg', '2024-05-18', 1),
(28, 5, 'Taslan Short', '', 'taslan-short', 100, 1, 'taslan-short.jpg', '2024-05-21', 3),
(29, 4, 'Long Pants LANSITE Fashion', '', 'long-pants-lansite-fashion', 180, 0, 'long-pants-lansite-fashion.jpg', '2024-05-20', 1),
(30, 4, 'DS Unisex Pants Adidas', '', 'ds-unisex-pants-adidas', 150, 90, 'ds-unisex-pants-adidas.jpg', '2024-05-21', 1),
(31, 4, 'Jeans Fashionable Pants for Men', '<ul>\r\n	<li>Brand: No Brand</li>\r\n	<li>98% Cotton, 2% Spandex</li>\r\n	<li>Imported</li>\r\n	<li>Zipper Closure</li>\r\n	<li>Hand / Machine Wash</li>\r\n	<li>Comfortable Stretch Span Pants</li>\r\n	<li>Slim Fit Style</li>\r\n	<li>Zip Fly and Button Closure</li>\r\n	<li>Available Size: 27</li>\r\n</ul>\r\n', 'jeans-fashionable-pants-men', 250, 4, 'jeans-fashionable-basic-casual-skinny-denim-pants-men.jpg', '2024-05-20', 2),
(33, 4, 'ADIDAS PREMIUM JOGGER', '<p>Adidas Overruns Jogger Makapal</p>\r\n\r\n<p>* premium quality 100%</p>\r\n\r\n<p>* cotton super nice ang tela and naka embroide po yung adidas brand</p>\r\n\r\n<p>stretch and comfy 100% cotton</p>\r\n', 'adidas-premium-jogger', 150, 97, 'adidas-premium-jogger.jpg', '2024-05-19', 5),
(34, 4, 'Ripped Stretch Skinny Jeans', '<p>PREMIUM JEANS: Constructed with high quality jeans with a brilliant curve hug while creating a slim look. The fabric which contains mostly Cotton and an amount of Elastane creates the perfect thickness helps hiding all your physical flaws while embracing your curve.</p>\r\n', 'ripped-stretch-skinny-jeans', 200, 94, 'women-plus-size-ripped-stretch-skinny-jeans-high-rise-distressed-denim-jegging.jpg', '2024-05-20', 1),
(35, 4, 'Skinny Stretchable Pants For Men', '<ul>\r\n	<li>Brand : No Brand</li>\r\n	<li>SKU : 281624829_PH-431496112</li>\r\n	<li>Wash Color : Light</li>\r\n	<li>Clothing Material : DENIM</li>\r\n</ul>\r\n', 'skinny-stretchable-pants-men', 250, 50, 'light-blue-jeans-men-skinny-stretchable-pants.jpg', '2024-04-03', 2),
(36, 5, 'Cargo Short Above the knee', '<ul>\r\n	<li>Brand : No Brand</li>\r\n	<li>Pattern : Plain</li>\r\n	<li>Clothing Material : Shorts</li>\r\n	<li>Fit Type : Regular</li>\r\n	<li>Pants Fly : Zip</li>\r\n	<li>fa_season : Summer</li>\r\n	<li>Length : Cropped</li>\r\n</ul>\r\n', 'cargo-short-above-knee', 100, 6, 'cargo-short-above-knee.jpg', '2024-05-21', 6),
(37, 5, 'Taslan Shorts Quick-Drying ', '', 'taslan-shorts-quick-drying', 100, 20, 'taslan-shorts-quick-drying-shorts-best-seller-board-short.jpg', '2024-05-19', 18),
(38, 5, 'PRINCE APPAREL  Taslan Shorts ', '<ul>\r\n	<li>Brand : No Brand</li>\r\n	<li>16 inches length</li>\r\n	<li>26-35 waistline</li>\r\n	<li>Unisex</li>\r\n	<li>Material : Taslan</li>\r\n	<li>Occasion : Casual or Swimming</li>\r\n	<li>Summer Shorts Type</li>\r\n	<li>Comfortable to wear</li>\r\n	<li>Proper Packaging</li>\r\n	<li>Disinfected before shipping (safe to wear)</li>\r\n</ul>\r\n', 'prince-apparel-taslan-shorts', 100, 20, 'prince-apparel-taslan-vans-checkered-shorts-men-checkboard-shorts-skateboard-sweatshorts-skating-shorts.jpg', '2024-05-21', 18),
(39, 8, 'FILA Shoes', '', 'fila-shoes', 300, 0, 'fila-shoes.jpg', '2024-05-21', 3),
(40, 7, 'Shoulder Bag', '', 'shoulder-bag', 100, 94, 'shoulder-bag.jpg', '2024-05-18', 4),
(41, 9, 'Women Shirt', '<p>Botton Spandex</p>\r\n', 'women-shirt', 100, 98, 'women-shirt.jpg', '2024-04-02', 2),
(42, 9, 'Nike Men T-shirt', '<p>Nike Men T-shirt</p>\r\n', 'nike-men-t-shirt', 200, 0, 'nike-men-t-shirt.jpg', '2024-05-19', 23),
(43, 10, 'Sandals', '', 'sandals', 100, 7, 'sandals.jpg', '2024-05-20', 4),
(44, 7, 'bag', '<p>bag</p>\r\n', 'bag', 100, 100, 'bag.jpg', '2024-05-17', 1),
(45, 9, 'T-shirt', '', 't-shirt', 60, 100, 't-shirt.jpg', '2024-05-20', 2),
(46, 3, 'Eye Glass 2', '', 'eye-glass-2', 150, 100, 'eye-glass-2.jpg', '2024-05-18', 1),
(47, 8, 'Shoes 1', '<p>shoes</p>\r\n', 'shoes-1', 350, 8, 'shoes-1.jpg', '2024-05-21', 1),
(64, 9, 'CropTops', '', 'croptops', 65, 99, 'croptops.jpg', '2024-04-09', 1),
(65, 3, 'Watch', '<p>Watch for women</p>\r\n', 'watch', 100.5, 100, 'watch.jpg', '2024-05-19', 5),
(66, 3, 'Eye Glass', '', 'eye-glass', 120, 99, 'eye-glass.png', '2024-05-21', 1),
(67, 3, 'Bracelet', '', 'bracelet', 50, 100, 'bracelet.jpg', '0000-00-00', 0),
(68, 4, 'Pants', '', 'pants', 120, 100, 'pants.jpg', '2024-05-17', 1),
(69, 4, 'Jogger Pants', '', 'jogger-pants', 120, 100, 'jogger-pants.jpg', '0000-00-00', 0),
(70, 4, 'Square Pants', '', 'square-pants', 100, 100, 'square-pants.jpg', '0000-00-00', 0),
(71, 5, 'Tiktok Shorts', '', 'tiktok-shorts', 50, 100, 'tiktok-shorts.jpg', '2024-05-17', 3),
(72, 5, 'Sexy Shorts', '', 'sexy-shorts', 95, 100, 'sexy-shorts.png', '2024-05-17', 2),
(73, 5, 'Sexy Short', '', 'sexy-short', 100, 97, 'sexy-short.jpg', '2024-04-17', 2),
(74, 5, 'Cargo Short', '', 'cargo-short', 250, 100, 'cargo-short.jpg', '2024-05-17', 2),
(75, 9, 'Cotton Blouse', '', 'cotton-blouse', 70, 99, 'cotton-blouse.jpg', '2024-05-17', 2),
(76, 9, 'Sexy Tops', '', 'sexy-tops', 80, 100, 'sexy-tops.jpg', '2024-05-17', 1),
(77, 9, 'Branded T-shirts', '', 'branded-t-shirts', 150, 97, 'branded-t-shirts.jpg', '2024-05-21', 1),
(78, 8, 'Shoes', '', 'shoes', 350, 100, 'shoes.jpg', '2024-05-18', 1),
(79, 7, 'Mens Bag', '', 'mens-bag', 100, 99, 'men-s-bag.png', '2024-05-20', 1),
(80, 7, 'Bag Pack', '', 'bag-pack', 200, 100, 'bag-pack.jpg', '2024-05-17', 5),
(81, 7, '3n\'1 Bag', '', '3n-1-bag', 330, 100, '3n-1-bag.jpg', '2024-05-17', 1),
(82, 3, 'Headband', '', 'headband', 70, 97, 'headband.jpg', '2024-05-20', 5),
(83, 3, 'Clip', '', 'clip', 30, 50, 'clip.jpg', '2024-05-19', 3),
(84, 3, 'Plastic Earing\'s', '', 'plastic-earing-s', 20, 20, 'plastic-earing-s.jpg', '2024-05-19', 1),
(87, 17, 'Suns Jersy ', '<p>100% cotton</p>\r\n', 'suns-jersy', 120, 9, 'suns-jersy.jpg', '2024-05-20', 1),
(88, 17, 'Tiktok Terno ', '', 'tiktok-terno', 120, 9, 'tiktok-terno.jpg', '2024-05-21', 13),
(89, 17, 'Dickies', '', 'dickies', 120, 10, 'dickies.jpg', '2024-05-19', 1),
(90, 17, 'Rockets', '', 'rockets', 120, 10, 'rockets.jpg', '2024-05-20', 3),
(91, 17, 'Grizzlies', '', 'grizzlies', 120, 7, 'grizzlies.jpg', '2024-05-21', 17),
(92, 17, 'Lakers', '', 'lakers', 120, 9, 'lakers.jpg', '2024-05-20', 3);

-- --------------------------------------------------------

--
-- Table structure for table `ratings`
--

CREATE TABLE `ratings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(50) NOT NULL,
  `rating` varchar(20) NOT NULL,
  `created_at` date NOT NULL,
  `updated_at` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ratings`
--

INSERT INTO `ratings` (`id`, `user_id`, `product_id`, `rating`, `created_at`, `updated_at`) VALUES
(20, 163, 43, '5', '2024-05-18', '0000-00-00'),
(21, 163, 43, '1', '2024-05-18', '0000-00-00'),
(22, 163, 43, '5', '2024-05-18', '0000-00-00'),
(23, 163, 43, '4', '2024-05-18', '0000-00-00'),
(24, 163, 43, '5', '2024-05-18', '0000-00-00'),
(25, 180, 47, '4', '2024-05-18', '0000-00-00'),
(26, 163, 45, '5', '2024-05-18', '0000-00-00'),
(27, 163, 88, '3', '2024-05-18', '0000-00-00'),
(28, 163, 87, '5', '2024-05-18', '0000-00-00'),
(29, 180, 37, '4', '2024-05-19', '0000-00-00'),
(30, 180, 37, '1', '2024-05-19', '0000-00-00'),
(31, 180, 83, '5', '2024-05-19', '0000-00-00'),
(32, 180, 34, '5', '2024-05-19', '0000-00-00'),
(36, 180, 34, '4', '2024-05-19', '0000-00-00'),
(37, 13, 39, '5', '2024-05-20', '0000-00-00'),
(38, 178, 39, '3', '2024-05-20', '0000-00-00'),
(39, 178, 91, '3', '2024-05-20', '0000-00-00');

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
(38, 211, 'Large', 'Red', 36, 1, 0),
(39, 218, '24', 'Blue', 31, 1, 100),
(40, 218, '25', 'Blue', 31, 1, 0),
(41, 238, 'null', 'Red', 40, 1, 100),
(42, 248, '24', 'Blue', 33, 1, 100),
(43, 248, '26', 'Blue', 33, 2, 0),
(44, 249, '24', 'Blue', 68, 2, 100),
(45, 250, 'Small', 'Red', 36, 1, 100),
(46, 250, 'Medium', 'Red', 36, 1, 0),
(47, 251, 'null', 'Red', 81, 2, 100),
(48, 268, 'Small', 'Red', 45, 3, 100),
(49, 268, 'Small', 'Red', 88, 1, 100),
(50, 268, 'Small', 'Red', 87, 1, 100);

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
(62, 268, '6648dec50f276', 163, '100', '2024-05-19');

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
(245, 163, '6646fed63422a', '2024-05-17', 3),
(247, 163, '6647115814828', '2024-05-17', 3),
(259, 163, '664747828098f', '2024-05-17', 3),
(260, 179, '664754cb4a450', '2024-05-17', 3),
(262, 178, '664763288f7c8', '2024-05-17', 3),
(263, 178, '66477896a918e', '2024-05-17', 3),
(264, 163, '664896790deea', '2024-05-18', 3),
(265, 163, '6648aab2f13aa', '2024-05-18', 3),
(267, 180, '6648cdd340323', '2024-05-18', 3),
(269, 180, '664a11f76cd6c', '2024-05-19', 3),
(272, 178, '664af23dc3f5b', '2024-05-20', 2),
(273, 178, '664b267142093', '2024-05-20', 3),
(274, 178, '664b623a5723f', '2024-05-20', 0),
(275, 178, '664b66097a505', '2024-05-20', 3),
(276, 185, '664b6df497788', '2024-05-20', 2),
(277, 186, '664b7c15bfd8c', '2024-05-20', 2);

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
  `address2` varchar(50) NOT NULL,
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

INSERT INTO `users` (`id`, `email`, `password`, `type`, `firstname`, `lastname`, `address`, `address2`, `contact_info`, `photo`, `status`, `activate_code`, `reset_code`, `created_on`) VALUES
(1, 'admin@gmail.com', '$2y$10$u.3hXKCTs4QPXJ8Vb10TyeUXM1e7HxPiqYH7pap/dAyd9eNpjdDfS', 1, 'Overruns Sa Tisa', 'Online Shop', '', '', '', 'logo1.png', 1, '', 'ZXOSlN9fg7MoyLP', '2023-04-20'),
(13, 'garciaalyzzajyen04@gmail.com', '$2y$10$/ViSAl8nww4MviHnfcdFlORpqNmf477UVQ7Oe1zbwQPZ5l5ACzj..', 0, 'Alyzza Jyen', 'Garcia', 'Kabangbang, Bantayan, Cebu', 'Purok Quarry', '09124165259', 'alyzza.jpeg', 1, '', 'gJq8kaVKeQSENlB', '2023-04-10'),
(99, 'staff@gmail.com', '$2y$10$HB7ktkUtVxRmo//2mVJA.uKBDwRbN10WXn6JW7D/cEi3gAcbKYBZ2', 2, 'Staff', '50', '', '', '', '', 1, '', '', '2023-04-20'),
(163, 'secuya@gmail.com', '$2y$10$RNRsFJub3dDmqD73tFoH5.7N8oL5C50sOj0yS86InNsLFrYWL7fhW', 0, 'ROWEN', 'Secuya', 'Bantayan, Kabangbang, ', 'Purok Quarry', '09073711101', 'gwapo.jpg', 1, '', '', '2024-04-01'),
(178, 'rowen@gmail.com', '$2y$10$HtHdP6qk5rVdV4THjdHLK.ujfnSz6sNp3fDAbQxqz7CzKsd6EzyrO', 0, 'Secuya', 'Bolldog', 'Kabangbang', 'Purok Nangka', '09073711101', '', 1, '', '', '2024-05-17'),
(180, 'miranda@gmail.com', '$2y$10$J/EYzR062VpAWFONQKPCK.GyQ9qTc4hdJYi2dRRZrYrZClO1m2yQS', 0, 'Manilyn', 'Miranda', 'Mojon, Bantayan, Cebu', 'Purok Tunga', '09412565365', '', 1, '', '', '2024-05-18'),
(181, 'ronel@gmail.com', '$2y$10$GE7SscGBkEODGWqWOT1Ooe.Je4Sv3NEmftvNxIjXgBbz4nAONdcly', 0, 'Secuya', 'Ronel', 'Bantayan, Kabangbang', 'Purok Quarry', '09073715688', '', 1, '', '', '2024-05-18'),
(185, 'armelyn@gmail.com', '$2y$10$dhhUkzQyqVzWtRaOkfaTcefByU1QB88MFniPUG97OEgLY/iq89Us6', 0, 'Armelyn', 'Escala', 'Madridejos, Kangwayan', 'Purok Nangka', '09124141411', '', 1, '', '', '2024-05-20'),
(186, 'try@gmail.com', '$2y$10$MvX4cm5vOU66xapafEUVLuaA5HtxOibwFylM/8eCHbD3FSzfZzn82', 0, 'Try', 'Botsok', 'Santa Fe, Okoy', 'Purok Nangka', '09073711101', '', 1, '', '', '2024-05-20');

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
-- Indexes for table `comment`
--
ALTER TABLE `comment`
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
-- Indexes for table `ratings`
--
ALTER TABLE `ratings`
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=660;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `comment`
--
ALTER TABLE `comment`
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=477;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=93;

--
-- AUTO_INCREMENT for table `ratings`
--
ALTER TABLE `ratings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `return_details`
--
ALTER TABLE `return_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `return_products`
--
ALTER TABLE `return_products`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=278;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=187;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
