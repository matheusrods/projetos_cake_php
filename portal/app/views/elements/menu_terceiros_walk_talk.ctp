<?php
$menusCadastro    = '';
$menusOperacoes   = '';
$menusConsultas   = '';

//cadastros
$menusCadastro  .= $this->BMenu->link('Conf. Quantidade de Participantes Walk & Talk',array('controller' => 'swt', 'action' => 'index_qtd_participantes'), array('wrapper'=>'li'));
$menusCadastro  .= $this->BMenu->link('Configuração Walk & Talk',array('controller' => 'clientes', 'action' => 'configuracao_swt'), array('wrapper'=>'li'));
$menusCadastro  .= $this->BMenu->link('Formulários Dinâmicos Walk & Talk',array('controller' => 'swt', 'action' => 'index_form'), array('wrapper'=>'li'));
$menusCadastro  .= $this->BMenu->link('Metas da Área',array('controller' => 'swt', 'action' => 'index_metas'), array('wrapper'=>'li'));

//consultas
$menusConsultas  .= $this->BMenu->link('Relatório de Walk & Talk',array('controller' => 'swt', 'action' => 'relatorio_swt'), array('wrapper'=>'li'));
$menusConsultas  .= $this->BMenu->link('Relatório de Análises de Walk & Talk',array('controller' => 'swt', 'action' => 'relatorio_analise_swt'), array('wrapper'=>'li'));

?>
<?php if (!isset($terceiros)): ?>
	<ul class="sf-menu menu-admin">
<?php endif; ?>
	<?php $diff_name = isset($terceiros) ? ' Terceiros' : '' ?>
	<?php if (!empty($menusCadastro)): ?>
		<li><?php echo $this->Html->link('Cadastro '.$diff_name,'javascript:void(0)'); ?>
			<ul>
				<?php echo $menusCadastro ?>
			</ul>
		</li>
	<?php endif; ?>
	<?php if (!empty($menusOperacoes)): ?>
		<li><?php echo $this->Html->link('Operação '.$diff_name,'javascript:void(0)'); ?>
			<ul>
				<?php echo $menusOperacoes ?>
			</ul>
		</li>
	<?php endif; ?>
	<?php if (!empty($menusConsultas)): ?>
		<li><?php echo $this->Html->link('Consulta '.$diff_name,'javascript:void(0)'); ?>
			<ul>
				<?php echo $menusConsultas ?>
			</ul>
		</li>
	<?php endif; ?>
<?php if (!isset($terceiros)): ?>
	</ul>
<?php endif; ?>
