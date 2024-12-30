<?php
include_once("carstorage.php");
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
$id = $_GET['id'];
$CarsStorage = new CarStorage();
$cars = $CarsStorage->findAll();
$car = $cars[$id]; 

$Bookings = new BookingsStorage();

function validate($GET, &$data, &$errors)
{
        // Validate 'from' - ensure it's a valid date
    if (isset($GET["from"]) && trim($GET["from"]) != '') {
        $data['from'] = $GET["from"];
        if (strtotime($data['from']) === false) {
            $errors['from'] = "Invalid 'From' date.";
        }
    }
    else
    {
        $errors['from'] = "'From' date cannot be empty.";
    }

    // Validate 'until' - ensure it's a valid date
    if (isset($GET["until"]) && trim($GET["until"]) != '') {
        $data['until'] = $GET["until"];
        if (strtotime($data['until']) === false) {
            $errors['until'] = "Invalid 'Until' date.";
        }
    }
    else
    {
        $errors['until'] = "'Until' date cannot be empty.";
    }

   // Ensure 'from' is not after 'until'
    if (isset($data['from'], $data['until']) && strtotime($data['from']) > strtotime($data['until'])) {
         $errors['from'] = "'From' date cannot be later than 'Until' date.";
    }

    return count($errors) === 0;
}

function isCarAvailable($Bookings, $carId, $fromDate, $untilDate) {
    $bookings = $Bookings->findAll();
    foreach ($bookings as $booking) {
        if ($booking['car_id'] == $carId && !(strtotime($untilDate) < strtotime($booking['from_date']) || strtotime($fromDate) > strtotime($booking['until_date'])
            )) {
            return false;
        }
    }
    return true; 
}


$data = [];
$errors = [];


if (count($_POST) > 0) {
    if (validate($_POST, $data, $errors)) {

        if (!isCarAvailable($Bookings, $id, $data['from'], $data['until'])) {
            $s = "bookingFailed.php?name=" . ($car['brand'] ) . ' ' . ($car['model']) . "&from=" . ($data['from']) . "&until=" . ($data['until']);
            redirect($s);
        }
        else{

            $s = "bookingSuccesfull.php?name=" . ($car['brand'] ) . ' ' . ($car['model']) . "&from=" . ($data['from']) . "&until=" . ($data['until']);
            $Bookings->addBooking($id,$data['from'],$data['until'],$_SESSION[$sessionKey]['id']);
            redirect($s);
        }
        


    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $car['brand']; ?> <?php echo $car['model']; ?> - iKarRental</title>
    <link rel="stylesheet" href="cardetail.css">
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

    <main>
        <section class="car-detail">
            <div class="car-image-container">
                <img src="<?php echo ($car['image']); ?>" alt="<?php echo ($car['brand']); ?>  <?php echo ($car['model']); ?>" class="car-image">
            </div>
            <div class="car-info">
                <h2 class="car-name"><?php echo ($car['brand']); ?>  <?php echo ($car['model']); ?></h2>
                <p><strong>Fuel Type:</strong> <?php echo ($car['fuel_type']); ?></p>
                <p><strong>Shifter:</strong> <?php echo ($car['transmission']); ?></p>
                <p><strong>Year of manufacture:</strong> <?php echo ($car['year']); ?></p>
                <p><strong>Number of seats:</strong> <?php echo ($car['passengers']); ?></p>
                <p class="car-price">HUF <?php echo ($car['daily_price_huf']); ?>/day</p>
                
                <form action="" method="POST" class="date-form" novalidate>
                    <label for="start_date">From:</label>
                    <input type="date" id="start_date" name="from">
                    <?php if (isset($errors['from'])): ?>
                        <span class="error-message"><?= ($errors['from']) ?></span>
                    <?php endif; ?>
                    <label for="end_date">Until:</label>
                    <input type="date" id="end_date" name="until" >
                    <?php if (isset($errors['until'])): ?>
                        <span class="error-message"><?= ($errors['until']) ?></span>
                    <?php endif; ?>
                    <button type="submit" class="submit-date-btn">Book Now</button>
            </form>

            </div>
        </section>
    </main>
</body>
</html>
