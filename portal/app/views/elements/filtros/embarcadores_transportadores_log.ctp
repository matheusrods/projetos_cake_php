<div class='well'>
    <?php echo $bajax->form('EmbarcadorTransportadorLog', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'EmbarcadorTransportadorLog', 'element_name' => 'embarcadores_transportadores_log'), 'divupdate' => '.form-procurar')) ?>
        <div class="row-fluid inline">
            <?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente_embarcador', 'Embarcador',false,'EmbarcadorTransportadorLog') ?>
            <?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente_transportador', 'Transportador',false,'EmbarcadorTransportadorLog') ?>
        </div>
        <div class="row-fluid inline">
            <?php echo $this->Buonny->input_periodo($this, 'EmbarcadorTransportadorLog', 'data_inicial', 'data_final') ?>
        </div>
        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn btn-filtro')); ?>
        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn btn-filtro')); ?>
    <?php echo $this->BForm->end(); ?>
</div>
<?php echo $this->Javascript->codeBlock("
        $(document).ready(function($){  
            setup_mascaras();
            var div = jQuery('div.lista');
            bloquearDiv(div);
            div.load(baseUrl + 'embarcadores_transportadores_log/listagem/' + Math.random());

            var div = jQuery('div.lista_dados');
            bloquearDiv(div);
            div.load(baseUrl + 'clientes_produtos_pagadores_log/listagem/' + Math.random());
            setup_datepicker();

            $('#limpar-filtro').click(function(){
                bloquearDiv(jQuery('.form-procurar'));
                $('.form-procurar').load(baseUrl + '/filtros/limpar/model:EmbarcadorTransportadorLog/element_name:embarcadores_transportadores_log/' + Math.random());
                $('.form-procurar').load(baseUrl + '/filtros/limpar/model:ClienteProdutoPagadorLog/element_name:embarcadores_transportadores_log/' + Math.random());
            }); 
        });
    ", false);

?>
