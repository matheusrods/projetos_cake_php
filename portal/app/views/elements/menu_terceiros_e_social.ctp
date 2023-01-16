<?php
$menusCadastro    = '';
$menusOperacoes   = '';
$menusConsultas   = '';
$subMenusSaude  = '';

//cadastro
$menusCadastro  .= $this->BMenu->link('Certificado Digital',array('controller' => 'MensageriaEsocial', 'action' => 'index_certificado'), array('wrapper'=>'li'));

//operacoes
$menusOperacoes  .= $this->BMenu->link('Tabela S-2210',array('controller' => 'esocial', 'action' => 's2210'), array('wrapper'=>'li'));
$menusOperacoes  .= $this->BMenu->link('Tabela S-2220',array('controller' => 'esocial', 'action' => 's2220'), array('wrapper'=>'li'));
$menusOperacoes  .= $this->BMenu->link('Tabela S-2221',array('controller' => 'esocial', 'action' => 's2221'), array('wrapper'=>'li'));
$menusOperacoes  .= $this->BMenu->link('Tabela S-2230',array('controller' => 'esocial', 'action' => 's2230'), array('wrapper'=>'li'));
$menusOperacoes  .= $this->BMenu->link('Tabela S-2240',array('controller' => 'esocial', 'action' => 's2240'), array('wrapper'=>'li'));
//consultas
$menusConsultas  .= $this->BMenu->link('Consulta Inconsistências',array('controller' => 'esocial', 'action' => 'relatorio_inconsistencias'), array('wrapper'=>'li'));
$menusConsultas  .= $this->BMenu->link('Integração Eventos',array('controller' => 'MensageriaEsocial', 'action' => 'index_eventos'), array('wrapper'=>'li'));
$menusConsultas  .= $this->BMenu->link('Logs Integrações Esocial',array('controller' => 'logs_integracoes', 'action' => 'integracao_esocial'), array('wrapper'=>'li'));
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