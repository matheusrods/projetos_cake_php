<?php echo $this->BForm->create('TVploValorPadraoLogistico', array('url' => array('controller' => 'clientes','action' => 'parametrizacao_clientes'), 'type' => 'POST')); ?>
<div class="row-fluid inline">
    <?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', 'Cliente', 'TVploValorPadraoLogistico' ); ?>
    <?php echo $this->BForm->input("Cliente.razao_social", array('label' => 'Razão Social', 'class' => 'input-xxlarge', 'readonly'=>true)) ?>
</div>
<? if(empty($authUsuario['Usuario']['codigo_cliente'])): ?>
    <div class="row-fluid inline">
        <?= $html->link('Buscar', array('action' => false), array('class' => 'btn', 'onclick'=> 'javascript: pesquisarCliente(); return false;')); ?>
    </div><br/>
<? endif; ?>

<div id="divDadosParametrizacao">
    <? if (!empty($codigo_cliente)): ?>
        <?php echo $this->element('clientes/parametros_cliente_logistico'); ?>
    <? endif; ?>
</div>
<?php echo $this->BForm->end(); ?>
<?php $this->addScript($this->Buonny->link_js('clientes.js')); ?>
<?php echo $this->Javascript->codeBlock("
    $(function() {
        setup_time();
        setup_mascaras();
        setup_datepicker();
    });

    function resetaDivDados() {
        $('#ClienteRazaoSocial').val('');
        $('#divDadosParametrizacao').html('&nbsp;');
    }

    function pesquisarCliente() {
        var codigo_cliente = $('#TVploValorPadraoLogisticoCodigoCliente').val();
        bloquearDiv(jQuery('#divDadosParametrizacao'));

        if (codigo_cliente != '') {
            $.ajax({
                url:baseUrl + 'embarcadores_transportadores/listar_por_cliente/' + codigo_cliente + '/' + Math.random(),
                dataType: 'json',
                success: function(data) {
                    if (data) {
                        $('#ClienteRazaoSocial').val(data.razao_social);
                        console.log(baseUrl + '/clientes/detalhes_parametrizacao_cliente/' + codigo_cliente + '/' + Math.random());
                        jQuery('#divDadosParametrizacao').load(baseUrl + '/clientes/detalhes_parametrizacao_cliente/' + codigo_cliente + '/' + Math.random())
                    } else {
                        resetaDivDados();
                        jQuery('#divDadosParametrizacao').unblock();
                    }
                },
                error: function(data) {
                    resetaDivDados();
                    jQuery('#divDadosParametrizacao').unblock();
                }
            })
        } else {
            resetaDivDados();
            jQuery('#divDadosParametrizacao').unblock();
        }        
    }

    jQuery(document).ready(function(){
        $('#TVploValorPadraoLogisticoCodigoCliente').blur(function(){
            resetaDivDados();
        });


    });
");


?>

