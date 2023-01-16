var cliente_aparelho_audiometrico_edit = new Object();

jQuery(document).ready(function(){

    setup_mascaras(); 
    setup_time(); 
    setup_datepicker();
    $("#multiselect").multiselectMulti();
    carregaUnidades();
    carregaPrestadores();

    jQuery("#AparelhoAudiometricoCodigoMatriz").change(function() {
        carregaUnidades();
        carregaPrestadores();
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
        var codigo_cliente = $("#AparelhoAudiometricoCodigoMatriz").val();     
        $.ajax({
            type: "POST",
            url: baseUrl + "cliente_aparelho_audiometrico/get_prestadores/" + codigo_cliente,
            dataType: "json",
            beforeSend: function() {
                $("#carregando").show();
            },
            success: function(data) {
                $("#carregando").hide();
                if(data) {
                    console.log(data);
                    var qtd = data.length;
                    $("#multiselect option").remove();
                    // $("#multiselect_to option").remove();

                    for(var i = 0; i < qtd; i++){
                        var nome = data[i]['Fornecedor'].nome;
                        var codigo = data[i]['Fornecedor'].codigo;
                       $("#multiselect").append("<option value='" + codigo + "'>" + codigo + " - " + nome + "</option>")
                    }
                    $("#carregando").hide();
                } else {
                    var msg_erro = "Nenhum prestador encontrado pra este cliente.";
                    $("#multiselect").append("<option value='" + msg_erro + "'>" + msg_erro + "</option>")
                }
            },
            error: function(erro){
                var msg_erro = "Nenhum prestador encontrado pra este cliente.";
                $("#multiselect").append("<option value='" + msg_erro + "'>" + msg_erro + "</option>")
            }
        });
    }

    function carregaPrestadores() {
        var codigo_aparelho = $("#AparelhoAudiometricoCodigoCapaudiCliente").val();     
        $.ajax({
            type: "POST",
            url: baseUrl + "cliente_aparelho_audiometrico/get_prestadores_ap_cliente/" + codigo_aparelho,
            dataType: "json",
            beforeSend: function() {
                $("#carregando").show();
            },
            success: function(data) {
                $("#carregando").hide();
                if(data) {
                    console.log(data);
                    var nome_fantasia_prestador = data.Fornecedor.nome;
                    var codigo_prestador = data.ApAudioFornecedor.codigo_fornecedor;

                    console.log(codigo_prestador);
                    console.log(nome_fantasia_prestador);
                    $("#multiselect_to").append("<option value='" + codigo_prestador + "'>" + codigo_prestador + " - " + nome_fantasia_prestador + "</option>")
                    // var qtd = data.length;
                    // $("#multiselect option").remove();
                    // $("#multiselect_to option").remove();

                    // for(var i = 0; i < qtd; i++){
                    //     var nome = data[i]['Fornecedor'].nome;
                    //     var codigo = data[i]['Fornecedor'].codigo;
                    // }
                    $("#carregando").hide();
                } else {
                    var msg_erro = "Nenhum prestador encontrado pra este cliente.";
                    $("#multiselect_to").append("<option value='" + msg_erro + "'>" + msg_erro + "</option>")
                }
            },
            error: function(erro){
                var msg_erro = "Nenhum prestador encontrado pra este cliente.";
                $("#multiselect_to").append("<option value='" + msg_erro + "'>" + msg_erro + "</option>")
            }
        });
    }
});