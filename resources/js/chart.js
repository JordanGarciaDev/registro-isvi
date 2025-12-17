import Chart from "chart.js/auto";

document.addEventListener("DOMContentLoaded", function () {
    
    var canvas1 = document.getElementById("myChart");
    var canvas2 = document.getElementById("myChart2");

    console.log(canvas1);

    if (canvas1) {
        var ctx = canvas1.getContext("2d");
        new Chart(ctx, {
            type: "bar",
            data: {
                labels: ["Enero", "Febrero", "Marzo", "Abril"],
                datasets: [
                    {
                        label: "Ventas",
                        data: [10, 20, 30, 40],
                        backgroundColor: "rgba(54, 162, 235, 0.5)",
                        borderColor: "rgba(54, 162, 235, 1)",
                        borderWidth: 1,
                    },
                ],
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true },
                },
            },
        });
    } else {
        console.error("El canvas 'myChart' no existe en el DOM.");
    }

    if (canvas2) {
        var ctx2 = canvas2.getContext("2d");
        new Chart(ctx2, {
            type: "bubble",
            data: {
                datasets: [
                    {
                        label: "Seguimiento de turnos",
                        data: [
                            { x: 10, y: 20, r: 15 },
                            { x: 30, y: 10, r: 10 },
                            { x: 20, y: 30, r: 25 },
                            { x: 40, y: 40, r: 20 },
                        ],
                        backgroundColor: "rgba(54, 162, 235, 0.5)",
                        borderColor: "rgba(54, 162, 235, 1)",
                        borderWidth: 1,
                    },
                ],
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        type: "linear",
                        position: "bottom",
                    },
                    y: {
                        beginAtZero: true,
                    },
                },
            },
        });
    } else {
        console.error("El canvas 'myChart2' no existe en el DOM.");
    }
});
