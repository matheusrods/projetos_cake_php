function configura_campos() {
    $('#quantidade_cheque').blur ( function(){
        if ($('#quantidade_cheque').val() != ''){
            // checa se o campo e numerico
            // coloca casas decimais
            v = $('#quantidade_cheque').val();
            v = v.replace(/\D/g,""); //Remove tudo o que não é dígito

            if (v == 'NaN'){
                v = '';
            }

            $('#quantidade_cheque').val(v);
        }

        if ($('#quantidade_cheque').val() == ''){

            $erro = "Limite de cheques não pode ser vazio";
            alert($erro);
            $('#quantidade_cheque').focus();
        }
    })

    $('#valor_serasa').blur ( function(){
        if ($('#valor_serasa').val() != ''){
            // checa se o campo e numerico
            // coloca casas decimais
            v = $('#valor_serasa').val();
            v = v.replace(/\D/g,""); //Remove tudo o que não é dígito
            v = v.replace(/(\d{2})$/,".$1"); //Coloca a virgula

            $('#valor_serasa').val(v);
        }

        if ($('#valor_serasa').val() == ''){

            $erro = "SERASA não pode ser vazio";
            alert($erro);
            $('#valor_serasa').focus();
        }
    })

    $('#quantidade_minutos_espera_envio_email').blur ( function(){
        if ($('#quantidade_minutos_espera_envio_email').val() != ''){
            // checa se o campo e numerico
            // coloca casas decimais
            v = $('#quantidade_minutos_espera_envio_email').val();
            v = v.replace(/\D/g,""); //Remove tudo o que não é dígito

            if (v == 'NaN'){
                v = '';
            }

            $('#quantidade_minutos_espera_envio_email').val(v);
        }

        if ($('#quantidade_minutos_espera_envio_email').val() == ''){

            $erro = "Tempo de espera para envio do retorno não pode ser vazio";
            alert($erro);
            $('#quantidade_minutos_espera_envio_email').focus();
        }
    })

    $("#salvar").click( function(){

        if($('#codigo_produto').val() == '' ){
            alert("Preencha o Produto");
            return false;
        }

        if($('#quantidade_cheque').val() == '' ){
            alert("Preencha o Limite de cheques");
            return false;
        }

        if($('#codigo_status_anterior').val() == ''){
            alert("Preencha o Status da última pesquisa");
            return false;
        }

        if($('#valor_serasa').val() == ''){
            alert("Preencha campo SERASA");
            return false;
        }

        if($('#quantidade_minutos_espera_envio_email').val() == ''){
            alert("Preencha campo Tempo de espera para envio do retorno");
            return false;
        }

        if($('#verificar_profissional_negativado').val() == ""){
            alert("Preencha campo Verificar Profissional Negativado");
            return false;
        }

        if($('#verificar_validade_cnh').val() == ""){
            alert("Preencha campo Verificar Validade CNH");
            return false;
        }

        $('.frm').submit()
    });
}

function setup_mascaras(mascaras){
    jQuery('.moeda').keyup(function() {
        moeda(this)
    });
    jQuery('.moeda_com_negativo').keyup(function() {
        moeda_com_negativo(this)
    });
    if(jQuery('.telefone').length > 0)
        jQuery('.telefone').setMask( {
            mask : '(99)9999-99999'
        });

    jQuery('.moeda3').keyup(function() {
        moeda3(this);
    });
}

function moeda_com_negativo(z){
    negativo = z.value.indexOf('-') >= 0;
    moeda(z);
    if(negativo)
        z.value = "-" + z.value;
}

function moeda(z){
    v = z.value;
    v = v.replace(/\D/g,""); //Remove tudo o que não é dígito
    v = v.replace(/(\d{2})$/,",$1"); //Coloca a virgula
    v = v.replace(/(\d+)(\d{3},\d{2})$/g,"$1.$2"); //Coloca o primeiro ponto

    var qtdLoop = (v.length-3)/3;
    var count = 0;

    while (qtdLoop > count) {
        count++;
        v = v.replace(/(\d+)(\d{3}.*)/,"$1.$2"); //Coloca o resto dos pontos
    }
    v=v.replace(/^(0)(\d)/g,"$2"); //Coloca hífen entre o quarto e o quinto dígitos
    return z.value = v;
}

/**
    Função para colocar 3 casas decimais no campo input

    parametros:
    z = string de entrada

    saida:
    999.999,999

    Forma de usar:
    <?php echo $javascript->link('pesquisa.js'); ?>
    <?php echo $javascript->codeblock('setup_mascaras();'); ?>

    colocar na class moeda3

 
*/
function moeda3(z) {

    //var v = z.toFixed(3).toString();
    v = z.value;
    v = v.replace(/\D/g,""); // Remove tudo o que não é dígito
    v = v.replace(/(\d{3})$/,",$1"); // Coloca a virgula
    v = v.replace(/(\d+)(\d{3},\d{2})$/g,"$1.$2"); // Coloca o primeiro ponto

    var qtdLoop = (v.length-3)/3;
    var count = 0;

    while (qtdLoop > count) {
        count++;
        v = v.replace(/(\d+)(\d{3}.*)/,"$1.$2"); // Coloca o resto dos pontos
    }
    v=v.replace(/^(0)(\d)/g,"$2"); // Coloca hífen entre o quarto e o quinto dígitos

    return z.value = v;

} //fim moeda3
