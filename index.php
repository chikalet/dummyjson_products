<?php


// Database connection
$servername = "localhost";
$username = "root";
$password = "root";  // Замените на ваш пароль
$dbname = "dummyjson_products";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
    exit();
}

// Fetch products from API
function fetchProductsFromApi() {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_URL, "https://dummyjson.com/products/search?q=iPhone");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_VERBOSE, true);  // Add this line to capture errors
    $output = curl_exec($ch);
    $curl_error = curl_error($ch);
    curl_close($ch);

    if ($curl_error) {
        echo "Failed to fetch products from API: " . $curl_error;
        exit();
    }

    return json_decode($output, true);
}

// Save products to database
function saveProductsToDatabase($products, $conn)
{
    foreach ($products as $product) {
        $stmt = $conn->prepare("INSERT INTO products (product_id, title, description, price, brand, category, thumbnail) VALUES (?, ?, ?, ?, ?, ?, ?)");

        if (!$stmt) {
            die("Error preparing statement: " . $conn->error);
        }

        $stmt->bind_param(
            "issdsss",
            $product['id'],
            $product['title'],
            $product['description'],
            $product['price'],
            $product['brand'],
            $product['category'],
            $product['thumbnail']
        );

        if (!$stmt->execute()) {
            die("Error executing statement: " . $stmt->error);
        }

    }
}

// Fetch saved products from database
function getProductsFromDatabase($conn) {
    $result = $conn->query("SELECT * FROM products");
    $products = [];
    while($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    return $products;
}

// API Routing
$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'GET') {
    // Fetch saved products
    $products = getProductsFromDatabase($conn);
    header('Content-Type: application/json');
    echo json_encode($products);

} elseif ($method == 'POST') {
    // Fetch from external API and save to DB
    $response = fetchProductsFromApi();
    if (isset($response['products'])) {
        saveProductsToDatabase($response['products'], $conn);
        echo "Products saved successfully!";
    } else {
        echo "Failed to fetch products from API.";
    }
}

// Close the database connection
$conn->close();
