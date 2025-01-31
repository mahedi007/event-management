document.addEventListener("DOMContentLoaded", () => {
    const totalEventsElem = document.getElementById("totalEvents");
    const totalAttendeesElem = document.getElementById("totalAttendees");
    const totalCapacityElem = document.getElementById("totalCapacity");
    const completedEventsElem = document.getElementById("completedEvents");
    const upcomingEventsElem = document.getElementById("upcomingEvents");

    const completedPaginationElem = document.getElementById("completedPagination");
    const upcomingPaginationElem = document.getElementById("upcomingPagination");

    const eventsTodayElem = document.getElementById("eventsToday");

    const eventsPerPage = 5;
    let completedEvents = [];
    let upcomingEvents = [];
    let completedPage = 0;
    let upcomingPage = 0;

    function renderPagination(events, container, paginationContainer, page, isAutoSlide = false) {
        const totalPages = Math.ceil(events.length / eventsPerPage);
        container.innerHTML = "";

        if (events.length) {
            const start = page * eventsPerPage;
            const end = start + eventsPerPage;
            const eventsToShow = events.slice(start, end);

            eventsToShow.forEach(event => {
                container.innerHTML += `<li><span style="color: #ff9800; font-style: italic;">${event.name}</span> - ${event.date}</li>`;
            });
        } else {
            container.innerHTML = "<li>No events</li>";
        }

        // Show dots only if more than 5 events
        paginationContainer.innerHTML = "";
        if (totalPages > 1) {
            paginationContainer.style.display = "flex"; // Show pagination
            for (let i = 0; i < totalPages; i++) {
                const dot = document.createElement("span");
                dot.classList.add("pagination-dot");
                if (i === page) dot.classList.add("active");
                dot.addEventListener("click", () => {
                    if (container === completedEventsElem) {
                        completedPage = i;
                        renderPagination(completedEvents, completedEventsElem, completedPaginationElem, completedPage);
                    } else {
                        upcomingPage = i;
                        renderPagination(upcomingEvents, upcomingEventsElem, upcomingPaginationElem, upcomingPage);
                    }
                });
                paginationContainer.appendChild(dot);
            }
        } else {
            paginationContainer.style.display = "none"; // Hide if only 1 page
        }

        // Auto-slide functionality
        if (isAutoSlide && totalPages > 1) {
            setTimeout(() => {
                if (container === completedEventsElem) {
                    completedPage = (completedPage + 1) % totalPages;
                    renderPagination(completedEvents, completedEventsElem, completedPaginationElem, completedPage, true);
                } else {
                    upcomingPage = (upcomingPage + 1) % totalPages;
                    renderPagination(upcomingEvents, upcomingEventsElem, upcomingPaginationElem, upcomingPage, true);
                }
            }, 5000); // Auto-slide every 5 seconds
        }
    }

    // Fetch stats from the backend
    fetch("../ajax/dashboard_stats.php")
        .then((response) => response.json())
        .then((data) => {
            if (data.error) {
                console.error("Error fetching stats:", data.error);
                return;
            }

            // Update the stats
            totalEventsElem.textContent = data.total_events;
            totalAttendeesElem.textContent = data.total_attendees;
            totalCapacityElem.textContent = data.total_capacity;

            // Store completed and upcoming events
            completedEvents = data.completed_events;
            upcomingEvents = data.upcoming_events;

            // Render initial pages with auto-slide enabled
            renderPagination(completedEvents, completedEventsElem, completedPaginationElem, completedPage, true);
            renderPagination(upcomingEvents, upcomingEventsElem, upcomingPaginationElem, upcomingPage, true);

            // Update Events Today
            eventsTodayElem.innerHTML = data.events_today.length
                ? data.events_today.join(", ")
                : "No events today";
        })
        .catch((error) => console.error("Error fetching dashboard stats:", error));
});


