<div class='well'>
    <?php $searcher = !empty($searcher)? $searcher : $this->data['Cid']['searcher'];?>
    <?php $display = !empty($display)? $display : $this->data['Cid']['display'];?>
    
  <?php echo $bajax->form('Cid', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Cid', 'element_name' => 'cid_buscar_codigo', 'searcher' => $searcher, 'display' => $display), 'divupdate' => '.form-procurar-codigo-cid')) ?>

		<div class="row-fluid inline">
			<?php echo $this->BForm->input('codigo_cid10', array('class' => 'input-mini', 'placeholder' => 'CID10', 'label' => false, 'type' => 'text')) ?>
			<?php echo $this->BForm->input('descricao', array('class' => 'input-large', 'placeholder' => 'Descrição', 'label' => false)) ?>
			
			<?php echo $this->BForm->hidden('searcher', array('value' => !empty($searcher)? $searcher : $this->data['Cid']['searcher'])); ?>
			<?php echo $this->BForm->hidden('display', array('value' => !empty($display)? $display : $this->data['Cid']['display'])); ?>
		
		</div>        
		<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
		<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro-cid', 'class' => 'btn')) ;?>


  <?php echo $this->BForm->end() ?>
</div>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        jQuery("#limpar-filtro-cid").click(function(){
            bloquearDiv(jQuery(".form-procurar-codigo-cid"));
            jQuery(".form-procurar-codigo-cid").load(baseUrl + "/filtros/limpar/model:Cid/element_name:cid_buscar_codigo/searcher:'.$searcher.'/display:'.$display.'/" + Math.random())
        });
		
        atualizaListaCidVisualizar("cid_buscar_codigo", "'.$searcher.'","'.$display.'");
    });', false);
?>