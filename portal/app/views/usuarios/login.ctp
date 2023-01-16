<link rel="stylesheet" href="/portal/css/sweetalert.css" />
<?php echo $this->Buonny->link_js('sweetalert.min'); ?>

<div class="container">
    <div class="alert-warning">
		<?php echo $this->Buonny->flash(); ?>
	</div>

	<div class="container" style="border-style: solid; border-color: #ddd; border-radius: 25px 25px 25px 25px; border-width: 1px; background-color: #fff; position: relative; width: 330px;">
		<div class="row">
		    <?php echo $this->Form->create('Usuario', array('action' => 'login', 'class' => 'form-signin')); ?>
		        <p class="box-login-title">Login</p>
		        <input type="hidden" name="data[ref]" value="homepage">
		        <input type="text" class="form-control" name="data[Usuario][apelido]" placeholder="Digite seu Login" required autofocus><br />
		        <input type="password" class="form-control" name="data[Usuario][senha]" placeholder="Digite sua Senha" required>
		        <button class="btn btn-lg btn-primary btn-block" type="submit">Entrar no Sistema</button>
		    <?php echo $this->Form->end(); ?>
		    
		    <?php echo $this->Html->link('Esqueci minha senha.', '#', array('onclick' => 'manipula_modal("modal-lembrar-senha", 1);', 'style' => 'margin: 5px 15px 10px 0; float: right;')); ?>
		    
		</div>
	</div>
</div>

<div class="modal fade" id="modal-lembrar-senha" data-backdrop="static" style="width: 65%; left: 16%; top: 10%; margin: 0 auto;">
	<div class="modal-dialog modal-md" style="position: static;">
		<div class="modal-content">
			<div class="modal-header" style="text-align: center;">
				<h3>ESQUECI MINHA SENHA</h3>
			</div>
			<div class="modal-body" style="min-height: 200px;">
				
				<div class="row">
					<div class="col-xs-12">
						Preencha seu usuário abaixo, e você receberá sua senha em seu e-mail de cadastro:
					</div>	
				</div>
				<div>&nbsp;</div>
				<div class="row">
					<div class="col-xs-12">
						<?php echo $this->BForm->create('EsqueciMinhaSenha', array('type' => 'post', 'url' => array('controller' => 'usuarios', 'action' => 'esqueci_minha_senha'))); ?>
							<div class="row">
								<div class="col-xs-12">				
									<?php echo $this->BForm->input('nome', array('label' => 'Usuário:', 'class' => 'form-control', 'div' => 'form-group', 'style' => 'width: 510px;')); ?>
									<div id="mensagem" style="color: #e25151;"></div>
								</div>
								
							</div>
							<hr>
							<div class="row">
								<div class="col-xs-12">
									<a href="javascript:void(0);" onclick="enviar_formulario(this);" class="btn btn-success">Enviar Senha!</a>
									<a href="javascript:void(0);" onclick="manipula_modal('modal-lembrar-senha', 0);" class="btn btn-danger">Cancelar</a>
								</div>
							</div>
						<?php echo $this->BForm->end(); ?>
					</div>
				</div>				
			</div>
		</div>
	</div>
</div>	
								
<?php echo $this->Javascript->codeBlock("
		
	jQuery(document).ready(function() {
		$('.modal').css('z-index', '-1');
	});
		
	function manipula_modal(id, mostra) {
		if(mostra) {
			$('#' + id).css('z-index', '1050');
			$('#' + id).modal('show');
		} else {
			$('.modal').css('z-index', '-1');
			$('#' + id).modal('hide');
		}
	}			
		
	function enviar_formulario(elemento) {
		
		if($('#EsqueciMinhaSenhaNome').val()) {
		
			$('#EsqueciMinhaSenhaNome').css('border', '1px solid #CCC');
			$('#mensagem').hide();	
		
			bkp_elemento = $(elemento).html();
		
			$.ajax({
		        type: 'POST',
		        url: '/portal/usuarios/esqueci_minha_senha/',
		        dataType: 'json',
				data: $('#EsqueciMinhaSenhaLoginForm').serialize(),
		        beforeSend: function() {
					$(elemento).html('<img src=\"/portal/img/default.gif\"> Enviando Senha...');
				},
		        success: function(json) {
					if(json.resultado) {
						
						manipula_modal('modal-lembrar-senha', 0);
						swal({type: 'success', title: 'Senha Enviada!', text: json.mensagem});
						
					} else {
			
						manipula_modal('modal-lembrar-senha', 0);
						swal({type: 'error', title: 'Não foi possível enviar sua senha!', text: json.mensagem});
			
					}
		        },
		        complete: function() {
					$(elemento).html(bkp_elemento);
					$('#EsqueciMinhaSenhaNome').val('');
				}
		    });		
		} else {
		
			$('#EsqueciMinhaSenhaNome').css('border', '2px solid #e25151');
			$('#mensagem').show().html('campo obrigatório!');		
		
		}
	}
		
"); ?>