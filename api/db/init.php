<?php
$db = new SQLite3(__DIR__ . '/electromart.db');

$db->exec("CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL UNIQUE,
    email TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    name TEXT,
    phone TEXT,
    address TEXT,
    image_url TEXT
)");

$db->exec("CREATE TABLE IF NOT EXISTS products (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    description TEXT,
    price REAL NOT NULL,
    category TEXT,
    stock INTEGER,
    image_url TEXT
)");

$db->exec("CREATE TABLE IF NOT EXISTS cart (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    variant_id INTEGER NOT NULL,
    quantity INTEGER NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
)");

$db->exec("CREATE TABLE IF NOT EXISTS orders (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    order_date TEXT NOT NULL,
    total REAL NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
)");

echo "Database initialized successfully.\n";
?>
