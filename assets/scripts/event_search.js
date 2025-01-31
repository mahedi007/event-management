const searchInput = document.getElementById("searchInput");
const filterDateInput = document.getElementById("filterDate");
const resultsDiv = document.getElementById("searchResults");
let currentPage = 1;
const rowsPerPage = 10;

// Event listeners for search and filter
searchInput.addEventListener("input", () => loadEvents());
filterDateInput.addEventListener("change", () => loadEvents());

// Event delegation for dynamically created View buttons
resultsDiv.addEventListener("click", function (event) {
    if (event.target.classList.contains("view-button")) {
        const button = event.target;
        const eventId = button.dataset.id;
        const eventName = button.dataset.name;
        const eventDescription = button.dataset.description;
        const eventDate = button.dataset.date;
        const eventCapacity = button.dataset.capacity;
        const registeredAttendees = button.dataset.registeredAttendees;

        viewEvent(eventId, eventName, eventDescription, eventDate, eventCapacity, registeredAttendees);
    }
});

// Handle alerts for operations
const params = new URLSearchParams(window.location.search);
const status = params.get('status');
const message = params.get('message');

if (status && message) {
    displayMessage(decodeURIComponent(message), status === 'success' ? 'success' : 'danger');

    // Clean up the query parameters to avoid showing the message on refresh
    history.replaceState({}, document.title, window.location.pathname);
}

// Function to display the alert message
function displayMessage(message, type) {
    const alertContainer = document.createElement("div");
    alertContainer.className = `alert alert-${type} fade show position-fixed top-0 start-50 translate-middle-x mt-3 shadow`;
    alertContainer.style.zIndex = "1055";
    alertContainer.textContent = message;

    document.body.appendChild(alertContainer);

    setTimeout(() => {
        alertContainer.classList.remove("show");
        setTimeout(() => alertContainer.remove(), 200);
    }, 3000);
}

// Delete button logic
document.body.addEventListener("click", function (event) {
    if (event.target.classList.contains("delete-button")) {
        const eventId = event.target.getAttribute("data-id");

        if (confirm("Are you sure you want to delete this event?")) {
            fetch("../admin/delete_event.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `id=${eventId}`,
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === "success") {
                    displayMessage(data.message, "danger");

                    const row = event.target.closest("tr");
                    if (row) row.remove();
                } else {
                    displayMessage(data.message, "danger");
                }
            })
            .catch(error => {
                displayMessage("An error occurred: " + error.message, "danger");
            });
        }
    }
});

// Edit event modal logic
document.body.addEventListener("click", function (event) {
    if (event.target.classList.contains("edit-button")) {
        const id = event.target.getAttribute("data-id");
        const name = event.target.getAttribute("data-name");
        const description = event.target.getAttribute("data-description");
        const date = event.target.getAttribute("data-date");
        const capacity = event.target.getAttribute("data-capacity");

        document.getElementById("editEventId").value = id;
        document.getElementById("editName").value = name;
        document.getElementById("editDescription").value = description;
        document.getElementById("editDate").value = date;
        document.getElementById("editCapacity").value = capacity;

        const editModal = new bootstrap.Modal(document.getElementById("editEventModal"));
        editModal.show();
    }
});

document.querySelector("#editEventForm").addEventListener("submit", function (event) {
    event.preventDefault();

    const formData = new FormData(this);

    fetch("../admin/edit_event.php", {
        method: "POST",
        body: formData,
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === "success") {
            displayMessage("Event updated successfully!", "success");
            updateTableRow(formData);

            setTimeout(() => {
                const modal = bootstrap.Modal.getInstance(document.getElementById("editEventModal"));
                modal.hide();
            }, 1000);
        } else {
            displayMessage(data.message, "danger");
        }
    })
    .catch(error => {
        displayMessage("An error occurred: " + error.message, "danger");
    });
});

function updateTableRow(formData) {
    const eventId = formData.get("id");
    const button = document.querySelector(`button[data-id="${eventId}"]`);
    if (button) {
        const row = button.closest("tr");
        if (row) {
            row.cells[1].textContent = formData.get("name");
            row.cells[2].textContent = formData.get("description");
            row.cells[3].textContent = formData.get("date");
            row.cells[4].textContent = formData.get("capacity");
        }
    }
}

