<?php
$menusCadastro    = '';
$menusOperacoes   = '';
$menusConsultas   = '';
//menu cadastro
$menusCadastro .= $this->BMenu->link('Nota Fiscal', array('controller' => 'notas_fiscais_servico', 'action' => 'index'), array('wrapper'=>'li'));
$menusCadastro .= $this->BMenu->link('Motivos de Acréscimo', array('controller' => 'motivos_acrescimo', 'action' => 'index'), array('wrapper'=>'li'));
$menusCadastro .= $this->BMenu->link('Motivos de Desconto', array('controller' => 'motivos_desconto', 'action' => 'index'), array('wrapper'=>'li'));
$menusCadastro .= $this->BMenu->link('Motivos de Aprovação com Ajuste', array('controller' => 'motivos_aprovado_ajuste', 'action' => 'index'), array('wrapper'=>'li'));
$menusCadastro .= $this->BMenu->link('Tipo de Glosas', array('controller' => 'tipo_glosas', 'action' => 'index'), array('wrapper'=>'li'));
$menusCadastro .= $this->BMenu->link('Tipos de Serviços NFs', array('controller' => 'tipo_servicos_nfs', 'action' => 'index'), array('wrapper'=>'li'));
//menu operacao
$menusOperacoes .= $this->BMenu->link('Auditoria de Exames',array('controller' => 'fornecedores', 'action' => 'auditoria_exames'), array('wrapper'=>'li'));
$menusOperacoes .= $this->BMenu->link('Consolidação Nota Fiscal x Exames', array('controller' => 'notas_fiscais_servico', 'action' => 'consolida_nfs_exame'), array('wrapper'=>'li'));
//menu consultas
$menusConsultas .= $this->BMenu->link('Relatório de Glosas',array('controller' => 'glosas', 'action' => 'index'), array('wrapper'=>'li'));
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
	<?php  echo $this->element('menu_terceiros_contas_medicas', array('terceiros' => false)); ?>
</ul>
