<?php
// Example car data (Add image paths correctly)

include_once('carstorage.php');
include_once('bookingstorage.php');
include_once('auth.php');
include_once('utils.php');
include_once('userstorage.php');


session_start();
$auth = new Auth(new UserStorage());
if(!$auth->is_authenticated()){

    redirect("homepage.php");
}


function validateFilter($GET, &$data, &$errors)
{
    // Validate seats
    if (!is_numeric($GET["seats"])) {
        $errors['seats'] = "Seats must be a valid number.";
    } elseif ((int)$GET["seats"] < 0) {
        $errors['seats'] = "Seats must be greater than or equal to 0.";
    } else {
        $data['seats'] = (int)$GET["seats"];
    }

        // Validate 'from' - ensure it's a valid date
    if (isset($GET["from"]) && trim($GET["from"]) != '') {
        $data['from'] = $GET["from"];
        if (strtotime($data['from']) === false) {
            $errors['from'] = "Invalid 'From' date.";
        }
    }

    // Validate 'until' - ensure it's a valid date
    if (isset($GET["until"]) && trim($GET["until"]) != '') {
        $data['until'] = $GET["until"];
        if (strtotime($data['until']) === false) {
            $errors['until'] = "Invalid 'Until' date.";
        }
    }


   // Ensure 'from' is not after 'until'
    if (isset($data['from'], $data['until']) && strtotime($data['from']) > strtotime($data['until'])) {
         $errors['from'] = "'From' date cannot be later than 'Until' date.";
    }


    // Validate 'transmission' - must be either 'automatic' or 'manual'
    if (isset($GET["transmission"])) {
        $data['transmission'] = $GET["transmission"];
        if ($data['transmission'] !== '' && $data['transmission'] !== 'automatic' && $data['transmission'] !== 'manual' && $data['transmission'] !== 'any' ) {
            $errors['transmission'] = "Transmission must be either 'automatic' or 'manual'.";
        }
    }

        // Validate 'min_price' - ensure it's a valid number
    if (isset($GET["min_price"])) {
        if (!is_numeric($GET["min_price"])) {
            $errors['min_price'] = "Min Price must be a valid number.";
        } elseif ((int)$GET["min_price"] < 0) {
            $errors['min_price'] = "Min Price must be greater than or equal to 0.";
        } else {
            $data['min_price'] = (int)$GET["min_price"];
        }
    }

    // Validate 'max_price' - ensure it's a valid number
    if (isset($GET["max_price"])) {
        if (!is_numeric($GET["max_price"])) {
            $errors['max_price'] = "Max Price must be a valid number.";
        } elseif ((int)$GET["max_price"] < 0) {
            $errors['max_price'] = "Max Price must be greater than or equal to 0.";
        } else {
            $data['max_price'] = (int)$GET["max_price"];
        }

        // Ensure 'min_price' is not greater than 'max_price'
        if (isset($data['min_price']) && isset($data['max_price'])) {
            if ($data['min_price'] > $data['max_price']) {
                $errors['min_price'] = "Min Price cannot be greater than Max Price.";
            }
        }
    }


    return count($errors) === 0;
}



$CarsStorage = new CarStorage();
$cars = $CarsStorage->findAll();

$bookingstorage = new BookingsStorage();
$bookings = $bookingstorage->findAll();


$filtered_cars = $cars; // Default to showing all cars

// Main
$errors = [];
$data = [];


function isCarAvailable($carId, $fromDate, $untilDate, $bookings) {
    foreach ($bookings as $booking) {
        if ($booking['car_id'] == ($carId)) {
            // Check for overlapping date ranges
            if (
                ($fromDate <= $booking['until_date'] && $fromDate >= $booking['from_date']) ||
                ($untilDate <= $booking['until_date'] && $untilDate >= $booking['from_date']) ||
                ($fromDate <= $booking['from_date'] && $untilDate >= $booking['until_date'])
            ) {
                return false; // Car is not available
            }
        }
    }
    return true; // Car is available
}


