document.addEventListener('DOMContentLoaded', function () {
    console.log("DOM fully loaded and parsed");
    // Get the button and popover elements
    const button = document.querySelector('#download-button');
    const popover = document.querySelector('#download-popover');

    // Add click event listener to the button
    button.addEventListener('click', function () {
      // Toggle the visibility of the popover
      popover.classList.toggle('is-hidden');
      //if the user ckicks outside the popover, it will be hidden
      document.addEventListener('click', function (event) {
        if (!popover.contains(event.target) && !button.contains(event.target)) {
          popover.classList.add('is-hidden');
        }
      });

    });

    // Get the download button element
    const downloadBtn = document.querySelector('#download-csv-button');

    // Add click event listener to the download button
    downloadBtn.addEventListener('click', function () {
      // Get the selected month and year values
      const monthSelect = document.querySelector('#month-select');
      const yearSelect = document.querySelector('#year-select');
      const month = monthSelect.value;
      const year = yearSelect.value;

      // Redirect to the CSV download URL with the selected month and year values
      const url = `api/getStudentsBlocksBought.php?block_type=FOCACCINA&month=${month}&year=${year}&mode=CSV`;
      window.location.href = url;
    });
  });