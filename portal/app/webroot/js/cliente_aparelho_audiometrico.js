var cliente_aparelho_audiometrico = new Object();

jQuery(document).ready(function(){

    setup_mascaras(); 
    setup_time(); 
    setup_datepicker();
    $("#multiselect").multiselectMulti();
    carregaUnidades();

    jQuery("#AparelhoAudiometricoCodigoCliente").change(function() {
        carregaUnidades();
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
        var codigo_cliente = $("#AparelhoAudiometricoCodigoCliente").val();     
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
                    $("#multiselect_to option").remove();

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
});