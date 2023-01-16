<?php if(isset($senha_expirada) && !empty($senha_expirada)): ?>
	<div style="width:720px; text-align:justify;">
		<p>Prezado Cliente,</p>
		<p>Sua senha de acesso ao Portal Buonny expirou. Favor entrar com a senha atual e definir uma nova senha.</p>
	</div>
	</br>
<?php endif; ?>
<?php echo $this->BForm->create('Usuario', array('action' => 'trocar_senha')) ?>
    <div class="row-fluid">
		<?php echo $this->BForm->hidden('senha_expirada', array('value' => isset($senha_expirada) ? $senha_expirada: 0)) ?>
        <?php echo $this->BForm->input('apelido', array('label' => 'Apelido', 'readonly' => true, 'value' => $authUsuario['Usuario']['apelido'])) ?>
        <?php echo $this->BForm->input('senha_antiga', array('label' => 'Senha Atual', 'type' => 'password')) ?>
        <?php echo $this->BForm->input('nova_senha', array('label' => 'Nova Senha', 'type' => 'password')) ?>
        <?php echo $this->BForm->input('confirmar_senha', array('label' => 'Confirmar Senha', 'type' => 'password')) ?>
    </div>
    <div class="form-actions">
      <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
    </div>
<?php echo $this->BForm->end() ?>