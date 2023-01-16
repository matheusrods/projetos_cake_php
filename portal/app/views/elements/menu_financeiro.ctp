<?php  
$menusCadastro = '';
$menusOperacoes = '';
$subMenusFaturamento = '';
$subMenuConsultaFaturamento = '';
$subMenuConsultaRelatorio = '';
$subMenuDemonstrativos = '';
$menusConsultas = '';

//migrado contas medicas - $menusCadastro .= $this->BMenu->link('Consolidação Nota Fiscal x Exames', array('controller' => 'notas_fiscais_servico', 'action' => 'consolida_nfs_exame'), array('wrapper'=>'li'));
$menusCadastro .= $this->BMenu->link('Exceções Fórmulas Naveg', array('controller' => 'excecoes_formulas', 'action' => 'index'), array('wrapper'=>'li'));
//migrado contas medicas - $menusCadastro .= $this->BMenu->link('Nota Fiscal', array('controller' => 'notas_fiscais_servico', 'action' => 'index'), array('wrapper'=>'li'));

$subMenusFaturamento .= $this->BMenu->link('Enviar faturamento para clientes', array('controller' => 'clientes', 'action' => 'enviar_fatura'), array('wrapper'=>'li'));
if (!empty($subMenusFaturamento)): 
	$menusOperacoes .= "<li>".$this->Html->link('Faturamento','javascript:void(0)'); 
    $menusOperacoes .= "<ul>".$subMenusFaturamento."</ul>";
    $menusOperacoes .= "</li>";
endif;

$menusOperacoes   .= $this->BMenu->link('Concessão de descontos', array('controller' => 'clientes_produtos_descontos', 'action' => 'index'), array('wrapper'=>'li'));

$menusOperacoes   .= $this->BMenu->link('Configuração Cliente Validador', array('controller' => 'clientes', 'action' => 'config_cliente_validador'), array('wrapper'=>'li'));
$menusOperacoes   .= $this->BMenu->link('Gestão Pré Faturamento', array('controller' => 'pre_faturamento', 'action' => 'gestao'), array('wrapper'=>'li'));

$menusOperacoes   .= $this->BMenu->link('Integração Faturamento', array('controller' => 'itens_pedidos', 'action' => 'integracao'), array('wrapper'=>'li'));
$menusOperacoes   .= $this->BMenu->link('Integração Manual', array('controller' => 'itens_pedidos', 'action' => 'pedidos_nao_integrados'), array('wrapper'=>'li'));
$menusOperacoes   .= $this->BMenu->link('Remessa/Retorno Bancário', array('controller' => 'remessa_bancaria', 'action' => 'index'), array('wrapper'=>'li'));

$menusConsultas .= $this->BMenu->link('Clientes',array('controller'=>'clientes','action'=>'visualizar_clientes'), array('wrapper'=>'li'));
$menusConsultas .= $this->BMenu->link('Exames a Faturar',array('controller'=>'pedidos_exames','action'=>'relatorio_faturamento'), array('wrapper'=>'li'));  
$menusConsultas .= $this->BMenu->link('Faturamento Per Capita',array('controller'=>'CtrPreFatPerCapita','action'=>'index'), array('wrapper'=>'li'));  
$menusConsultas .= $this->BMenu->link('Títulos pagos por Centro de Custo',array('controller'=>'pagamentos_transacoes','action'=>'listar_titulos_pagos'), array('wrapper'=>'li'));  
$subMenuConsultaFaturamento .= $this->BMenu->link('Faturamento Anual',array('controller'=>'itens_notas_fiscais','action'=>'comparativo_anual'), array('wrapper'=>'li'));
$subMenuConsultaFaturamento .= $this->BMenu->link('Faturamento por Empresa',array('controller'=>'itens_notas_fiscais','action'=>'por_empresa'), array('wrapper'=>'li'));
if (!empty($subMenuConsultaFaturamento)): 
	$menusConsultas .= "<li>".$this->Html->link('Faturamento','javascript:void(0)'); 
        $menusConsultas .= "<ul>".$subMenuConsultaFaturamento."</ul>";
    $menusConsultas .= "</li>";
endif;

//indices do menu de relatorio
$subMenuConsultaRelatorio .= $this->BMenu->link('Demonstrativo Contas Médicas',array('controller'=>'notas_fiscais_servico','action'=>'demonstrativo_contas_medicas'), array('wrapper'=>'li'));
$subMenuConsultaRelatorio .= $this->BMenu->link('Exames',array('controller'=>'exames','action'=>'relatorio_exames'), array('wrapper'=>'li'));
$subMenuConsultaRelatorio .= $this->BMenu->link('Exames sem NFS',array('controller'=>'notas_fiscais_servico','action'=>'relatorio_exames_sem_nfs'), array('wrapper'=>'li'));
$subMenuConsultaRelatorio .= $this->BMenu->link('Relatório Glosas',array('controller'=>'notas_fiscais_servico','action'=>'relatorio_glosas'), array('wrapper'=>'li'));
//verifica se existe o menu de relatorio
if (!empty($subMenuConsultaRelatorio)): 
	$menusConsultas .= "<li>".$this->Html->link('Relatório','javascript:void(0)'); 
        $menusConsultas .= "<ul>".$subMenuConsultaRelatorio."</ul>";
    $menusConsultas .= "</li>";
endif;
?>

<ul class="sf-menu menu-admin">
	<?php if (!empty($menusCadastro)): ?>
		<li><?php echo $this->Html->link('Cadastro','javascript:void(0)'); ?>
			<ul>
				<?php echo $menusCadastro ?>
			</ul>
		</li>
	<?php endif; ?>
	<?php if (!empty($menusOperacoes)): ?>
		<li><?php echo $this->Html->link('Operação','javascript:void(0)'); ?>
			<ul>
				<?php echo $menusOperacoes ?>
			</ul>
		</li>
	<?php endif; ?>
	<?php if (!empty($menusConsultas)): ?>
		<li><?php echo $this->Html->link('Consulta','javascript:void(0)'); ?>
			<ul>
				<?php echo $menusConsultas ?>
			</ul>
		</li>
	<?php endif; ?>
	<?php echo $this->element('menu_terceiros_financeiro', array('terceiros' => true)); ?>
</ul>
