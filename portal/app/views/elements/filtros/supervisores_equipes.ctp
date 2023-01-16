<div class="well">
	<h5><?= $this->Html->link((!empty($filtrado) ? 'Listagem Filtrada' : 'Definir Filtros'), 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show')) ?></h5>
	<div id="filtros">
		<?php echo $this->Bajax->form('Uperfil', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Uperfil', 'element_name' => 'supervisores_equipes'), 'divupdate' => '.form-procurar')) ?>
		<div class="row-fluid inline">
			<?php echo $this->BForm->input('codigo', array('label' => 'Perfil', 'class' => 'input-largexx', 'options' => $perfis, 'empty' => 'Selecione')); ?>
		</div>
		<?php echo $this->BForm->submit('Filtrar', array('div' => false, 'class' => 'btn', 'id'=>'filtrar')); ?>
		<?php echo $this->BForm->end();?>
	</div>
</div>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
		jQuery("a#filtros").click(function(){
            jQuery("div#filtros").slideToggle("slow");
        });
		var div = jQuery("div.lista");
		bloquearDiv(div);
		div.load(baseUrl + "/supervisores_equipes/listagem/" + Math.random());
    });', false);
?>