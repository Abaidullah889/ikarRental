<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Successful - iKarRental</title>
    <link rel="stylesheet" href="BookingSuccesfull.css">
</head>
<body>
    <header>
        <div class="logo"><a href="homepage.php">iKarRental</a></div>
        <div class="nav">
            <a href="logout.php" class="button">Logout</a>
        </div>
    </header>

    <main class="success-page">
        <div class="icon-container">
            <div class="check-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="none" stroke="white" stroke-width="3" viewBox="0 0 24 24" class="feather feather-check-circle"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14-4-4 1.41-1.41L11 13.17l7.59-7.59L20 7l-9 9z"></path></svg>
            </div>
        </div>
        <h1>Successful booking!</h1>
        <p class="message">The <?= $name ?> has been successfully booked for the interval <?=$from?>–<?=$until?>. You can track the status of your reservation on your profile page.</p>
        <a href="profilepage.php" class="my-profile-btn">My profile</a>
    </main>
</body>
</html>
