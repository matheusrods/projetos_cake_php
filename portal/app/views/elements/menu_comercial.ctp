<?php
$menusCadastro    = '';
$menusOperacoes   = '';
$menusConsultas   = '';
$subMenusProdutos = '';
$subMenusGruposEconomicos = '';
$subMenusClientes = '';
$subMenusUsuarios = '';


//SUB MENU. ITEM: CLIENTES
$menusCadastro .= $this->BMenu->link('Atribuição',array('controller' => 'atribuicoes', 'action' => 'index'), array('wrapper'=>'li'));
$menusCadastro .= $this->BMenu->link('Clientes',array('controller'=>'clientes','action'=>'index'), array('wrapper'=>'li'));
$menusCadastro .= $this->BMenu->link('CNAE',array('controller'=>'cnae','action'=>'index'), array('wrapper'=>'li'));
$menusCadastro .= $this->BMenu->link('Corretoras',array('controller'=>'Corretoras','action'=>'index'), array('wrapper'=>'li'));
$menusCadastro .= $this->BMenu->link('Endereços',array('controller'=>'Enderecos','action'=>'index'), array('wrapper'=>'li'));
$menusCadastro .= $this->BMenu->link('Formas de Pagamento',array('controller'=>'formas_pagto','action'=>'index'), array('wrapper'=>'li'));
$menusCadastro .= $this->BMenu->link('Planos de Saúde',array('controller'=>'PlanosDeSaude','action'=>'index'), array('wrapper'=>'li'));
$menusCadastro .= $this->BMenu->link('Planos',array('controller'=>'servicos_planos_saude','action'=>'listar_planos_saude'), array('wrapper'=>'li'));
$menusCadastro .= $this->BMenu->link('Tipos Ações', array('controller'=>'tipos_acoes','action'=>'index'), array('wrapper'=>'li'));
$menusCadastro .= $this->BMenu->link('Tipo Digitalização', array('controller'=>'tipo_digitalizacao','action'=>'index'), array('wrapper'=>'li'));
$menusCadastro .= $this->BMenu->link('Vendedores',array('controller'=>'vendedores','action'=>'index'), array('wrapper'=>'li'));

//SUB MENU. ITEM: PRODUTOS
$subMenusProdutos .= $this->BMenu->link('Listas de Preço',array('controller' => 'listas_de_preco', 'action' => 'index'), array('wrapper'=>'li'));
$subMenusProdutos .= $this->BMenu->link('Produtos',array('controller'=>'produtos','action'=>'index'), array('wrapper'=>'li'));
$subMenusProdutos .= $this->BMenu->link('Serviços',array('controller'=>'servicos','action'=>'index'), array('wrapper'=>'li'));

if (!empty($subMenusProdutos)):
	$menusCadastro .= "<li>".$this->Html->link('Produtos','javascript:void(0)');
        $menusCadastro .= "<ul>".$subMenusProdutos."</ul>";
    $menusCadastro .= "</li>";
endif;

//SUB MENU. ITEM: RELAÇÕES ENTRE CLIENTES
$subMenusGruposEconomicos   .= $this->BMenu->link('Grupos Econômicos',array('controller' => 'grupos_economicos', 'action' => 'index'), array('wrapper'=>'li'));
$subMenusGruposEconomicos   .= $this->BMenu->link('Matrizes e Filiais',array('controller' => 'MatrizesFiliais', 'action' => 'index'), array('wrapper'=>'li'));

if (!empty($subMenusGruposEconomicos)):
	$menusCadastro .= "<li>".$this->Html->link('Relações entre Clientes','javascript:void(0)');
	$menusCadastro .= "<ul>".$subMenusGruposEconomicos."</ul>";
	$menusCadastro .= "</li>";
endif;

$subMenusUsuarios   .= $this->BMenu->link('Recuperar Senha',array('controller'=>'usuarios','action'=>'recuperar_senha'), array('wrapper'=>'li'));
$subMenusUsuarios   .= $this->BMenu->link('Usuários de Clientes',array('controller' => 'clientes', 'action' => 'usuarios'), array('wrapper'=>'li'));
$subMenusUsuarios   .= $this->BMenu->link('Usuários de Fornecedores',array('controller' => 'fornecedores', 'action' => 'usuarios'), array('wrapper'=>'li'));

if (!empty($subMenusUsuarios)):
	$menusCadastro .= "<li>".$this->Html->link('Usuários','javascript:void(0)');
        $menusCadastro .= "<ul>".$subMenusUsuarios."</ul>";
    $menusCadastro .= "</li>";
endif;

