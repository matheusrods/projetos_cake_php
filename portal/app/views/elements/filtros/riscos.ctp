<div class='well'>
  <div id='filtros'>
    <?php echo $bajax->form('Risco', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Risco', 'element_name' => 'riscos'), 'divupdate' => '.form-procurar')) ?>
      <?php echo $this->element('riscos/fields_filtros') ?>
    <?php echo $this->BForm->end() ?>
  </div>
</div>

<?php $this->addScript($this->Buonny->link_js('comum.js')); ?>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        atualizaListaRiscos();
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Risco/element_name:riscos/" + Math.random())
        });
        
        function atualizaListaRiscos() {
            var div = jQuery("div.lista");
            bloquearDiv(div);
            div.load(baseUrl + "riscos/listagem/" + Math.random());
        }
        
        $(".multiselect-grupo").multiselect({
            maxHeight: 300,
            nonSelectedText: "Grupo",
            numberDisplayed: 1,
            includeSelectAllOption: true
        });        
        
    });', false);
?>