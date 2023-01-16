<div class='well'>    
    <div id='filtros'>        
        <?php echo $this->Bajax->form('ClienteProdutoDesconto', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'ClienteProdutoDesconto', 'element_name' => 'consulta_desconto'), 'divupdate' => '.form-procurar')) ?>

        <div class="row-fluid inline">
            <?php echo $this->Buonny->input_periodo($this, 'ClienteProdutoDesconto') ?>
        </div>       
        
        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn btn-filtro')); ?>
        <?php echo $this->BForm->end();?>
    </div>
</div>

<?php $this->addScript($this->Buonny->link_css('tablesorter')); ?>
<?php $this->addScript($this->Buonny->link_js('jquery.tablesorter.min')) ?>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        atualizaConsultaDesconto();
        
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:ClienteProdutoDesconto/element_name:consulta_desconto/" + Math.random())
        });           
        
    });', false);
?>
