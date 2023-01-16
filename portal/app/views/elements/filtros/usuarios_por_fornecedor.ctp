<div class="row-fluid inline">
	<?php echo $this->BForm->input('apelido', array('class' => 'input-small', 'label' => 'Login')) ?>
	<?php echo $this->BForm->input('nome', array('class' => 'input-medium', 'label' => 'Nome')) ?>
	<?php echo $this->BForm->input('email', array('class' => 'input-large', 'label' => 'Email')) ?>
	<?php echo $this->BForm->input('codigo_uperfil', array('label' => 'Perfil', 'class' => 'input-medium', 'options' => $perfil, 'empty' => 'Todos', 'default' => '')); ?>
    <?php if(empty($authUsuario['Usuario']['codigo_cliente'])):?>
	    <?php if ( $authUsuario['Usuario']['codigo_uperfil'] === Uperfil::ADMIN || $authUsuario['Usuario']['admin'] === 1): ?>
			<?php echo $this->BForm->input('ativo', array( 'label' => 'Status', 'class' => 'input-small', 'options' => array('Inativos', 'Ativos'), 'empty' => 'Todos', 'default' => 1)); ?>
		<?php endif; ?>
	<?php endif; ?>	
</div>
<?php echo $this->Javascript->codeBlock('$(document).ready(function() {
	var div = jQuery("div.lista");
	setup_mascaras();
	$.ajax({
		url: baseUrl + "clientes/listagem/" + destino.toLowerCase() + "/" + Math.random(),
		dataType: "html",
		beforeSend: function() {
			bloquearDiv(div);
		},
		success: function(data){
			div.html(data);
		},
		error: function(error) {
			div.unblock();
		}
	});
});');
?>