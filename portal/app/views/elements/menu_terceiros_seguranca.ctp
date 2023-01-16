<?php
$menusCadastro    	= '';
$menusOperacoes   	= '';
$menusConsultas   	= '';
$subMenusSeguranca  = '';

//operacao
$menusOperacoes	 .= $this->BMenu->link('Agentes de Risco', array('controller'=>'agentes_riscos', 'action'=>'index'), array('wrapper'=>'li'));
$menusOperacoes  .= $this->BMenu->link('CAT', array('controller'=>'cat', 'action'=>'index'), array('wrapper'=>'li'));
$menusOperacoes	 .= $this->BMenu->link('Chamados', array('controller'=>'chamados', 'action'=>'index'), array('wrapper'=>'li'));
$menusOperacoes	 .= $this->BMenu->link('Configurações', array('controller'=>'clientes', 'action'=>'visualizar_clientes_gestao_de_risco'), array('wrapper'=>'li'));
$menusOperacoes	 .= $this->BMenu->link('Gestão Cronogramas', array('controller'=>'clientes_implantacao', 'action'=>'gestao_cronograma_ppra'), array('wrapper'=>'li'));
$menusOperacoes  .= $this->BMenu->link('GHE', array('controller'=>'ghe', 'action'=>'index'), array('wrapper'=>'li'));
$menusOperacoes	 .= $this->BMenu->link('Perigos Aspectos', array('controller'=>'perigos_aspectos', 'action'=>'index'), array('wrapper'=>'li'));
$menusOperacoes	 .= $this->BMenu->link('PGR', array('controller'=>'clientes_implantacao', 'action'=>'index_ppra_ext'), array('wrapper'=>'li'));
$menusOperacoes	 .= $this->BMenu->link('Processos', array('controller'=>'processos', 'action'=>'index'), array('wrapper'=>'li'));
$menusOperacoes	 .= $this->BMenu->link('Riscos Impactos', array('controller'=>'riscos_impactos', 'action'=>'index'), array('wrapper'=>'li'));
$menusOperacoes  .= $this->BMenu->link('Riscos Tipo', array('controller'=>'riscos_tipos', 'action'=>'index'), array('wrapper'=>'li'));
$menusOperacoes	 .= $this->BMenu->link('Unidades de Medida', array('controller'=>'unidades_medicao', 'action'=>'index'), array('wrapper'=>'li'));

//consultas
$menusConsultas   .= $this->BMenu->link('Versões PGR',array('controller' => 'ppra_versoes', 'action' => 'versoes_ppra'), array('wrapper'=>'li'));
$menusConsultas   .= $this->BMenu->link('Dashboard Riscos Engenharia', array('controller' => 'painel', 'action' => 'dashboard_riscos_engenharia'), array('wrapper' => 'li'));


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
