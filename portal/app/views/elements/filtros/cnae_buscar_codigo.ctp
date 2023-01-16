<div class='well'>
    <?php $searcher = !empty($searcher)? $searcher : $this->data['Cnae']['searcher'];?>
    <?php $display = !empty($display)? $display : $this->data['Cnae']['display'];?>
    
  <?php echo $bajax->form('Cnae', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Cnae', 'element_name' => 'cnae_buscar_codigo', 'searcher' => $searcher, 'display' => $display), 'divupdate' => '.form-procurar-codigo-cnae')) ?>

		<div class="row-fluid inline">
			<?php echo $this->BForm->input('cnae', array('class' => 'input-mini', 'placeholder' => 'Cnae', 'label' => false, 'type' => 'text')) ?>
			<?php echo $this->BForm->input('descricao', array('class' => 'input-large', 'placeholder' => 'Ramo de Atividade', 'label' => false)) ?>
			
			<?php echo $this->BForm->hidden('searcher', array('value' => !empty($searcher)? $searcher : $this->data['Cnae']['searcher'])); ?>
			<?php echo $this->BForm->hidden('display', array('value' => !empty($display)? $display : $this->data['Cnae']['display'])); ?>
		
		</div>        
		<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
		<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro-cnae', 'class' => 'btn')) ;?>


  <?php echo $this->BForm->end() ?>
</div>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        jQuery("#limpar-filtro-cnae").click(function(){
            bloquearDiv(jQuery(".form-procurar-codigo-cnae"));
            jQuery(".form-procurar-codigo-cnae").load(baseUrl + "/filtros/limpar/model:Cnae/element_name:cnae_buscar_codigo/searcher:'.$searcher.'/display:'.$display.'/" + Math.random())
        });
		
        atualizaListaCnaeVisualizar("cnae_buscar_codigo", "'.$searcher.'","'.$display.'");
    });', false);
?>