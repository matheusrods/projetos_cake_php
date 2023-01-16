<?php 
$menusCadastro = '';
$menusOperacoes = '';
$menusConsultas = '';

$menusCadastro .= $this->BMenu->link('Minha Proposta', array('controller' => 'propostas_credenciamento', 'action' => 'minha_proposta', $authUsuario['Usuario']['codigo_proposta_credenciamento'], 'visualizar'), array('wrapper'=>'li'));

$menusConsultas .= $this->BMenu->link('Anexos reprovados', array('controller' => 'anexos', 'action' => 'index'), array('wrapper'=>'li'));
$menusConsultas .= $this->BMenu->link('Relatório Faturamento Credenciado', array('controller' => 'fornecedores', 'action' => 'relatorio_fat_cred'), array('wrapper'=>'li'));

?>
<?php
if (!isset($clientes)): ?>
	<ul class="sf-menu menu-admin">
<?php endif; ?>
	<?php $diff_name = isset($clientes) ? ' Terceiro' : '' ?>
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
<?php if (!isset($clientes)): ?>
	</ul>
<?php endif; ?>