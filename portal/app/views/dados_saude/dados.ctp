		<style>
			.error {color: #E84228; }
			.ui-datepicker-trigger{margin-top: 8px;}
		</style>

		<nav class="navbar navbar-default">
			<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
				<div class="container">
					<div class="navbar-header">
						<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
							<span class="sr-only">Menu</span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span>
						</button>
						<a class="navbar-brand logo" href="/"> <img src="/portal/img/todosbem/logo.png" alt="logomarma"> </a>
					</div>
					<div class="navbar-collapse collapse">
						<ul class="nav navbar-nav navbar-right">
							<li class="active hidden-sm hidden-md"><a href="/">Home</a></li>
                		    <li class="">
								<ul class="dropdown-menu" role="menu">
									<li><a href="javascript:void(0)" onclick="scrollToElement('#areaFriendsAnchor')">Lista de amigos</a></li>
								</ul>
							</li>
							<li class="dropdown ">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown">
									Minha Conta <span class="caret"></span>
								</a>
								<ul class="dropdown-menu" role="menu">
									<li><a href="#">Mudar Senha</a></li>
									<li class="divider"></li>
									<li class="dropdown-header hidden-xs">Configurações</li>
									<li><a href="#">Minhas Preferências</a></li>
									<li><a href="/portal/usuarios/logout">Sair do Sistema</a></li>
								</ul>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</nav>
		<div class="container">
			<h2>Seja Bem-vindo(a), <strong><?php echo $usuario['Usuario']['nome']; ?></strong>!</h2>
			<p>Você está a 3 passos de começar a melhorar a sua saúde.</p>
			
			<input type="hidden" id="status-step-01" value="<?php echo $usuario_info ? "ok" : ""; ?>"/>
			<input type="hidden" id="status-step-02" value="<?php echo $usuario_imc && count($usuario_imc) ? "ok" : ""; ?>"/>

			<div class="col-md-12 col-sm-12">
				<div class="box-steps ">
					<div class="row">
						<div class="col-md-1 col-xs-2">
							<div class="bullet b1"></div>
						</div>
						<div class="col-md-9 col-xs-8">
							<p class="item-step">Completar seu cadastro.</p>
						</div>
						<div class="col-md-2 col-xs-2">
							<div class="link-on-first-steps" id="step-01">
								<?php if($usuario_info) : ?>
									<label class="label label-success">OK - Etapa Concluída!</label>
								<?php else : ?>
									<a onclick="manipula_modal('modal_cadastro', 1);" href="javascript:void(0);">
										Clique aqui <i class="glyphicon glyphicon-chevron-right"></i>
									</a>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</div>

				<div class="box-steps ">
					<div class="row">
						<div class="col-md-1 col-xs-2">
							<div class="bullet b2"></div>
						</div>
						<div class="col-md-9 col-xs-8">
							<p class="item-step">Informe seu peso e altura.</p>
						</div>
						<div class="col-md-2 col-xs-2">
							<div class="link-on-first-steps" id="step-02">
								<?php if($usuario_imc && count($usuario_imc)) : ?>
									<label class="label label-success">OK - Etapa Concluída!</label>
								<?php else : ?>
									<a class="item-line" onclick="manipula_modal('modal_imc', 1);" href="javascript:void(0)">
										Clique aqui <i class="glyphicon glyphicon-chevron-right"></i>
									</a>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</div>

				<div class="box-steps ">
					<div class="row">
						<div class="col-md-1  col-xs-2">
							<div class="bullet b3"></div>
						</div>
						<div class="col-md-9  col-xs-10">
							<p class="item-step">Faça pelo menos 1 questionário.</p>
						</div>
						<div class="col-md-2  hidden-xs">
							<div class="link-on-first-steps">
								<a href="javascript:void(0);" onclick="proxima_etapa()">
									Clique aqui <i class="glyphicon glyphicon-chevron-right"></i>
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<div class="modal fade" id="modal_cadastro">
			<div class="modal-dialog modal-md" style="position: static;">
				<div class="modal-content">
					<?php echo $this->BForm->create('UsuariosDados', array('type' => 'post' ,'url' => array('controller' => 'dados_saude','action' => 'grava_info')));?>
						<div class="modal-header">
							<h4 class="modal-title" id="gridSystemModalLabel"> Complete com seus dados cadastrais: </h4>
						</div>
				    	<div class="modal-body">
							<label><b>CPF:</b></label>
							<?php echo $this->BForm->input('UsuariosDados.cpf', array('class' => 'input-small cpf form-control obrigatorio','label' => false,'type' => 'text', 'style' => 'width: 200px;', 'size'=>'14')); ?>
							<div class="cpf-error"></div>
							<br />							
							<label><b>Data Nascimento:</b></label>
							<?php echo $this->BForm->input('UsuariosDados.data_nascimento', array('class' => 'pull-left input-small data datepicker form-control obrigatorio', 'label' => false , 'type' => 'text', 'style' => 'width: 200px;')); ?>
							<div class="data_nascimento-error"></div>
							<br />
							
							<label><b>Sexo:</b></label>
							<?php echo $this->BForm->input('UsuariosDados.sexo', array('class' => 'input-xlarge form-control obrigatorio', 'label' => false, 'options' => array('' => 'Selecione', 'M' => 'Masculino', 'F' => 'Feminino') )); ?>
							<div class="sexo-error"></div>
							<br />
				    	</div>
				    	<div class="modal-footer">
				    		<a href="javascript:void(0);" class="btn btn-success" onclick="grava_info(this);">Confirmar</a>
				    	</div>
			    	<?php echo $this->BForm->end(); ?>
			    </div>
			</div>
		</div>
		<div class="modal fade" id="modal_imc">
			<div class="modal-dialog modal-md" style="position: static;">
				<div class="modal-content">
					<?php echo $this->BForm->create('UsuariosImc', array('type' => 'post' ,'url' => array('controller' => 'dados_saude','action' => 'grava_imc')));?>
						<div class="modal-header">
							<h4 class="modal-title" id="gridSystemModalLabel"> ATUALIZE SEU PESO: </h4>
						</div>
				    	<div class="modal-body">
							<label><b>Seu Peso Atual:</b></label>
							<?php echo $this->BForm->input('UsuariosImc.peso', array('class' => 'input-xsmall peso form-control right obrigatorio', 'label' => false, 'style' => 'width: 200px; text-align: right;')); ?>
							<br />
			
							<label><b>Altura:</b></label>
							<?php echo $this->BForm->input('UsuariosImc.altura', array('class' => 'input-xsmall altura form-control obrigatorio', 'label' => false, 'style' => 'width: 200px; text-align: right;')); ?>
							<br />
				    	</div>
				    	<div class="modal-footer">
				    		<a href="javascript:void(0);" class="btn btn-success" onclick="grava_imc(this);">Confirmar</a>
				    	</div>
					<?php echo $this->BForm->end(); ?>   	
			    </div>
			</div>
		</div>

<?php echo $this->Javascript->codeBlock('
	$(function() {
		$(".data").datepicker({
					dateFormat: "dd/mm/yy",
					showOn : "button",
					buttonImage : baseUrl + "img/calendar.gif",
					buttonImageOnly : true,
					buttonText : "Escolha uma data",
					dayNames : ["Domingo","Segunda","Terça","Quarta","Quinta","Sexta","Sabado"],
					dayNamesShort : ["Dom","Seg","Ter","Qua","Qui","Sex","Sab"],
					dayNamesMin : ["D","S","T","Q","Q","S","S"],
					monthNames : ["Janeiro","Fevereiro","Março","Abril","Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro"],
					monthNamesShort : ["Jan","Fev","Mar","Abr","Mai","Jun","Jul","Ago","Set","Out","Nov","Dez"]
				}).setMask("99/99/9999");

		$(".data").setMask("99/99/9999");
		
		$(".altura").setMask({mask: "9.99", reverse: true});
		$(".altura").setMask({mask: "99,9", type: "reverse"});
		$(".peso").setMask({mask: "9,999", type: "reverse"});
		$(".cpf").setMask({mask: "999.999.999-99"});

		$(".cpf").change(function() {
				//retira o label inserido
				$(".cpf-error").html("");
				
				if (!validarCPF($(this).val())) {
				  $(".cpf-error").html("<label class=\"label error_label label-danger\"> CPF inválido! </label>");
				}
				else{
					$.ajax({
						url: "/portal/dados_saude/valida_cpf_dados",
						type: "POST",
						dataType: "json",
						data: {cpf: $(this).val()},
				        success: function(retorno) {
							if(retorno == 0) {
							  $(".cpf-error").html("<label class=\"label error_label label-danger\"> CPF já utilizado! </label>");
							}
				        }

					});	

				}

			});

	});
		
	function grava_info(element) {
		var form = $("#UsuariosDadosForm").serialize();
		var elemento = $(element).html();
		
		if(validaForm("UsuariosDadosForm")) {
			$.ajax({
				url: "/portal/dados_saude/grava_info",
				type: "POST",
				dataType: "json",
				data: form,
		        beforeSend: function() {
					$(element).html("<img src=\"/portal/img/default.gif\">");
				},
		        success: function(retorno) {
					$(".error").html("");
					if(retorno["resultado"] == 1) {
						$("#step-01").html("<label class=\"label label-success\">OK - Etapa Concluída!</label>");
						$("#status-step-01").val("ok");
						manipula_modal("modal_cadastro", 0);
					} else {
						if(retorno["erro"] != undefined){
						$("."+retorno["erro"]["campo"]+"-error").html("<label class=\"label error_label label-danger\">"+retorno["erro"]["mensagem"]+"</label>");
						}
					}
		        },
		        complete: function() {
					$(element).html(elemento);
				}
			});				
		}
	}
		
	function grava_imc(element) {
		var form = $("#UsuariosImcDadosForm").serialize();
		var elemento = $(element).html();
		
		if(validaForm("UsuariosImcDadosForm")) {
			$.ajax({
				url: "/portal/dados_saude/grava_imc",
				type: "POST",
				dataType: "json",
				data: form,
		        beforeSend: function() {
					$(element).html("<img src=\"/portal/img/default.gif\">");
				},
		        success: function(retorno) {
					if(retorno) {
						$("#step-02").html("<label class=\"label label-success\">OK - Etapa Concluída!</label>");
						$("#status-step-02").val("ok");
						manipula_modal("modal_imc", 0);
					}
		        },
		        complete: function() {
					$(element).html(elemento);
				}
			});		
		}
	}	
		
	function validaForm(form) {
		
		// remove todas as validacoes!
		$(".label-danger").remove();
		
		var libera = true;		
		$("form#" + form + " :input").each(function() {
 			var input = $(this);
		
			if(input.hasClass("obrigatorio") && (input.val() == "")) {
				$(input).after("<label class=\"label label-danger\"> Campo obrigatório! </label>");
				libera = false;
			}
		});
		
		return libera;
	}			
		
	function proxima_etapa() {
		if(($("#status-step-01").val() == "ok") && ($("#status-step-02").val() == "ok")) {
			location.href = "/portal/dados_saude/dashboard";
		} else if (($("#status-step-01").val() == "ok")){
			swal({type: "error", title: "Não é possível Avançar", text: "É obrigatório o preenchimento da etapa 2!"});
			return false;
		} else if (($("#status-step-02").val() == "ok")){
			swal({type: "error", title: "Não é possível Avançar", text: "É obrigatório o preenchimento da etapa 1!"});
			return false;
		} else {
			swal({type: "error", title: "Não é possível Avançar", text: "É obrigatório o preenchimento das etapas 1 e 2!"});
			return false;		
		}
	}
		
	function manipula_modal(id, mostra) {
		if(mostra) {
			$(".modal").css("z-index", "-1");
			$("#" + id).css("z-index", "1050");
			$("#" + id).modal("show");
		} else {
			$("#" + id).css("z-index", "-1");
			$("#" + id).modal("hide");
		}
	}		
		
'); ?>