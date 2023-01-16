<?php
$menusCadastro    = '';
$menusOperacoes   = '';
$menusConsultas   = '';

//operacao
$menusOperacoes  .= $this->BMenu->link('Gestão Covid',array('controller' => 'usuario_grupo_covid', 'action' => 'index'), array('wrapper'=>'li'));
$menusOperacoes  .= $this->BMenu->link('Gestão Funcionários',array('controller' => 'funcionarios', 'action' => 'index_funcionario_liberacao'), array('wrapper'=>'li'));

//consultas
$menusConsultas  .= $this->BMenu->link('Mapa Covid-19 Brasil',array('controller' => 'covid', 'action' => 'brasil_io'), array('wrapper'=>'li'));
$menusConsultas  .= $this->BMenu->link('Dash. Lyn',array('controller' => 'covid', 'action' => 'lyn'), array('wrapper'=>'li'));
$menusConsultas  .= $this->BMenu->link('Dash. Lyn RH',array('controller' => 'covid', 'action' => 'lyn_rh'), array('wrapper'=>'li'));
$menusConsultas  .= $this->BMenu->link('Resultado de Exame',array('controller' => 'covid', 'action' => 'resultado_exame_sintetico'), array('wrapper'=>'li'));

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