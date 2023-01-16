<div class='well'>    
    <div id='filtros'>
        
        <?php echo $bajax->form('TAatuAreaAtuacao', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'TAatuAreaAtuacao', 'element_name' => 'estatistica_distribuidor_automatico'), 'divupdate' => '.form-procurar')) ?>

        <div class="row-fluid inline">
            <?php echo $this->Buonny->input_periodo($this, 'TAatuAreaAtuacao') ?>
            <?= $this->BForm->input('hora_inicial', array('label' => false, 'class' => 'hora input-mini')) ?>
            <?= $this->BForm->input('hora_final', array('label' => false, 'class' => 'hora input-mini')) ?>
        </div>          
        
        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn btn-filtro')); ?>
        <?php echo $this->BForm->end();?>
    </div>
</div>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        $(".hora").mask("99:99");
        setup_mascaras();
        atualizaListaTAatuAreaAtuacao();
        
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:TAatuAreaAtuacao/element_name:estatistica_distribuidor_automatico/" + Math.random())
        });  
                
    });', false);
?>