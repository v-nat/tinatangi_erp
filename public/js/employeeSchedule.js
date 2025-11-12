const getInitialView = () => (window.innerWidth < 768 ? "listWeek" : "dayGridMonth");

document.addEventListener("DOMContentLoaded", () => {
    const calendarEl = document.getElementById("employeeScheduleCalendar");
    if (!calendarEl) {
        return;
    }

    const eventsUrl = calendarEl.dataset.eventsUrl;
    const emptyStateEl = document.getElementById("scheduleEmptyState");
    const loadingOverlay = document.getElementById("LoadingScreen");

    const calendar = new FullCalendar.Calendar(calendarEl, {
        height: "auto",
        initialView: getInitialView(),
        themeSystem: "bootstrap5",
        headerToolbar: {
            start: "prev,next today",
            center: "title",
            end: "dayGridMonth,timeGridWeek,timeGridDay,listWeek",
        },
        buttonText: {
            today: "Today",
            month: "Month",
            week: "Week",
            day: "Day",
            list: "Agenda",
        },
        navLinks: true,
        nowIndicator: true,
        dayMaxEvents: true,
        slotMinTime: "05:00:00",
        slotMaxTime: "23:00:00",
        eventTimeFormat: {
            hour: "numeric",
            minute: "2-digit",
            meridiem: "short",
        },
        events: {
            url: eventsUrl,
            failure() {
                if (typeof Toast !== "undefined") {
                    Toast.fire("Failed to load schedule", "Please try again later.", "error");
                } else {
                    Swal.fire("Failed to load schedule", "Please try again later.", "error");
                }
            },
        },
        eventDidMount(info) {
            const { description, timeLabel } = info.event.extendedProps || {};
            const details = [timeLabel, description].filter(Boolean).join("\n");
            if (details) {
                info.el.setAttribute("title", details);
            }
        },
        eventsSet(events) {
            if (!emptyStateEl) return;

            if (!events.length) {
                emptyStateEl.classList.remove("d-none");
            } else {
                emptyStateEl.classList.add("d-none");
            }
        },
        loading(isLoading) {
            if (!loadingOverlay) return;
            if (isLoading) {
                $(loadingOverlay).stop(true, true).fadeIn(150);
            } else {
                $(loadingOverlay).stop(true, true).fadeOut(150);
            }
        },
    });

    calendar.render();

    window.addEventListener("resize", () => {
        const newView = getInitialView();
        if (calendar.view.type !== newView) {
            calendar.changeView(newView);
        }
    });
});

