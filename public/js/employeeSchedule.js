const getInitialView = () =>
    window.innerWidth < 768 ? "listWeek" : "dayGridMonth";

$(function () {
    const $calendarEl = $("#employeeScheduleCalendar");
    if (!$calendarEl.length) return;

    const eventsUrl = $calendarEl.data("eventsUrl");
    const $emptyState = $("#scheduleEmptyState");
    const $loadingOverlay = $("#LoadingScreen");

    const calendar = new FullCalendar.Calendar($calendarEl[0], {
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
                    Toast.fire(
                        "Failed to load schedule",
                        "Please try again later.",
                        "error"
                    );
                } else {
                    Swal.fire(
                        "Failed to load schedule",
                        "Please try again later.",
                        "error"
                    );
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
            if (!$emptyState.length) return;

            if (!events.length) {
                $emptyState.removeClass("d-none");
            } else {
                $emptyState.addClass("d-none");
            }
        },
        loading(isLoading) {
            if (!$loadingOverlay.length) return;
            if (isLoading) {
                $loadingOverlay.stop(true, true).fadeIn(150);
            } else {
                $loadingOverlay.stop(true, true).fadeOut(150);
            }
        },
    });

    calendar.render();

    $(window).on("resize", () => {
        const newView = getInitialView();
        if (calendar.view.type !== newView) {
            calendar.changeView(newView);
        }
    });
});
