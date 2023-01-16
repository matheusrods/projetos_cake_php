<div class='well'>
  <div id='filtros'>
    <?php echo $bajax->form('HospitaisEmergencia', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'HospitaisEmergencia', 'element_name' => 'hospitais_emergencia'), 'divupdate' => '.form-procurar')) ?>

      <?php echo $this->element('hospitais_emergencia/fields_filtros_hospitais_emergencia') ?>
      
      <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
      <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro-digitalizacao', 'class' => 'btn')) ;?>
    <?php echo $this->BForm->end() ?>
  </div>
</div>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
   
        setup_datepicker();

        atualizaListaDigitalizacao();

        jQuery("#limpar-filtro-digitalizacao").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:HospitaisEmergencia/element_name:hospitais_emergencia/" + Math.random())
        });

        function atualizaListaDigitalizacao(){
            //verifica se existe algum codigo para pesquisar
            if($("#HospitaisEmergenciaCodigoCliente").val() != "") {
                var div = jQuery("div.lista");
                bloquearDiv(div);
                div.load(baseUrl + "hospitais_emergencia/lista_unidades/" + Math.random());
            }
        }
    });', false);
?>