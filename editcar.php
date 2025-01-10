<?php
include_once('carstorage.php');
include_once('auth.php');
include_once('utils.php');
include_once('userstorage.php');

session_start();
$auth = new Auth(new UserStorage());


if (!$auth->is_authenticated()) {
    redirect("homepage.php");
}

// Initialize variables
$errors = [];
$data = [];
$carId = isset($_GET['id']) ? (int)$_GET['id'] : null;


if ($carId === null) {
    redirect("homepage.php");
}

$carStorage = new CarStorage();
$car = $carStorage->findById($carId);


if ($car === null) {
    redirect("homepage.php");
}


function validateEditForm($post, &$errors) {
    // Validate brand
    if (!isset($post['brand']) || trim($post['brand']) === '') {
        $errors['brand'] = "Brand is required.";
    }

    // Validate model
    if (!isset($post['model']) || trim($post['model']) === '') {
        $errors['model'] = "Model is required.";
    }

    // Validate year
    if (!isset($post['year']) || !is_numeric($post['year'])){
        $errors['year'] = "Year must be a valid number between 1886 and the current year.";
    }

    // Validate transmission
    if (!isset($post['transmission']) || !in_array(strtolower($post['transmission']), ['manual', 'automatic'])) {
        $errors['transmission'] = "Transmission must be 'manual' or 'automatic'.";
    }

    // Validate fuel type
    if (!isset($post['fuel_type']) || !in_array(strtolower($post['fuel_type']), ['petrol', 'diesel', 'electric'])) {
        $errors['fuel_type'] = "Fuel type must be 'Petrol', 'Diesel', 'Electric'.";
    }

    // Validate passengers
    if (!isset($post['passengers']) || !is_numeric($post['passengers']) || (int)$post['passengers'] <= 0) {
        $errors['passengers'] = "Passengers must be a valid positive number.";
    }

    // Validate daily price
    if (!isset($post['daily_price_huf']) || !is_numeric($post['daily_price_huf']) || (int)$post['daily_price_huf'] < 0) {
        $errors['daily_price_huf'] = "Price must be a valid positive number.";
    }

    if (!isset($post['image']) || trim($post['image']) === '') {
        $errors['image'] = "Image is required.";
    }

    return count($errors) === 0;
}



if (count($_POST) > 0) {

    if (validateEditForm($_POST, $errors)) {
        $data = $_POST;
        $updatedCar = [
            'id' => $carId,
            'brand' => htmlspecialchars($data['brand']),
            'model' => htmlspecialchars($data['model']),
            'year' => (int)$data['year'],
            'transmission' => strtolower($data['transmission']),
            'fuel_type' => strtolower($data['fuel_type']),
            'passengers' => (int)$data['passengers'],
            'daily_price_huf' => (int)$data['daily_price_huf'],
            'image' => htmlspecialchars($data['image']),
        ];

        $carStorage->update($carId, $updatedCar);
        redirect("adminhomepage.php"); 
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Car</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <div class="logo"><a href="homepage.php">iKarRental</a></div>
        <div class="nav">
            <a href="profilepage.php" class="profile-button">
                <img src="https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_1280.png" alt="Profile">
            </a>              
            <a href="logout.php" class="button">Logout</a>
        </div>
    </header>
    <main class="edit-container">
        <h1 style="color:darkorange">Edit Car</h1>
        <form method="POST" novalidate>
            <div class="form-group">
                <label for="brand">Brand</label>
                <input type="text" name="brand" id="brand" value="<?= ($car['brand']) ?>">
                <?php if (isset($errors['brand'])): ?>
                    <span class="error-message"><?= ($errors['brand']) ?></span>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="model">Model</label>
                <input type="text" name="model" id="model" value="<?= ($car['model']) ?>">
                <?php if (isset($errors['model'])): ?>
                    <span class="error-message"><?= ($errors['model']) ?></span>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="year">Year</label>
                <input type="number" name="year" id="year" value="<?= ($car['year']) ?>">
                <?php if (isset($errors['year'])): ?>
                    <span class="error-message"><?= ($errors['year']) ?></span>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="transmission">Transmission</label>
                <select name="transmission" id="transmission">
                    <option value="manual" <?= $car['transmission'] === 'manual' ? 'selected' : '' ?>>Manual</option>
                    <option value="automatic" <?= $car['transmission'] === 'automatic' ? 'selected' : '' ?>>Automatic</option>
                </select>
                <?php if (isset($errors['transmission'])): ?>
                    <span class="error-message"><?= ($errors['transmission']) ?></span>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="fuel_type">Fuel Type</label>
                <select name="fuel_type" id="fuel_type">
                    <option value="petrol" <?= $car['fuel_type'] === 'petrol' ? 'selected' : '' ?>>Petrol</option>
                    <option value="diesel" <?= $car['fuel_type'] === 'diesel' ? 'selected' : '' ?>>Diesel</option>
                    <option value="electric" <?= $car['fuel_type'] === 'electric' ? 'selected' : '' ?>>Electric</option>
                </select>
                <?php if (isset($errors['fuel_type'])): ?>
                    <span class="error-message"><?= ($errors['fuel_type']) ?></span>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="passengers">Passengers</label>
                <input type="number" name="passengers" id="passengers" value="<?= ($car['passengers']) ?>">
                <?php if (isset($errors['passengers'])): ?>
                    <span class="error-message"><?= ($errors['passengers']) ?></span>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="daily_price_huf">Daily Price (HUF)</label>
                <input type="number" name="daily_price_huf" id="daily_price_huf" value="<?= ($car['daily_price_huf']) ?>">
                <?php if (isset($errors['daily_price_huf'])): ?>
                    <span class="error-message"><?= ($errors['daily_price_huf']) ?></span>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="image">Image URL</label>
                <input type="text" name="image" id="image" value="<?= ($car['image']) ?>">
                <?php if (isset($errors['image'])): ?>
                    <span class="error-message"><?= ($errors['image']) ?></span>
                <?php endif; ?>
            </div>
            <button type="submit">Save Changes</button>
        </form>
    </main>
</body>
</html>
