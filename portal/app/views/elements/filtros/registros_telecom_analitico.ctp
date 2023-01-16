<div class='well'>
    <div id='filtros'>
        <?php echo $bajax->form('RegistroTelecom', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'RegistroTelecom', 'element_name' => 'registros_telecom_analitico'), 'divupdate' => '.form-procurar')) ?>
        <?php echo $this->element('filtros/registros_telecom'); ?>
        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn' )) ?>
        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro-registros_telecom', 'class' => 'btn')) ;?>
        <?php echo $this->BForm->end() ?>
    </div>    
</div>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        var div = jQuery("div.lista");
        bloquearDiv(div);
        div.load(baseUrl + "registros_telecom/analitico_listagem/" + Math.random());

        setup_datepicker();
        jQuery("#limpar-filtro-registros_telecom").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:RegistroTelecom/element_name:registros_telecom_analitico/" + Math.random())
        });
    });', false);?>