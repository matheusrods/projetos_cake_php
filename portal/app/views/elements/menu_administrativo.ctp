<?php 
$menusCadastro = '';
$menusOperacoes = '';
$menusConsultas = '';
$subMenusAlertas = '';
$subMenusUsuarios = '';


$menusCadastro .= $this->BMenu->link('Cadastro de Layout',array('controller' => 'criacao_layouts', 'action' => 'index'), array('wrapper'=>'li'));

if(isset($authUsuario['Usuario']) && !is_null($authUsuario['Usuario']['codigo_empresa'])) {
	$menusCadastro .= $this->BMenu->link('Configuração Empresa',array('controller'=>'multi_empresas','action'=>'editar', $authUsuario['Usuario']['codigo_empresa']), array('wrapper'=>'li'));	
}

$menusCadastro .= $this->BMenu->link('Configurações de Sistema',array('controller'=>'configuracoes','action'=>'index'), array('wrapper'=>'li'));
$menusCadastro .= $this->BMenu->link('Conselho de Classe',array('controller'=>'medicos','action'=>'conselho_classe'), array('wrapper'=>'li'));
$menusCadastro .= $this->BMenu->link('Parametrização de Cargos',array('controller'=>'configuracoes','action'=>'index_param_cargos'), array('wrapper'=>'li'));
$menusCadastro .= $this->BMenu->link('Perfil',array('controller'=>'uperfis','action'=>'index'), array('wrapper'=>'li'));
$menusCadastro .= $this->BMenu->link('Profissional',array('controller'=>'medicos','action'=>'index'), array('wrapper'=>'li'));
$menusCadastro .= $this->BMenu->link('Usuários',array('controller'=>'usuarios','action'=>'index'), array('wrapper'=>'li'));

$menusOperacoes .= $this->BMenu->link('Separação Grupo Econômico',array('controller'=> 'grupos_economicos','action'=>'index_grupos_economicos'), array('wrapper'=>'li'));

$menusConsultas .=$this->BMenu->link('Histórico de Acessos',array('controller'=>'usuarios_historicos','action'=>'relatorio_logins_users'), array('wrapper'=>'li'));
$menusConsultas .=$this->BMenu->link('Log Integrações',array('controller'=>'logs_integracoes','action'=>'integracao'), array('wrapper'=>'li'));
?>

<ul class="sf-menu menu-admin">
	<?php if (!empty($menusCadastro)): ?>
		<li><?php echo $this->Html->link('Cadastros','javascript:void(0)'); ?>
			<ul>
				<?php echo $menusCadastro ?>
			</ul>
		</li>
	<?php endif; ?>
	<?php if (!empty($menusOperacoes)): ?>
		<li><?php echo $this->Html->link('Operações','javascript:void(0)'); ?>
			<ul>
				<?php echo $menusOperacoes ?>
			</ul>
		</li>
	<?php endif; ?>
	<?php if (!empty($menusConsultas)): ?>
		<li><?php echo $this->Html->link('Consultas','javascript:void(0)'); ?>
			<ul>
				<?php echo $menusConsultas ?>
			</ul>
		</li>
	<?php endif; ?>
</ul>