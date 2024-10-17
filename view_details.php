<?php
session_start();

if (!isset($_SESSION['valid']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit; 
}

require_once 'db_connect.php';

// Fetch parameters from URL
$username = isset($_GET['username']) ? $_GET['username'] : '';
$age = isset($_GET['age']) ? $_GET['age'] : '';
$email = isset($_GET['email']) ? $_GET['email'] : '';

// Fetch CV path from database based on username
$stmt = $pdo->prepare("SELECT cv_path FROM cv_uploads WHERE Username = ?");
$stmt->execute([$username]);
$cv = $stmt->fetch(PDO::FETCH_ASSOC);
$cv_path = isset($cv['cv_path']) ? $cv['cv_path'] : '';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style.css">
    <title>User Details</title>
</head>
<body>
    <div class="box form-box">
        <div class='message'>
            <header>User Details</header><br>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($username);?></p>
            <p><strong>Age:</strong> <?php echo htmlspecialchars($age); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
            <?php if (!empty($cv_path)): ?>
                <p><a href="<?php echo htmlspecialchars($cv_path); ?>" target="_blank">Download CV</a></p>
            <?php else: ?>
                <p>No CV uploaded for this user.</p>
            <?php endif; ?>
            <br>
            <p><a href="view_cv.php">Go Back</a></p>
        </div>
    </div>
</body>
</html>
