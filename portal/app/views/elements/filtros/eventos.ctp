<div class='well'>
    <?php echo $bajax->form('TEspaEventoSistemaPadrao', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'TEspaEventoSistemaPadrao', 'element_name' => 'eventos'), 'divupdate' => '.form-procurar')) ?>
    <div class="row-fluid inline">
        <?php echo $this->BForm->input('espa_codigo', array('class' => 'input-mini', 'placeholder' => 'Código', 'label' => false, 'type' => 'text')) ?>        
        <?php echo $this->BForm->input('espa_descricao', array('class' => 'input-xlarge', 'placeholder' => 'Descricao', 'label' => false)) ?>          
    </div>
    <div class="row-fluid inline">
        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')); ?>
        <?php echo $this->BForm->end() ?>
    </div>
</div>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        
        atualizaListaEventos();
        
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:TEspaEventoSistemaPadrao/element_name:eventos/" + Math.random())
        });      
        
    });', false);
?>
