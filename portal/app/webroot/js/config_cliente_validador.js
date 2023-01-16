var config_cliente_validador = new Object();

jQuery(document).ready(function(){

    setup_mascaras(); 
    setup_time(); 
    setup_datepicker();
    $("#multiselect").multiselectMulti();
    $("#multiselectusuarios").multiselectMulti();

    $("body").on("keydown", "input, select, textarea", function(e) {
        var self = $(this)
          , form = self.parents("form:eq(0)")
          , focusable
          , next
          ;
        if (e.keyCode == 13) {
            focusable = form.find("input,a,select,button,textarea").filter(":visible");
            next = focusable.eq(focusable.index(this)+1);
            if (next.length) {
                next.focus();
            } else {
                form.submit();
            }
            return false;
        }
    });
});

jQuery("#ClienteValidadorCodigoCliente").change(function() {
    carregaUnidades();
    carregaUsuarios();
});

function carregaUnidades() {
    var codigo_cliente = $("#ClienteValidadorCodigoCliente").val();     
    $.ajax({
        type: "POST",
        url: baseUrl + "clientes/get_campos_unidades/" + codigo_cliente,
        dataType: "json",
        beforeSend: function() {
            $("#carregando").show();
        },
        success: function(data) {
            $("#carregando").hide();
            if(data) {
                // console.log(data[0][\"Cliente\"].nome_fantasia);
                console.log(data);
                var qtd = data.length;
                // alert("encontrou");
                $("#multiselect option").remove();
                $("#multiselect_to option").remove();

                for(var i = 0; i < qtd; i++){
                    var nome_fantasia = data[i]['Cliente'].nome_fantasia;
                    var codigo = data[i]['Cliente'].codigo;
                    $("#multiselect").append("<option value='" + codigo + "'>" + codigo + " - " + nome_fantasia + "</option>")
                }
                $("#carregando").hide();
            } else {
                $("#carregando").hide();
                swal("Ops!", "Matriz ainda nao tem liberacao para validação de Pré-Faturamento.", "error");
                $('#multiselect option').remove();
                $('#multiselect_to option').remove();
            }
        },
        error: function(erro){
            $("#carregando").hide();
            swal("Ops!", "Matriz não encontrada!", "error");
            $('#multiselect option').remove();
            $('#multiselect_to option').remove();
        }
    });
}

function carregaUsuarios() {
    var codigo_cliente = $("#ClienteValidadorCodigoCliente").val();     
    $.ajax({
        type: "POST",
        url: baseUrl + "clientes/get_campos_usuarios/" + codigo_cliente,
        dataType: "json",
        beforeSend: function() {
            $("#carregando").show();
        },
        success: function(data) {
            $("#carregando").hide();
            if(data) {
                // console.log(data[0]['Usuario'].apelido);
                console.log(data);
                var qtd = data.length;
                $("#multiselectusuarios option").remove();
                $("#multiselectusuarios_to option").remove();

                for(var i = 0; i < qtd; i++){
                    var apelido = data[i]['Usuario'].apelido;
                    var codigo = data[i]['Usuario'].codigo;
                    $("#multiselectusuarios").append("<option value='" + codigo + "'>"  + apelido + "</option>")
                }
                $("#carregando").hide();
            } else {
                $("#carregando").hide();
                $("#multiselectusuarios option").remove();
                $("#multiselectusuarios_to option").remove();
            }
        },
        error: function(erro){
            $("#carregando").hide();
            $("#multiselectusuarios option").remove();
            $("#multiselectusuarios_to option").remove();
        }
    });
}

// $('.btn-primary').click(function(event) {
// });


