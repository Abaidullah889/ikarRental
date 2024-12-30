<?php
include_once("storage.php");


class CarStorage extends Storage {

    public function __construct()
    {
        parent::__construct(new JsonIO('cars.json'));
        
    }

    public function deleteCar(string $id): bool {
        if ($this->findById($id)) 
        {
            $this->delete($id);
            return true;
        }
        return false;
    }
    public function updateCar(string $id, array $updatedData): bool {
        $car = $this->findById($id);
        if ($car) {
            $this->update($id, $updatedData);
            return true;
        }
        return false;
    }

    public function addCar(array $carData){

        $this->add($carData);
    }


}

?>