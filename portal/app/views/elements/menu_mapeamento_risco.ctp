<?php
$menusCadastro    = '';
$menusOperacoes   = '';
$menusConsultas   = '';
$subMenusMapeamentoRisco  = '';

$menusCadastro   .= $this->BMenu->link('Questionários',array('controller' => 'questionarios', 'action' => 'index'), array('wrapper'=>'li'));
$menusCadastro   .= $this->BMenu->link('Características de Questionários',array('controller' => 'caracteristicas', 'action' => 'index'), array('wrapper'=>'li'));
?>

<ul class="sf-menu menu-admin">
	<?php if (!empty($menusCadastro)): ?>
		<li><?php echo $this->Html->link('Cadastros','javascript:void(0)'); ?>
			<ul>
				<?php echo $menusCadastro ?>
			</ul>
		</li>
	<?php endif; ?>
	<?php if (!empty($menusOperacoes)): ?>
		<li><?php echo $this->Html->link('Operações','javascript:void(0)'); ?>
			<ul>
				<?php echo $menusOperacoes ?>
			</ul>
		</li>
	<?php endif; ?>	
	<?php if (!empty($menusConsultas)): ?>
		<li><?php echo $this->Html->link('Consultas','javascript:void(0)'); ?>
			<ul>
				<?php echo $menusConsultas ?>
			</ul>
		</li>
	<?php endif; ?>
	<?php echo $this->element('menu_terceiros_mapeamento_risco', array('terceiros' => true)); ?>
</ul>
