<?php
$menusCadastro    = '';
$menusOperacoes   = '';
$menusConsultas   = '';

//cadastros
$menusCadastro  .= $this->BMenu->link('Tipos de Observação',array('controller' => 'pos_categorias', 'action' => 'buscar_clientes'), array('wrapper'=>'li'));
$menusCadastro  .= $this->BMenu->link('Locais de Observação',array('controller' => 'pos_obs_local', 'action' => 'index'), array('wrapper'=>'li'));

$menusConsultas  .= $this->BMenu->link('Relatório de Observações Realizadas',array('controller' => 'pos_obs_relatorio_realizadas', 'action' => 'index'), array('wrapper'=>'li'));
$menusConsultas  .= $this->BMenu->link('Relatório de Análises de Qualidade',array('controller' => 'pos_obs_relatorio_analise_qualidade', 'action' => 'index'), array('wrapper'=>'li'));

$menusOperacoes  .= $this->BMenu->link('Configurações Observador EHS',array('controller' => 'clientes', 'action' => 'configuracao_obs'), array('wrapper'=>'li'));

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
