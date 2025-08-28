<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit;
}

require 'config.php';

// Handle form submissions for each section
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_home'])) {
        $title = $_POST['home_title'];
        $description = $_POST['home_description'];
        
        // Check if home content exists
        $stmt = $pdo->query("SELECT COUNT(*) FROM home_content");
        if ($stmt->fetchColumn() > 0) {
            $stmt = $pdo->prepare("UPDATE home_content SET title = ?, description = ? WHERE id = 1");
            $stmt->execute([$title, $description]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO home_content (id, title, description) VALUES (1, ?, ?)");
            $stmt->execute([$title, $description]);
        }
        $home_success = "Home section updated successfully!";
    }
    
    // Similar handling for other sections...
}

// Fetch current data
$home_content = $pdo->query("SELECT * FROM home_content WHERE id = 1")->fetch();
$about_content = $pdo->query("SELECT * FROM about_content WHERE id = 1")->fetch();
$education = $pdo->query("SELECT * FROM education")->fetchAll();
$skills = $pdo->query("SELECT * FROM skills")->fetchAll();
$projects = $pdo->query("SELECT * FROM projects")->fetchAll();
$research = $pdo->query("SELECT * FROM research")->fetchAll();
$messages = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <style>
        /* Add your admin panel styles here */
        body {
            font-family: 'Poppins', sans-serif;
            background: #081b29;
            color: #ededed;
            margin: 0;
            padding: 0;
        }
        .admin-header {
            background: #112e42;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .admin-nav {
            background: #0a2535;
            padding: 1rem;
        }
        .admin-nav a {
            color: #ededed;
            text-decoration: none;
            margin-right: 1.5rem;
            padding: 0.5rem 1rem;
            border-radius: 0.4rem;
            transition: 0.3s;
        }
        .admin-nav a:hover {
            background: #00abf0;
            color: #081b29;
        }
        .admin-container {
            padding: 2rem;
        }
        .section {
            background: #112e42;
            padding: 1.5rem;
            border-radius: 0.8rem;
            margin-bottom: 2rem;
        }
        .section h2 {
            color: #00abf0;
            margin-top: 0;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
        }
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 0.8rem;
            border: 0.2rem solid #00abf0;
            border-radius: 0.6rem;
            background: transparent;
            color: #ededed;
        }
        .btn {
            padding: 0.8rem 1.5rem;
            background: #00abf0;
            border: none;
            border-radius: 0.6rem;
            color: #081b29;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }
        .btn:hover {
            background: #0490d1;
        }
        .success {
            color: #4caf50;
            margin-top: 1rem;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        table, th, td {
            border: 1px solid #00abf0;
        }
        th, td {
            padding: 0.8rem;
            text-align: left;
        }
        th {
            background: #00abf0;
            color: #081b29;
        }
    </style>
</head>
<body>
    <div class="admin-header">
        <h1>Portfolio Admin Panel</h1>
        <a href="logout.php" class="btn">Logout</a>
    </div>
    
    <div class="admin-nav">
        <a href="#home">Home</a>
        <a href="#about">About</a>
        <a href="#education">Education</a>
        <a href="#skills">Skills</a>
        <a href="#projects">Projects</a>
        <a href="#research">Research</a>
        <a href="#messages">Messages</a>
    </div>
    
    <div class="admin-container">
        <!-- Home Section Form -->
        <div class="section" id="home">
            <h2>Home Section</h2>
            <form method="post">
                <div class="form-group">
                    <label for="home_title">Title</label>
                    <input type="text" id="home_title" name="home_title" value="<?php echo $home_content['title'] ?? ''; ?>" required>
                </div>
                <div class="form-group">
                    <label for="home_description">Description</label>
                    <textarea id="home_description" name="home_description" rows="5" required><?php echo $home_content['description'] ?? ''; ?></textarea>
                </div>
                <button type="submit" name="update_home" class="btn">Update Home</button>
                <?php if (isset($home_success)): ?>
                    <p class="success"><?php echo $home_success; ?></p>
                <?php endif; ?>
            </form>
        </div>
        
        <!-- Messages Section -->
        <div class="section" id="messages">
            <h2>Contact Messages</h2>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Subject</th>
                        <th>Message</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($messages as $message): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($message['name']); ?></td>
                        <td><?php echo htmlspecialchars($message['email']); ?></td>
                        <td><?php echo htmlspecialchars($message['subject']); ?></td>
                        <td><?php echo htmlspecialchars($message['message']); ?></td>
                        <td><?php echo $message['created_at']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Add similar forms for other sections -->
    </div>
</body>
</html>