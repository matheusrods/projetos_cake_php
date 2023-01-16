<?php
$menusCadastro    = '';
$menusOperacoes   = '';
$menusConsultas   = '';
$subMenusMapeamentoRisco  = '';

$subMenusMapeamentoRisco .= $this->BMenu->link('Colaboradores e Atestados', array('controller' => 'dados_saude_consultas', 'action' => 'dashboard', 'colaboradores_atestados'), array('wrapper'=>'li'));
$subMenusMapeamentoRisco .= $this->BMenu->link('Dados Gerais', array('controller' => 'dados_saude_consultas', 'action' => 'dashboard', 'dados_gerais'), array('wrapper'=>'li'));
if (!empty($subMenusMapeamentoRisco)): 
	$menusConsultas .= "<li>".$this->Html->link('Dashboard','javascript:void(0)'); 
    $menusConsultas .= "<ul>".$subMenusMapeamentoRisco."</ul>";
    $menusConsultas .= "</li>";
endif;

$menusConsultas   .= $this->BMenu->link('Faixa Etária', array('controller' => 'dados_saude_consultas', 'action' => 'relatorio_faixa_etaria'), array('wrapper'=>'li'));
$menusConsultas   .= $this->BMenu->link('Fatores de Risco', array('controller' => 'dados_saude_consultas', 'action' => 'relatorio_fatores_risco'), array('wrapper'=>'li'));
$menusConsultas   .= $this->BMenu->link('IMC', array('controller' => 'dados_saude_consultas', 'action' => 'relatorio_imc'), array('wrapper'=>'li'));
$menusConsultas   .= $this->BMenu->link('Percentual (Homens / Mulheres)', array('controller' => 'dados_saude_consultas', 'action' => 'relatorio_genero'), array('wrapper'=>'li'));
$menusConsultas   .= $this->BMenu->link('Posição de Preenchimento Questionários', array('controller' => 'dados_saude_consultas', 'action' => 'relatorio_posicao_questionarios'), array('wrapper'=>'li'));
?>
<?php if (!isset($terceiros)): ?>
	<ul class="sf-menu menu-admin">
<?php endif; ?>
	<?php $diff_name = isset($terceiros) ? ' Terceiros' : ''; ?>
	<?php if (!empty($menusCadastro)): ?>
		<li><?php echo $this->Html->link('Cadastros'.$diff_name,'javascript:void(0)'); ?>
			<ul>
				<?php echo $menusCadastro ?>
			</ul>
		</li>
	<?php endif; ?>
	<?php if (!empty($menusOperacoes)): ?>
		<li><?php echo $this->Html->link('Operações'.$diff_name,'javascript:void(0)'); ?>
			<ul>
				<?php echo $menusOperacoes ?>
			</ul>
		</li>
	<?php endif; ?>	
	<?php if (!empty($menusConsultas)): ?>
		<li><?php echo $this->Html->link('Consultas'.$diff_name,'javascript:void(0)'); ?>
			<ul>
				<?php echo $menusConsultas ?>
			</ul>
		</li>
	<?php endif; ?>	
<?php if (!isset($terceiros)): ?>
	</ul>
<?php endif; ?>
