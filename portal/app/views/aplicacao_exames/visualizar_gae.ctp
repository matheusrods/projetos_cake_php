<div class = 'form-procurar'>
	<?= $this->element('/filtros/aplicacao_exames') ?>
</div>
<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', array('controller' => 'aplicacao_exames', 'action' => 'incluir', $codigo_unidade), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar Aplicação de Exames'));?>
</div>
<div class='lista'></div>

<script type="text/javascript">
jQuery(document).ready(function(){
    jQuery(".icon-plus").parent().remove();
});
</script>