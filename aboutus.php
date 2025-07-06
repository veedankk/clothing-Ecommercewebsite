<?php
// about_us.php

// You can set a dynamic title or other variables here
$title = "About Us";

// Header part (Optional)
function headerSection($title) {
    echo "<!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>" . htmlspecialchars($title) . "</title>
        <link rel='stylesheet' href='styles.css'>
    </head>
    <body>
        <header>
            <h1>Welcome to Our Company</h1>
            <nav>
                <ul>
                    <li><a href='index.php'>Home</a></li>
                    <li><a href='about_us.php'>About Us</a></li>
                    <li><a href='contact.php'>Contact</a></li>
                </ul>
            </nav>
        </header>";
}

// Footer part (Optional)
function footerSection() {
    echo "<footer>
            <p>&copy; " . date("Y") . " Your Company. All rights reserved.</p>
          </footer>
    </body>
    </html>";
}

// Call header function
headerSection($title);

?>

<main>
    <section class="about-us">
        <h2>About Us</h2>
        <p>Welcome to our company! We are a passionate team dedicated to providing the best services for our customers.</p>
        <h3>Our Mission</h3>
        <p>Our mission is to deliver high-quality products and services that make life easier and more enjoyable for our clients.</p>
        <h3>Our Values</h3>
        <ul>
            <li>Integrity</li>
            <li>Innovation</li>
            <li>Customer Satisfaction</li>
        </ul>
    </section>
</main>

<?php
// Call footer function
footerSection();
?>
