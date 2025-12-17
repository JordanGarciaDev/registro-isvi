$(document).ready(function () {
    
    $("#region").on("change", function () {
        var regionId = $(this).val();

        if (regionId) {
            // petición que retorna las zonas de la region seleccionada por el usuario
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

                    $zona.val(null).trigger("change");

                    var selectedZones = [];
                    if ($("#worker_zones").val()) {
                        try {
                            selectedZones = JSON.parse(
                                $("#worker_zones").val()
                            );
                        } catch (e) {
                            console.error(
                                "Error al parsear las zonas seleccionadas:",
                                e
                            );
                        }
                    }

                    if (Array.isArray(selectedZones)) {
                        selectedZones.forEach(function (zoneId) {
                            $zona
                                .find("option[value='" + zoneId + "']")
                                .prop("selected", true);
                        });
                    }

                    $zona.trigger("change");
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

document
    .getElementById("form-edit")
    .addEventListener("submit", function (event) {
        event.preventDefault();

        let isValid = true;
        let firstInvalidField = null;
        let requiredFields = [
            "name",
            "lastname",
            "document",
            "phone",
            "bonding",
            "cost_center",
            "cargo",
        ];

        toastr.options = {
            showMethod: "show",
            hideMethod: "hide",
            showDuration: 250,
            hideDuration: 800,
            timeOut: 5000,
        };

        requiredFields.forEach((field) => {
            let input = document.getElementById(field);
            if (!input.value.trim()) {
                input.classList.add("is-invalid");
                isValid = false;
                if (!firstInvalidField) firstInvalidField = input;
            } else {
                input.classList.remove("is-invalid");
            }
        });

        let photoInput = document.getElementById("photoInput");
        let previewImage = document.getElementById("previewImage");

        if (!photoInput.files.length && previewImage.src.includes("user.png")) {
            isValid = false;
            toastr.error(
                "La foto del personal es requerida, Por favor, cargue una foto valida."
            );
            return;
        }

        if (!isValid) {
            toastr.error(
                "Por favor, complete toda la información requerida del formulario."
            );
            firstInvalidField.focus();
            return;
        }

        this.submit();
    });
