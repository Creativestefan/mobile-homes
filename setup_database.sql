-- Mobile Home Dealership Database Setup
-- Create the database if it doesn't exist
CREATE DATABASE IF NOT EXISTS `utbonlin_mobilehomes`;
USE `utbonlin_mobilehomes`;

-- --------------------------------------------------------

-- Table structure for table `inventory`
CREATE TABLE IF NOT EXISTS `inventory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `type` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `beds` int(11) NOT NULL,
  `baths` decimal(3,1) NOT NULL,
  `sqft` int(11) NOT NULL,
  `status` enum('Standard','Featured','Sold') DEFAULT 'Standard',
  `year_built` int(4) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `features` text DEFAULT NULL,
  `image` text NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert the demo inventory data
INSERT INTO `inventory` (`id`, `title`, `type`, `price`, `beds`, `baths`, `sqft`, `status`, `year_built`, `category`, `address`, `features`, `image`, `description`) VALUES
(1, 'The Southern Charm', 'Double Wide', 85900.00, 3, 2.0, 1450, 'Featured', 2024, 'New', '123 Meadow Lane, Austin, TX 78701', '- Energy Star Certified\n- Open Floor Plan\n- Kitchen Island', 'https://images.unsplash.com/photo-1549517045-bc93de075e53?auto=format&fit=crop&q=80&w=800', 'Discover comfortable living in this beautiful Double Wide model. With 1,450 square feet of intelligently designed space, The Southern Charm offers a modern open floor plan perfect for families and entertaining. Featuring 3 spacious bedrooms and 2 full baths, it''s built with high-quality materials to ensure lasting value and energy efficiency. Available for immediate delivery and setup.'),
(2, 'The Heritage', 'Single Wide', 54500.00, 2, 2.0, 900, 'Featured', 2022, 'Pre-Owned', '456 Oak View Drive, Round Rock, TX 78664', '- Vinyl Siding\n- Shingle Roof\n- Corner Lot Setup', 'https://images.unsplash.com/photo-1518780664697-55e3ad937233?auto=format&fit=crop&q=80&w=800', 'Discover comfortable living in this beautiful Single Wide model. With 900 square feet of intelligently designed space, The Heritage offers a modern open floor plan perfect for families and entertaining. Featuring 2 spacious bedrooms and 2 full baths, it''s built with high-quality materials to ensure lasting value and energy efficiency. Available for immediate delivery and setup.'),
(3, 'Cozy Cabin Retreat', 'Tiny Home', 42000.00, 1, 1.0, 399, 'Featured', 2024, 'New', '789 Pine Tree Rd, Georgetown, TX 78626', '- Loft Area\n- Custom Wood Cabinets\n- Porch Included', 'https://images.unsplash.com/photo-1449844908441-8829872d2607?auto=format&fit=crop&q=80&w=800', 'Discover comfortable living in this beautiful Tiny Home model. With 399 square feet of intelligently designed space, Cozy Cabin Retreat offers a modern open floor plan perfect for families and entertaining. Featuring 1 spacious bedrooms and 1 full baths, it''s built with high-quality materials to ensure lasting value and energy efficiency. Available for immediate delivery and setup.'),
(4, 'The Grand Estate', 'Modular', 135000.00, 4, 3.0, 2200, 'Standard', 2023, 'New', '101 Horizon Path, Cedar Park, TX 78613', '- Walk-in Closets\n- High Ceilings\n- Luxury Bath', 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&q=80&w=800', 'Discover comfortable living in this beautiful Modular model. With 2,200 square feet of intelligently designed space, The Grand Estate offers a modern open floor plan perfect for families and entertaining. Featuring 4 spacious bedrooms and 3 full baths, it''s built with high-quality materials to ensure lasting value and energy efficiency. Available for immediate delivery and setup.'),
(5, 'The Starter', 'Single Wide', 48900.00, 2, 1.0, 750, 'Standard', 2020, 'Pre-Owned', '202 Willow Bend, Leander, TX 78641', '- Fully Furnished\n- Move-in Ready', 'https://images.unsplash.com/photo-1570129477492-45c003edd2be?auto=format&fit=crop&q=80&w=800', 'Discover comfortable living in this beautiful Single Wide model. With 750 square feet of intelligently designed space, The Starter offers a modern open floor plan perfect for families and entertaining. Featuring 2 spacious bedrooms and 1 full baths, it''s built with high-quality materials to ensure lasting value and energy efficiency. Available for immediate delivery and setup.'),
(6, 'Modern Minimalist', 'Tiny Home', 55000.00, 1, 1.0, 450, 'Standard', 2024, 'New', '303 Tiny Lane, Austin, TX 78758', '- Smart Home Tech\n- Off-grid Capable', 'https://images.unsplash.com/photo-1523217582562-09d0def993a6?auto=format&fit=crop&q=80&w=800', 'Discover comfortable living in this beautiful Tiny Home model. With 450 square feet of intelligently designed space, Modern Minimalist offers a modern open floor plan perfect for families and entertaining. Featuring 1 spacious bedrooms and 1 full baths, it''s built with high-quality materials to ensure lasting value and energy efficiency. Available for immediate delivery and setup.');

-- --------------------------------------------------------

-- Table structure for table `leads`
CREATE TABLE IF NOT EXISTS `leads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` enum('Financing','Sell','Inquiry') NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(50) NOT NULL,
  `details` text DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

-- Table structure for table `admin_users`
CREATE TABLE IF NOT EXISTS `admin_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default admin user: username 'admin', password 'password123'
-- NOTE: You should change this password immediately in a production environment
INSERT INTO `admin_users` (`username`, `password_hash`) VALUES
('admin', '$2y$10$8/XGxkH5Jd.x1Z1E2sXXueT2/1U7T5O5WQKv.f6qU1A/iA1V5k1j2');
