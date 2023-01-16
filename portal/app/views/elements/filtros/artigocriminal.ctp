<div class='well'>
    <h5><?= $this->Html->link((!empty($filtrado) ? 'Listagem Filtrada' : 'Definir Filtros'), 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show')) ?></h5>
    <div id='filtros'>
    	<?php echo $bajax->form('ArtigoCriminal', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'ArtigoCriminal', 'element_name' => 'artigocriminal'), 'divupdate' => '.form-procurar')) ?>
            <div class="row-fluid inline">
                <?php echo $this->BForm->input('numeroarquivo', array('class' => 'input-small', 'label'=>'Número Artigo')) ?>
                <?php echo $this->BForm->input('descricao', array('class' => 'input-xxlarge','label'=>'Descrição')) ?>
                <?php echo $this->BForm->input('vigente', array('class' => 'input-small', 'options' => array(1 => 'Sim', 2 => 'Não'), 'empty' => 'Todos', 'label'=>'Artigo Vigente')) ?>
             
            </div>

            <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
            <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
        <?php echo $this->BForm->end() ?>
    </div> 
</div> 

   


<?php
/*echo $this->Javascript->codeBlock("$(document).ready(function() { atualizaListaArtigosCriminais();
     });", false);*/
?>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){ 
        atualizaListaArtigosCriminais(); 
        jQuery("a#filtros").click(function(){
            jQuery("div#filtros").slideToggle("slow");
        });
         
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:ArtigoCriminal/element_name:artigocriminal/" + Math.random())
        });
        
    });', false); 
?>
<?php if (!empty($filtrado)): ?>
    <?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){jQuery("div#filtros").hide()})');?>
 <?php endif; ?>
   
           