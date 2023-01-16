<div class='well'>
    <h5><?= $this->Html->link((!empty($filtrado) ? 'Listagem Filtrada' : 'Definir Filtros'), 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show')) ?></h5>
    <div id='filtros'>
    	<?php echo $bajax->form('Proprietario', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Proprietario', 'element_name' => 'proprietario'), 'divupdate' => '.form-procurar')) ?>
            <div class="row-fluid inline">
                <?php echo $this->BForm->input('cpfcnpj', array('placeholder' => 'CNPJ/CPF','class'=>'input-medium' ,'label'=>'CPF/CNPJ')) ?>
                <?php echo $this->BForm->input('nomerazaosocial', array('placeholder' => 'Nome','class'=>'input-xlarge' ,'label'=>'Nome')) ?>
                <br>
                
             
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
        atualizaListaProprietarios(); 
        jQuery("a#filtros").click(function(){
            jQuery("div#filtros").slideToggle("slow");
        });
         
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Proprietario/element_name:proprietario/" + Math.random())
        });
        
    });', false); 
?>
<?php if (!empty($filtrado)): ?>
    <?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){jQuery("div#filtros").hide()})');?>
 <?php endif; ?>
   
           