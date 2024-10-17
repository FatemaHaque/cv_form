<?php 
session_start();

include("php/config.php");

if(!isset($_SESSION['valid'])){
    header("Location: index.php");
    exit;
}

$id = $_SESSION['id'];
$query = mysqli_query($con, "SELECT * FROM users WHERE Id=$id");

if (!$query) {
    die("Query failed: " . mysqli_error($con));
}

while ($result = mysqli_fetch_assoc($query)) {
    $res_Uname = $result['Username'];
    $res_Email = $result['Email'];
    $res_Age = $result['Age'];
    $res_id = $result['Id'];

    
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style.css">
    <title>Home</title>
</head>
<body>
    <div class="nav">
        <div class="logo">
            <p><a href="home.php">HOME</a> </p>
        </div>

        <div class="right-links">
            <a href='edit.php?Id=<?php echo $res_id ?>'>Change Profile</a>
            <a href="php/logout.php"><button class="btn">Log Out</button></a>
        </div>
    </div>

    <main>
        <div class="main-box top">
            <div class="top">
                <div class="box">
                    <p>Hello <b><?php echo htmlspecialchars($res_Uname); ?></b>, Welcome</p>
                </div>
                <div class="box">
                    <p>Your email is <b><?php echo htmlspecialchars($res_Email); ?></b>.</p>
                </div>
            </div>
            <div class="bottom">
                <div class="box">
                    <p>And you are <b><?php echo htmlspecialchars($res_Age); ?> years old</b>.</p>
                </div>
            </div>

            <?php
            // Check if upload message is set
            if (isset($_SESSION['upload_message'])) {
                echo "<div class='message'><p>" . htmlspecialchars($_SESSION['upload_message']) . "</p></div>";
                unset($_SESSION['upload_message']); // Clear the message once displayed
            }

            // Check if CV upload form should be displayed
            if (isset($_SESSION['cv_uploaded']) && $_SESSION['cv_uploaded']) {
                // Display uploaded CV
                $query = mysqli_query($con, "SELECT * FROM cv_uploads WHERE Username='$res_Uname' ORDER BY id DESC LIMIT 1");
                if ($row = mysqli_fetch_assoc($query)) {
                    $cv_path = $row['cv_path'];
                    echo "<div class='wrapper'>";
                    echo "<h2>Uploaded CV</h2>";
                    echo "<p>You can &nbsp;<a href='$cv_path' target='_blank'>View your CV</a></p>";
                    echo "</div>";
                }
            } else {
            ?>
            <div class="wrapper">
                <h2>Upload Your CV</h2>
                <form action="upload_process.php" method="post" enctype="multipart/form-data">
                    <input type="file" name="cv_file" accept=".pdf" required>
                    <button type="submit">Upload</button>
                </form>
            </div>
            <?php
            }
            ?>

        </div>
    </main>
</body>
</html>