<?php
session_start();
require 'config.php';

// Check if the user is logged in and determine if they are an admin
$isLoggedIn = isset($_SESSION['user']);
$isAdmin = $isLoggedIn && isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin';

// Fetch products from the database
$products = [];
$sql = "SELECT * FROM products";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

// Get unique tags from products
$allTags = [];
foreach ($products as $product) {
    $tags = explode(',', $product['tags']);
    foreach ($tags as $tag) {
        $allTags[trim($tag)] = true;
    }
}
$allTags = array_keys($allTags); // Unique tags array

// Filter products by selected tag if applicable
$selectedTag = isset($_GET['tag']) ? $_GET['tag'] : null;
if ($selectedTag) {
    $products = array_filter($products, function($product) use ($selectedTag) {
        return in_array($selectedTag, array_map('trim', explode(',', $product['tags'])));
    });
}

// Initialize cart if not already set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle Add to Cart functionality
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    $productName = $_POST['product_name'];
    if (isset($_SESSION['cart'][$productName])) {
        $_SESSION['cart'][$productName]['quantity'] += 1;
    } else {
        $_SESSION['cart'][$productName] = ['quantity' => 1];
    }
    // Redirect to avoid form resubmission
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

// Group products by category
$categories = [];
foreach ($products as $product) {
    $categories[$product['category']][] = $product;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formula 1 Store</title>
    <style>
        /* Global Styles */
        body {
    font-family: inline bold;
    margin: 0;
    padding: 0;
    background-image: url('uploads/L.jpg'); /* Add the path to your image here */
    background-size:contain;
    color: black;
}


/* Header Styles */
.header {
    padding: 20px;
    background-color: #111;
    color: #fff;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.header h1 {
    font-size: 3em;
    margin: 0;
    color: #e63946;
}

.header .buttons {
    display: flex;
    align-items: center;
    gap: 15px;
}

.header .buttons a {
    color: #fff;
    text-decoration: arial;
    padding: 8px 12px;
    background-color: #e63946;
    border-radius: 10px;
    font-weight: bold;
    transition: background-color 0.3s;
}

.header .buttons a:hover {
    background-color: #d62828;
}

/* Tags Section */
.header .tags {
    display: flex;
    gap: 5px;
    margin-top: 10px;
}

.header .tag-link {
    color: #fff;
    text-decoration: none;
    padding: 8px 12px;
    background-color: #555;
    border-radius: 20px;
    font-size: 0.9em;
    transition: background-color 0.3s, transform 0.2s;
}

.header .tag-link:hover {
    background-color: #333;
    transform: scale(1.05);
}

/* Main Container */
.container {
    width: 90%;
    max-width: 1000px;
    margin: 20px auto;
}

/* Categories Section */
.category-section h3 {
    font-size: 2.4em;
    margin-bottom: 20px;
    color: black;
}

.product-container {
    display: flex;
    flex-wrap: wrap;
    gap: 30px;
    justify-content: space-around;
}

/* Product Card */
.product {
    background-color:#008080;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
    width: 250px;
    transition: transform 0.3s, box-shadow 0.3s;
}

.product:hover {
    transform: translateY(-5px);
    box-shadow: 0px 6px 15px rgba(0, 0, 0, 0.15);
}

.product img {
    max-width: 100%;
    height: 180px;
    border-radius: 8px;
    object-fit: cover;
    margin-bottom: 15px;
}

.product h2 {
    font-size: 1.2em;
    margin: 0 0 10px;
    color: #fff;
}

.product .price {
    color: #BL;
    font-weight: bold;
    margin-bottom: 10px;
    font-size: 1.1em;
}

.product p {
    color: #FFF;
    font-size: 0.95em;
    line-height: 1.4em;
}

.product .tags {
    margin-top: 10px;
}

.product .tag {
    display: inline-block;
    padding: 5px 10px;
    margin-right: 5px;
    background-color: #006d77;
    color: #fff;
    border-radius: 15px;
    font-size: 0.8em;
}

/* Add to Cart Button */
.add-to-cart-button {
    margin-top: 15px;
    padding: 10px 15px;
    background-color: #06d6a0;
    color: black;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    width: 100%;
    font-size: 1em;
    transition: background-color 0.3s, transform 0.2s;
}

.add-to-cart-button:hover {
    background-color: #05c17c;
    transform: scale(1.05);
}

    </style>
</head>
<body>
    <div class="header">
        <h1>Formula 1 Store</h1>
        <div class="buttons">
    <!-- Shopping Bag Icon Button (for Cart) -->
    <button class="cart-button" onclick="location.href='cart.php';">
        <i class="fas fa-shopping-bag"></i>
    </button>
    <!-- Font Awesome CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

            <?php if ($isLoggedIn): ?>
                <a href="admin login.php">Profile</a>
                <?php if ($isAdmin): ?>
                    <a href="dashboard.php">Admin Dashboard</a>
                <?php endif; ?>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
        </div>
        
        <!-- Tags filter links -->
        <div class="tags">
            <a href="home.php" class="tag-link">All</a>
            <?php foreach ($allTags as $tag): ?>
                <a href="home.php?tag=<?php echo urlencode($tag); ?>" class="tag-link">
                    <?php echo htmlspecialchars($tag); ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="container">
        <h2>Categories</h2>
        
        <?php foreach ($categories as $category => $categoryProducts): ?>
            <div class="category-section">
                <h3><?php echo htmlspecialchars($category); ?></h3>
                <div class="product-container">
                    <?php foreach ($categoryProducts as $product): ?>
                        <div class="product">
                            <img src="uploads/<?php echo htmlspecialchars(basename($product['image'])); ?>" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>">
                            <h2><?php echo htmlspecialchars($product['name']); ?></h2>
                            <p class="price">₹<?php echo htmlspecialchars($product['price']); ?></p>
                            <p><?php echo htmlspecialchars($product['description']); ?></p>
                            <div class="tags">
                                <?php
                                $tags = explode(',', $product['tags']);
                                foreach ($tags as $tag): ?>
                                    <span class="tag"><?php echo htmlspecialchars(trim($tag)); ?></span>
                                <?php endforeach; ?>
                            </div>
                            <form method="post">
                                <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($product['name']); ?>">
                                <input type="hidden" name="add_to_cart" value="1">
                                <button type="submit" class="add-to-cart-button">
                                    <i class="fas fa-cart-plus"></i> Add to Cart
                                </button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
