document.getElementById("searchInput").addEventListener("keyup", function () {
    const searchTerm = this.value.toLowerCase();
    const eventId = new URLSearchParams(window.location.search).get("event_id");

    fetch(`../ajax/attendee_search.php?event_id=${eventId}&query=${searchTerm}`)
        .then((response) => response.text())
        .then((data) => {
            document.getElementById("attendeeTableContainer").innerHTML = data;
        })
        .catch((error) => console.error("Error fetching attendees:", error));
});
