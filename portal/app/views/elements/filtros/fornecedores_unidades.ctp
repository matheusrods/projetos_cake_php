<div class='well'><?php $codigo_fornecedor_matriz = empty($this->passedArgs[0])?$this->passedArgs['codigo_fornecedor_matriz']:$this->passedArgs[0];?>
    <?php echo $bajax->form('FornecedorUnidade', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'FornecedorUnidade', 'element_name' => 'fornecedores_unidades', 'codigo_fornecedor_matriz' => $codigo_fornecedor_matriz), 'divupdate' => '.form-procurar')) ?>
        <?php echo $this->element('fornecedores_unidades/fields_filtros') ?>
        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
    <?php echo $this->BForm->end() ?>
</div>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        atualizaListaFornecedoresUnidades('.$codigo_fornecedor_matriz.');
        setup_datepicker();
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:FornecedorUnidade/element_name:fornecedores_unidades/codigo_fornecedor_matriz:'.$codigo_fornecedor_matriz.'/" + Math.random())
        });

        function atualizaListaFornecedoresUnidades(codigo_fornecedor_matriz) {
            var div = jQuery("div.lista");
            bloquearDiv(div);
            div.load(baseUrl + "fornecedores_unidades/listagem/"+ codigo_fornecedor_matriz +"/"+ Math.random());
        }
    });', false);


?>
