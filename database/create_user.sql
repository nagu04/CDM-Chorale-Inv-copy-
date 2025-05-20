CREATE USER 'cdm_user'@'localhost' IDENTIFIED BY 'cdm123';
GRANT ALL PRIVILEGES ON sd_chorale.* TO 'cdm_user'@'localhost';
FLUSH PRIVILEGES; 