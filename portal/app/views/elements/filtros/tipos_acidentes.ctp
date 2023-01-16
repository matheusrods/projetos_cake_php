<div class='well'>
  <div id='filtros'>
    <?php echo $bajax->form('TipoAcidente', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'TipoAcidente', 'element_name' => 'tipos_acidentes'), 'divupdate' => '.form-procurar')) ?>
      <?php echo $this->element('tipos_acidentes/fields_filtros') ?>
      <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
      <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
    <?php echo $this->BForm->end() ?>
  </div>
</div>

<?php $this->addScript($this->Buonny->link_js('comum.js')); ?>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        atualizaListaTiposAcidentes();
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:TipoAcidente/element_name:tipo_acidentes/" + Math.random())
        });
        
        function atualizaListaTiposAcidentes() {
            var div = jQuery("div.lista");
            bloquearDiv(div);
            div.load(baseUrl + "tipos_acidentes/listagem/" + Math.random());
        }
        
    });', false);
?>