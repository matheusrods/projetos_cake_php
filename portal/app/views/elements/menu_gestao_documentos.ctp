<?php 
$menusCadastro = '';
$menusOperacoes = '';
$menusConsultas = '';

$menusCadastro .= $this->BMenu->link('Modelos',array('controller'=>'gd_modelos', 'action'=>'index'), array('wrapper'=>'li'));
$menusCadastro .= $this->BMenu->link('Templates',array('controller'=>'gestao_doc', 'action'=>'index'), array('wrapper'=>'li'));
?>

<ul class="sf-menu menu-admin">
	<?php if (!empty($menusCadastro)): ?>
		<li><?php echo $this->Html->link('Cadastro','javascript:void(0)'); ?>
			<ul>
				<?php echo $menusCadastro ?>
			</ul>
		</li>
	<?php endif; ?>
	<?php if (!empty($menusOperacoes)): ?>
		<li><?php echo $this->Html->link('Operação','javascript:void(0)'); ?>
			<ul>
				<?php echo $menusOperacoes ?>
			</ul>
		</li>
	<?php endif; ?>
	<?php if (!empty($menusConsultas)): ?>
		<li><?php echo $this->Html->link('Consulta','javascript:void(0)'); ?>
			<ul>
				<?php echo $menusConsultas ?>
			</ul>
		</li>
	<?php endif; ?>
</ul>