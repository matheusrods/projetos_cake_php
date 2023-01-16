<?php if ($this->Buonny->seUsuarioForMulticliente()) :?>
	<div class="row-fluid inline">
		<?php echo $this->Buonny->input_codigo_cliente($this); ?>
	</div>
<?php endif; ?>
<div class="row-fluid inline">

	<?php if (!$this->Buonny->seUsuarioForMulticliente()) :?>
		<?php 
			$codigo_cliente = (is_array($this->data['Usuario']['codigo_cliente'])) ? implode(',',$this->data['Usuario']['codigo_cliente']) : $this->data['Usuario']['codigo_cliente'];
		?>
	
		<?php echo $this->BForm->input('codigo_cliente', array('type' => 'hidden','value' => $codigo_cliente)) ?>
	<?php endif; ?>

	<?php echo $this->BForm->input('apelido', array('class' => 'input-small', 'label' => 'Login')) ?>
	<?php echo $this->BForm->input('nome', array('class' => 'input-medium', 'label' => 'Nome')) ?>
	<?php echo $this->BForm->input('email', array('class' => 'input-large', 'label' => 'Email')) ?>
	<?php echo $this->BForm->input('codigo_uperfil', array('label' => 'Perfil', 'class' => 'input-medium', 'options' => $perfil, 'empty' => 'Todos', 'default' => '')); ?>
	    <?php if ( $authUsuario['Usuario']['codigo_uperfil'] === Uperfil::ADMIN || $authUsuario['Usuario']['admin'] === 1): ?>
			<?php echo $this->BForm->input('ativo', array( 'label' => 'Status', 'class' => 'input-small', 'options' => array('Inativos', 'Ativos'), 'empty' => 'Todos', 'default' => 1)); ?>
		<?php endif; ?>
</div>
<?php echo $this->Javascript->codeBlock('$(document).ready(function() {setup_mascaras();});');
?>
