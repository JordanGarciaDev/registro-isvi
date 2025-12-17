document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("changeScheduleForm");
    const zoneIdInput = document.getElementById("zoneIdInput");
    const selectedScheduleInput = document.getElementById("selectedScheduleId");
    const dropdownButton = document.getElementById("dropdownMenuButton");

    document.querySelectorAll(".change-schedule-btn").forEach((button) => {
        button.addEventListener("click", function () {
            const zoneId = this.getAttribute("data-id");
            const current = this.getAttribute("data-current");
            const currentId = this.getAttribute("data-current-id");

            zoneIdInput.value = zoneId;
            form.action = `/ubicaciones/zonas/${zoneId}`;

            selectedScheduleInput.value = "";
            dropdownButton.textContent = "Selecciona una programación";

            if (currentId) {
                document
                    .querySelectorAll(".schedule-option")
                    .forEach((item) => {
                        if (item.dataset.id === currentId) {
                            const name = item.dataset.name;
                            const daySince = item.querySelector(
                                "strong:nth-of-type(1)"
                            ).textContent;
                            const dayUntil = item.querySelector(
                                "strong:nth-of-type(2)"
                            ).textContent;
                            const valText =
                                item.querySelector(".text-success").textContent;

                            selectedScheduleInput.value = currentId;
                            dropdownButton.textContent = `${name} - ${daySince} a ${dayUntil} (${valText})`;
                        }
                    });
            }
        });
    });

    document
        .getElementById("changeScheduleForm")
        .addEventListener("submit", function (e) {
            const selectedSchedule =
                document.getElementById("selectedScheduleId").value;

            if (!selectedSchedule) {
                e.preventDefault();

                toastr.options = {
                    showMethod: "show",
                    hideMethod: "hide",
                    showDuration: 250,
                    hideDuration: 800,
                    timeOut: 5000,
                };

                toastr.error(
                    "Oops, la selección de el tipo de programación es obligatoria."
                );

                return false;
            }
        });

    document.querySelectorAll(".schedule-option").forEach((item) => {
        item.addEventListener("click", function (e) {
            e.preventDefault();

            const scheduleId = this.dataset.id;
            const name = this.dataset.name;
            const daySince = this.querySelector(
                "strong:nth-of-type(1)"
            ).textContent;
            const dayUntil = this.querySelector(
                "strong:nth-of-type(2)"
            ).textContent;
            const valText = this.querySelector(".text-success").textContent;

            selectedScheduleInput.value = scheduleId;
            dropdownButton.textContent = `${name} - ${daySince} a ${dayUntil} (${valText})`;
        });
    });

    $("#zoneDetailsModal").on("hidden.bs.modal", function () {
        $("body").removeClass("modal-open");
        $(".modal-backdrop").remove();
        $("body").css("overflow", "");
    });

    document.querySelectorAll(".btn-details").forEach((button) => {
        button.addEventListener("click", function () {
            $('zoneDetailsModal').modal('show');

            const zoneId = this.getAttribute("data-id");
            const zoneIdCustomer =
                this.getAttribute("data-id_customer") || "No disponible";
            const zoneName = this.getAttribute("data-name") || "Sin nombre";
            const zoneSchedule =
                this.getAttribute("data-schedule") || "No disponible";
            const zoneAddress =
                this.getAttribute("data-address") || "No disponible";
            const zonePhone =
                this.getAttribute("data-phone") || "No disponible";
            const zoneEmail =
                this.getAttribute("data-email") || "No disponible";
            const zoneRegion = this.getAttribute("data-region") || "Ninguna";
            const zoneDescriptions =
                this.getAttribute("data-descriptions") || "Ninguna";
            const zoneImage =
                this.getAttribute("data-image") ||
                "{{ asset('img/zone.png') }}";
            const zoneCreated = this.getAttribute("data-created") || "N/A";

            const zoneStatus =
                this.getAttribute("data-status") === "1"
                    ? "Activo"
                    : "Inactivo";
            const statusElement = document.getElementById("zoneStatus");
            if (statusElement) {
                statusElement.className =
                    "badge rounded-pill " +
                    (zoneStatus === "Activo"
                        ? "badge-success"
                        : "badge-danger");
                statusElement.textContent = zoneStatus;
            }
            const zoneUser = this.getAttribute("data-user") || "No disponible";
            const zoneSalary = this.getAttribute("data-salary") || "Ninguno";

            document.getElementById("zoneIdCustomer").textContent =
                zoneIdCustomer;
            document.getElementById("zoneName").textContent = zoneName;
            document.getElementById("zoneSchedule").textContent = zoneSchedule;
            document.getElementById("zoneAddress").textContent = zoneAddress;
            document.getElementById("zonePhone").textContent = zonePhone;
            document.getElementById("zoneEmail").textContent = zoneEmail;
            document.getElementById("zoneRegion").textContent = zoneRegion;
            document.getElementById("zoneDescriptions").textContent =
                zoneDescriptions;
            document.getElementById("zoneImage").src = zoneImage;
            document.getElementById("zoneCreated").textContent = zoneCreated;
            document.getElementById("zoneUser").textContent = zoneUser;
            document.getElementById("zoneSalary").textContent = zoneSalary;

            const btnInactivar = document.querySelector(
                ".btn-danger.delete-btn"
            );
            const btnActivar = document.querySelector(
                ".btn-primary.delete-btn"
            );

            if (this.getAttribute("data-status") === "1") {
                btnInactivar.style.display = "inline-block";
                btnActivar.style.display = "none";
            } else {
                btnInactivar.style.display = "none";
                btnActivar.style.display = "inline-block";
            }

            btnInactivar.onclick = () => confirmDestroy(zoneId, "inactivar");
            btnActivar.onclick = () => confirmDestroy(zoneId, "activar");
        });
    });

    function confirmDestroy(zoneId, action) {
        const actionText = action === "inactivar" ? "inactivar" : "activar";

        Swal.fire({
            title: `¿Estás seguro de ${actionText} esta zona?`,
            text: "Esta acción puede ser reversible.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: action === "inactivar" ? "#d33" : "#3085d6",
            confirmButtonColor: "#d33",
            cancelButtonColor: "#e2eaf7",
            confirmButtonText: `Sí, ${actionText}`,
            cancelButtonText: "Cancelar",
            customClass: {
                confirmButton: "btn btn-danger btn-rounded",
                cancelButton: "btn btn-secondary btn-rounded shadow",
            },
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement("form");
                form.method = "POST";
                form.action = `/ubicaciones/zonas/${zoneId}`;

                const csrfInput = document.createElement("input");
                csrfInput.type = "hidden";
                csrfInput.name = "_token";
                csrfInput.value = document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content");

                const methodInput = document.createElement("input");
                methodInput.type = "hidden";
                methodInput.name = "_method";
                methodInput.value = "DELETE";

                form.appendChild(csrfInput);
                form.appendChild(methodInput);
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
});

function confirmInactive(event, userId) {
    event.preventDefault();

    Swal.fire({
        title: "¿Estás seguro?",
        text: "Esta acción no se puede deshacer.",
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
            document.getElementById("inactiveForm-" + userId).submit();
        }
    });
}

function confirmActive(event, userId) {
    event.preventDefault();

    Swal.fire({
        title: "¿Estás seguro?",
        text: "Esta acción no se puede deshacer.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#e2eaf7",
        confirmButtonText: "Sí, Activar",
        cancelButtonText: "Cancelar",
        customClass: {
            confirmButton: "btn btn-danger btn-rounded",
            cancelButton: "btn btn-secondary btn-rounded shadow",
        },
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById("activeForm-" + userId).submit();
        }
    });
}
