<div class='well'>
    <?php echo $bajax->form('MatrizFilialLog', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'MatrizFilialLog', 'element_name' => 'matrizes_filiais_log'), 'divupdate' => '.form-procurar')) ?>
        <div class="row-fluid inline">
            <?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente_matriz', 'Matriz', false,'MatrizFilialLog') ?>
            <?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente_filial', 'Filial', false,'MatrizFilialLog') ?>
        </div>
        <div class="row-fluid inline">
            <?php echo $this->Buonny->input_periodo($this, 'MatrizFilialLog', 'data_inicial', 'data_final') ?>
        </div>
        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn btn-filtro')); ?>
        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn btn-filtro')); ?>
    <?php echo $this->BForm->end(); ?>
</div>
<?php $this->addScript($this->Buonny->link_js('solicitacoes_monitoramento')) ?>
<?php $this->addScript($this->Buonny->link_js('estatisticas')) ?>
<?php echo $this->Javascript->codeBlock("
        $(document).ready(function($){  
            setup_mascaras();
            var div = jQuery('div.lista');
            bloquearDiv(div);
            div.load(baseUrl + 'matrizes_filiais_log/listagem/' + Math.random());

            var div = jQuery('div.lista_dados');
            bloquearDiv(div);
            div.load(baseUrl + 'matrizes_produtos_pagadores_log/listagem/' + Math.random());
            setup_datepicker();

            $('#limpar-filtro').click(function(){
                bloquearDiv(jQuery('.form-procurar'));
                $('.form-procurar').load(baseUrl + '/filtros/limpar/model:MatrizFilialLog/element_name:matrizes_filiais_log/' + Math.random());
                // $('.form-procurar').load(baseUrl + '/filtros/limpar/model:MatrizProdutoPagadorLog/element_name:matrizes_produtos_pagadores_log/' + Math.random());
            }); 
        });
    ", false);

?>
