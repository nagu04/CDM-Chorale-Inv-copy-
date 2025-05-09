-- Script to create the deleted_items table
CREATE TABLE IF NOT EXISTS `deleted_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `item_name` varchar(100) NOT NULL,
  `item_type` enum('instrument','accessory','clothing','member') NOT NULL,
  `quantity` int(11) DEFAULT NULL,
  `condition_status` varchar(50) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `deleted_by` varchar(100) DEFAULT NULL,
  `deleted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `details` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- If you need to alter an existing table:
ALTER TABLE `deleted_items` 
MODIFY COLUMN `item_type` enum('instrument','accessory','clothing','member') NOT NULL; 