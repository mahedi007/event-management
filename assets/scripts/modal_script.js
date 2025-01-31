document.addEventListener("DOMContentLoaded", function () {
  const searchInput = document.getElementById("searchAttendees");
  const attendeeTableBody = document.querySelector("#attendeeTable");
  const editModal = document.getElementById("editModal");

  // Function to fetch attendees dynamically
  const fetchAttendees = () => {
      const query = searchInput.value.trim();
      const eventId = searchInput.dataset.eventId; // Ensure event ID is set in the input field

      fetch(`../ajax/attendee_search.php?event_id=${eventId}&query=${query}`)
          .then(response => response.text())
          .then(html => {
              attendeeTableBody.innerHTML = html;
              reattachEventListeners(); // Ensure buttons still work after table updates
          })
          .catch(error => console.error("Error fetching attendees:", error));
  };

  // Function to reattach event listeners after table updates
  const reattachEventListeners = () => {
      document.querySelectorAll(".edit-btn").forEach(button => {
          button.addEventListener("click", function () {
              const attendeeId = this.getAttribute("data-id");
              const attendeeName = this.getAttribute("data-name");
              const attendeeEmail = this.getAttribute("data-email");

              document.getElementById("attendee_id").value = attendeeId;
              document.getElementById("attendee_name").value = attendeeName;
              document.getElementById("attendee_email").value = attendeeEmail;
          });
      });

      document.querySelectorAll(".remove-btn").forEach(button => {
          button.addEventListener("click", function (event) {
              if (!confirm("Are you sure you want to remove this attendee?")) {
                  event.preventDefault();
              }
          });
      });
  };

  // Attach search event listener
  searchInput.addEventListener("input", function () {
      fetchAttendees();
  });

  // Load full attendee list on page load
  fetchAttendees();
});
