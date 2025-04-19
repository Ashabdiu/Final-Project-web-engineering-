<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital Management System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="logo-container">
            <img src="images/logo.png" alt="Hospital Logo" class="logo">
            <h1>Hospital Management System</h1>
        </div>
        <nav>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
            <a href="contact.php">Contact</a>
        </nav>
    </header>
    
    <main>
        <section class="hero">
            <img src="images/hospital-building.jpg" alt="Our Hospital" class="hero-image">
            <div class="hero-text">
                <h2>Quality Healthcare Services</h2>
                <p>Compassionate care for all our patients</p>
                <a href="register.php" class="btn">Register Now</a>
            </div>
        </section>
        
        <section class="features">
            <div class="feature-card">
                <img src="images/doctor-icon.png" alt="Doctors">
                <h3>Expert Doctors</h3>
                <p>Our specialized physicians provide top-notch care</p>
            </div>
            <div class="feature-card">
                <img src="images/patient-icon.png" alt="Patients">
                <h3>Patient Portal</h3>
                <p>Manage your health records online</p>
            </div>
        </section>
    </main>
    
    <footer>
        <img src="images/logo.png" alt="Hospital Logo" class="footer-logo">
        <p>&copy; <?php echo date('Y'); ?> Hospital Management System</p>
    </footer>
</body>
</html>