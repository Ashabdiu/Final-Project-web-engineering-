<?php
require 'auth.php';
require 'db.php';

if (!isAdmin()) {
    header("Location: index.php");
    exit();
}

$patient_id = (int)$_GET['id'];
$patient = $pdo->query("
    SELECT p.*, u.email, u.username 
    FROM patients p 
    JOIN users u ON p.user_id = u.id 
    WHERE p.id = $patient_id
")->fetch();

if (!$patient) {
    $_SESSION['error'] = "Patient not found";
    header("Location: patients.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $username = $_POST['username'];
    
    try {
        $pdo->beginTransaction();
   
        $stmt = $pdo->prepare("UPDATE patients SET full_name = ?, phone = ? WHERE id = ?");
        $stmt->execute([$full_name, $phone, $patient_id]);
   
        $stmt = $pdo->prepare("UPDATE users SET email = ?, username = ? WHERE id = ?");
        $stmt->execute([$email, $username, $patient['user_id']]);
        
        $pdo->commit();
        $_SESSION['success'] = "Patient updated successfully";
    } catch (PDOException $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Error updating patient: " . $e->getMessage();
    }
    
    header("Location: patients.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Patient</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <main class="container">
        <h2>Edit Patient</h2>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert error"><?= $_SESSION['error'] ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        
        <form method="post">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="full_name" value="<?= htmlspecialchars($patient['full_name']) ?>" required>
            </div>
            
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($patient['email']) ?>" required>
            </div>
            
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" value="<?= htmlspecialchars($patient['username']) ?>" required>
            </div>
            
            <div class="form-group">
                <label>Phone</label>
                <input type="tel" name="phone" value="<?= htmlspecialchars($patient['phone'] ?? '') ?>">
            </div>
            
            <button type="submit" class="btn">Update Patient</button>
            <a href="patients.php" class="btn">Cancel</a>
        </form>
    </main>
</body>
</html>