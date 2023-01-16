<div class='well'>
  <div id='filtros'>
    <?php echo $bajax->form('RiscoAtributoDetalhe', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'RiscoAtributoDetalhe', 'element_name' => 'riscos_atributos_detalhes'), 'divupdate' => '.form-procurar')) ?>

      	<?php echo $this->element('riscos_atributos_detalhes/fields_filtros') ?>

      	<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>

		    <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
    <?php echo $this->BForm->end() ?>
  </div>
</div>

<?php $this->addScript($this->Buonny->link_js('comum.js')); ?>
<?php echo $this->Javascript->codeBlock('
	jQuery(document).ready(function(){
        atualizaListaRiscoAtributoDetalhe();
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:RiscoAtributoDetalhe/element_name:riscos_atributos_detalhes/" + Math.random())
        });
        
        function atualizaListaRiscoAtributoDetalhe() {
            var div = jQuery("div.lista");
            bloquearDiv(div);
            div.load(baseUrl + "riscos_atributos_detalhes/listagem/" + Math.random());
        }
        
    });', false);
?>