<?php 
session_start();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/untitled.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <title>Login</title>
</head>
<body>
      <div class="container">
        <div class="box form-box">
            <?php 
             include("php/config.php");
             if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $email = mysqli_real_escape_string($con, $_POST['email']);
                $password = mysqli_real_escape_string($con, $_POST['password']);
            
                $result = mysqli_query($con, "SELECT * FROM users WHERE Email='$email' AND Password='$password'");
                $row = mysqli_fetch_assoc($result);
            
                if ($row) {
                    $_SESSION['valid'] = $row['Email'];
                    $_SESSION['username'] = $row['Username'];
                    $_SESSION['age'] = $row['Age'];
                    $_SESSION['id'] = $row['Id'];
                    $_SESSION['role'] = $row['Role']; 
            
                    if ($row['Role'] == 'admin') {
                        header("Location: view_cv.php");
                    } else {
                        header("Location: home.php");
                    }
                    exit();

                
                
                } else {
                    echo "<div class='message'><p>Wrong Username or Password</p></div><br>";
                    echo "<a href='index.php'><button class='btn'>Go Back</button></a>";
                }

            }else{

            ?>
            <header>Login</header>
            <form action="" method="post">
                <div class="field input">
                    <label for="email">Email</label>
                    <input type="text" name="email" id="email" autocomplete="off" required>
                    <i class='bx bxs-user'></i>
                </div>

                <div class="field input">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" autocomplete="off" required>
                    <i class='bx bxs-lock-alt'></i>
                </div>

                <div class="field">
                    
                    <input type="submit" class="btn" name="submit" value="Login" required>
                </div>
                <div class="links">
                    Don't have account? <a href="register.php">Sign Up Now</a>
                </div>
            </form>
        </div>
        <?php } ?>
      </div>

    
</body>
</html>