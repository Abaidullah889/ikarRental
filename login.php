<?php

include_once('userstorage.php');
include_once('auth.php');
include_once('utils.php');

function validate($post, &$data, &$errors)
{
    // Validate email
    if (!isset($post["email"])) {
        $errors["email"] = "Email is not set.";
    } elseif (trim($post["email"]) === "") {
        $errors["email"] = "Email is empty.";
    } elseif (!filter_var($post["email"], FILTER_VALIDATE_EMAIL)) {
        $errors["email"] = "Email format is invalid.";
    } else {
        $data["email"] = trim($post["email"]);
    }

    // Validate password
    if (!isset($post["password"])) {
        $errors["password"] = "Password is not set.";
    } elseif (trim($post["password"]) === "") {
        $errors["password"] = "Password is empty.";
    } else {
        $data["password"] = trim($post["password"]);
    }

    return count($errors) === 0;
}


// main
session_start();
$user_storage = new UserStorage();
$auth = new Auth($user_storage);
$data = [];
$errors = [];

if (count($_POST) > 0) {
    if (validate($_POST, $data, $errors)) {
        $logged_in_user = $auth->authenticate($data['email'], $data['password']);
        if (!$logged_in_user) {
            $errors['global'] = "Invalid email or password.";
        } else {
            $auth->login($logged_in_user);

            // Check if user is admin
            if (in_array('admin', $logged_in_user['roles'])) 
            {
                redirect('adminhomepage.php'); // Redirect admin
            } 
            else 
            {
                redirect('userhomepage.php'); // Redirect normal user
            }
        }
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <div class="logo"><a href="homepage.php">iKarRental</a></div>
        <div class="nav">
            <a href="login.php">Login</a>
            <a href="registration.php" class="button">Registration</a>
        </div>
    </header>

    <div class="container">
        <h1>Login</h1>
        <?php if (isset($errors['global'])): ?>
            <div class="global-error-message"><?= ($errors['global']) ?></div>
        <?php endif; ?>
        <form action="" method="POST">
            <div class="form-group">
                <label for="email">Email address</label>
                <input type="text" id="email" name="email">
                <?php if (isset($errors['email'])): ?>
                    <span class="error-message"><?= ($errors['email']) ?></span>
                <?php endif ?>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" >
                <?php if (isset($errors['password'])): ?>
                    <span class="error-message"><?= ($errors['password']) ?></span>
                <?php endif ?>
            </div>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>
