<?php echo $this->Form->create('Usuario', array('action' => 'login', 'class' => 'form-signin')); ?>
<p class="box-login-title">Login</p>
<input type="text" class="form-control" name="data[Usuario][apelido]" placeholder="Digite seu Login" required autofocus>
<input type="password" class="form-control" name="data[Usuario][senha]" placeholder="Digite sua Senha" required>
<button class="btn btn-lg btn-primary btn-block" type="submit">Entrar no Sistema</button>
<?php echo $this->Form->end(); ?>
