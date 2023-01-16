<div class='well'>
	<?php echo $this->Bajax->form('Notafis', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Notafis', 'element_name' => 'notas_fiscais_por_banco'), 'divupdate' => '.form-procurar')) ?>
	<div class="row-fluid inline">
		<?php echo $this->BForm->input('ano', array('options' => $anos, 'class' => 'input-small', 'label' => false, 'default' => date('Y'))); ?>
		<?php echo $this->BForm->input('mes', array('options' => $meses, 'class' => 'input-small', 'label' => false, 'default' => date('m'))); ?>
	</div>
	<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
	<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
	<?php echo $this->BForm->end();?>
</div>
<?php $this->addScript($this->Buonny->link_js('twitter/bootstrap-scrollspy')) ?>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
		atualizaListaNotasFiscaisPorBanco();
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Notafis/element_name:notas_fiscais_por_banco/" + Math.random())
        });
    });', false);
?>