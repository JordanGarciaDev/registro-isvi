function confirmInactivation(workerId) {
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
            document.getElementById("inactivateForm-" + workerId).submit();
        }
    });
}

function confirmActivation(workerId) {
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
            document.getElementById("activateForm-" + workerId).submit();
        }
    });
}

document.addEventListener("DOMContentLoaded", function () {
    var viewModal = document.getElementById("viewWorkerModal");
    const lastUpdatedSpan = document.getElementById("lastUpdated");

    document.querySelectorAll(".btn-details").forEach((button) => {
        button.addEventListener("click", function () {
            const zonasList = document.getElementById("zonasList");
            const zonaNamesJSON = this.getAttribute("data-zone-names");
            const zonaNames = JSON.parse(zonaNamesJSON);
            const updatedAt = this.getAttribute("data-updated-at");

            zonasList.innerHTML = "";
            zonaNames.sort().forEach((nombre) => {
                zonasList.innerHTML += `<li class="list-group-item">${nombre}</li>`;
            });

            lastUpdatedSpan.textContent = updatedAt;
        });
    });

    toastr.options = {
        showMethod: "show",
        hideMethod: "hide",
        showDuration: 250,
        hideDuration: 800,
        timeOut: 5000,
    };

    const editBtn = document.getElementById("editWorkerBtn");

    if (editBtn) {
        editBtn.addEventListener("click", function () {
            let viewModal = document.getElementById("viewWorkerModal");
            let workerId = viewModal.getAttribute("data-worker-id");

            if (workerId) {
                window.location.href = `/personal/${workerId}/edit`;
            } else {
                toastr.info(
                    "Oops, el id del personal no ha sido encontrado. Por favor, vuelve a intentar."
                );
            }
        });
    }

    if (viewModal) {
        viewModal.addEventListener("show.bs.modal", function (event) {
            var button = event.relatedTarget;

            // Obtener los atributos data del botón
            var name = button.getAttribute("data-name") || "No disponible";
            let rawPhoto = button.getAttribute("data-photo") || "";
            let photo =
                rawPhoto.trim().endsWith("/storage") || rawPhoto.trim() === ""
                    ? "img/user.png"
                    : rawPhoto;

            var documentId =
                "C.C " + button.getAttribute("data-document") ||
                "No disponible";
            var phone = button.getAttribute("data-phone") || "No disponible";
            var type = button.getAttribute("data-type") || "No disponible";
            var costCenter =
                button.getAttribute("data-cost-center") || "No disponible";
            var bonding =
                button.getAttribute("data-bonding") || "No disponible";
            var cargo = button.getAttribute("data-cargo") || "No disponible";
            var created =
                button.getAttribute("data-created") || "No disponible";
            var status = button.getAttribute("data-status") || "No disponible";
            var workerIdd =
                button.getAttribute("data-worker-id") || "No disponible";
            var area = button.getAttribute("data-area") || "No disponible";
            var proyecto =
                button.getAttribute("data-proyecto") || "No disponible";
            var email =
                button.getAttribute("data-email") || "No disponible";

            viewModal.setAttribute("data-worker-id", workerIdd);

            var workerName = document.getElementById("workerName");
            if (workerName) workerName.textContent = name;

            var workerPhoto = document.getElementById("workerPhoto");

            if (workerPhoto) {
                if (!photo || photo.trim() === "") {
                    workerPhoto.src = "img/user.png";
                } else {
                    var imgTest = new Image();
                    imgTest.onload = function () {
                        workerPhoto.src = photo;
                    };
                    imgTest.onerror = function () {
                        workerPhoto.src = "img/user.png";
                    };
                    imgTest.src = photo;
                }
            }

            var workerDocument = document.getElementById("workerDocument");
            if (workerDocument) workerDocument.textContent = documentId;

            var workerPhone = document.getElementById("workerPhone");
            if (workerPhone) workerPhone.textContent = phone;

            var workerType = document.getElementById("workerType");
            if (workerType) workerType.textContent = type;

            var workerCostCenter = document.getElementById("workerCostCenter");
            if (workerCostCenter) workerCostCenter.textContent = costCenter;

            var workerBonding = document.getElementById("workerBonding");
            if (workerBonding) workerBonding.textContent = bonding;

            var workerCargo = document.getElementById("workerCargo");
            if (workerCargo) workerCargo.textContent = cargo;

            var workerArea = document.getElementById("workerArea");
            if (workerArea) workerArea.textContent = area;

            var workerProyecto = document.getElementById("workerProyecto");
            if (workerProyecto) workerProyecto.textContent = proyecto;

            var workerEmail = document.getElementById("workerEmail");
            if (workerEmail) workerEmail.textContent = email;

            var workerCreated = document.getElementById("workerCreated");
            if (workerCreated) workerCreated.textContent = created;

            var workerStatus = document.getElementById("workerStatus");
            if (workerStatus) {
                workerStatus.textContent = status;
                workerStatus.classList.remove("badge-success", "badge-danger");

                if (status === "Activo") {
                    workerStatus.classList.add("badge-success");
                } else if (status === "Inactivo") {
                    workerStatus.classList.add("badge-danger");
                }
            }

            let activateBtn = document.getElementById("activateWorkerBtn");
            let inactivateBtn = document.getElementById("inactivateWorkerBtn");

            viewModal.setAttribute("data-worker-id", workerIdd);

            if (status == "Inactivo") {
                activateBtn.classList.remove("d-none");
                inactivateBtn.classList.add("d-none");
            } else {
                activateBtn.classList.add("d-none");
                inactivateBtn.classList.remove("d-none");
            }
        });

        viewModal.addEventListener("hidden.bs.modal", function () {
            document
                .getElementById("activateWorkerBtn")
                .classList.add("d-none");
            document
                .getElementById("inactivateWorkerBtn")
                .classList.add("d-none");
        });

        document
            .getElementById("inactivateWorkerBtn")
            .addEventListener("click", function () {
                let workerId = viewModal.getAttribute("data-worker-id");

                if (!workerId) {
                    Swal.fire(
                        "Error",
                        "No se pudo obtener el ID del trabajador",
                        "error"
                    );
                    return;
                }

                Swal.fire({
                    title: "¿Estás seguro?",
                    text: "Esta acción no se puede deshacer.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#e2eaf7",
                    confirmButtonText: "Sí, Inactivar",
                    cancelButtonText: "Cancelar",
                    customClass: {
                        confirmButton: "btn btn-danger btn-rounded",
                        cancelButton: "btn btn-secondary btn-rounded shadow",
                    },
                }).then((result) => {
                    if (result.isConfirmed) {
                        let csrfToken = document
                            .querySelector('meta[name="csrf-token"]')
                            .getAttribute("content");

                        let form = document.createElement("form");
                        form.action = `/personal/${workerId}`;
                        form.method = "POST";

                        let csrfInput = document.createElement("input");
                        csrfInput.type = "hidden";
                        csrfInput.name = "_token";
                        csrfInput.value = csrfToken;

                        let methodInput = document.createElement("input");
                        methodInput.type = "hidden";
                        methodInput.name = "_method";
                        methodInput.value = "DELETE";

                        form.appendChild(csrfInput);
                        form.appendChild(methodInput);
                        document.body.appendChild(form);

                        form.submit();
                    }
                });
            });

        document
            .getElementById("activateWorkerBtn")
            .addEventListener("click", function () {
                let workerId = viewModal.getAttribute("data-worker-id");

                if (!workerId) {
                    Swal.fire(
                        "Error",
                        "No se pudo obtener el ID del trabajador",
                        "error"
                    );
                    return;
                }

                Swal.fire({
                    title: "¿Estás seguro?",
                    text: "Deseas activar este trabajador.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#28a745",
                    cancelButtonColor: "#e2eaf7",
                    confirmButtonText: "Sí, Activar",
                    cancelButtonText: "Cancelar",
                    customClass: {
                        confirmButton: "btn btn-success btn-rounded",
                        cancelButton: "btn btn-secondary btn-rounded shadow",
                    },
                }).then((result) => {
                    if (result.isConfirmed) {
                        let csrfToken = document
                            .querySelector('meta[name="csrf-token"]')
                            .getAttribute("content");

                        let form = document.createElement("form");
                        form.action = `/personal/${workerId}`;
                        form.method = "POST";

                        let csrfInput = document.createElement("input");
                        csrfInput.type = "hidden";
                        csrfInput.name = "_token";
                        csrfInput.value = csrfToken;

                        let methodInput = document.createElement("input");
                        methodInput.type = "hidden";
                        methodInput.name = "_method";
                        methodInput.value = "DELETE";

                        form.appendChild(csrfInput);
                        form.appendChild(methodInput);
                        document.body.appendChild(form);

                        form.submit();
                    }
                });
            });
    }
});
