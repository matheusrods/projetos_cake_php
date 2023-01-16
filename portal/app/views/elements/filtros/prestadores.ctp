<div class='well'>
    <?php echo $bajax->form('Prestador', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Prestador', 'element_name' => 'prestadores'), 'divupdate' => '.form-procurar')) ?>
    	<div class="row-fluid inline">
            <?php echo $this->BForm->input('nome', array('class' => 'input-xlarge', 'placeholder' => 'Razão Social', 'label' => false)) ?>
            <?php echo $this->BForm->input('codigo_documento', array('class' => 'input-medium', 'placeholder' => 'CNPJ/CPF', 'label' => false)) ?>            
        </div>
        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
    <?php echo $this->BForm->end() ?>
</div>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        var div = jQuery(".lista");
        bloquearDiv(div);
        div.load(baseUrl + "prestadores/listagem/"+Math.random() );        
        setup_datepicker();
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Prestador/element_name:prestadores/" + Math.random())
        });
    });', false);
?>
