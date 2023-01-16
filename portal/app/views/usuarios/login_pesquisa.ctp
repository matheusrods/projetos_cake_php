<link rel="stylesheet" href="/portal/css/theme.css" />
<div class="container">
	<div class="row">
		<nav class="navbar navbar-default">
			<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
				<div class="container">
					<div class="navbar-header">
						<a class="navbar-brand logo" href="<?php echo $this->webroot ?>"> <img src="/portal/img/todosbem/logo.png" alt="logomarma"> </a>
					</div>
				</div>
			</div>
		</nav>
	</div>
</div>
<div class="container">
	<div class="alert-warning">
		<?php echo $this->Buonny->flash(); ?>
	</div>

	<div class="container" style="border-style: solid; border-color: #ddd; border-radius: 25px 25px 25px 25px; border-width: 1px; background-color: #fff; position: relative; width: 330px;">
		<div class="row">
			<?php echo $this->BForm->create('Usuario', array('action' => 'login', 'class' => 'form-signin')); ?>
			<p class="box-login-title">Login</p>
			<input type="hidden" name="data[ref]" value="homepage">
			<input type="text" class="form-control form-adjust" name="data[Usuario][apelido]" placeholder="Digite seu Login" required autofocus><br />
			<input type="password" class="form-control form-adjust" name="data[Usuario][senha]" placeholder="Digite sua Senha" required>
			<button class="btn btn-lg btn-primary btn-block" type="submit">Entrar no Sistema</button>

		    <?php echo $this->Html->link('Cadastre-se!', '#', array('onclick' => 'abreModal()', 'style' => 'margin: 5px 0 10px 15px; float: left;')); ?>
		    <?php echo $this->Html->link('Esqueci minha senha.', '#', array('onclick' => 'manipula_modal("modal-lembrar-senha", 1);', 'style' => 'margin: 5px 15px 10px 0; float: right;')); ?>
		    
			<?php echo $this->BForm->end(); ?>
		</div>
	</div>
</div>

<div class="modal-registro" <?php echo ((isset($error))? 'style="display: block"' : '' ); ?>>
	<div class="container">
		<div class="m-content"> 
			<div class="row">
				<div class="col-xs-12">
					<h3>Cadastro de usuário</h3>
				</div>
			</div>
			<hr>
			<div class="row">
				<div class="col-xs-12">
					Preencha os campos abaixo:
				</div>	
			</div>
			<div>&nbsp;</div>
			<?php echo $this->BForm->create('Usuario', array('url' => 'registra_novo_usuario')); ?>
			<div class="row">
				<div class="col-xs-6">
					<?php echo $this->BForm->input('nome', array('label' => 'Nome completo:', 'class' => 'form-control form-adjust', 'div' => 'form-group')); ?>
					<?php echo $this->BForm->input('data_nascimento', array('label' => 'Data de nascimento: ', 'class' => 'form-control form-adjust input datepicker nascimento', 'div' => 'form-group', 'maxlength' => 10)); ?>
					<?php echo $this->BForm->input('email', array('label' => 'E-mail:', 'class' => 'form-control form-adjust', 'div' => 'form-group')); ?>
				</div>
				<div class="col-xs-6">
					<?php echo $this->BForm->input('cpf', array('label' => 'CPF:', 'class' => 'form-control form-adjust', 'maxlength' => 11, 'div' => 'form-group')); ?>
					<label>Gênero:</label>
					<div class="clear"></div>
					<div class="form-adjust">
						<?php echo $this->BForm->radio('sexo', array('M' => 'Masculino', 'F' => 'Feminino'), array('legend' => false)) ?>
					</div>
				</div>
			</div>
			<hr>
			<div class="row">
				<div class="col-xs-6">
					<?php echo $this->BForm->input('apelido', array('label' => 'Usuário de login:', 'class' => 'form-control form-adjust', 'div' => 'form-group')); ?>

				</div>
				<div class="col-xs-6">
					<?php echo $this->BForm->input('senha', array('type' => 'password', 'label' => 'Senha:', 'class' => 'form-control form-adjust senha-revelar', 'div' => 'form-group')); ?>
					
				</div>
			</div>
			<hr>
			<div class="row">
				<div class="col-xs-12">
					<?php echo $this->BForm->button('Cadastrar', array('class' => 'btn btn-success')) ?>
					<?php echo $this->BForm->button('Cancelar', array('type' => 'button', 'class' => 'btn btn-danger modal-cancel')); ?>
				</div>
			</div>
			<?php echo $this->BForm->end(); ?>
			<div>&nbsp;</div>
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
									<?php echo $this->BForm->input('nome', array('label' => 'Usuário:', 'class' => 'form-control', 'div' => 'form-group', 'style' => 'width: 510px; background-color: #fff;')); ?>
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
		
	$(document).ready(function() {
		$('.modal').css('z-index', '-1');
		
		//setup_mascaras();
		$('.datepicker').datepicker({
			dateFormat: 'dd/mm/yy',
			dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
			dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
			dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
			monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
			monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
			nextText: 'Próximo',
			prevText: 'Anterior'
		}).setMask('99/99/9999');

		$('.modal-cancel').click(function() {
			$('.modal-registro').hide();
		});

		$('.senha-revelar').after($('<span>', {class: 'glyphicon glyphicon-eye-open revelar-senha', 'aria-hidden': 'true', style: 'float:right;top:-25px;right:10px;cursor:pointer;', 'data-toggle': 'tooltip', 'title': 'Revelar senha'}));

		$('body').on('click', '.revelar-senha', function() {
			if($(this).hasClass('revelada')) {
				$(this).removeClass('glyphicon-eye-close revelada').addClass('glyphicon-eye-open').attr('data-original-title', 'Revelar senha');
				$('.senha-revelar').attr('type', 'password');
			} else {
				$(this).removeClass('glyphicon-eye-open').addClass('glyphicon-eye-close revelada').attr('data-original-title', 'Esconder senha');
				$('.senha-revelar').attr('type', 'text');
			}
		});
	});
		
	function abreModal() {
		$('.modal-registro').fadeIn();
	}		
		
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
