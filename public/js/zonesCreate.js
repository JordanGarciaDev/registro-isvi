$(document).ready(function () {
    $("#schedule").select2({
        placeholder: "Tipo de programación",
        allowClear: true,
    });

    let maxSelection = 0;

    $("#personal_worker").select2({
        placeholder: "Agregar personal",
        allowClear: true,
    });

    $("#n_workers").on("change", function () {
        maxSelection = parseInt($(this).val()); 
        $("#personal_worker").val(null).trigger("change"); 
    });

    $("#personal_worker").on("change", function () {
        const selected = $(this).val() || [];

        if (selected.length > maxSelection) {
            selected.pop();
            $(this).val(selected).trigger("change");

            toastr.options = {
                showMethod: "show",
                hideMethod: "hide",
                showDuration: 250,
                hideDuration: 800,
                timeOut: 5000,
            };

            toastr.error(
                `Oops, solo puedes seleccionar ${maxSelection} trabajador(es).`
            );
        }
    });
});

const salaryInput = document.getElementById("salary");

salaryInput.addEventListener("input", function () {
    let valor = this.value.replace(/\D/g, "");
    valor = new Intl.NumberFormat("es-CO").format(valor);
    this.value = valor;
});

document
    .getElementById("photoInput")
    .addEventListener("change", function (event) {
        const previewImage = document.getElementById("previewImage");
        const clearPhoto = document.getElementById("clearPhoto");
        const defaultImage = "{{ asset('img/zone.png') }}";

        toastr.options = {
            showMethod: "show",
            hideMethod: "hide",
            showDuration: 250,
            hideDuration: 800,
            timeOut: 5000,
        };

        const file = event.target.files[0];

        if (file) {
            const allowedExtensions = [
                "image/jpeg",
                "image/jpg",
                "image/png",
                "image/webp",
            ];

            if (!allowedExtensions.includes(file.type)) {
                toastr.error(
                    "Solo se permiten archivos en formato JPG, JPEG, PNG o WEBP"
                );
                event.target.value = "";
                previewImage.src = defaultImage;
                clearPhoto.style.display = "none";
                return;
            }

            const reader = new FileReader();
            reader.onload = function (e) {
                previewImage.src = e.target.result;
                clearPhoto.style.display = "block";
                toastr.success("La imagen ha sido cargada");
            };
            reader.readAsDataURL(file);
        }
    });

document.getElementById("clearPhoto").addEventListener("click", function () {
    const previewImage = document.getElementById("previewImage");
    const photoInput = document.getElementById("photoInput");
    const clearPhoto = document.getElementById("clearPhoto");

    if (typeof defaultImage !== "undefined") {
        previewImage.src = defaultImage;
    } else {
        console.error("defaultImage no está definido");
    }

    photoInput.value = "";
    clearPhoto.style.display = "none";

    toastr.info("La imagen ha sido limpiada");
});

document.getElementById("description").addEventListener("input", function () {
    let max = this.getAttribute("maxlength");
    let actual = this.value.length;
    document.getElementById(
        "contador"
    ).innerText = `${actual}/${max} caracteres`;
});

document.addEventListener("DOMContentLoaded", function () {
    toastr.options = {
        showMethod: "show",
        hideMethod: "hide",
        showDuration: 250,
        hideDuration: 800,
        timeOut: 5000,
    };

    const form = document.getElementById("form-create");

    form.addEventListener("submit", function (event) {
        event.preventDefault();

        let isValid = true;
        let isValidPhoto = true;
        const fields = document.querySelectorAll("input, select");

        fields.forEach((field) => {
            if (field.hasAttribute("name") && field.type !== "file") {
                if (field.value.trim() === "") {
                    field.classList.add("is-invalid");
                    isValid = false;
                } else {
                    field.classList.remove("is-invalid");
                }
            }

            if (field.tagName === "SELECT") {
                const selectValue = $(field).val();
                if (!selectValue) {
                    $(field).addClass("is-invalid");
                    isValid = false;
                } else {
                    $(field).removeClass("is-invalid");
                }
            }

            if (field.type === "file" && field.value === "") {
                isValidPhoto = false;
            }
        });

        if (!isValidPhoto) {
            toastr.error(
                "La foto de la zona es requerida, Por favor, cargue una foto valida."
            );
            return;
        }

        if (!isValid) {
            toastr.error(
                "Por favor, complete toda la información requerida del formulario."
            );
            return;
        }

        Swal.fire({
            title: "¿Estás seguro?",
            text: "El registro no podrá ser editado despues.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#007bff",
            cancelButtonColor: "#e2eaf7",
            confirmButtonText: "Sí, Registrar",
            cancelButtonText: "Cancelar",
            customClass: {
                confirmButton: "btn btn-danger btn-rounded",
                cancelButton: "btn btn-secondary btn-rounded shadow",
            },
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            } else {
                return;
            }
        });
    });
});
