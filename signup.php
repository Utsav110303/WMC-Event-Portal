<?php

require_once "config.php";

$username = $password = $confirm_password = "";
$username_err = $password_err = $confirm_password_err = "";
$signedin = false;

if($_SERVER['REQUEST_METHOD'] == "POST"){

    if (empty(trim($_POST["username"]))){
        $username_err = "Username cannot be blank";
    }

    else{
        $sql = "SELECT id FROM users WHERE username = ?";
        $stmt = mysqli_prepare($conn, $sql);
        if($stmt){
            mysqli_stmt_bind_param($stmt, "s", $param_username);

            $param_username = trim($_POST['username']);

            if (mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                if (mysqli_stmt_num_rows($stmt) == 1){
                    $username_err = "This username is already taken";
                } 
                else{
                    $username = trim($_POST['username']);
                }
            }
            else{
                echo "Something went wrong";
            }
        }
    }

    mysqli_stmt_close($stmt);

    if (empty(trim($_POST['password']))){
        $password_err = "Password cannot be blank";
    }

    elseif (strlen(trim($_POST['password'])) < 5){
        $password_err = "Weak Password (less than 5 characters)";
    }
    else{
        $password = trim($_POST['password']);
    }

    if (trim($_POST['password']) != trim($_POST['confirm_password'])){
        $password_err = "Passwords don't match";
    }

    if (empty($username_err) && empty($password_err) && empty($confirm_password_err)){
        $sql = "INSERT INTO users (username, password) VALUES (?, ?)";
        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt){
            mysqli_stmt_bind_param($stmt, "ss", $param_username, $param_password);

            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT);

            if (mysqli_stmt_execute($stmt)){
                /*header("location: login.php");*/
                $signedin = true;
            }
            else{
                echo "Something went wrong... cannot redirect";
            }
        }

        mysqli_stmt_close($stmt);
    }

    mysqli_close($conn);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Portal - Login Page</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="loginPageStyle.css">
</head>

<body>
    <div class="bg">
        <div class="loginform">
            <form action="signup.php" method="post">
                <h3>Sign Up</h3>
                <div class="input" id="label1">
                    <label>Username :</label>
                    <input type="text" name="username" id="username" placeholder="Enter Username">
                </div>
                <div class="input" id="label2">
                    <label>Password : </label>
                    <input type="password" name="password" id="password" placeholder="Enter Password">
                </div>
                <div class="input" id="label3">
                    <label>Confirm Password : </label>
                    <input type="password" name="confirm_password" id="confirm_password" placeholder="Enter Password">
                </div>
                <div class="input">
                    <button type="submit" class="btn" name="signup">Sign Up</button>
                </div>
                <p>Are you an existing user? <a href="login.php">Sign In</a></p>
                <p style='color:white;'>
                    <?php
                        echo $username_err . "\n" . $password_err;
                    ?>
                </p>
                <p style='color: green;'>
                    <?php
                        if ($signedin==true){
                            echo "User registered successfully! Sign in Now";
                        }
                    ?>
                </p>    
            </form>
        </div>
    </div>
</body>
</html>