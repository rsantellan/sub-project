$(document).ready(function() {
    $("#sub_inscription_level").select2({
        placeholder: "Seleccione Nivel (ver descripción en tabla inferior)",
        allowClear: true
    });

    $("#sub_inscription_sections").select2({
        placeholder: "Seccional a asociarse (optativo, máximo 2)"
    });
});