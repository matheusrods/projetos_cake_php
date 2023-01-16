<div class='well'>    
    <div id='filtros'>        
        <?php echo $bajax->form('FichaForense', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'FichaForense', 'element_name' => 'forense'), 'divupdate' => '.form-procurar')) ?>

        <div class="row-fluid inline">            
            <?php echo $this->BForm->input('codigo_ficha', array('class' => 'input-small', 'label' => false, 'placeholder' => 'Código Ficha','maxlength'=>false)); ?>
            <?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', false,'FichaForense') ?>
            <?php echo $this->BForm->input('codigo_seguradora',array('label' => false, 'empty' => 'Selecione uma Seguradora','options' => $seguradoras,'class'=>'input-large' ));?>
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
        atualizaFichaForense();
        
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:FichaForense/element_name:forense/" + Math.random())
        });  
                
    });', false);
?>
