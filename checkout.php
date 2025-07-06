<?php
session_start();
require 'config.php';

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

// Initialize variables
$address = '';
$area = '';
$state = '';
$zipCode = '';
$country = 'India'; // Default country
$phone = '';
$paymentMethod = '';
$totalAmount = 0;
$message = '';
$errorMessage = '';

// Ensure cart is set to avoid undefined errors
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Calculate total amount
if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $cartItem) {
        $price = $cartItem['price'] ?? 0;
        $quantity = $cartItem['quantity'] ?? 1;
        $totalAmount += $quantity * $price;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userId = $_SESSION['user']['id']; // Assuming user ID is stored in session
    $address = $_POST['address'];
    $area = $_POST['area'];
    $state = $_POST['state'];
    $zipCode = $_POST['zip_code'];
    $country = $_POST['country'];
    $phone = $_POST['phone'];
    $paymentMethod = $_POST['payment_method'];

    // Basic validation
    if (!empty($address) && !empty($area) && !empty($state) && !empty($zipCode) && !empty($phone) && !empty($paymentMethod) && !empty($_SESSION['cart'])) {
        
        // Loop through the cart to insert each item into the orders table
        foreach ($_SESSION['cart'] as $productName => $cartItem) {
            $quantity = $cartItem['quantity'] ?? 1;
            $price = $cartItem['price'] ?? 0;

            // Insert order into database
            $query = "INSERT INTO orders (user_id, address, area, state, zip_code, country, phone, product_name, quantity, payment_method, totalAmount, order_date) 
                      VALUES ('$userId', '$address', '$area', '$state', '$zipCode', '$country', '$phone', '$productName', '$quantity', '$paymentMethod', '$totalAmount', NOW())";

            if (!mysqli_query($conn, $query)) {
                $errorMessage = "Failed to place the order: " . mysqli_error($conn);
                break; // Stop processing if there is an error
            }
        }

        // Clear the cart after checkout
        if (empty($errorMessage)) {
            $_SESSION['cart'] = [];
            $message = "Thank you for your order! Your order will be processed shortly.";
        }
    } else {
        $errorMessage = "Please fill in all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <style>
        /* Add your CSS styles here */
        body {
            font-family:Modern No. 20;
            background-color: #f4f4f4;
            background-image:url(bg.jpg);
            margin: 0;
            padding: 0;
        }

        .header {
            background-color: teal;
            color:black;
            background-image: url('uploads/L.jpg'); /* Add the path to your image here */
            background-size:cover;
            padding: 1px;
            text-align: center;
        }

        .container {
            width: 50%;
            margin: 4px auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        h2 {
            font-size: 24px;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 10px;
        }

        input[type="text"], input[type="tel"], select {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .submit-button {
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .submit-button:hover {
            background-color: #0056b3;
        }

        .message {
            text-align: center;
            margin-top: 20px;
            font-size: 18px;
            color: green;
        }

        .error {
            color: red;
            text-align: center;
        }

        .product-details {
            margin: 20px 0;
        }

        .product-details th, .product-details td {
            padding: 8px;
            border: 1px solid #ddd;
        }

        .product-details th {
            background-color: #f2f2f2;
        }

        .total-amount {
            text-align: right;
            font-size: 18px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Checkout</h1>
    </div>

    <div class="container">
        <?php if (!empty($message)): ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php elseif (!empty($errorMessage)): ?>
            <div class="error"><?php echo htmlspecialchars($errorMessage); ?></div>
        <?php endif; ?>

        <!-- Displaying product details -->
        <div class="product-details">
            <h3>Products in Your Cart:</h3>
            <table class="product-details">
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Quantity</th>
                        <th>Price (per unit)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($_SESSION['cart'] as $productName => $cartItem): 
                        $quantity = $cartItem['quantity'] ?? 1;
                        $price = $cartItem['price'] ?? 1; ?>
                        <tr>
                            <td><?php echo htmlspecialchars($productName); ?></td>
                            <td><?php echo htmlspecialchars($quantity); ?></td>
                            <td>₹<?php echo htmlspecialchars($price); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="total-amount">Total Amount: ₹<?php echo $totalAmount; ?></div>
        </div>

        <h2>Enter Your Details</h2>
        <form method="post">
            <label for="address">Address:</label>
            <input type="text" name="address" id="address" required value="<?php echo htmlspecialchars($address); ?>">

            <label for="area">Area:</label>
            <input type="text" name="area" id="area" required value="<?php echo htmlspecialchars($area); ?>">

            <label for="state">State:</label>
            <input type="text" name="state" id="state" required value="<?php echo htmlspecialchars($state); ?>">

            <label for="zip_code">Zip Code:</label>
            <input type="text" name="zip_code" id="zip_code" required value="<?php echo htmlspecialchars($zipCode); ?>">

            <label for="country">Country:</label>
            <input type="text" name="country" id="country" value="<?php echo htmlspecialchars($country); ?>" readonly>

            <label for="phone">Phone Number:</label>
            <input type="tel" name="phone" id="phone" required value="<?php echo htmlspecialchars($phone); ?>">

            <label for="payment_method">Payment Method:</label>
            <select name="payment_method" id="payment_method" required>
                <option value="">Select Payment Method</option>
                <option value="credit_card" <?php echo $paymentMethod == 'credit_card' ? 'selected' : ''; ?>>Credit Card</option>
                <option value="paypal" <?php echo $paymentMethod == 'paypal' ? 'selected' : ''; ?>>PayPal</option>
                <option value="bank_transfer" <?php echo $paymentMethod == 'bank_transfer' ? 'selected' : ''; ?>>Bank Transfer</option>
            </select>

            <button type="submit" class="submit-button">Place Order</button>
        </form>
    </div>
</body>
</html>
