<div class='well'>
  <div id='filtros'>
    <?php echo $bajax->form('Epi', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Epi', 'element_name' => 'epi'), 'divupdate' => '.form-procurar')) ?>
      	<?php echo $this->element('epi/fields_filtros') ?>
      	<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
		<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
    <?php echo $this->BForm->end() ?>
  </div>
</div>

<?php $this->addScript($this->Buonny->link_js('comum.js')); ?>
<?php echo $this->Javascript->codeBlock('
	jQuery(document).ready(function(){
        atualizaListaEpi();
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Epi/element_name:epi/" + Math.random())
        });
        
        function atualizaListaEpi() {
            var div = jQuery("div.lista");
            bloquearDiv(div);
            div.load(baseUrl + "epi/listagem/" + Math.random());
        }
        
    });', false);
?>