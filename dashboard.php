<?php
session_start();
require 'config.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user']) || !$_SESSION['is_admin']) {
    header("Location: dashboard.php");
    exit();
}

// Handle Add Product
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['product_name'])) {
    $productName = $_POST['product_name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $tags = $_POST['tags'];
    $category = $_POST['category'];

    // Check if the image is uploaded
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image = $_FILES['image']['name'];
        $image_tmp_name = $_FILES['image']['tmp_name'];
        $image_folder = 'uploads/';
        $target_file = $image_folder . basename($image);

        if (move_uploaded_file($image_tmp_name, $target_file)) {
            $sql = "INSERT INTO products (name, price, description, tags, category, image) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sdssss", $productName, $price, $description, $tags, $category, $target_file);

            if ($stmt->execute()) {
                echo "<script>alert('Product added successfully');</script>";
            } else {
                echo "<script>alert('Error adding product');</script>";
            }
            $stmt->close();
        } else {
            echo "<script>alert('Failed to upload image; please try again.');</script>";
        }
    } else {
        echo "<script>alert('Image file is required.');</script>";
    }
}

// Handle delete request
if (isset($_GET['delete_id'])) {
    $deleteId = $_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $deleteId);
    $stmt->execute();
    $stmt->close();
    header("Location: dashboard.php"); // Refresh the page
    exit();
}

// Handle update request
if (isset($_POST['update_product'])) {
    $productId = $_POST['product_id'];
    $productName = $_POST['product_name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $tags = $_POST['tags'];
    $category = $_POST['category'];
    $imagePath = "";

    // Check if a new image is uploaded
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image = $_FILES['image']['name'];
        $image_tmp_name = $_FILES['image']['tmp_name'];
        $image_folder = 'uploads/';
        $target_file = $image_folder . basename($image);

        if (move_uploaded_file($image_tmp_name, $target_file)) {
            $imagePath = $target_file;
        }
    }

    // Prepare SQL statement to update product details
    if ($imagePath) {
        $sql = "UPDATE products SET name=?, price=?, description=?, tags=?, category=?, image=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sdssssi", $productName, $price, $description, $tags, $category, $imagePath, $productId);
    } else {
        $sql = "UPDATE products SET name=?, price=?, description=?, tags=?, category=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sdsssi", $productName, $price, $description, $tags, $category, $productId);
    }

    $stmt->execute();
    $stmt->close();
    header("Location: dashboard.php"); // Refresh the page
    exit();
}

