<div class='well'>
  <div id='filtros'>
    <?php echo $bajax->form('Epc', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Epc', 'element_name' => 'epc'), 'divupdate' => '.form-procurar')) ?>
      	<?php echo $this->element('epc/fields_filtros') ?>
      	<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
		<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
    <?php echo $this->BForm->end() ?>
  </div>
</div>

<?php $this->addScript($this->Buonny->link_js('comum.js')); ?>
<?php echo $this->Javascript->codeBlock('
	jQuery(document).ready(function(){
        atualizaListaEpc();
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Epc/element_name:epc/" + Math.random())
        });
        
        function atualizaListaEpc() {
            var div = jQuery("div.lista");
            bloquearDiv(div);
            div.load(baseUrl + "epc/listagem/" + Math.random());
        }
        
    });', false);
?>