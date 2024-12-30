<?php




$name = $_GET['name'];
$from = $_GET['from'];
$until = $_GET['until'];


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Failed - iKarRental</title>
    <link rel="stylesheet" href="bookingFailed.css">
</head>
<body>
    <header>
        <div class="logo"><a href="homepage.php">iKarRental</a></div>
        <div class="nav">
            <a href="logout.php" class="button">Logout</a>
        </div>
    </header>

    <main class="failed-page">
    <div class="icon-container">
  <div class="error-icon">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <circle cx="12" cy="12" r="10" fill="#e74c3c"/>
      <line x1="16" y1="8" x2="8" y2="16"/>
      <line x1="8" y1="8" x2="16" y2="16"/>
    </svg>
  </div>
</div>
        <h1>Booking failed!</h1>
        <p class="message">The <?= $name?> is not available in the specified interval from <?= $from?> to <?=$until?>.<br>Try entering a different interval or search for another vehicle.</p>
        <a href="homepage.php" class="back-btn">Back to the vehicle side</a>
    </main>
</body>
</html>
