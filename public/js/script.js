$(document).ready(function () {
    $("#datatable").DataTable({
        scrollX: false,  
        paging: true,
        autoWidth: false,  
        fixedHeader: {
            header: true,  
            footer: true  
        },
        columnDefs: [
            {
                defaultContent: "-",
                targets: "_all",
            },
        ],
        language: {
            search: "Buscar",
            emptyTable: "No hay datos disponibles en la tabla",
            infoEmpty: "Mostrando 0 a 0 de 0 entradas",
            lengthMenu: "Mostrar _MENU_ registros por página",
            info: "Mostrando página _PAGE_ de _PAGES_",
            zeroRecords: "No se encontraron registros coincidentes",
            infoFiltered: "(filtrado de _MAX_ registros en total)",
        },
        initComplete: function () {
            $(".dataTables_wrapper").css("font-size", "8px");
        },
    });
});
