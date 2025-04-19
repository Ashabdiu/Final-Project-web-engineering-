<?php
require 'auth.php';
require 'db.php';

if (!isAdmin()) {
    header("Location: index.php");
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT * FROM contact_messages WHERE id = ?");
$stmt->execute([$id]);
$message = $stmt->fetch();

if (!$message) {
    header("Location: contact_view.php");
    exit();
}

$pdo->prepare("UPDATE contact_messages SET status = 'read' WHERE id = ?")->execute([$id]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Message Details</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .message-container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .message-header {
            border-bottom: 1px solid #ddd;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }
        .message-row {
            display: flex;
            margin-bottom: 10px;
        }
        .message-label {
            font-weight: bold;
            width: 120px;
            color: #2c3e50;
        }
        .message-content {
            margin-top: 20px;
            padding: 15px;
            background: white;
            border-radius: 4px;
        }
        .action-buttons {
            margin-top: 20px;
            display: flex;
            gap: 10px;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    
    <main class="container">
        <div class="message-container">
            <div class="message-header">
                <h2>Message Details</h2>
            </div>
            
            <div class="message-row">
                <span class="message-label">From:</span>
                <span><?= htmlspecialchars($message['name']) ?></span>
            </div>
            
            <div class="message-row">
                <span class="message-label">Email:</span>
                <span><?= htmlspecialchars($message['email']) ?></span>
            </div>
            
            <div class="message-row">
                <span class="message-label">Phone:</span>
                <span><?= !empty($message['phone']) ? htmlspecialchars($message['phone']) : 'Not provided' ?></span>
            </div>
            
            <div class="message-row">
                <span class="message-label">Subject:</span>
                <span><?= htmlspecialchars($message['subject']) ?></span>
            </div>
            
            <div class="message-row">
                <span class="message-label">Received:</span>
                <span><?= date('F j, Y g:i a', strtotime($message['created_at'])) ?></span>
            </div>
            
            <div class="message-content">
                <h3>Message Content:</h3>
                <p><?= nl2br(htmlspecialchars($message['message'])) ?></p>
            </div>
            
            <div class="action-buttons">
                <a href="mailto:<?= htmlspecialchars($message['email']) ?>?subject=Re: <?= rawurlencode($message['subject']) ?>" 
                   class="btn">Reply via Email</a>
                <a href="contact_view.php?delete=<?= $message['id'] ?>" 
                   class="btn danger"
                   onclick="return confirm('Are you sure you want to delete this message?')">Delete Message</a>
                <a href="contact_view.php" class="btn">Back to Messages</a>
            </div>
        </div>
    </main>
</body>
</html>