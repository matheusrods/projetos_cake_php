<?php 
	$menusCadastro = '';
	$menusOperacoes = '';
	$menusConsultas = '';
	
	if($authUsuario['Usuario']['codigo_uperfil'] == Uperfil::CREDENCIANDO) {
		$menusCadastro .= $this->BMenu->link('Envio dos Documentos', array('controller' => 'propostas_credenciamento', 'action' => 'minha_proposta', $authUsuario['Usuario']['codigo_proposta_credenciamento'], 'documentacao'), array('wrapper'=>'li'));
?>

<?php if (!isset($clientes)): ?>
	<ul class="sf-menu menu-admin">
<?php endif; ?>
	<?php $diff_name = isset($clientes) ? ' Terceiros' : '' ?>
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
<?php if (!isset($clientes)): ?>
	</ul>
<?php endif; ?>