<?php

include_once('auth.php');
include_once('utils.php');
include_once('userstorage.php');
include_once('bookingstorage.php');

session_start();
 $auth = new Auth(new UserStorage());
 if (!$auth->authorize(['admin'])) {
    redirect('login.php');
}

$BookingStorage = new BookingsStorage();

$val = $BookingStorage->deleteCar($_GET['id']);
redirect('adminprofilepage.php');

?>