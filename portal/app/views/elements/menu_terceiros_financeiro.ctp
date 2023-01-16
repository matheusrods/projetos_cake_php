<?php 
$menusCadastro = '';
$menusOperacoes = '';
$menusConsultas = '';

$menusOperacoes .= $this->BMenu->link('Pré Faturamento', array('controller' => 'clientes', 'action' => 'pre_faturamento'), array('wrapper'=>'li'));

$menusConsultas .= $this->BMenu->link('Segunda Via Faturamento', array('controller' => 'clientes', 'action' => 'gerar_segunda_via_faturamento'), array('wrapper'=>'li'));
$menusConsultas .= $this->BMenu->link('Utilização de Serviços',array('controller'=>'clientes','action'=>'utilizacao_de_servicos'), array('wrapper'=>'li'));
$menusConsultas .= $this->BMenu->link('Utilização de Serviços Histórico (Demonstrativo)',array('controller'=>'clientes','action'=>'utilizacao_de_servicos_historico'), array('wrapper'=>'li'));
?>
<?php

//pr($terceiros);
if (!isset($terceiros)): ?>
	<ul class="sf-menu menu-admin">
<?php endif; ?>
	<?php $diff_name = isset($terceiros) ? ' Terceiros' : '' ?>
	<?php if (!empty($menusCadastro)): ?>
		<li><?php echo $this->Html->link('Cadastro'.$diff_name,'javascript:void(0)'); ?>
			<ul>
				<?php echo $menusCadastro ?>
			</ul>
		</li>
	<?php endif; ?>
	<?php if (!empty($menusOperacoes)): ?>
		<li><?php echo $this->Html->link('Operação'.$diff_name,'javascript:void(0)'); ?>
			<ul>
				<?php echo $menusOperacoes ?>
			</ul>
		</li>
	<?php endif; ?>
	<?php if (!empty($menusConsultas)): ?>
		<li><?php echo $this->Html->link('Consulta'.$diff_name,'javascript:void(0)'); ?>
			<ul>
				<?php echo $menusConsultas ?>
			</ul>
		</li>
	<?php endif; ?>
<?php if (!isset($terceiros)): ?>
	</ul>
<?php endif; ?>
