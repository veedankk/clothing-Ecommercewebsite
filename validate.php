<?php
include 'config.php';

if (isset($_POST['username'])) {
    $username = $_POST['username'];

    $sql = "SELECT * FROM userdetails WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "Username is taken";
    } else {
        echo "Username is available";
    }

    $stmt->close();
    $conn->close();
}
?>
