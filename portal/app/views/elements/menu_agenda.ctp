<?php 
$menusCadastro = '';
$menusOperacoes = '';
$menusConsultas = '';

$menusOperacoes .= $this->BMenu->link('Pedido de Exame',array('controller'=>'clientes_funcionarios','action'=>'selecao_funcionarios'), array('wrapper'=>'li'));
$menusOperacoes .= $this->BMenu->link('Fila de Agendamento',array('controller'=>'agendamento','action'=>'fila'), array('wrapper'=>'li'));

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