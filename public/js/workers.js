$(document).ready(function () {
    $("#cost_center").select2({
        placeholder: "Centro de costos",
        allowClear: true,
    });
    $("#bonding").select2({
        placeholder: "Tipo de Vinculación",
        allowClear: true,
    });
    $("#region").select2({
        placeholder: "Región o Sucursal",
        allowClear: true,
    });
    $("#zona").select2({
        placeholder: "Zonas o Puestos",
        allowClear: true,
    });
    $("#region").on("change", function () {
        var regionId = $(this).val();

        if (regionId) {
            $.ajax({
                url: "/get-zones-by-region/" + regionId,
                type: "GET",
                success: function (data) {
                    var $zona = $("#zona");
                    $zona.empty(); 

                    $zona.append(
                        "<option disabled selected>Zonas o puestos</option>"
                    );

                    data.forEach(function (zone) {
                        $zona.append(
                            '<option value="' +
                                zone.id +
                                '">' +
                                zone.name +
                                "</option>"
                        );
                    });

                    // No seleccionamos nada por defecto
                    $zona.val(null).trigger("change");
                },
                error: function () {
                    alert("Error al cargar las zonas.");
                },
            });
        }
    });
});

document
    .getElementById("photoInput")
    .addEventListener("change", function (event) {
        const previewImage = document.getElementById("previewImage");
        const clearPhoto = document.getElementById("clearPhoto");
        const defaultImage = "{{ asset('img/user.png') }}";

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
            if (field.type === "file" && field.value === "") {
                isValidPhoto = false;
            }
        });

        if (!isValidPhoto) {
            toastr.error(
                "La foto del personal es requerida, Por favor, cargue una foto valida."
            );
            return;
        }

        if (!isValid) {
            toastr.error(
                "Por favor, complete toda la información requerida del formulario."
            );
            return;
        }

        form.submit();
    });
});
