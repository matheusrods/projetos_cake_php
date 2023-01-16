<div class='well'>
  <div id='filtros'>
    <?php echo $bajax->form('RiscoAtributoDetalheExterno', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'RiscoAtributoDetalheExterno', 'element_name' => 'riscos_atributos_detalhes_externo'), 'divupdate' => '.form-procurar')) ?>
      <?php echo $this->element('riscos_atributos_detalhes/fields_externo_filtros') ?>
      <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
      <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
    <?php echo $this->BForm->end() ?>
  </div>
</div>

<?php $this->addScript($this->Buonny->link_js('comum.js')); ?>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        atualizaLista();
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:RiscoAtributoDetalheExterno/element_name:riscos_atributos_detalhes_externo/" + Math.random())
        });
        
        function atualizaLista() {
            var div = jQuery("div.lista");
            bloquearDiv(div);
            div.load(baseUrl + "riscos_atributos_detalhes/listagem_externo/" + Math.random());
        }
        
    });', false);
?>