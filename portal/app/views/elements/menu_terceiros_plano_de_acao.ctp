<?php
$menusCadastro    = '';
$menusOperacoes   = '';
$menusConsultas   = '';

//cadastros
$menusCadastro .= $this->BMenu->link('Ações melhorias tipo', array('controller' => 'acoes_melhorias_tipo', 'action' => 'index'), array('wrapper'=>'li'));
$menusCadastro .= $this->BMenu->link('Área de atuação', array('controller' => 'area_atuacao', 'action' => 'index'), array('wrapper'=>'li'));
$menusCadastro .= $this->BMenu->link('Configuração da ação', array('controller' => 'clientes', 'action' => 'regras_acao'), array('wrapper'=>'li'));
$menusCadastro .= $this->BMenu->link('Criticidades', array('controller' => 'clientes', 'action' => 'config_criticidade'), array('wrapper'=>'li'));
$menusCadastro .= $this->BMenu->link('Matriz de responsabilidade', array('controller' => 'clientes', 'action' => 'matriz_responsabilidade'), array('wrapper'=>'li'));
$menusCadastro .= $this->BMenu->link('Origens', array('controller' => 'origem_ferramenta', 'action' => 'index'), array('wrapper'=>'li'));
$menusCadastro .= $this->BMenu->link('Regras da Ação ', array('controller' => 'pda_config_regra', 'action' => 'index_pda_regra'), array('wrapper'=>'li'));
$menusCadastro .= $this->BMenu->link('Subperfil', array('controller' => 'subperfil', 'action' => 'index'), array('wrapper'=>'li'));
$menusCadastro .= $this->BMenu->link('Usuários', array('controller' => 'clientes', 'action' => 'usuarios'), array('wrapper'=>'li'));

//consultas
$menusOperacoes .= $this->BMenu->link('Ações Cadastradas', array('controller' => 'clientes', 'action' => 'acoes_cadastradas'), array('wrapper'=>'li'));

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
