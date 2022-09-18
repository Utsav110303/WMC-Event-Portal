<?php

session_start();

if (isset($_SESSION['username'])){
    header("location: index.html");
    exit;
}

require_once "config.php";

$username = $password = "";
$err = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    if (empty(trim($_POST['username'])) || empty(trim($_POST['password']))){
        $err = "Username or password field is empty";
    }
    else{
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
    }

    if (empty($err)){
        $sql = "SELECT id, username, password FROM users WHERE username = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $param_username);
        $param_username= $username;

        if (mysqli_stmt_execute($stmt)){
            mysqli_stmt_store_result($stmt);
            if (mysqli_stmt_num_rows($stmt) == 1){
                mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password);
                if (mysqli_stmt_fetch($stmt)){
                    if (password_verify($password, $hashed_password)){

                        session_start();
                        $_SESSION["username"] = $username;
                        $_SESSION["id"] = $id;
                        $_SESSION["loggedin"] = true;

                        header("location: index.html");

                    }
                }
            }
        }
    }
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
            <form action="login.php" method="post">
                <h3>Sign In</h3>
                <div class="input" id="label1">
                    <label>Username :</label>
                    <input type="text" name="username" id="username" placeholder="Enter Username">
                </div>
                <div class="input" id="label2">
                    <label>Password : </label>
                    <input type="password" name="password" id="password" placeholder="Enter Password">
                </div>
                <div class="input">
                    <button type="submit" class="btn" name="signin">Sign In</button>
                </div>
                <p>Are you a new user? <a href="signup.php">Sign Up</a></p>
            </form>
        </div>
    </div>
</body>
</html>