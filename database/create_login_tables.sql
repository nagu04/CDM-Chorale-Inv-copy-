-- Create login table for admin users
CREATE TABLE IF NOT EXISTS `login` (
  `login_id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `full_name` varchar(100) DEFAULT '',
  `email` varchar(100) DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create user_login table for regular users
CREATE TABLE IF NOT EXISTS `user_login` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `full_name` varchar(100) DEFAULT '',
  `email` varchar(100) DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert default admin user
INSERT INTO `login` (`login_id`, `username`, `password`, `full_name`, `email`) VALUES
(1, 'admin', 'admin', 'Tamara Verdan', 'maamtammy@edu.ph'),
(2, 'user', 'password', 'Rinnel Pacinos', 'kuyarinnel@cdm.edu.ph'); 