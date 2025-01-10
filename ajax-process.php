<?php

include_once('bookingstorage.php');
include_once('carstorage.php');
include_once('auth.php');
include_once('userstorage.php');

session_start();
$auth = new Auth(new UserStorage());
if (!$auth->is_authenticated()) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

if (!isset($_POST['car_id']) || trim($_POST['car_id']) === '') {
    echo json_encode(['success' => false, 'message' => 'Missing car_id']);
    exit;
}

if (!isset($_POST['from']) || trim($_POST['from']) === '') {
    echo json_encode(['success' => false, 'message' => 'Missing from date']);
    exit;
}

if (!isset($_POST['until']) || trim($_POST['until']) === '') {
    echo json_encode(['success' => false, 'message' => 'Missing until date']);
    exit;
}

$carId = $_POST['car_id'];
$from = $_POST['from'];
$until = $_POST['until'];

$Bookings = new BookingsStorage();
$CarsStorage = new CarStorage();
$car = $CarsStorage->findById($carId);

if (!$car) {
    echo json_encode(['success' => false, 'message' => 'Car not found']);
    exit;
}

// Add booking
$sessionKey = key($_SESSION);
$userId = $_SESSION[$sessionKey]['id'];
$Bookings->addBooking($carId, $from, $until, $userId);

echo json_encode([
    'success' => true,
    'car_name' => $car['brand'] . ' ' . $car['model'],
    'from' => $from,
    'until' => $until,
]);

exit;
?>
