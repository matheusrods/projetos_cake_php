var editar_config_cliente_validador = new Object();

jQuery(document).ready(function(){

    setup_mascaras(); 
    setup_time(); 
    setup_datepicker();
    $("#multiselectccv").multiselectMulti();
    $("#multiselectusuariosccv").multiselectMulti();
    carregaUnidades();
    carregaUsuarios();
    carregaDados();

    jQuery("#ClienteValidadorCodigoClienteMatriz").change(function() {
        carregaUnidades();
        carregaUsuarios();
        carregaDados();
    });

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

    function carregaUnidades() {
        var codigo_cliente = $("#ClienteValidadorCodigoClienteMatriz").val();     
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
                    var qtd = data.length;
                    $("#multiselectccv option").remove();

                    for(var i = 0; i < qtd; i++){
                        var nome_fantasia = data[i]['Cliente'].nome_fantasia;
                        var codigo = data[i]['Cliente'].codigo;
                        $("#multiselectccv").append("<option value='" + codigo + "'>" + codigo + " - " + nome_fantasia + "</option>")
                    }
                    $("#carregando").hide();
                } else {
                    $("#carregando").hide();
                    swal("Ops!", "Matriz ainda nao tem liberacao para validação de Pré-Faturamento.", "error");
                    $('#multiselectccv option').remove();
                }
            },
            error: function(erro){
                $("#carregando").hide();
                swal("Ops!", "Matriz não encontrada!", "error");
                $('#multiselectccv option').remove();
            }
        });
    }

    function carregaUsuarios() {
        var codigo_cliente = $("#ClienteValidadorCodigoClienteMatriz").val();     
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
                    var qtd = data.length;
                    $("#multiselectusuariosccv option").remove();

                    for(var i = 0; i < qtd; i++){
                        var apelido = data[i]['Usuario'].apelido;
                        var codigo = data[i]['Usuario'].codigo;
                        $("#multiselectusuariosccv").append("<option value='" + codigo + "'>"  + apelido + "</option>")
                    }
                    $("#carregando").hide();
                } else {
                    $("#carregando").hide();
                    $("#multiselectusuariosccv option").remove();
                }
            },
            error: function(erro){
                $("#carregando").hide();
                $("#multiselectusuariosccv option").remove();
            }
        });
    }

    function carregaDados() {
        var codigo_ccv = $("#ClienteValidadorCodigo").val();     
        $.ajax({
            type: "POST",
            url: baseUrl + "clientes/get_campos_editarccv/" + codigo_ccv,
            dataType: "json",
            beforeSend: function() {
                $("#carregando").show();
            },
            success: function(data) {
                $("#carregando").hide();
                if(data) {
                    // console.log(data);
                    $("#multiselect_ccv option").remove();

                    var nome_fantasia_ccv = data.Unidade.nome_fantasia;
                    var codigo_unidade = data.Unidade.codigo;
                    var nome_usuario = data.Usuario.apelido;
                    var codigo_usuario = data.ClienteValidador.codigo_usuario;

                    $("#multiselectccv_to").append("<option value='" + codigo_unidade + "'>" + codigo_unidade + " - " + nome_fantasia_ccv + "</option>")
                    $("#multiselectusuariosccv_to").append("<option value='" + codigo_usuario + "'>"  + nome_usuario + "</option>")
                    $("#carregando").hide();
                } else {
                    $("#carregando").hide();
                    swal("Ops!", "Matriz ainda nao tem liberacao para validação de Pré-Faturamento.", "error");
                    $('#multiselect_ccv option').remove();
                }
            },
            error: function(erro){
                $("#carregando").hide();
                swal("Ops!", "Matriz não encontrada!", "error");
                $('#multiselect_ccv option').remove();
            }
        });
    }
});