if (count($_GET) > 0) {
    if (validateFilter($_GET, $data, $errors)) {

        $seats = isset($data['seats']) ? $data['seats'] : 0;
         //validate Date Range
         $from = isset($data['from']) ? $data['from'] : '';
         $until = isset($data['until']) ? $data['until'] : '';

        $transmission = isset($data['transmission']) ? $data['transmission'] : '';
        $min_price = isset($data['min_price']) ? $data['min_price'] : 0;
        $max_price = isset($data['max_price']) ? $data['max_price'] : 99999;


        $filtered_cars = array_filter($cars, function ($car) use ($seats, $transmission, $min_price, $max_price,$from,$until,$bookings) {
            // Validate seating capacity
            $seatingCondition = ($seats == 0 || (int)$car['passengers'] == $seats);

        
            // Validate transmission type
            $transmissionCondition = ($transmission == '' || $transmission == 'any' || strtolower($car['transmission']) === strtolower($transmission));
        
            // Validate price range
            $priceCondition = ((int)$car['daily_price_huf'] >= $min_price && (int)$car['daily_price_huf'] <= $max_price);
            
           

            $availabilityCondition = true;
            if ($from != '' && $until != '') 
            {
                $availabilityCondition = isCarAvailable($car['id'], $from, $until, $bookings); 
            }
        
            return $seatingCondition && $transmissionCondition && $priceCondition && $availabilityCondition;
        });   
    }
    else
    {

        $filtered_cars = $cars;
    }
}

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

    <main>

        <section class="hero">
            <h1>Rent cars easily!</h1><br>
            <form class="filter-form" method="GET" novalidate>
                <div>
                    <label for="seats">Seats</label>
                <input type="number" name="seats" id="seats" min="0" value="<?php echo htmlspecialchars($_GET['seats'] ?? ''); ?>">
            <?php if (isset($errors['seats'])): ?>
                <span class="error-message"><?= htmlspecialchars($errors['seats']) ?></span>
            <?php endif; ?>
        </div>
        <div>
            <label for="from">From</label>
            <input type="date" name="from" id="from" value="<?php echo htmlspecialchars($_GET['from'] ?? ''); ?>">
            <?php if (isset($errors['from'])): ?>
                <span class="error-message"><?= htmlspecialchars($errors['from']) ?></span>
            <?php endif; ?>
        </div>
        <div>
            <label for="until">Until</label>
            <input type="date" name="until" id="until" value="<?php echo htmlspecialchars($_GET['until'] ?? ''); ?>">
            <?php if (isset($errors['until'])): ?>
                <span class="error-message"><?= htmlspecialchars($errors['until']) ?></span>
            <?php endif; ?>
        </div>
        <div>
            <label for="transmission">Gear type</label>
            <select name="transmission" id="transmission">
                <option value="any" <?php echo ($_GET['transmission'] ?? '') === 'any' ? 'selected' : ''; ?>>Any</option>
                <option value="automatic" <?php echo ($_GET['transmission'] ?? '') === 'automatic' ? 'selected' : ''; ?>>Automatic</option>
                <option value="manual" <?php echo ($_GET['transmission'] ?? '') === 'manual' ? 'selected' : ''; ?>>Manual</option>
            </select>
            <?php if (isset($errors['transmission'])): ?>
                <span class="error-message"><?= htmlspecialchars($errors['transmission']) ?></span>
            <?php endif; ?>
        </div>
        <div>
            <label for="min_price">Min Price</label>
            <input type="number" name="min_price" id="min_price" value="<?php echo htmlspecialchars($_GET['min_price'] ?? 0); ?>">
            <?php if (isset($errors['min_price'])): ?>
                <span class="error-message"><?= htmlspecialchars($errors['min_price']) ?></span>
            <?php endif; ?>
        </div>
        <div>
            <label for="max_price">Max Price</label>
            <input type="number" name="max_price" id="max_price" value="<?php echo htmlspecialchars($_GET['max_price'] ?? 99999); ?>">
            <?php if (isset($errors['max_price'])): ?>
                <span class="error-message"><?= htmlspecialchars($errors['max_price']) ?></span>
            <?php endif; ?>
        </div>
        <button type="submit">Filter</button>
    </form>

                

        </section>

        <section class="car-list">
            <?php foreach ($filtered_cars as $car): ?>
                <div class="car-card">
            <div class="car-image-container">
                <img src="<?php echo htmlspecialchars($car['image']); ?>" alt="<?php echo htmlspecialchars($car['brand']); ?>" class="car-image">
            </div>
            <div class="car-info">
                <p class="car-price"><?php echo number_format($car['daily_price_huf']); ?> Ft</p>
                <h7 class="car-name"><?php echo htmlspecialchars($car['brand']); ?> - <?php echo htmlspecialchars($car['model']); ?> </h7>
                <p class="car-details"><?php echo htmlspecialchars($car['passengers']); ?> seats - <?php echo ucfirst($car['transmission']); ?> transmission</p>
                <a href="cardetail.php?id=<?php echo $car['id']; ?>" 
                    class="book-button">
                    Book
                </a>
            </div>
                </div>
            <?php endforeach; ?>
        </section>

    </main>
</body>
</html>
