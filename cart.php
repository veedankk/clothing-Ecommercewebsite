
<?php
session_start();
require 'config.php';


// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

// Initialize cart if not already set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Fetch all products from the database
$products = [];
$sql = "SELECT * FROM products";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[$row['name']] = $row; // Use product name as key for easy lookup
    }
}

// Handle updating cart items
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $productName = $_POST['product_name'] ?? '';

    if (!empty($productName) && isset($products[$productName])) {
        // Handle updating quantity
        if (isset($_POST['update_cart'])) {
            $quantity = intval($_POST['quantity']);
            if ($quantity > 0) {
                $_SESSION['cart'][$productName] = [
                    'quantity' => $quantity,
                    'price' => $products[$productName]['price']
                ];
            } else {
                // Remove item if quantity is 0
                unset($_SESSION['cart'][$productName]);
            }
        }

        // Handle removing item from cart
        if (isset($_POST['remove_item'])) {
            unset($_SESSION['cart'][$productName]);
        }
    }

    // Redirect to avoid form resubmission
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

// Function to get product details by name
function getProductDetails($productName, $products) {
    return $products[$productName] ?? null;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart</title>
    <style>
         body {
            font-family: Modern No. 20;
            background-color: #f4f4f4;
            background-image:url(bg.jpg);
            margin: 0;
            padding: 0;
        }

        .header {
            background-color: #111;
            color: white;
            padding: 15px;
            text-align: center;
        }

        .container {
            width: 90%;
            margin: 20px auto;
        }

        h2 {
            font-size: 24px;
            margin-bottom: 20px;
        }

        .cart-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .cart-table th, .cart-table td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        .cart-table th {
            background-color: #f2f2f2;
        }

        .cart-table img {
            width: 50px;
            height: auto;
            border-radius: 5px;
        }

        .update-button {
            padding: 8px 12px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        .update-button:hover {
            background-color: #218838;
        }

        .remove-button {
            padding: 8px 12px;
            background-color: #dc3545;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        .remove-button:hover {
            background-color: #c82333;
        }

        .checkout-button {
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
            font-size: 16px;
            width: 200px;
            display: block;
            margin: 0 auto;
            text-decoration: none;
        }

        .checkout-button:hover {
            background-color: #0056b3;
        }

        .empty-cart-message {
            text-align: center;
            font-size: 18px;
            margin-top: 20px;
        }

        /* Cart icon */
        .cart-icon {
            font-size: 100px;
            color: #ccc;
            text-align: center;
            margin: 20px 0;
        }

        .cart-icon i {
            font-size: 120px;
            color: #d9b99f;
        }

        .empty-cart-message p {
            font-size: 22px;
            color: #555;
        }
    </style>
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
</head>
<body>
    <div class="header">
        <h1>Your Shopping Cart</h1>
    </div>

    <div class="container">
        <h2>Cart Items</h2>

        <?php if (!empty($_SESSION['cart'])): ?>
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Image</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($_SESSION['cart'] as $productName => $cartItem): 
                        $product = getProductDetails($productName, $products);
                        if ($product): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($product['name']); ?></td>
                                <td><img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" width="50" height="50"></td>
                                <td>₹<?php echo htmlspecialchars($product['price']); ?></td>
                                <td>
                                    <form method="post">
                                        <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($product['name']); ?>">
                                        <input type="number" name="quantity" value="<?php echo $cartItem['quantity']; ?>" min="0">
                                        <button type="submit" class="update-button" name="update_cart">Update</button>
                                    </form>
                                </td>
                                <td>
                                    <form method="post">
                                        <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($product['name']); ?>">
                                        <button type="submit" class="remove-button" name="remove_item">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Checkout button -->
            <a href="checkout.php" class="checkout-button">Proceed to Checkout</a>

        <?php else: ?>
            <!-- Empty cart message -->
            <div class="cart-icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <div class="empty-cart-message">
                <p>Your cart is empty!</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

