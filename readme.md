CREATE DATABASE pc;
use pc;

CREATE TABLE users (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE parts (
	id_part INT AUTO_INCREMENT PRIMARY KEY,
    product VARCHAR(100) NOT NULL,
    quantity INT NOT NULL,
    category VARCHAR(50) NOT NULL,
    price DECIMAL(10,2) NOT NULL
);

CREATE TABLE cart(
	id_cart INT AUTO_INCREMENT PRIMARY KEY,
	id_user INT NOT NULL,
    id_part INT NOT NULL,
    quantity INT NOT NULL,
	
    FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE CASCADE,
    FOREIGN KEY (id_part) REFERENCES parts(id_part) ON DELETE CASCADE
);

drop table parts;

select * from parts;
select * from users;
drop table parts;

select * from cart;
drop table cart;

INSERT INTO parts (product, quantity, category, price) VALUES
('Intel Core i9-13900K', 10, 'Desktop', 589.99),
('AMD Ryzen 9 7900X', 8, 'Desktop', 549.99),
('NVIDIA RTX 4070', 5, 'Desktop', 599.99),
('Dell XPS 13 Laptop', 7, 'Laptop', 999.99),
('MacBook Pro 14-inch', 4, 'Laptop', 1999.99),
('Logitech MX Master 3 Mouse', 15, 'Peripherals', 99.99),
('Corsair K95 RGB Keyboard', 12, 'Peripherals', 199.99),
('Samsung 970 EVO SSD 1TB', 20, 'Desktop', 129.99),
('Razer DeathAdder V2 Mouse', 10, 'Peripherals', 69.99),
('HP Spectre x360', 6, 'Laptop', 1249.99);