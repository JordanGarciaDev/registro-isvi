function confirmDelete(event, userId) {
    event.preventDefault(); // Evita el envío inmediato del formulario

    Swal.fire({
        title: "¿Estás seguro?",
        text: "Esta acción no se puede deshacer.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#e2eaf7",
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar",
        customClass: {
            confirmButton: "btn btn-danger btn-rounded",
            cancelButton: "btn btn-secondary btn-rounded shadow",
        },
    }).then((result) => {
        if (result.isConfirmed) {
            // Envía el formulario correspondiente al usuario
            document.getElementById("deleteForm-" + userId).submit();
        }
    });
}

$(document).ready(function () {
    $("#fechaNacimiento").datepicker({
        format: "yyyy-mm-dd",
        autoclose: true,
        todayHighlight: true,
        endDate: new Date(),
        language: "es",
    });

    $("#edit_birthdate").datepicker({
        format: "yyyy-mm-dd",
        autoclose: true,
        todayHighlight: true,
        endDate: new Date(),
        language: "es",
    });

    function validarCampo(id) {
        let campo = $("#" + id);
        let mensajeError = "";
        let valor = campo.is("select") ? campo.val() : campo.val().trim();
        let formGroup = campo.closest(".mb-3, .form-outline");
        let contenedorError = formGroup.next(".error-message");

        if (valor === "" || valor === null) {
            mensajeError = "Este campo es obligatorio.";
        } else if (
            campo.attr("type") === "email" &&
            !/^\S+@\S+\.\S+$/.test(valor)
        ) {
            mensajeError = "Ingrese un correo válido.";
        } else if (campo.attr("type") === "number" && isNaN(valor)) {
            mensajeError = "Ingrese un número válido.";
        } else if (campo.attr("id") === "phone" && !/^\d{10}$/.test(valor)) {
            mensajeError = "Ingrese un número de celular válido (10 dígitos).";
        } else if (
            campo.attr("id") === "verify_email" &&
            valor !== $("#email").val().trim()
        ) {
            mensajeError = "Los correos no coinciden.";
        }

        if (contenedorError.length) {
            contenedorError.remove();
        }

        if (mensajeError !== "") {
            campo.addClass("is-invalid");
            formGroup.after(
                `<div class="text-danger small error-message" style="margin-top: -5px; margin-bottom: 8px;">${mensajeError}</div>`
            );
        } else {
            campo.removeClass("is-invalid");
        }
    }

    $("input, select").on("input change", function () {
        validarCampo($(this).attr("id"));
    });

    $("#form-modal").on("submit", function (e) {
        let campos = [
            "names",
            "document",
            "fechaNacimiento",
            "genero",
            "phone",
            "email",
            "verify_email",
            "password",
            "role",
        ];
        let esValido = true;

        campos.forEach(function (campo) {
            validarCampo(campo);
            if ($("#" + campo).hasClass("is-invalid")) {
                esValido = false;
            }
        });

        if (!esValido) {
            e.preventDefault();
        }
    });

    // EDITAR
    $(".btn-edit").click(function () {
        let id = $(this).data("id");

        $("#edit-form-modal").attr("action", "/usuarios/" + id);

        $("#edit_user_id").val(id);
        $("#edit_names").val($(this).data("name")).trigger("change");
        $("#edit_document").val($(this).data("document")).trigger("change");
        $("#edit_birthdate").val($(this).data("birthdate")).trigger("change");
        $("#edit_gender").val($(this).data("gender")).trigger("change");
        $("#edit_phone").val($(this).data("phone")).trigger("change");
        $("#edit_email").val($(this).data("email")).trigger("change");

        let role = $(this).data("role");
        $("#edit_role").val(role).change();

        $("#editUserModal .form-outline").each(function () {
            new mdb.Input(this).update();
        });

        $("#editUserModal").modal("show");
    });

    $("#editUserModal").on("hidden.bs.modal", function () {
        $("#edit-form-modal")[0].reset();
        $("#editUserModal .form-outline label").removeClass("active");
    });

    $("#editUserModal").on("hidden.bs.modal", function () {
        $("body").removeClass("modal-open");
        $(".modal-backdrop").remove();
        $("body").css("overflow", "auto");
    });

    $("#edit-form-modal").on("submit", function (e) {
        e.preventDefault();

        let campos = [
            "edit_names",
            "edit_document",
            "edit_birthdate",
            "edit_gender",
            "edit_phone",
            "edit_email",
            "edit_password",
            "edit_role",
        ];

        let esValido = true;

        campos.forEach(function (campo) {
            validarCampo(campo);
            if ($("#" + campo).hasClass("is-invalid")) {
                esValido = false;
            }
        });

        if (esValido) {
            this.submit();
        }
    });
});