$subMenuClientes = "";
$subMenuClientes  .= $this->BMenu->link('Informações',array('controller'=>'clientes','action'=>'visualizar_clientes'), array('wrapper'=>'li'));
$subMenuClientes  .= $this->BMenu->link('Por data',array('controller'=>'clientes','action'=>'clientes_data_cadastro'), array('wrapper'=>'li'));
if (!empty($subMenuClientes)): 
	$menusConsultas .= "<li>".$this->Html->link('Clientes','javascript:void(0)'); 
        $menusConsultas .= "<ul>".$subMenuClientes."</ul>";
    $menusConsultas .= "</li>";
endif; 

$menusConsultas   .= $this->BMenu->link('Clientes sem Exames Contratados',array('controller' => 'clientes_sem_exames', 'action' => 'index'), array('wrapper'=>'li'));

$menusConsultas   .= $this->BMenu->link('Confirmação Validação Per Capita',array('controller' => 'funcionarios', 'action' => 'index_confirmacao_percapita'), array('wrapper'=>'li'));

$subMenuEstatisticas = "";
$subMenuEstatisticas  .= $this->BMenu->link('Clientes',array('controller'=>'clientes','action'=>'estatistica_clientes'), array('wrapper'=>'li'));
if (!empty($subMenuEstatisticas)): 
	$menusConsultas .= "<li>".$this->Html->link('Estatísticas','javascript:void(0)'); 
        $menusConsultas .= "<ul>".$subMenuEstatisticas."</ul>";
    $menusConsultas .= "</li>";
endif; 

$menusConsultas   .= $this->BMenu->link('Informações da Empresa',array('controller' => 'exames', 'action' => 'informacao_empresa'), array('wrapper'=>'li'));

$subMenuRanking = "";
$subMenuRanking  .= $this->BMenu->link('Clientes',array('controller'=>'notas_fiscais','action'=>'ranking_faturamento'), array('wrapper'=>'li'));
$subMenuRanking  .= $this->BMenu->link('Produtos',array('controller'=>'itens_notas_fiscais','action'=>'por_produto'), array('wrapper'=>'li'));

if (!empty($subMenuRanking)): 
	$menusConsultas .= "<li>".$this->Html->link('Ranking','javascript:void(0)'); 
        $menusConsultas .= "<ul>".$subMenuRanking."</ul>";
    $menusConsultas .= "</li>";
endif; 
$menusConsultas   .= $this->BMenu->link('Vidas',array('controller' => 'clientes_funcionarios', 'action' => 'vidas'), array('wrapper'=>'li'));
$menusConsultas	  .= $this->BMenu->link('Vigência de Contratos',array('controller' => 'clientes_produtos_contratos_vigencia', 'action' => 'index'), array('wrapper' => 'li'));
$menusConsultas  .= $this->BMenu->link('PGR e PCMSO pendentes',array('controller'=>'consultas','action'=>'consulta_ppra_pcmso_pendente'), array('wrapper'=>'li'));

/* MENU OPERAÇÕES*/
$menusOperacoes  .= $this->BMenu->link('Assinaturas de Clientes',array('controller' => 'clientes_produtos', 'action' => 'assinatura'), array('wrapper'=>'li'));
$menusOperacoes  .= $this->BMenu->link('Copia de Assinatura',array('controller' => 'clientes_produtos', 'action' => 'assinatura_cliente_para_cliente'), array('wrapper'=>'li'));
$menusOperacoes  .= $this->BMenu->link('Cotação',array('controller' => 'cotacoes', 'action' => 'index'), array('wrapper'=>'li'));
$menusOperacoes  .= $this->BMenu->link('Implantação', array('controller'=>'clientes_implantacao', 'action'=>'index'), array('wrapper'=>'li'));
$menusOperacoes  .= $this->BMenu->link('Pedidos Assinaturas',array('controller' => 'itens_pedidos', 'action' => 'listar'), array('wrapper'=>'li'));
$menusOperacoes  .= $this->BMenu->link('Pedidos',array('controller' => 'itens_pedidos', 'action' => 'listar_v2'), array('wrapper'=>'li'));
$menusOperacoes  .= $this->BMenu->link('SMS',array('controller' => 'sms', 'action' => 'index'), array('wrapper'=>'li'));
$menusOperacoes  .= $this->BMenu->link('Contratos de Produtos do Cliente',array('controller' => 'clientes_produtos_contratos', 'action' => 'listagem_contratos_por_codigo'), array('wrapper'=>'li'));
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
	<?php  echo $this->element('menu_terceiros_comercial', array('terceiros' => true)); ?>
</ul>

