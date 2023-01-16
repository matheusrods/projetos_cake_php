<?php 
$menusCadastro = '';
$menusOperacoes = '';
$menusConsultas = '';

$menusCadastro .= $this->BMenu->link('Atribuições do Cargo',array('controller'=>'atribuicoes_cargos', 'action'=>'index'), array('wrapper'=>'li'));
$menusCadastro .= $this->BMenu->link('Cargos',array('controller'=>'clientes', 'action'=>'cargos'), array('wrapper'=>'li'));
$menusCadastro .= $this->BMenu->link('Funcionários',array('controller'=>'clientes', 'action'=>'funcionarios'), array('wrapper'=>'li'));
// $menusCadastro .= $this->BMenu->link('Grupos Homogêneos',array('controller'=>'clientes','action'=>'clientes_grupos_homogeneos'), array('wrapper'=>'li'));
$menusCadastro .= $this->BMenu->link('Setores',array('controller'=>'clientes','action'=>'setores'), array('wrapper'=>'li'));
$menusOperacoes .= $this->BMenu->link('Implantação',array('controller'=>'clientes_implantacao','action'=>'index'), array('wrapper'=>'li'));
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