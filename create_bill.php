<?php
require 'auth.php';
require 'db.php';

if (!isDoctor()) {
    header("Location: index.php");
    exit();
}

$doctor_id = $pdo->query("SELECT id FROM doctors WHERE user_id = {$_SESSION['user_id']}")->fetchColumn();
$error = '';
$success = '';

$patients = $pdo->query("
    SELECT DISTINCT p.id, p.full_name 
    FROM appointments a
    JOIN patients p ON a.patient_id = p.id
    WHERE a.doctor_id = $doctor_id
    ORDER BY a.appointment_date DESC
    LIMIT 50
")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patient_id = (int)$_POST['patient_id'];
    $amount = (float)$_POST['amount'];
    $description = trim($_POST['description']);
    $appointment_id = !empty($_POST['appointment_id']) ? (int)$_POST['appointment_id'] : null;

    if ($amount <= 0) {
        $error = "Amount must be greater than 0";
    } else {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO bills 
                (patient_id, doctor_id, appointment_id, amount, description) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([$patient_id, $doctor_id, $appointment_id, $amount, $description]);
            $success = "Bill created successfully!";
        } catch (PDOException $e) {
            $error = "Error creating bill: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Bill</title>
    <link rel="stylesheet" href="style.css">
    <script src="script.js"></script>
</head>
<body>
    <?php include 'header.php'; ?>
    
    <main class="container">
        <h2>Create New Bill</h2>
        
        <?php if ($error): ?>
            <div class="alert error"><?= $error ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert success"><?= $success ?></div>
        <?php endif; ?>
        
        <form method="post">
            <div class="form-group">
                <label>Patient *</label>
                <select name="patient_id" id="patientSelect" required>
                    <option value="">Select Patient</option>
                    <?php foreach ($patients as $patient): ?>
                        <option value="<?= $patient['id'] ?>">
                            <?= htmlspecialchars($patient['full_name']) ?> (ID: <?= $patient['id'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Appointment (optional)</label>
                <select name="appointment_id" id="appointmentSelect">
                    <option value="">Select Appointment</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Amount *</label>
                <input type="number" name="amount" min="0" step="0.01" required>
            </div>
            
            <div class="form-group">
                <label>Description *</label>
                <textarea name="description" rows="4" required></textarea>
            </div>
            
            <button type="submit" class="btn">Create Bill</button>
            <a href="dashboard.php" class="btn">Cancel</a>
        </form>
    </main>

    <script>
       
        document.getElementById('patientSelect').addEventListener('change', function() {
            const patientId = this.value;
            const appointmentSelect = document.getElementById('appointmentSelect');
            
            if (!patientId) {
                appointmentSelect.innerHTML = '<option value="">Select Appointment</option>';
                return;
            }
            
            fetch(`get_appointments.php?patient_id=${patientId}`)
                .then(response => response.json())
                .then(appointments => {
                    let options = '<option value="">Select Appointment</option>';
                    appointments.forEach(apt => {
                        const date = new Date(apt.appointment_date);
                        options += `
                            <option value="${apt.id}">
                                ${date.toLocaleDateString()} - ${apt.appointment_time} (${apt.status})
                            </option>
                        `;
                    });
                    appointmentSelect.innerHTML = options;
                });
        });
    </script>
</body>
</html>