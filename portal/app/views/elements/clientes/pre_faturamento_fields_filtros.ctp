<div class="row-fluid inline">
    <?php 
    if(!$this->Buonny->seUsuarioForMulticliente()){
        
        if(empty($authUsuario['Usuario']['codigo_cliente'])){ 
            //o campo codigo_cliente é liberado para o usuário digitar
            echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente' ,'Código', 'Cliente'); 
        }else{
            //bloqueia o campo codigo_cliente com readonly
            echo $this->BForm->input('codigo_cliente', array('readonly' => true, 'class' => 'input-mini', 'placeholder' => 'Código', 'label' => 'Código')); 
        }
    
        //input nome do cliente
        echo $this->BForm->input('nome_cliente', array('label' => 'Nome Cliente', 'class' => 'input-xlarge', 'type' => 'text', 'readonly' => true));
    
    }else{
        echo $this->Buonny->input_codigo_cliente2($this, array('checklogin' => false), 'PreFaturamento');
    }

    echo "<label>Unidade</label>";
    echo $this->Buonny->input_unidades($this, 'Clientes', $unidades);
    ?>
</div>

<div class="row-fluid inline">
    <?php 
    if(!$this->Buonny->seUsuarioForMulticliente()){
        echo $this->BForm->input('codigo_pagador', array('class' => 'input-mini', 'placeholder' => 'Código', 'label' => 'Código'));
        echo $this->BForm->input('nome_pagador', array('label' => 'Pagador', 'class' => 'input-xlarge', 'readonly' => true));        
    }else{
        echo $this->Buonny->input_codigo_cliente2($this, array('input_name' => 'codigo_pagador', 'label' => 'Código Pagador', 'checklogin' => false), 'PreFaturamento'); 
    }
    ?>
</div>

<div class="row-fluid inline">
    <span class="label label-info">Exibição:</span>
    <div id='agrupamento'>
        <?php echo $this->BForm->input('exibicao', array('type' => 'radio', 'options' => $exibicao, 'default'=>1, 'legend' => false, 'label' => array('class' => 'radio inline input-xsmall'))) ?>
    </div>
    <?php    
    echo $this->BForm->input('forma_de_cobranca', array('label' => false, 'type' => 'select','class' => 'input-large form-control', 'options' => $produtos)); 
    echo $this->BForm->input('mes', array('label' => false, 'placeholder' => 'Mês', 'type' => 'select', 'class' => 'input-small form-control', 'options' => $meses, 'default'=>date("m")-1)); 
    echo $this->BForm->input('ano', array('label' => false, 'placeholder' => 'Ano', 'type' => 'text', 'class' => 'input-small form-control', 'default'=>date("Y")));
    ?> 
</div>

<style type="text/css">
	.error-message{ color: red; }
</style>

<script>
    $(document).ready(function(){
        var codigo_cliente = $('#ClienteCodigoCliente').val();        
        if(codigo_cliente){
            preenche_nome_cliente(codigo_cliente);
        }
    });

    $(document).on('blur', '#ClienteCodigoCliente', function() { 
        var codigo_cliente = $('#ClienteCodigoCliente').val();
        if (codigo_cliente) {
            preenche_nome_cliente(codigo_cliente);
        }
    });

    function preenche_nome_cliente(codigo_cliente){
        var input = $('#ClienteNomeCliente');
        $.ajax({
            url:baseUrl + 'clientes/buscar/' + codigo_cliente + '/' + Math.random(),
            dataType: 'json',
            beforeSend: function() {
                bloquearDiv(input.parent());
            },
            success: function(data) {
                if (data.sucesso) {
                    var input_name_display = $('#ClienteNomeCliente').val(data.dados.razao_social);
                } else {
                    var input_name_display = $('#ClienteNomeCliente').val('');
                }
            },
            complete: function() {
                input.parent().unblock();
            }
        });
    }

    $(document).on('blur', '#ClienteCodigoPagador', function() { 
        var codigo_cliente = $('#ClienteCodigoPagador').val();
        if (codigo_cliente) {
            var input = $('#ClienteNomePagador');
            $.ajax({
                url:baseUrl + 'clientes/buscar/' + codigo_cliente + '/' + Math.random(),
                dataType: 'json',
                beforeSend: function() {
                    bloquearDiv(input.parent());
                },
                success: function(data) {
                    if (data.sucesso) {
                        var input_name_display = $('#ClienteNomePagador').val(data.dados.razao_social);
                    } else {
                        var input_name_display = $('#ClienteNomePagador').val('');
                    }
                },
                complete: function() {
                    input.parent().unblock();
                }
            });
        }
    })
</script>