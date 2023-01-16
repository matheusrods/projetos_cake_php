<?php 
$menusCadastro = '';
$menusOperacoes = '';
$menusConsultas = '';
$subMenusFuncionario = '';

//feature de adaptação cliente setores e cargos
$menusCadastro .= $this->BMenu->link('Assinatura eletrônica', array('controller'=>'assinatura_eletronica','action'=>'index'), array('wrapper'=>'li'));

$menusCadastro .= $this->BMenu->link('Cargos', array('controller'=>'cargos', 'action'=>'cargo_terceiros'), array('wrapper'=>'li'));

$menusCadastro .= $this->BMenu->link('Funcionário', array('controller'=>'clientes', 'action'=>'funcionarios'), array('wrapper'=>'li'));

$menusCadastro .= $this->BMenu->link('Funcionário Per capita',array('controller' => 'clientes', 'action' => 'funcionarios_percapita'), array('wrapper'=>'li'));

$menusCadastro .= $this->BMenu->link('Logo & Cores', array('controller' => 'clientes', 'action' => 'logos_cores_cliente'), array('wrapper'=>'li'));

$menusCadastro .= $this->BMenu->link('Profissional',array('controller'=>'medicos','action'=>'index'), array('wrapper'=>'li'));

//feature de adaptação cliente setores e cargos
$menusCadastro .= $this->BMenu->link('Setores', array('controller'=>'setores', 'action'=>'setor_terceiros'), array('wrapper'=>'li'));

$menusCadastro .= $this->BMenu->link('Tomador de Serviço', array('controller'=>'clientes', 'action'=>'cliente_tomador'), array('wrapper'=>'li'));

$menusCadastro .= $this->BMenu->link('Unidades', array('controller'=>'clientes', 'action'=>'cliente_terceiros'), array('wrapper'=>'li'));

$menusCadastro .= $this->BMenu->link('Tipos Ações', array('controller'=>'tipos_acoes', 'action'=>'index'), array('wrapper'=>'li'));

//operacao
$menusOperacoes .= $this->BMenu->link('Digitalização', array('controller'=>'tipo_digitalizacao', 'action'=>'operacao_digitalizacao_terceiros'), array('wrapper'=>'li'));
$menusOperacoes .= $this->BMenu->link('Estrutura', array('controller'=>'clientes_implantacao', 'action'=>'implantation'), array('wrapper'=>'li'));
$menusOperacoes .= $this->BMenu->link('Importação de dados', array('controller'=>'importacao_layouts', 'action'=>'index'), array('wrapper'=>'li'));
$menusConsultas   .= $this->BMenu->link('Digitalização',array('controller' => 'tipo_digitalizacao', 'action' => 'consulta_digitalizacao_terceiros'), array('wrapper'=>'li'));
$menusConsultas   .= $this->BMenu->link('PGR e PCMSO pendentes',array('controller' => 'consultas', 'action' => 'ppra_pcmso_pendente_terceiros'), array('wrapper'=>'li'));
$menusConsultas   .= $this->BMenu->link('Riscos Exames Aplicados',array('controller' => 'riscos_exames', 'action' => 'aplicados'), array('wrapper'=>'li'));
$menusConsultas   .= $this->BMenu->link('Vidas',array('controller' => 'clientes_funcionarios', 'action' => 'consulta_vidas'), array('wrapper'=>'li'));
$menusConsultas   .= $this->BMenu->link('Vigência de PGR e PCMSO',array('controller' => 'aplicacao_exames', 'action' => 'vigencia_ppra_pcmso'), array('wrapper'=>'li'));

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
