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

document.addEventListener("DOMContentLoaded", function () {
    const zonasList = document.getElementById("zonasList");

    document.querySelectorAll(".btn-details").forEach((button) => {
        button.addEventListener("click", function () {
            const zonasList = document.getElementById("zonasList");
            const zonaNamesJSON = this.getAttribute("data-zone-names");
            const zonaNames = JSON.parse(zonaNamesJSON);

            zonasList.innerHTML = "";

            zonaNames.sort().forEach((nombre) => {
                zonasList.innerHTML += `<li class="list-group-item">${nombre}</li>`;
            });
        });
    });
});
$(document).ready(function () {
    $("#zones").select2({
        placeholder: "Selecciona las zonas",
        allowClear: true,
        width: "100%",
    });

    $("#formEdit").on("submit", function (e) {
        e.preventDefault();

        let isValid = true;
        const nombre = $("#name");
        const zonas = $("#zones");

        nombre.removeClass("is-invalid");
        zonas
            .next(".select2")
            .find(".select2-selection")
            .removeClass("is-invalid");

        if (nombre.val().trim() === "") {
            nombre.addClass("is-invalid");
            isValid = false;
        }

        if (!zonas.val() || zonas.val().length === 0) {
            zonas
                .next(".select2")
                .find(".select2-selection")
                .addClass("is-invalid");
            isValid = false;
        }

        if (!isValid) {
            toastr.error(
                "Por favor completa todos los campos obligatorios.",
                "Advertencia"
            );
            return;
        }

        this.submit();
    });

    $("#name").on("input", function () {
        $(this).removeClass("is-invalid");
    });

    $("#zones").on("change", function () {
        $(this)
            .next(".select2")
            .find(".select2-selection")
            .removeClass("is-invalid");
    });
});
