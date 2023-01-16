<div class="form-procurar">
    <?php echo $this->element('/filtros/liberacoes_provisorias'); ?>
</div>
<div class="actionbar-right">
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', array( 'controller' => $this->name, 'action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar Perfil Adequado por Prazo'));?>
</div>
<div class="lista"></div>
