<div class='well'>    
    <div id='filtros'>        
        <?php echo $bajax->form('FichaForenseLiberar', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'FichaForenseLiberar', 'element_name' => 'liberar_fichas_forense'), 'divupdate' => '.form-procurar')) ?>

        <div class="row-fluid inline">                 
            <?php echo $this->BForm->input('codigo_documento',array('label' => false,'type' => 'text','class' => 'input-medium formata-cpf', 'placeholder' => 'CPF')) ?>
        </div>
        
        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn btn-filtro')); ?>
        <?php echo $this->BForm->end();?>
    </div>
</div>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        setup_mascaras();
        atualizaLiberarFichaForense();
        
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:FichaForenseLiberar/element_name:liberar_fichas_forense/" + Math.random())
        });  
                
    });', false);
?>
