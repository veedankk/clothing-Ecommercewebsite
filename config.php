<?php
// Database configuration
  define('DB_SERVERNAME','localhost');
 define('DB_USERNAME','root');
 define('DB_PASSWORD','');
 define('DB_NAME','sports');

// Create a connection
$conn = mysqli_connect(DB_SERVERNAME, DB_USERNAME, DB_PASSWORD,DB_NAME);

// Check connection
if ($conn === false) {
    die('Connection failed (' . mysqli_connect_errno() . '): ' . mysqli_connect_error());
}
?>
