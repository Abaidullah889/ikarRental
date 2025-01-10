<?php
include_once("storage.php");



class BookingsStorage extends Storage {

    public function __construct()
    {
        parent::__construct(new JsonIO('bookings.json'));
        
    }

    public function addBooking($car, $fromDate, $untilDate, $user) {
        $newBooking = [
            'car_id' => $car,           
            'user_id' => $user,         
            'from_date' => $fromDate,         
            'until_date' => $untilDate        
        ];

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