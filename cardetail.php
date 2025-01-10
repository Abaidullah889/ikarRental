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

if(!isset($_GET['id'])){
    redirect('login.php');
}


$sessionKey = key($_SESSION);
$id = $_GET['id'];
$CarsStorage = new CarStorage();
$cars = $CarsStorage->findAll();
$car = $cars[$id]; 

$Bookings = new BookingsStorage();

function validate($GET, &$data, &$errors)
{
        // Validate 'from' 
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

    // Validate 'until' 
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

    if (isset($data['from'], $data['until']) && strtotime($data['from']) > strtotime($data['until'])) {
         $errors['from'] = "'From' date cannot be later than 'Until' date.";
    }

    return count($errors) === 0;
}


$data = [];
$errors = [];
$name = "";
$from = "";
$until = "";


if (count($_POST) > 0) {
    if (validate($_POST, $data, $errors)) {

        $Bookings->addBooking($id,$data['from'],$data['until'],$_SESSION[$sessionKey]['id']);
        $from=$data['from'];
        $until=$data['until'];
        $name= $car['brand'].' '.$car['model'];
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/dark.css">
    <link rel="stylesheet" href="BookingSuccesfull.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
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
                <img src="<?php echo ($car['image']); ?>" alt="<?php echo ($car['brand']); ?> <?php echo ($car['model']); ?>" class="car-image">
            </div>
            <div class="car-info">
                <h2 class="car-name"><?php echo ($car['brand']); ?> <?php echo ($car['model']); ?></h2>
                <p><strong>Fuel Type:</strong> <?php echo ($car['fuel_type']); ?></p>
                <p><strong>Shifter:</strong> <?php echo ($car['transmission']); ?></p>
                <p><strong>Year of manufacture:</strong> <?php echo ($car['year']); ?></p>
                <p><strong>Number of seats:</strong> <?php echo ($car['passengers']); ?></p>
                <p class="car-price">HUF <?php echo ($car['daily_price_huf']); ?>/day</p>
                
                <form action="" method="POST" class="date-form" data-car-id=<?=$id?> novalidate>
                    <label for="start_date">From:</label>
                    <input type="text" id="start_date" name="from" >
                    <?php if (isset($errors['from'])): ?>
                        <span class="error-message"><?= ($errors['from']) ?></span>
                    <?php endif; ?>
                    <label for="end_date">Until:</label>
                    <input type="text" id="end_date" name="until" >
                    <?php if (isset($errors['until'])): ?>
                        <span class="error-message"><?= ($errors['until']) ?></span>
                    <?php endif; ?>
                    <button type="submit" class="submit-date-btn">Book Now</button>
                </form>
            </div>
        </section>


        <div id="success-page" class="success-page" style="display: none;">
            <div class="icon-container">
                <div class="check-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="none" stroke="white" stroke-width="3" viewBox="0 0 24 24" class="feather feather-check-circle"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-10 10-10S17.52 2 12 2zm-1 14-4-4 1.41-1.41L11 13.17l7.59-7.59L20 7l-9 9z"></path></svg>
                    </div>
            </div>
            <h1>Successful booking!</h1>
            <p class="message">The <span id="car-name"></span> has been successfully booked for the interval <span id="booking-interval"></span>. You can track the status of your reservation on your profile page.</p>
            <a href="profilepage.php" class="my-profile-btn">My profile</a>
        </div>

        <script src="calendar.js"></script>
    </main>


</body>
</html>
