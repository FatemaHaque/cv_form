<?php 
session_start();

include("php/config.php");
if(!isset($_SESSION['valid'])){
    header("Location: index.php");
    exit();
}

$edit_query = "";

if(isset($_POST['submit'])){
    $username = $_POST['username'];
    $email = $_POST['email'];
    $age = $_POST['age'];
    $id = $_SESSION['id'];

    // Update user profile
    $edit_query = mysqli_query($con, "UPDATE users SET Username='$username', Email='$email', Age='$age' WHERE Id=$id");

    // Handle CV file upload
    $upload_dir = "cv_uploads/";
    $cv_name = $_FILES["cv_path"]["name"];
    $cv_path = $upload_dir . $cv_name;
    $fileType = strtolower(pathinfo($cv_path, PATHINFO_EXTENSION));
    
    
    
    if ($fileType == "pdf") { 
        if (move_uploaded_file($_FILES["cv_path"]["tmp_name"], $cv_path)) {
        // Insert CV details into database
        $sql = "UPDATE cv_uploads SET cv_name = ?, cv_path = ? WHERE Username = ?";
            $stmt = $con->prepare($sql);
            $stmt->bind_param("sss", $cv_name, $cv_path, $username);
            $stmt->execute();
    } else {
        echo "Error uploading file.";
    }
} }

// Fetch current user details
$id = $_SESSION['id'];
$query = mysqli_query($con, "SELECT * FROM users WHERE Id=$id");
$result = mysqli_fetch_assoc($query);
$res_Uname = $result['Username'];
$res_Email = $result['Email'];
$res_Age = $result['Age'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style.css">
    <title>Change Profile</title>
</head>
<body>
    <div class="nav">
        <div class="logo">
            <p><a href="home.php">HOME</a></p>
        </div>

        <div class="right-links">
            <a href="#">Change Profile</a>
            <a href="php/logout.php"><button class="btn">Log Out</button></a>
        </div>
    </div>

    <div class="container">
        <div class="box form-box">
       
        <?php
        if($edit_query){
        echo "<div class='message'>
                <p>Profile Updated!</p>
              </div> <br>";
        echo "<a href='home.php'><button class='btn'>Go Home</button></a>";
        exit();
        }

    ?>
            <header>Change Profile</header>
            <form action="" method="post" enctype="multipart/form-data">
                <div class="field input">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" value="<?php echo $res_Uname; ?>" autocomplete="off" required>
                </div>

                <div class="field input">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" value="<?php echo $res_Email; ?>" autocomplete="off" required>
                </div>

                <div class="field input">
                    <label for="age">Age</label>
                    <input type="number" name="age" id="age" value="<?php echo $res_Age; ?>" autocomplete="off" required>
                </div>

                <div class="field input">
                    <label for="cv_path">Upload New CV (PDF only)</label>
                    <input type="file" name="cv_path" id="cv_path" accept=".pdf" required>
                </div>
                
                <div class="field">
                    <input type="submit" class="btn" name="submit" value="Update">
                </div>
            </form>
        </div>
    </div>
</body>
</html>