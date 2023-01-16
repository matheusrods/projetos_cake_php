<div class = 'form-procurar'>
    <?= $this->element('filtros/configuracao_cliente_validador') ?>
</div>
<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i> incluir', array('controller' => 'clientes', 'action' => 'incluir_config_cliente_validador'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Incluir Configuração Cliente Validador'));?>
</div>
<div class='lista'></div>