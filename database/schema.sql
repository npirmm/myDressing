CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);

CREATE TABLE brands (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);

CREATE TABLE materials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);

CREATE TABLE colors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);

CREATE TABLE nuanciers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);

CREATE TABLE storage_locations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);

CREATE TABLE suppliers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);

CREATE TABLE articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    short_description TEXT,
    long_description TEXT,
    category_id INT,
    brand_id INT,
    size VARCHAR(255),
    weight FLOAT,
    status ENUM('en stock', 'utilisé', 'prêté', 'donné', 'vendu', 'en réparation', 'en retouche', 'au nettoyage', 'détruit', 'archivé'),
    `condition` ENUM('neuf', 'excellent', 'bon état', 'médiocre', 'à réparer/retoucher'),
    storage_location_id INT,
    storage_shelf VARCHAR(255),
    storage_id VARCHAR(255),
    purchase_date DATE,
    purchase_price DECIMAL(10, 2),
    supplier_id INT,
    clean_instructions TEXT,
    notes TEXT,
    rating ENUM('1', '2', '3'),
    favorite BOOLEAN DEFAULT FALSE,
    created_by INT,
    owner_id INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    main_photo_id INT DEFAULT NULL,
    FOREIGN KEY (category_id) REFERENCES categories(id),
    FOREIGN KEY (brand_id) REFERENCES brands(id),
    FOREIGN KEY (storage_location_id) REFERENCES storage_locations(id),
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id),
    FOREIGN KEY (main_photo_id) REFERENCES article_photos(id)
);

CREATE TABLE article_photos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    article_id INT,
    photo_name VARCHAR(255) NOT NULL,
    label VARCHAR(255),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (article_id) REFERENCES articles(id)
);
