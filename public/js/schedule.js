document.addEventListener("DOMContentLoaded", function () {
    toastr.options = {
        showMethod: "show",
        hideMethod: "hide",
        showDuration: 250,
        hideDuration: 800,
        timeOut: 5000,
    };

    function validarCampos(campos, prefix = "") {
        let isValid = true;
        campos.forEach((campo) => {
            let input = document.getElementById(
                prefix ? `${campo}-${prefix}` : campo
            );
            if (input && input.value.trim() === "") {
                input.classList.add("is-invalid");
                isValid = false;
            } else if (input) {
                input.classList.remove("is-invalid");
            }
        });
        return isValid;
    }

    function manejarValidacion(event, form, prefix = "") {

        let isValid = true;

        let campos = [
            "name",
            "schedule_type",
            "fechaDesde",
            "fechaHasta",
            "fechaHasta",
            "day_hour_since",
            "day_hour_until",
            "night_hour_since",
            "night_hour_until",
            "break_hour_since",
            "break_hour_until",
        ];

        if (!validarCampos(campos, prefix)) {
            isValid = false;
            toastr.error(
                "Oops, todos los campos son obligatorios, Por favor, completalos."
            );
        }

        return isValid;
    }

    // Validación para el formulario de CREAR
    let saveBtn = document.getElementById("saveBtn");

    if (saveBtn) {
        saveBtn.addEventListener("click", function (event) {
            event.preventDefault();
            let isValid = manejarValidacion(
                event,
                document.getElementById("form-modal")
            );

            if (isValid) {
                document.getElementById("form-modal").submit();
            }
        });
    }
});

function openModal(scheduleId) {
    let modalElement = document.getElementById(
        "editScheduleModal-" + scheduleId
    );
    let modal = new bootstrap.Modal(modalElement);
    let form = document.getElementById("editScheduleForm-" + scheduleId);
    let updateBtn = form.querySelector("#updateBtn");

    function validarCampos(campos, prefix = "") {
        let isValid = true;
        campos.forEach((campo) => {
            let input = document.getElementById(
                prefix ? `${campo}-${prefix}` : campo
            );
            if (input && input.value.trim() === "") {
                input.classList.add("is-invalid");
                isValid = false;
            } else if (input) {
                input.classList.remove("is-invalid");
            }
        });
        return isValid;
    }

    function manejarValidacion(event, form, prefix = "") {
        
        let isValid = true;

        let campos = [
            "name",
            "schedule_type",
            "fechaDesde",
            "fechaHasta",
            "day_hour_since",
            "day_hour_until",
            "night_hour_since",
            "night_hour_until",
            "break_hour_since",
            "break_hour_until",
        ];

        if (!validarCampos(campos, prefix)) {
            isValid = false;
            toastr.error(
                "Oops, todos los campos son obligatorios. Por favor, complétalos."
            );
        }

        return isValid;
    }

    if (updateBtn && !updateBtn.dataset.listenerAdded) {
        updateBtn.addEventListener("click", function (event) {
            event.preventDefault();
            let isValid = manejarValidacion(event, form, scheduleId);
            if (isValid) {
                form.submit();
            }
        });

        updateBtn.dataset.listenerAdded = true;
    }

    modal.show();
}

function confirmDelete(event, userId) {
    event.preventDefault();

    Swal.fire({
        title: "¿Estás seguro?",
        text: "Esta acción convella consecuencias en los turnos.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#e2eaf7",
        confirmButtonText: "Sí, inactivar",
        cancelButtonText: "Cancelar",
        customClass: {
            confirmButton: "btn btn-danger btn-rounded",
            cancelButton: "btn btn-secondary btn-rounded shadow",
        },
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById("deleteForm-" + userId).submit();
        }
    });
}

function confirmActivate(event, userId) {
    event.preventDefault();

    Swal.fire({
        title: "¿Quieres activar esta programación?",
        text: "Esto permitirá que se reactiven los turnos asociados.",
        icon: "success",
        showCancelButton: true,
        confirmButtonColor: "#28a745",
        cancelButtonColor: "#e2eaf7",
        confirmButtonText: "Sí, activar",
        cancelButtonText: "Cancelar",
        customClass: {
            confirmButton: "btn btn-success btn-rounded",
            cancelButton: "btn btn-secondary btn-rounded shadow",
        },
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById("activateForm-" + userId).submit();
        }
    });
}

$(document).ready(function () {
    $(
        "#fechaDesde, #fechaHasta, [id^='fechaDesde-'], [id^='fechaHasta-']"
    ).datepicker({
        format: "yyyy-mm-dd",
        autoclose: true,
        todayHighlight: true,
        language: "es",
    });

    function initTimeDropper(selector, setCurrentTime = true) {
        $(selector).each(function () {
            const $input = $(this);

            if (!$input.length) return;

            if (typeof $input.timeDropper !== "function") {
                console.error("timeDropper no está disponible para", selector);
                return;
            }

            try {
                if ($input.data("timeDropper")) {
                    $input.timeDropper("destroy");
                }
            } catch (e) {
                console.warn("No se pudo destruir timeDropper:", e);
            }

            $input.timeDropper({
                meridians: false,
                format: "HH:mm",
                setCurrentTime: setCurrentTime,
            });
        });
    }

    const timeFields = [
        { selector: "#horaDesdeDiurno", setCurrentTime: false },
        { selector: '[id^="horaDesdeDiurno-"]', setCurrentTime: false },
        { selector: "#horaHastaDiurno", setCurrentTime: false },
        { selector: '[id^="horaHastaDiurno-"]', setCurrentTime: false },
        { selector: "#horaDesdeNocturno", setCurrentTime: false },
        { selector: '[id^="horaDesdeNocturno-"]', setCurrentTime: false },
        { selector: "#horaHastaNocturno", setCurrentTime: false },
        { selector: '[id^="horaHastaNocturno-"]', setCurrentTime: false },
        { selector: "#horaDesdeDescanso", setCurrentTime: false },
        { selector: '[id^="horaDesdeDescanso-"]', setCurrentTime: false },
        { selector: "#horaHastaDescanso", setCurrentTime: false },
        { selector: '[id^="horaHastaDescanso-"]', setCurrentTime: false },
    ];

    // Abrir los modales
    $("#newSchedule").on("shown.bs.modal", function () {
        timeFields.forEach((field) => {
            initTimeDropper(field.selector, field.setCurrentTime);
        });
    });

    $("[id^='editScheduleModal']").on("shown.bs.modal", function () {
        timeFields.forEach((field) => {
            initTimeDropper(field.selector, field.setCurrentTime);
        });
    });
});