// Modal view logic
function viewEvent(id, name, description, date, capacity, registeredAttendees) {
    const modalTitle = document.getElementById("eventModalLabel");
    const modalBody = document.getElementById("eventModalBody");

    modalTitle.innerHTML = `<h4><strong class="text-warning">Event Details:</strong> ${name}</h4>`;
    modalBody.innerHTML = `
        <p><strong class="text-warning">Description:</strong> ${description}</p>
<p><strong class="text-warning">Date:</strong> ${date}</p>
<p><strong class="text-warning">Capacity:</strong> ${capacity}</p>
<p><strong class="text-warning">Registered Attendees:</strong> ${registeredAttendees}</p>
`;
    const modal = new bootstrap.Modal(document.getElementById("eventModal"));
    modal.show();
}

// Pagination Functions
function paginateEvents(events) {
    const start = (currentPage - 1) * rowsPerPage;
    const end = start + rowsPerPage;
    return events.slice(start, end);
}

function createPaginationControls(totalEvents) {
    const totalPages = Math.ceil(totalEvents / rowsPerPage);

    let paginationHTML = `<nav aria-label="Event pagination">
        <ul class="pagination justify-content-center mt-3">`;

    for (let page = 1; page <= totalPages; page++) {
        paginationHTML += `
            <li class="page-item ${page === currentPage ? "active" : ""}">
                <button class="page-link" data-page="${page}">${page}</button>
            </li>`;
    }

    paginationHTML += `</ul></nav>`;
    return paginationHTML;
}

function renderEvents(events) {
    const paginatedEvents = paginateEvents(events);

    let output = `<table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th class="text-nowrap">No.</th>
                <th class="text-nowrap w-25">Event Name</th>
                <th class="text-nowrap w-15">Description</th>
                <th class="text-nowrap">Date</th>
                <th class="text-nowrap">Capacity</th>
                <th class="text-nowrap">Registered Attendees</th>
                <th class="text-nowrap w-50">Actions</th>
            </tr>
        </thead>
        <tbody>`;

    if (paginatedEvents.length > 0) {
        paginatedEvents.forEach((event, index) => {
            output += `
<tr>
    <td>${index + 1 + (currentPage - 1) * rowsPerPage}</td>
    <td>${event.name}</td>
    <td>${event.description}</td>
    <td>${event.date}</td>
    <td>${event.capacity}</td>
    <td>${event.registered_attendees}</td>
    <td class="d-flex flex-wrap gap-2">
    <button class="btn btn-info btn-sm view-button px-3 py-2" data-id="${event.id}" 
        data-name="${event.name}" data-description="${event.description}" 
        data-date="${event.date}" data-capacity="${event.capacity}" 
        data-registered-attendees="${event.registered_attendees}">
        View
    </button>
    <button class="btn btn-warning btn-sm edit-button px-3 py-2" data-id="${event.id}" 
        data-name="${event.name}" data-description="${event.description}" 
        data-date="${event.date}" data-capacity="${event.capacity}">
        Edit
    </button>
    <button class="btn btn-danger btn-sm delete-button px-3 py-2" data-id="${event.id}">Delete</button>
    <a href="../admin/register_attendee.php?event_id=${event.id}" class="btn btn-success btn-sm px-3 py-2">Register</a>
    <a href="../admin/attendee_list.php?event_id=${event.id}" class="btn btn-secondary btn-sm px-3 py-2">View Attendees</a>
</td>

</tr>`;
        });
    } else {
        output += `<tr><td colspan="7" class="text-center">No events found.</td></tr>`;
    }

    output += `</tbody></table>`;
    output += createPaginationControls(events.length);

    resultsDiv.innerHTML = output;

    resultsDiv.querySelectorAll(".page-link").forEach(button => {
        button.addEventListener("click", (event) => {
            currentPage = parseInt(event.target.dataset.page);
            loadEvents();
        });
    });
}

function loadEvents() {
    const query = searchInput.value;
    const filterDate = filterDateInput.value;

    fetch("../ajax/event_search.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `search=${encodeURIComponent(query)}&filter_date=${encodeURIComponent(filterDate)}`
    })
    .then(response => response.json())
    .then(events => {
        renderEvents(events);
    })
    .catch(error => {
        console.error("Error fetching events:", error);
    });
}

document.addEventListener("DOMContentLoaded", () => {
    loadEvents();
});
