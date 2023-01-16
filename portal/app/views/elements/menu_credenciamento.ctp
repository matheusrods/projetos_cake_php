<?php
	$menusCadastro    = '';
	$menusOperacoes   = '';
	$menusConsultas   = '';
	$subMenusProposta = '';
	
	$menusCadastro   .= $this->BMenu->link('Prestador',array('controller'=>'fornecedores','action'=>'index'), array('wrapper'=>'li'));
	$menusCadastro   .= $this->BMenu->link('Agenda do Prestador',array('controller'=>'fornecedores_capacidade_agenda','action'=>'index'), array('wrapper'=>'li'));
	$menusCadastro   .= $this->BMenu->link('Motivos de Recusa',array('controller' => 'motivos_recusa', 'action' => 'index'), array('wrapper'=>'li'));
	$menusCadastro   .= $this->BMenu->link('Tipo de Documento',array('controller' => 'tipos_documentos', 'action' => 'index'), array('wrapper'=>'li'));
	
	
// migrado contas medicas	$menusOperacoes  .= $this->BMenu->link('Auditoria de Exames',array('controller' => 'fornecedores', 'action' => 'auditoria_exames'), array('wrapper'=>'li'));
	$menusOperacoes  .= $this->BMenu->link('Gerenciamento de Proposta',array('controller' => 'propostas_credenciamento', 'action' => 'index'), array('wrapper'=>'li'));
	$menusOperacoes  .= $this->BMenu->link('Manutenção Valores da Proposta',array('controller' => 'propostas_credenciamento', 'action' => 'alteracao_valores_exames'), array('wrapper'=>'li'));

	$menusConsultas  .= $this->BMenu->link('Consultar Mapeamento de Rede',array('controller' => 'clientes_fornecedores', 'action' => 'index'), array('wrapper'=>'li'));
	$menusConsultas  .= $this->BMenu->link('Documentos do Prestador',array('controller' => 'consultas', 'action' => 'consulta_documentos_vencidos_fornecedor'), array('wrapper'=>'li'));
	$menusConsultas  .= $this->BMenu->link('Documentos Pendentes Credenciado',array('controller' => 'consultas', 'action' => 'consulta_documentos_pendentes'), array('wrapper'=>'li'));
	$menusConsultas  .= $this->BMenu->link('Informações Credenciado',array('controller' => 'fornecedores', 'action' => 'info_credenciado'), array('wrapper'=>'li'));
	
	$menusConsultas  .= $this->BMenu->link('Produtos e Serviços',array('controller' => 'consultas', 'action' => 'consulta_produtos_servicos'), array('wrapper'=>'li'));
	
	//$subMenusProposta  .= $this->BMenu->link('Documentos Pendentes',array('controller' => 'consultas', 'action' => 'consulta_documentos_pendentes'), array('wrapper'=>'li'));
	//$subMenusProposta  .= $this->BMenu->link('Propostas',array('controller' => 'consultas', 'action' => 'consulta_propostas'), array('wrapper'=>'li'));
	$menusConsultas  .= $this->BMenu->link('Propostas',array('controller' => 'consultas', 'action' => 'consulta_propostas'), array('wrapper'=>'li'));
	
	// if (!empty($subMenusProposta)):
	// 	$menusConsultas .= "<li>".$this->Html->link('Propostas','javascript:void(0)');
	//         $menusConsultas .= "<ul>".$subMenusProposta."</ul>";
	//     $menusConsultas .= "</li>";
	// endif;
?>

<ul class="sf-menu menu-admin">
	<?php if (!empty($menusCadastro)): ?>
		<li><?php echo $this->Html->link('Cadastro','javascript:void(0)'); ?>
			<ul>
				<?php  echo $menusCadastro ?>
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
	<?php echo $this->element('menu_credenciado', array('clientes' => true)); ?>
</ul>