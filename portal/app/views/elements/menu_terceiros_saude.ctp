<?php
$menusCadastro    = '';
$menusOperacoes   = '';
$menusConsultas   = '';
$subMenusSaude  = '';

//cadastro
$menusCadastro .= $this->BMenu->link('Aparelhos Audiométricos', array('controller' => 'cliente_aparelho_audiometrico', 'action' => 'index'), array('wrapper' => 'li'));
$menusCadastro .= $this->BMenu->link('Hospitais de Emergência', array('controller' => 'hospitais_emergencia', 'action' => 'index'), array('wrapper' => 'li'));
$menusCadastro .= $this->BMenu->link('Materiais de Pronto Socorro', array('controller' => 'pmps', 'action' => 'index'), array('wrapper' => 'li'));

//operacoes
$menusOperacoes  .= $this->BMenu->link('Audiometria', array('controller' => 'audiometrias', 'action' => 'index'), array('wrapper' => 'li'));
$menusOperacoes  .= $this->BMenu->link('Absenteísmo', array('controller' => 'atestados', 'action' => 'index'), array('wrapper' => 'li'));
$menusOperacoes  .= $this->BMenu->link('Baixa de Pedidos', array('controller' => 'itens_pedidos_exames_baixa', 'action' => 'index'), array('wrapper' => 'li'));
$menusOperacoes  .= $this->BMenu->link('Exames Agendados', array('controller' => 'consultas_agendas', 'action' => 'index2'), array('wrapper' => 'li'));
$menusOperacoes  .= $this->BMenu->link('Emissão de Pedidos', array('controller' => 'clientes_funcionarios', 'action' => 'selecao_funcionarios'), array('wrapper' => 'li'));
$menusOperacoes  .= $this->BMenu->link('Ficha Clínica', array('controller' => 'fichas_clinicas', 'action' => 'index'), array('wrapper' => 'li'));
$menusOperacoes  .= $this->BMenu->link('Ficha Assistencial', array('controller' => 'fichas_assistenciais', 'action' => 'index'), array('wrapper' => 'li'));
$menusOperacoes  .= $this->BMenu->link('Ficha Psicossocial', array('controller' => 'ficha_psicossocial', 'action' => 'index'), array('wrapper' => 'li'));
$menusOperacoes  .= $this->BMenu->link('Manutenção Pedido', array('controller' => 'importar', 'action' => 'manutencao_pedido_exame'), array('wrapper' => 'li'));
$menusOperacoes	 .= $this->BMenu->link('PCMSO', array('controller' => 'clientes_implantacao', 'action' => 'index_pcmso_ext'), array('wrapper' => 'li'));
$menusOperacoes	 .= $this->BMenu->link('Gestão Cronogramas', array('controller' => 'clientes_implantacao', 'action' => 'gestao_cronograma_pcmso'), array('wrapper' => 'li'));

//consultas
$menusConsultas   .= $this->BMenu->link('Absenteísmo Sintético', array('controller' => 'atestados', 'action' => 'sintetico'), array('wrapper' => 'li'));
$menusConsultas   .= $this->BMenu->link('Corpo Clínico', array('controller' => 'medicos', 'action' => 'corpo_clinico'), array('wrapper' => 'li'));
$menusConsultas   .= $this->BMenu->link('Dashboard Absenteísmo Sintético', array('controller' => 'painel', 'action' => 'dashboard_absenteismo_sintetico'), array('wrapper' => 'li'));
$menusConsultas   .= $this->BMenu->link('Dashboard Exames Agendados', array('controller' => 'painel', 'action' => 'dashboard_exames_agendados'), array('wrapper' => 'li'));
$menusConsultas   .= $this->BMenu->link('Exames Agendados', array('controller' => 'consultas_agendas', 'action' => 'index'), array('wrapper' => 'li'));
$menusConsultas   .= $this->BMenu->link('Exames Baixados', array('controller' => 'consulta_pedidos_exames', 'action' => 'baixa_exames_sintetico'), array('wrapper' => 'li'));
$menusConsultas   .= $this->BMenu->link('Ficha Psicossocial', array('controller' => 'ficha_psicossocial', 'action' => 'ficha_psicossocial_terceiros'), array('wrapper' => 'li'));
$menusConsultas   .= $this->BMenu->link('Laudo Caracterizador de Deficiência', array('controller' => 'fichas_pcd', 'action' => 'index'), array('wrapper' => 'li'));
$menusConsultas   .= $this->BMenu->link('PPP', array('controller' => 'clientes', 'action' => 'funcionarios_ppp'), array('wrapper' => 'li'));
$menusConsultas   .= $this->BMenu->link('Posição de Exames', array('controller' => 'exames', 'action' => 'posicao_exames_sintetico'), array('wrapper' => 'li'));
$menusConsultas   .= $this->BMenu->link('Posição de Exames Analítico', array('controller' => 'exames', 'action' => 'posicao_exames_analitico2'), array('wrapper' => 'li'));
$menusConsultas   .= $this->BMenu->link('Relatório Anual', array('controller' => 'exames', 'action' => 'relatorio_anual'), array('wrapper' => 'li'));
$menusConsultas   .= $this->BMenu->link('Relatório Ficha Clínica', array('controller' => 'fichas_clinicas', 'action' => 'fichas_clinicas_terceiros'), array('wrapper' => 'li'));
$menusConsultas   .= $this->BMenu->link('Resultados de exames', array('controller' => 'consulta_pedidos_exames', 'action' => 'resultado_de_exames'), array('wrapper' => 'li'));
$menusConsultas   .= $this->BMenu->link('Versões PCMSO', array('controller' => 'pcmso_versoes', 'action' => 'versoes_pcmso'), array('wrapper' => 'li'));
?>
<?php if (!isset($terceiros)) : ?>
	<ul class="sf-menu menu-admin">
	<?php endif; ?>
	<?php $diff_name = isset($terceiros) ? ' Terceiros' : '' ?>
	<?php if (!empty($menusCadastro)) : ?>
		<li><?php echo $this->Html->link('Cadastro ' . $diff_name, 'javascript:void(0)'); ?>
			<ul>
				<?php echo $menusCadastro ?>
			</ul>
		</li>
	<?php endif; ?>
	<?php if (!empty($menusOperacoes)) : ?>
		<li><?php echo $this->Html->link('Operação ' . $diff_name, 'javascript:void(0)'); ?>
			<ul>
				<?php echo $menusOperacoes ?>
			</ul>
		</li>
	<?php endif; ?>
	<?php if (!empty($menusConsultas)) : ?>
		<li><?php echo $this->Html->link('Consulta ' . $diff_name, 'javascript:void(0)'); ?>
			<ul>
				<?php echo $menusConsultas ?>
			</ul>
		</li>
	<?php endif; ?>
	<?php if (!isset($terceiros)) : ?>
	</ul>
<?php endif; ?>