<div class='well'>    
    <div id='filtros'>
        
        <?php echo $bajax->form('MensagemDeAcesso', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'MensagemDeAcesso', 'element_name' => 'mensagens_de_acessos'), 'divupdate' => '.form-procurar')) ?>

        <div class="row-fluid inline">
            <?php echo $this->Buonny->input_periodo($this, 'MensagemDeAcesso') ?>
        </div>  

        <div class="row-fluid inline">            
            <?php echo $this->BForm->input('titulo', array('class' => 'input-xlarge', 'label' => false, 'placeholder' => 'Título')); ?>
        </div>
        
        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn btn-filtro')); ?>
        <?php echo $this->BForm->end();?>
    </div>
</div>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        setup_mascaras();
        atualizaListaMensagensDeAcessos();
        
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:MensagemDeAcesso/element_name:mensagens_de_acessos/" + Math.random())
        });  
                
    });', false);
?>
