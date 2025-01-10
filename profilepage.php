
<?php

include_once('carstorage.php');
include_once('auth.php');
include_once('utils.php');
include_once('userstorage.php');
include_once('bookingstorage.php');

session_start();
$auth = new Auth(new UserStorage());
if(!$auth->is_authenticated()){

    redirect("login.php");
}

$sessionKey = key($_SESSION);
$userId = $_SESSION[$sessionKey]['id'];

$CarsStorage = new CarStorage();

$cars = $CarsStorage->findAll();




$Bookingstorage = new BookingsStorage();
$bookings = $Bookingstorage->findAll();


$userBookings = array_filter($bookings, function ($booking) use ($userId) {
    return $booking['user_id'] === $userId;
});





?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>iKarRental</title>
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

    <main class="profile-container">

        <section class="profile">
            <img class="profile-image" src="https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_1280.png" alt="User Profile"><br>
            <h1><?= $_SESSION[$sessionKey]['fullname'] ?></h1>
        </section>

        <section class="reservations">
            <h2>My Reservations</h2>
        </section>

        <section class="reserved-cars-container">
            <div class="reserved-cars-list">
                <?php foreach ($userBookings as $booking): 
                    $car = $cars[$booking['car_id']];
                ?>
                <div class="reserved-cars-card">
                    <img src="<?= ($car['image']) ?>" alt="<?= ($car['brand'] . ' ' . $car['model']) ?>">
                    <div class="reserved-cars-info">
                        <p class="date"><?= ($booking['from_date'] . " – " . $booking['until_date']) ?></p>
                        <p class="reserved-cars-name"><?= ($car['brand'] . ' ' . $car['model']) ?></p>
                        <p class="reserved-cars-details"><?= ($car['passengers'] . ' seats · ' . ($car['transmission'])) ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>


    </main>
</body>
</html>
