<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Hospital System' ?></title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="images/medical-icon.png">
</head>
<body>
    <header>
        <div class="header-container">
            <div class="logo-container">
                <img src="images/logo.png" alt="Hospital Logo" class="logo">
                <h1>MediCare Hospital</h1>
            </div>
            <nav>
    <?php if (isset($_SESSION['user_id'])): ?>
        <a href="dashboard.php">Dashboard</a>
        <a href="appointments.php">Appointments</a>
        <a href="logout.php">Logout</a>
    <?php else: ?>
        <a href="index.php">Home</a>
        <a href="login.php">Login</a>
        <a href="register.php">Register</a>
    <?php endif; ?>
    <a href="contact.php">Contact</a>
</nav>
        </div>
    </header>