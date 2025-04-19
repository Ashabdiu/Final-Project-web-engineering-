<?php
require 'auth.php';
require 'db.php';

if (!isAdmin()) {
    header("Location: index.php");
    exit();
}

if (isset($_GET['mark_as_read'])) {
    $id = (int)$_GET['mark_as_read'];
    $pdo->prepare("UPDATE contact_messages SET status = 'read' WHERE id = ?")->execute([$id]);
    header("Location: contact_view.php");
    exit();
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM contact_messages WHERE id = ?")->execute([$id]);
    header("Location: contact_view.php");
    exit();
}

$messages = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Messages - Admin</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="logo-container">
            <img src="images/logo.png" alt="Hospital Logo" class="logo">
            <h1>Hospital Management System</h1>
        </div>
        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="contact_view.php" class="active">View Messages</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <main class="container">
        <h2>Patient Contact Messages</h2>
        
        <?php if (empty($messages)): ?>
            <p>No messages found.</p>
        <?php else: ?>
            <table class="message-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Subject</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($messages as $message): ?>
                    <tr class="<?= $message['status'] ?>">
                        <td><?= $message['id'] ?></td>
                        <td><?= htmlspecialchars($message['name']) ?></td>
                        <td><?= htmlspecialchars($message['email']) ?></td>
                        <td><?= htmlspecialchars($message['subject']) ?></td>
                        <td><?= date('M j, Y g:i a', strtotime($message['created_at'])) ?></td>
                        <td><?= ucfirst($message['status']) ?></td>
                        <td class="actions">
                            <a href="view_message.php?id=<?= $message['id'] ?>" class="btn small">View</a>
                            <?php if ($message['status'] === 'unread'): ?>
                                <a href="contact_view.php?mark_as_read=<?= $message['id'] ?>" class="btn small">Mark Read</a>
                            <?php endif; ?>
                            <a href="contact_view.php?delete=<?= $message['id'] ?>" class="btn small danger" 
                               onclick="return confirm('Are you sure you want to delete this message?')">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </main>

    <style>
        /* Add these styles to your style.css */
        .message-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .message-table th, .message-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .message-table th {
            background-color: #2c3e50;
            color: white;
        }
        
        .message-table tr.unread {
            font-weight: bold;
            background-color: #f8f9fa;
        }
        
        .message-table tr.read {
            background-color: #ffffff;
        }
        
        .actions {
            display: flex;
            gap: 5px;
        }
        
        .btn.small {
            padding: 5px 10px;
            font-size: 0.8em;
        }
        
        .btn.danger {
            background-color: #e74c3c;
        }
        
        .btn.danger:hover {
            background-color: #c0392b;
        }
    </style>
</body>
</html>