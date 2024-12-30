<?php

include_once('carstorage.php');
include_once('utils.php');
$CarsStorage = new CarStorage();


$val = $CarsStorage->deleteCar($_GET['id']);
redirect('adminhomepage.php');



?>