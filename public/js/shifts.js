document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("generateForm");
    const scheduleSelect = document.getElementById("schedule_select");
    const scheduleInfo = document.getElementById("schedule-info");
    const scheduleRange = document.getElementById("schedule-range");
    const scheduleType = document.getElementById("schedule-type");
    const zoneSelect = document.getElementById("zone_select");
    const verProgramacionBtn = document.getElementById("verProgramacionBtn");
    const scheduleAlert = document.getElementById("schedule-alert");

    zoneSelect.addEventListener("change", function () {
        const selectedOption = zoneSelect.options[zoneSelect.selectedIndex];
        const range = selectedOption.getAttribute("data-range");
        const type = selectedOption.getAttribute("data-type");

        if (range && range !== "Sin definir" && type) {
            scheduleRange.textContent = range;
            scheduleType.textContent = type;
            scheduleInfo.style.display = "block";
            verProgramacionBtn.style.display = "inline-block";
            scheduleAlert.classList.add("d-none");
        } else {
            scheduleInfo.style.display = "none";
            verProgramacionBtn.style.display = "none";
            scheduleAlert.classList.remove("d-none");
        }
    });

    form.addEventListener("submit", function (e) {
        let valid = true;

        toastr.options = {
            showMethod: "show",
            hideMethod: "hide",
            showDuration: 250,
            hideDuration: 800,
            timeOut: 5000,
        };

        scheduleSelect.classList.remove("is-invalid");
        zoneSelect.classList.remove("is-invalid");

        if (!scheduleSelect.value) {
            scheduleSelect.classList.add("is-invalid");
            valid = false;
        }

        if (!zoneSelect.value) {
            zoneSelect.classList.add("is-invalid");
            valid = false;
        }

        if (!valid) {
            e.preventDefault();
            toastr.error("Oops, Por favor, complete todos los campos");
        }
    });

    let calendarEl = document.getElementById("calendar");

    let calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: "dayGridMonth",
        locale: "es",
        height: "auto",
        selectable: true,
        editable: true,
        events: "/eventos",
        dateClick: function (info) {
            alert("Fecha seleccionada: " + info.dateStr);
        },
        eventClick: function (info) {
            alert("Evento: " + info.event.title);
        },
    });

    calendar.render();
});
