-- Create database
CREATE DATABASE restie;
USE restie;


-- Table for users (create this first because it's referenced by the orders table)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100),
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(15)NOT NULL,
    address VARCHAR(225) NOT NULL
);

-- Table for admin users
CREATE TABLE admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    profile_picture VARCHAR(255) DEFAULT 'default.png'
);


-- Insert an admin user
INSERT INTO admin (username, password) VALUES ('admin', SHA2('admin123', 256));

-- Table for food items
CREATE TABLE food_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    food_name VARCHAR(100) NOT NULL,
    description TEXT,
    quantity INT,
    price DECIMAL(10, 2) NOT NULL,
    food_image VARCHAR(255)
);
ALTER TABLE food_items ADD COLUMN buying_price DECIMAL(10,2) NOT NULL DEFAULT 0.00;

-- Insert the food items
INSERT INTO food_items (food_name, description,quantity, price, food_image) VALUES
('Idly', 'Mulli Poo Idly',10, 5.99, 'food/idly.jpg'),
('Dosa', 'Moru Moru Dosa',10, 8.49, 'food/dosa.webp'),
('Biriyani', 'Spicy Biriyani',10, 6.79, 'food/Biryani.webp'),
('Burger', 'Delicious Burger',10, 4.99, 'food/burger.jpeg'),
('Chicken Salad', 'Healthy Salad',10, 12.00, 'food/chicken salad.jpg'),
('Butter Pancake', 'Yummy Pancake',10, 14.99, 'food/butterpancake.webp'),
('Pudding Browine', 'Sweety Pudding Brownie Cake',10, 3.99, 'food/puddingbrownie.jpeg'),
('Chocolate Milk Shake', 'Chocolaty Milk Shake',10, 3.55, 'food/chocolatemilkshake.jpeg'),
('Burger', 'Healthy burger',10, 50.99, 'food/burgercombo.jpeg'),
('Biryani', 'Testy biryani',10, 18.49, 'food/Biryani(2).jpeg'),
('Chicken Shawarma', 'Chicken Shawarma',10, 16.79, 'food/chicken shawarma.jpeg'),
('Choco pancake', 'Delicious pancake',10, 14.99, 'food/Chocolate-Pancake.jpg'),
('Pudding cake', 'Pudding cake',10, 50.99, 'food/puddingcake.jpg'),
('strawberry icecream', 'Kulu kulu icecream',10, 8.49, 'food/strawberry-ice-cream.jpg'),
('Tandoori', 'Spicy Tandoori',10, 6.79, 'food/Tandoori.jpeg'),
('Vegetable Salad', 'healthy Salad',10, 4.99, 'food/Vegetablesalad.jpeg'),
('Pizza', 'Cheezy Pizza',10, 15.99, 'food/pizza.jpg'),
('KFG Chicken', 'Moru Moru Chicken',10, 8.49, 'food/KFG chicken.webp'),
('Oreacake', 'Orea Cake',10, 6.79, 'food/oreacake.jpeg'),
('Panner Curry', 'Delicious Panner',10, 40.99, 'food/Panner.jpeg');

CREATE TABLE cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    food_item_id INT,
    quantity INT,
    total_price DECIMAL(10,2),
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(user_id) REFERENCES users(id),
    FOREIGN KEY(food_item_id)REFERENCES food_items(id)
);
ALTER TABLE cart ADD COLUMN price DECIMAL(10,2) NOT NULL;
ALTER TABLE cart ADD COLUMN guest_id VARCHAR(255);

-- Table for orders (create this after users and food_items)
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    food_item_id INT,
    quantity INT NOT NULL,
    total_price DECIMAL(10, 2) NOT NULL,
    status VARCHAR(50) NOT NULL DEFAULT 'ordered',
    payment_method VARCHAR(50) NOT NULL, -- 'Cash on Delivery' or 'Online Payment'
    transaction_id VARCHAR(50) NOT NULL,
    cart_name VARCHAR(50) NOT NULL,
    cvv VARCHAR(50) NOT NULL,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (food_item_id) REFERENCES food_items(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE book_tables (
    id INT AUTO_INCREMENT PRIMARY KEY,
    table_name VARCHAR(100) NOT NULL,
    table_type VARCHAR(100) NOT NULL,
    table_image VARCHAR(255) NOT NULL,
    status VARCHAR(50) DEFAULT'available',
    price DECIMAL(10,2) NOT NULL -- Stores the price or rate for booking
);

INSERT INTO book_tables (table_name,table_type, table_image,status, price) VALUES
('C1','Couple Table', '1.png','available', 50.00),
('F1','Family Table', '2.jpg','available', 75.00),
('C2','Couple Table', '3.jpg','not available', 150.00),
('O1','Outdoor Table', '4.jpg','available', 60.00),
('C3','Couple Table', '5.jpg','available', 100.00),
('C4','Couple Table', '6.webp', 'available',80.00),
('C5','Couple Table', '7.jpg','not available', 70.00),
('C6','Couple Table', '8.jpg','available', 200.00),
('F2','Family Table', '9.webp','available', 90.00),
('F3','Family Table', '10.jpg','available', 40.00),
('F4','Family Table', '11.jpg','available', 120.00),
('O2','Outdoor Table', '12.jpg', 'available',110.00),
('O3','Outdoor Table', '13.webp','available', 130.00),
('O4','Outdoor Table', '14.webp','not available', 95.00),
('O5','Outdoor Table', '15.jpg', 'available',160.00),
('O6','Outdoor Table', '16.jpg', 'not available',180.00);

CREATE TABLE reservation (
    id INT AUTO_INCREMENT PRIMARY KEY,
    table_name VARCHAR(100),
    user_id INT,
    name VARCHAR(100),
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(15)NOT NULL,
    time TIME NOT NULL,
    num_guests INT NOT NULL,
    special_requests TEXT,
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(50) NOT NULL DEFAULT 'Available',
    price  DECIMAL(10, 2) NOT NULL,
    payment_method VARCHAR(50) NOT NULL, -- 'credit cart' or 'UPI'
    transaction_id VARCHAR(50) NOT NULL,
    cart_name VARCHAR(50) NOT NULL,
    cvv VARCHAR(50) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
CREATE TABLE feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    username VARCHAR(100),
    feedback TEXT NOT NULL,
    rating INT CHECK (rating BETWEEN 1 AND 5),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
-- Adding indexes for email field to optimize lookups
CREATE INDEX idx_email ON users(email);

ALTER TABLE orders DROP FOREIGN KEY orders_ibfk_1;
ALTER TABLE orders ADD CONSTRAINT orders_ibfk_1 
FOREIGN KEY (food_item_id) REFERENCES food_items(id) ON DELETE CASCADE;

