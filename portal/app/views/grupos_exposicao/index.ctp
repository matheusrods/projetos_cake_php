<div class = 'form-procurar'>
	<?= $this->element('/filtros/grupos_exposicao') ?>
</div>
<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', array('controller' => 'grupos_exposicao', 'action' => 'incluir', $codigo_cliente), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar Novos Grupos de Exposição'));?>
</div>
<div class='lista'></div>

<?php if(!empty($visualizar_gge) && $visualizar_gge) : ?>
	<script type="text/javascript">
	jQuery(document).ready(function(){
		jQuery(".icon-plus").parent().remove();
	});
	</script>
<?php endif; ?>