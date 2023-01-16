<div class='well'>
  <div id='filtros'>
    <?php echo $bajax->form('SistCombateIncendio', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'SistCombateIncendio', 'element_name' => 'sist_combate_incendio'), 'divupdate' => '.form-procurar')) ?>
      <?php echo $this->element('sist_combate_incendio/fields_filtros') ?>
      <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
      <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
    <?php echo $this->BForm->end() ?>
  </div>
</div>

<?php $this->addScript($this->Buonny->link_js('comum.js')); ?>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        atualizaListaSistCombateIncendio();
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:SistCombateIncendio/element_name:sist_combate_incendio/" + Math.random())
        });
        
        function atualizaListaSistCombateIncendio() {
            var div = jQuery("div.lista");
            bloquearDiv(div);
            div.load(baseUrl + "sist_combate_incendio/listagem/" + Math.random());
        }
        
    });', false);
?>