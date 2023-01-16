<?php
$menusCadastro    = '';
$menusOperacoes   = '';
$menusConsultas   = '';
$menusCadastroTerceiros = '';
$subMenusSeguranca = '';
$subMenuDepara = '';

//MENU. ITEM: SEGURANCA
$menusCadastro .= $this->BMenu->link('EPC',array('controller'=>'epc','action'=>'index'), array('wrapper'=>'li'));
$menusCadastro .= $this->BMenu->link('EPI',array('controller'=>'epi','action'=>'index'), array('wrapper'=>'li'));
$menusCadastro .= $this->BMenu->link('Efeitos Críticos',array('controller'=>'riscos_atributos_detalhes','action'=>'index'), array('wrapper'=>'li'));
$menusCadastro .= $this->BMenu->link('FISPQ',array('controller'=>'fispq','action'=>'index'), array('wrapper'=>'li'));
$menusCadastro .= $this->BMenu->link('Fontes Geradoras',array('controller'=>'fontes_geradoras','action'=>'index'), array('wrapper'=>'li'));
$menusCadastro .= $this->BMenu->link('Grupo de Risco',array('controller'=>'grupos_riscos','action'=>'index'), array('wrapper'=>'li'));
$menusCadastro .= $this->BMenu->link('IBUTG',array('controller'=>'ibutg','action'=>'index'), array('wrapper'=>'li'));
$menusCadastro .= $this->BMenu->link('Risco',array('controller'=>'riscos','action'=>'index'), array('wrapper'=>'li'));
$menusCadastro .= $this->BMenu->link('Prevenção e Combate a Incêndio',array('controller'=>'sist_combate_incendio','action'=>'index'), array('wrapper'=>'li'));
$menusCadastro .= $this->BMenu->link('Unidade de Medida',array('controller'=>'tecnicas_medicao','action'=>'index'), array('wrapper'=>'li'));
$menusCadastro .= $this->BMenu->link('Tipo de acidente',array('controller'=>'tipos_acidentes','action'=>'index'), array('wrapper'=>'li'));
$menusCadastro .= $this->BMenu->link('Responsáveis pelos registros ambientais',array('controller'=>'clientes_responsaveis_registros_ambientais','action'=>'index'), array('wrapper'=>'li'));
$menusCadastro .= $this->BMenu->link('Responsáveis pela monitoração biológica',array('controller'=>'clientes_responsaveis_monitoracao_biologicas','action'=>'index'), array('wrapper'=>'li'));

$menusOperacoes .= $this->BMenu->link('CAT', array('controller'=>'cat', 'action'=>'index'), array('wrapper'=>'li'));
$menusOperacoes .= $this->BMenu->link('PGR', array('controller'=>'clientes_implantacao', 'action'=>'index_ppra'), array('wrapper'=>'li'));
$menusCadastroTerceiros	 .= $this->BMenu->link('Perigos Aspectos', array('controller'=>'perigos_aspectos', 'action'=>'index'), array('wrapper'=>'li'));
$menusCadastroTerceiros .= $this->BMenu->link('Tipos de Métodos', array('controller'=>'metodos_tipo', 'action'=>'index'), array('wrapper'=>'li'));


$menusCadastroTerceiros .= $this->BMenu->link('Técnicas de Medição',array('controller'=>'tecnicas_medicao','action'=>'index_terceiros'), array('wrapper'=>'li'));
$menusCadastroTerceiros .= $this->BMenu->link('Riscos Tipo', array('controller'=>'riscos_tipos', 'action'=>'index'), array('wrapper'=>'li'));

$menusCadastroTerceiros	 .= $this->BMenu->link('Riscos Impactos', array('controller'=>'riscos_impactos', 'action'=>'index'), array('wrapper'=>'li'));

$subMenuDepara .= $this->BMenu->link('Atribuição',array('controller' => 'atribuicoes', 'action' => 'index'), array('wrapper'=>'li'));
$subMenuDepara .= $this->BMenu->link('Cargos',array('controller' => 'cargos', 'action' => 'index_externo'), array('wrapper'=>'li'));
$subMenuDepara .= $this->BMenu->link('Efeito Crítico',array('controller' => 'riscos_atributos_detalhes', 'action' => 'index_externo'), array('wrapper'=>'li'));
$subMenuDepara .= $this->BMenu->link('Epc',array('controller' => 'epc', 'action' => 'index_externo'), array('wrapper'=>'li'));
$subMenuDepara .= $this->BMenu->link('Epi',array('controller' => 'epi', 'action' => 'index_externo'), array('wrapper'=>'li'));
$subMenuDepara .= $this->BMenu->link('Exames',array('controller' => 'exames', 'action' => 'index_externo'), array('wrapper'=>'li'));
$subMenuDepara .= $this->BMenu->link('Fontes Geradoras',array('controller' => 'fontes_geradoras', 'action' => 'index_externo'), array('wrapper'=>'li'));
$subMenuDepara .= $this->BMenu->link('GHE',array('controller' => 'grupos_homogeneos', 'action' => 'index_externo'), array('wrapper'=>'li'));
$subMenuDepara .= $this->BMenu->link('Grupo Risco', array('controller' => 'grupos_riscos', 'action' => 'index_externo'), array('wrapper'=>'li'));
$subMenuDepara .= $this->BMenu->link('Motivos Afastamento', array('controller' => 'motivos_afastamento', 'action' => 'index_externo'), array('wrapper'=>'li'));
$subMenuDepara .= $this->BMenu->link('Riscos',array('controller' => 'riscos', 'action' => 'index_externo'), array('wrapper'=>'li'));
$subMenuDepara .= $this->BMenu->link('Setores',array('controller' => 'setores', 'action' => 'index_externo'), array('wrapper'=>'li'));
$subMenuDepara .= $this->BMenu->link('Unidades',array('controller' => 'clientes', 'action' => 'index_externo'), array('wrapper'=>'li'));

$menusCadastroTerceiros .= "<li>".$this->Html->link('Depara','javascript:void(0)'); 
    $menusCadastroTerceiros .= "<ul>".$subMenuDepara."</ul>";
    $menusCadastroTerceiros .= "</li>";

if (!empty($subMenusSeguranca)):
	$menusCadastro .= "<li>".$this->Html->link('Segurança','javascript:void(0)');
        $menusCadastro .= "<ul>".$subMenusSeguranca."</ul>";
    $menusCadastro .= "</li>";
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

	<?php if (!empty($menusCadastroTerceiros)): ?>
		<li><?php echo $this->Html->link('Cadastro Terceiros','javascript:void(0)'); ?>
			<ul>
				<?php echo $menusCadastroTerceiros ?>
			</ul>
		</li>
	<?php endif; ?>

	<?php  echo $this->element('menu_terceiros_seguranca', array('terceiros' => true)); ?>
</ul>