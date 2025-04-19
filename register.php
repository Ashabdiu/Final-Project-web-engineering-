<?php
require 'auth.php';
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $email = $_POST['email'];
    $role = $_POST['role'];
    $full_name = $_POST['full_name'];
    
    try {
        $pdo->beginTransaction();
   
        $stmt = $pdo->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$username, $password, $email, $role]);
        $user_id = $pdo->lastInsertId();
  
        if ($role === 'patient') {
            $stmt = $pdo->prepare("INSERT INTO patients (user_id, full_name, phone) VALUES (?, ?, ?)");
            $stmt->execute([
                $user_id, 
                $full_name, 
                $_POST['phone'] ?? null
            ]);
        } 
        elseif ($role === 'doctor') {
            $stmt = $pdo->prepare("INSERT INTO doctors (user_id, full_name, specialization, qualification, phone) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $user_id, 
                $full_name, 
                $_POST['specialization'],
                $_POST['qualification'] ?? 'Not specified', 
                $_POST['phone'] ?? null
            ]);
        }
        
        $pdo->commit();

        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $role;
        
        header('Location: dashboard.php');
        exit();
        
    } catch (PDOException $e) {
        $pdo->rollBack();
        $error = "Registration failed: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Hospital System</title>
    <link rel="stylesheet" href="style.css">
    <script src="script.js"></script>
</head>
<body>
    <div class="container">
        <h2>Register</h2>
        
        <?php if (isset($error)): ?>
            <div class="alert error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <form method="post" onsubmit="return validateRegister()">
            <div class="form-group">
                <label>Username:</label>
                <input type="text" name="username" required>
            </div>
            
            <div class="form-group">
                <label>Password:</label>
                <input type="password" name="password" required>
            </div>
            
            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label>Full Name:</label>
                <input type="text" name="full_name" required>
            </div>
            
            <div class="form-group">
                <label>Role:</label>
                <select name="role" id="roleSelect" onchange="toggleFields()" required>
                    <option value="">Select Role</option>
                    <option value="patient">Patient</option>
                    <option value="doctor">Doctor</option>
                </select>
            </div>
 
            <div id="patientFields" style="display:none;">
                <div class="form-group">
                    <label>Phone:</label>
                    <input type="tel" name="phone">
                </div>
            </div>

            <div id="doctorFields" style="display:none;">
                <div class="form-group">
                    <label>Specialization:</label>
                    <input type="text" name="specialization">
                </div>
                <div class="form-group">
                    <label>Qualification:</label>
                    <input type="text" name="qualification">
                </div>
                <div class="form-group">
                    <label>Phone:</label>
                    <input type="tel" name="doctor_phone">
                </div>
            </div>
            
            <button type="submit" class="btn">Register</button>
        </form>
        
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
</body>
</html>