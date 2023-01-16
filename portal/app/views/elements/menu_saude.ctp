<?php
$menusCadastro    = '';
$menusOperacoes   = '';
$menusConsultas   = '';
$subMenusSaude  = '';

//SUB MENU. ITEM: SAUDE
$menusCadastro   .= $this->BMenu->link('Aparelho Audiométrico', array('controller' => 'aparelhos_audiometricos', 'action' => 'index'), array('wrapper' => 'li'));

// $menusCadastro   .= $this->BMenu->link('Atribuição',array('controller' => 'atribuicoes', 'action' => 'index'), array('wrapper'=>'li'));
$menusCadastro   .= $this->BMenu->link('Atribuição e Exame', array('controller' => 'atribuicoes_exames', 'action' => 'index'), array('wrapper' => 'li'));
// $menusCadastro   .= $this->BMenu->link('Aplicação de Exames',array('controller' => 'aplicacao_exames', 'action' => 'index'), array('wrapper'=>'li'));
$menusCadastro   .= $this->BMenu->link('CID', array('controller' => 'cid', 'action' => 'index'), array('wrapper' => 'li'));
$menusCadastro   .= $this->BMenu->link('CID e CNAE', array('controller' => 'cid_cnae', 'action' => 'index'), array('wrapper' => 'li'));
$menusCadastro   .= $this->BMenu->link('Decreto para deficiência', array('controller' => 'decretos_deficiencia', 'action' => 'index'), array('wrapper' => 'li'));
$menusCadastro   .= $this->BMenu->link('Especialidade', array('controller' => 'especialidades', 'action' => 'index'), array('wrapper' => 'li'));
$menusCadastro   .= $this->BMenu->link('Exame', array('controller' => 'exames', 'action' => 'index'), array('wrapper' => 'li'));
$menusCadastro   .= $this->BMenu->link('Exame e Função', array('controller' => 'exames_funcoes', 'action' => 'index'), array('wrapper' => 'li'));
$menusCadastro   .= $this->BMenu->link('Função', array('controller' => 'funcoes', 'action' => 'index'), array('wrapper' => 'li'));
$menusCadastro	 .= $this->BMenu->link('Grupos de Exame', array('controller' => 'detalhes_grupos_exames', 'action' => 'busca_por_cliente'), array('wrapper' => 'li'));
$menusCadastro   .= $this->BMenu->link('Laboratório', array('controller' => 'laboratorios', 'action' => 'index'), array('wrapper' => 'li'));
$menusCadastro   .= $this->BMenu->link('Medicamento', array('controller' => 'medicamentos', 'action' => 'index'), array('wrapper' => 'li'));
$menusCadastro   .= $this->BMenu->link('Motivo Cancelamento de Pedidos', array('controller' => 'motivos_cancelamentos', 'action' => 'index'), array('wrapper' => 'li'));
$menusCadastro   .= $this->BMenu->link('Motivo licença médica', array('controller' => 'motivos_afastamento', 'action' => 'index'), array('wrapper' => 'li'));
$menusCadastro   .= $this->BMenu->link('Motivo Recusa Exames', array('controller' => 'motivos_recusa', 'action' => 'exames_index'), array('wrapper' => 'li'));
$menusCadastro   .= $this->BMenu->link('Regras Exames por Risco', array('controller' => 'riscos_exames', 'action' => 'index'), array('wrapper' => 'li'));
$menusCadastro   .= $this->BMenu->link('Tipos de deficiência', array('controller' => 'tipos_deficiencia', 'action' => 'index'), array('wrapper' => 'li'));
$menusCadastro   .= $this->BMenu->link('Tipos de licença médica', array('controller' => 'tipos_afastamento', 'action' => 'index'), array('wrapper' => 'li'));

if (!empty($subMenusSaude)) :
	$menusCadastro .= "<li>" . $this->Html->link('Saúde', 'javascript:void(0)');
	$menusCadastro .= "<ul>" . $subMenusSaude . "</ul>";
	$menusCadastro .= "</li>";
endif;

$menusOperacoes .= $this->BMenu->link('Agendar Sugestões', array('controller' => 'agendamento', 'action' => 'index'), array('wrapper' => 'li'));
//$menusOperacoes .= $this->BMenu->link('Baixa de Pedidos', array('controller' => 'itens_pedidos_exames_baixa', 'action' => 'index'), array('wrapper'=>'li'));
$menusOperacoes .= $this->BMenu->link('Fila de Agendamento', array('controller' => 'agendamento', 'action' => 'fila'), array('wrapper' => 'li'));
$menusOperacoes .= $this->BMenu->link('Moderação Exames Digitalizados', array('controller' => 'consultas_agendas', 'action' => 'moderacao_anexos'), array('wrapper' => 'li'));
$menusOperacoes .= $this->BMenu->link('PCMSO', array('controller' => 'clientes_implantacao', 'action' => 'index_pcmso'), array('wrapper' => 'li'));

//Saúde -> Consultas → Dashboard Exames Agendados.
$menusConsultas   .= $this->BMenu->link('Dashboard Absenteísmo Sintético', array('controller' => 'painel', 'action' => 'dashboard_absenteismo_sintetico'), array('wrapper' => 'li'));
$menusConsultas   .= $this->BMenu->link('Dashboard Exames Agendados', array('controller' => 'painel', 'action' => 'dashboard_exames_agendados'), array('wrapper' => 'li'));
$menusConsultas   .= $this->BMenu->link('Laudo Caracterizador de Deficiência', array('controller' => 'clientes', 'action' => 'laudo_pcd'), array('wrapper' => 'li'));
$menusConsultas   .= $this->BMenu->link('Pedidos de Exames Emitidos', array('controller' => 'pedidos_exames', 'action' => 'pedidos_exames_emitidos'), array('wrapper' => 'li'));
$menusConsultas   .= $this->BMenu->link('Resultado de Exames', array('controller' => 'resultados_exames', 'action' => 'index'), array('wrapper' => 'li'));

?>
<ul class="sf-menu menu-admin">
	<?php if (!empty($menusCadastro)) : ?>
		<li><?php echo $this->Html->link('Cadastro', 'javascript:void(0)'); ?>
			<ul>
				<?php echo $menusCadastro ?>
			</ul>
		</li>
	<?php endif; ?>
	<?php if (!empty($menusOperacoes)) : ?>
		<li><?php echo $this->Html->link('Operação', 'javascript:void(0)'); ?>
			<ul>
				<?php echo $menusOperacoes ?>
			</ul>
		</li>
	<?php endif; ?>
	<?php if (!empty($menusConsultas)) : ?>
		<li><?php echo $this->Html->link('Consulta', 'javascript:void(0)'); ?>
			<ul>
				<?php echo $menusConsultas ?>
			</ul>
		</li>
	<?php endif; ?>
	<?php echo $this->element('menu_terceiros_saude', array('terceiros' => true)); ?>
</ul>