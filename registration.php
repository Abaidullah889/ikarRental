<?php
include_once('userstorage.php');
include_once('auth.php');
include_once('utils.php');

function validate($post, &$data, &$errors)
{
    // Validate full name
    if (!isset($post["fullname"])) {
        $errors["fullname"] = "Full name is not set.";
    } elseif (trim($post["fullname"]) === "") {
        $errors["fullname"] = "Full name is empty.";
    } else {
        $data["fullname"] = trim($post["fullname"]);
    }

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
    } elseif (strlen($post["password"]) < 6) {
        $errors["password"] = "Password must be at least 6 characters.";
    } else {
        $data["password"] = trim($post["password"]);
    }

    // Validate password confirmation
    if (!isset($post["password-again"])) {
        $errors["password-again"] = "Password confirmation is not set.";
    } elseif (trim($post["password-again"]) === "") {
        $errors["password-again"] = "Password confirmation is empty.";
    } elseif ($post["password"] !== $post["password-again"]) {
        $errors["password-again"] = "Passwords do not match.";
    }

    return count($errors) === 0;
}


// main
$user_storage = new UserStorage();
$errors = [];
$auth = new Auth($user_storage);
$data = [];
if (count($_POST) > 0) {
  if (validate($_POST, $data, $errors)) 
  {
    print_r($data);
    if ($auth->user_exists($data["email"])) {
      $errors['global'] = "User already exists";
    } else {
      $auth->register($data);
      redirect("login.php");
    } 
  }
}

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Page</title>
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
        <h1>Sign Up</h1>
        <!-- global error message -->
        <?php if (isset($errors['global'])): ?>
            <div class="global-error-message"><?= htmlspecialchars($errors['global']) ?></div>
        <?php endif; ?>

        <form action="" method="POST" novalidate>
            <div class="form-group">
                <label for="fullname">Full name</label>
                <input type="text" id="fullname" name="fullname">
                <?php if (isset($errors['fullname'])): ?>
                    <span class="error-message"><?= htmlspecialchars($errors['fullname']) ?></span>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="email">Email address</label>
                <input type="email" id="email" name="email" >
                <?php if (isset($errors['email'])): ?>
                    <span class="error-message"><?= htmlspecialchars($errors['email']) ?></span>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" >
                <?php if (isset($errors['password'])): ?>
                    <span class="error-message"><?= htmlspecialchars($errors['password']) ?></span>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="password-again">Password again</label>
                <input type="password" id="password-again" name="password-again" >
                <?php if (isset($errors['password-again'])): ?>
                    <span class="error-message"><?= htmlspecialchars($errors['password-again']) ?></span>
                <?php endif; ?>
            </div>
            <button type="submit">Register</button>
        </form>
    </div>
</body>
</html>
