<?php
include_once('bookingstorage.php');


$Bookings = new BookingsStorage();
$carId = $_GET['car_id'];
$bookings = $Bookings->findAll();

$unavailableDates = [];
foreach ($bookings as $booking) {
    if ($booking['car_id'] == $carId) {
        $currentDate = strtotime($booking['from_date']);
        $endDate = strtotime($booking['until_date']);

        while ($currentDate <= $endDate) {
            $unavailableDates[] = date('Y-m-d', $currentDate);
            $currentDate = strtotime('+1 day', $currentDate);
        }
    }
}

echo json_encode($unavailableDates);
