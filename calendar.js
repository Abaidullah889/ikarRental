document.addEventListener("DOMContentLoaded",  function () {
    const formElement = document.querySelector(".date-form");
    const carId = formElement.getAttribute("data-car-id");

    const successPage = document.getElementById("success-page");
    const carNameElement = document.getElementById("car-name");
    const bookingIntervalElement = document.getElementById("booking-interval");
    const carDetailSection = document.querySelector(".car-detail");


    formElement.addEventListener("submit", async function (event) {
        event.preventDefault();

        const from = this.querySelector("#start_date").value; 
        const until = this.querySelector("#end_date").value;

        console.log(until);

        const response = await fetch("ajax-process.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded",
            },
            body: `from=${(from)}&until=${(until)}&car_id=${(carId)}`,
        });
        
        const result = await response.json();

        if (result.success) {
            // Populate success details
            carNameElement.textContent = result.car_name;
            bookingIntervalElement.textContent = `${result.from} - ${result.until}`;

            // Hide the car detail section and show the success section
            carDetailSection.style.display = "none";
            successPage.style.display = "block";
        } 


    });
});



function calendar()
{
    const formElement = document.querySelector(".date-form");
    const carId = formElement.getAttribute("data-car-id");
    const unavailableDatesEndpoint = `unavailable_dates.php?car_id=${carId}`;




    
    fetch(unavailableDatesEndpoint)
        .then(response => response.json())
        .then(unavailableDates => {
            const startDatePicker = document.querySelector("#start_date");
            const endDatePicker = document.querySelector("#end_date");

            const flatpickrStartOptions = {
                dateFormat: "Y-m-d",
                disable: unavailableDates,
                minDate: "today",
                onChange: function (selectedDates, dateStr) {
                    // Update the minimum date for the 'Until' picker
                    endDatePicker._flatpickr.set("minDate", dateStr);
                }
            };

            const flatpickrEndOptions = {
                dateFormat: "Y-m-d",
                disable: unavailableDates,
                minDate: "today",
                onChange: function (selectedDates, dateStr) {
                    startDatePicker._flatpickr.set("maxDate", dateStr);
                }
            };

            startDatePicker.flatpickr(flatpickrStartOptions);
            endDatePicker.flatpickr(flatpickrEndOptions);
        });

}

calendar();