// Fetch all products
$products = [];
$sql = "SELECT * FROM products";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}else {
    echo "<script>alert('No products found');</script>";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <style>
/* General body and page setup */
body, html {
    background-image: url(bg.jpg);
    margin: 0;
    padding: 0;
    background-image: url('uploads/L.jpg'); /* Add the path to your image here */
    background-size:cover;
    font-family: Modern No. 20;
    background-color: #f4f7f6;
    color: black;
    height: 100%;
}

/* Header Section */
.header {
    background-color: #0b1957;
    padding: 20px 0;
    text-align: center;
    color: white;
}

.header-content h1 {
    margin: 0;
    font-size: 28px;
}

.header nav {
    margin-top: 10px;
}

.header nav a {
    margin: 0 15px;
    color: white;
    text-decoration: none;
    font-size: 16px;
}

.header nav a:hover {
    text-decoration: underline;
}

/* Admin Dashboard Section */
h1 {
    text-align: center;
    font-size: 50px;
    margin-top: 20px;
    color: #0b1957;
}

/* Container for Add Product Section */
.container {
    width: 200px;
    padding: 20px;
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    margin: 30px auto;
    text-align: center;
}

h2 {
    text-align: center;
    font-size: 36px;
    color: #0b1957;
    margin-top: 40px;
}

/* Adjust Table to be More Compact */
table {
    width: 80%;
    margin: 20px auto;
    border-collapse: collapse;
}

th, td {
    padding: 6px 10px; /* Reduced padding to make the table more compact */
    text-align: center;
    border: 1px solid #ccc;
    font-size: 14px;
}

/* Style for the Table Header */
th {
    background-color: #0b1957;
    color: white;
    font-size: 20spx;
}

/* Style for the Table Cells */
td {
    background-color: #ffffff;
}

/* Optional: Style for the Table Image */
img {
    width: 70px; /* Smaller image size */
    height: 70px;
    object-fit: cover;
}

/* Compact Buttons and Links */
a {
    font-size: 14px;
    color: #0b1957;
}

a:hover {
    text-decoration: underline;
}

a.delete {
    color: red;
    font-weight: bold;
    text-decoration: none;
}

a.delete:hover {
    text-decoration: underline;
}

/* Update Form in the Table */
form {
    margin: 0;
    padding: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
}

form input[type="text"],
form input[type="number"],
form textarea,
form input[type="file"] {
    margin: 5px 0 10px;
    width: 50%; /* Input fields now take up 80% of the parent width */
}

form button {
    background-color: #4CAF50;
    color: red;
    border: none;
    padding: 6px 12px;
    font-size: 14px;
    border-radius: 4px;
    cursor: pointer;
    margin-top: 10px;
}

form button:hover {
    background-color: #45a049;
}

/* Action Links */
form a {
    display: flex;
    flex-direction:column;
    align-items:left;
    justify-content:left;
    text-align: center;
    margin-top: 5px;
    color: #0b1957;
    font-weight: bold;
    text-decoration: none;
}
input[type="text"],
input[type="number"],
textarea,
input[type="file"] {
    width: 90%; /* Increase input fields width to 90% of the parent container */
    padding: 8px; /* Add padding inside the input fields */
    margin-bottom: 16px; /* Add bottom margin for spacing between fields */
    text-align: left; /* Center the text inside the input fields */
    border: 1px solid #ccc; /* Set border color to light gray */
    border-radius: 4px; /* Round the corners of input fields */
    box-sizing: border-box; /* Ensure padding is included in the element's width and height */
    margin-left: auto; /* Center the input fields */
    margin-right: auto; /* Center the input fields */
}

/* Increase size of text area */
textarea {
    width: 90%; /* Increase the width of the textarea to 90% */
    height: 40px; /* Set a fixed height for the textarea */
    resize: vertical; /* Allow textarea to be resized vertically */
}

form a:hover {
    text-decoration: underline;
}

/* Confirmation and Alert Messages */
form button[type="submit"] {
    background-color: #4CAF50;
    color: white;
    border: none;
    padding: 10px 20px;
    font-size: 16px;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

form button[type="submit"]:hover {
    background-color: #45a049;
}

form button[type="submit"]:focus {
    outline: none;
    box-shadow: 0 0 5px rgba(76, 175, 80, 0.6);
}

form button[type="submit"]:active {
    background-color: #388e3c;
}

/* Logout Section */
.logout-link {
    text-align: center;
    margin-top: 20px;
    font-size: 16px;
}

.logout-link a {
    color: #0b1957;
    text-decoration: none;
}

.logout-link a:hover {
    text-decoration: underline;
}



</style>
</head>
<body>
    <h1>Welcome to Dashboard</h1>

    <!-- Navigation to Home Page -->
    <div class="buttons" style="text-align: right;">
        <a href="home.php" style="color: #0b1957; font-weight: bold;">Go to Home Page</a>
    </div>
    <div class="container">
        <h2>Welcome, Admin!</h2>
        <p>Manage orders:
        
        <!-- Button to access admin orders -->
        <a href="admin_orders.php" class="button">View Orders</a></p>

        <!-- Other admin functionalities can be added here -->
        
    </div>

    <!-- Form to Add New Product -->
    <h2>Add New Product</h2>
    <form method="post" enctype="multipart/form-data">
        <label>Product Name:</label>
        <input type="text" name="product_name" required><br>

        <label>Price:</label>
        <input type="number" step="0.01" name="price" required><br>

        <label>Description:</label>
        <textarea name="description" required></textarea><br>

        <label>Tags:</label>
        <input type="text" name="tags" required><br>

        <label>category:</label>
        <input type="text" name="category" required><br>

        <label>Product Image:</label>
        <input type="file" name="image" required><br>

        <button type="submit">Add Product</button>
    </form>

    <h2>Manage Products</h2>
    <table border="1">
        <tr>
            <th>Image</th>
            <th>Name</th>
            <th>Price</th>
            <th>Description</th>
            <th>Tags</th>
            <th>Category</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($products as $product): ?>
            <tr>
                <td><img src="<?php echo htmlspecialchars($product['image']); ?>" width="50" height="50" alt="Product Image"></td>
                <td><?php echo htmlspecialchars($product['name']); ?></td>
                <td>₹<?php echo htmlspecialchars($product['price']); ?></td>
                <td><?php echo htmlspecialchars($product['description']); ?></td>
                <td><?php echo htmlspecialchars($product['tags']); ?></td>
                <td><?php echo htmlspecialchars($product['category']); ?></td>
                <td>
                    <form method="post" enctype="multipart/form-data">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        <input type="text" name="product_name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                        <input type="number" step="0.01" name="price" value="<?php echo htmlspecialchars($product['price']); ?>" required>
                        <textarea name="description" required><?php echo htmlspecialchars($product['description']); ?></textarea>
                        <input type="text" name="tags" value="<?php echo htmlspecialchars($product['tags']); ?>" required>
                        <input type="text" name="category" value="<?php echo htmlspecialchars($product['category']); ?>" required>
                        <input type="file" name="image">
                        <button type="submit" name="update_product">Update</button>
                    </form>
                    <a href="?delete_id=<?php echo $product['id']; ?>" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <a href="logout.php">Logout</a>
</body>
</html>
