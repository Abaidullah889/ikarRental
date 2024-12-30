<?php
include_once("storage.php");



class BookingsStorage extends Storage {

    public function __construct()
    {
        parent::__construct(new JsonIO('bookings.json'));
        
    }

    public function addBooking($car, $fromDate, $untilDate, $user) {
        // Create a new booking record
        $newBooking = [
            'car_id' => $car,           // Car ID
            'user_id' => $user,         // User ID
            'from_date' => $fromDate,         // Booking start date
            'until_date' => $untilDate        // Booking end date
        ];

        // Add the booking and return its ID
        return $this->add($newBooking);
    }

    public function deleteCar(string $id): bool {
        if ($this->findById($id)) 
        {
            $this->delete($id);
            return true;
        }
        return false;
    }
}

?>