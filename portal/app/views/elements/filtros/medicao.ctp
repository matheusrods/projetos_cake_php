<div class='well'>
  <div id='filtros'>
    <?php echo $bajax->form('Medicao', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Medicao', 'element_name' => 'medicao'), 'divupdate' => '.form-procurar')) ?>
      	<?php echo $this->element('medicao/fields_filtros') ?>
      	<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
		<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
    <?php echo $this->BForm->end() ?>
  </div>
</div>

<?php $this->addScript($this->Buonny->link_js('comum.js')); ?>
<?php echo $this->Javascript->codeBlock('
	jQuery(document).ready(function(){
        atualizaListaMedicao();
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Medicao/element_name:medicao/" + Math.random())
        });
        
        function atualizaListaMedicao() {
            var div = jQuery("div.lista");
            bloquearDiv(div);
            div.load(baseUrl + "medicao/listagem/" + Math.random());
        }
        
    });', false);
?>