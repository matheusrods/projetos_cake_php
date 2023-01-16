<ul class="nav nav-tabs">
	<li class="active"><a href="#gerais" data-toggle="tab">Dados Gerais</a></li>
	<?php if(!empty($codigo)) { ?>
	<li><a href="#historico" data-toggle="tab">Histórico</a></li>
	<?php } ?>

</ul>
<div class="tab-content">
	<div class="tab-pane active" id="gerais">

		<?php echo $this->BForm->create('Funcionario', array('url' => array('controller' => 'funcionarios', 'action' => 'editar', $codigo, $codigo_cliente, $referencia), 'type' => 'post')); ?>
		<?php  echo $this->BForm->hidden('codigo_cliente', array('value' => $codigo_cliente));?>
		<?php  echo $this->BForm->hidden('codigo_matriz', array('value' => $codigo_matriz));?>

		<div class='well' style="min-height: 225px">
			<div class='row-fluid inline'>
				<!-- <div class="control-group input text required"> -->
				<?php echo $this->BForm->input('cpf', array('label' => 'CPF (*)', 'class' => 'input cpf', 'type'=>'text'));?>	
				<?php echo $html->link('', 'javascript:void(0)', array('class' => 'icon-search', 'style' => 'margin-top:28px;')); ?>

				<?php 
				if (isset($hasUsuario) && $hasUsuario === false) {
					echo $this->Html->link('Criar Usuário', 'javascript:verifica_email('.$funcionarios['Funcionario']['codigo'].');', array('class' => 'btn btn-primary pull-right btn_cria_usuario'));
				}
				?>
				<!-- </div> -->
			</div>
			<div class='rpw-fluid inline'>
				<span class='span9' style="margin-left: 0px;">
					<div class='row-fluid inline'>
						<?php echo $this->BForm->hidden('codigo', array('id' => 'FuncionarioCodigo', 'value' =>  !empty($this->data['Funcionario']['codigo'])? $this->data['Funcionario']['codigo'] : ''));?>
						<?php echo $this->BForm->input('nome', array('label' => 'Nome (*)', 'class' => 'input-xxlarge', 'type'=>'text'));?>
						<?php echo $this->BForm->input('data_nascimento', array('label' => 'Data de Nascimento (*)', 'class' => 'input data', 'type'=>'text'));?>
					</div>
                    <div class="row-fluid inline">
                        <?php echo $this->Form->input('flg_nome_social',array('type'=>'checkbox', 'class' => 'input', 'label' => '(Deseja utilizar o nome social?)')) ?>                                                
                    </div>
                    <div class='row-fluid inline' id="row-nome-social">
                        <?php echo $this->BForm->input('nome_social', array('label' => 'Nome Social(*)', 'class' => 'input-xxlarge', 'type'=>'text'));?>
                    </div>
					<div class='row-fluid inline'>                        
						<?php echo $this->BForm->input('sexo', array('options' => array('F' => 'Feminino', 'M' => 'Masculino'), 'empty' => 'Selecione', 'label' => 'Sexo (*)', 'class' => 'input', 'default' => ''));?>
						<?php echo $this->BForm->input('codigo_identidade_genero', array('options' => $identidades_genero, 'empty' => 'Selecione', 'label' => 'Identidade de Gênero', 'class' => 'input', 'default' => ''));?>
						<?php echo $this->BForm->input('estado_civil', array('options' => array('1' => 'Solteiro', '2' => 'Casado', '3' => 'Separado', '4' => 'Divorciado', '5' => 'Viúvo', '6' => 'Outros'), 'empty' => 'Selecione', 'label' => 'Estado Civil', 'class' => 'input', 'default' => ''));?>
						<?php echo $this->BForm->input('deficiencia', array('options' => array('1' => 'SIM', '0' => 'NÃO'), 'empty' => 'Selecione', 'label' => 'É deficiente ?', 'class' => 'input', 'default' => '')); ?>	
					</div>
				</span>
				<?php if($edit_mode) : ?>
					<span class="span2 well" style="padding: 10px;margin-top: 10px;margin-left: 75px;">
						<ul class="unstyled" style="margin-bottom: 0px;">
							<li>
								<span class="label label-info">Data Inclusão</span>
								<ul class="unstyled">
									<li><?= ( !empty($funcionarios['Funcionario']['data_inclusao']) ? $funcionarios['Funcionario']['data_inclusao'] : '-' ); ?></li>
									<li><?= ( !empty($funcionarios['UsuarioInclusao']['apelido']) ? $funcionarios['UsuarioInclusao']['apelido'] : '-' ); ?></li>
								</ul>
							</li>
							<li>
								<span class="label label-info">Data Atualização</span>
								<ul class="unstyled">
									<li><?= ( !empty($funcionarios['Funcionario']['data_alteracao']) ? $funcionarios['Funcionario']['data_alteracao'] : '-' ); ?></li>
									<li><?= ( !empty($funcionarios['UsuarioAlteracao']['apelido']) ? $funcionarios['UsuarioAlteracao']['apelido'] : '-' ); ?></li>
								</ul>
							</li>
						</ul>
					</span>
				<?php endif; ?>
			</div>
		</div>

		<?php 
		$formUsuario = array( 'classe_div' => 'hidden formUsuario', 'hide_form' => true );
		if (isset($hasUsuario) && $hasUsuario) {
			$formUsuario = array( 'classe_div' => 'formUsuario', 'hide_form' => false );
		}
		?>
		<div class='well <?php echo $formUsuario['classe_div'] ?>'>
			<div class="row-fluid inline">
				<?php 
				echo $this->BForm->input('Usuario.codigo', array('type' => 'hidden'));
				echo $this->BForm->input('Usuario.apelido', array('disabled' => 'disabled', 'class' => 'usuariosenha'));
				echo $this->BForm->input('Usuario.senha', array('type' => 'password', 'disabled' => $formUsuario['hide_form'], 'class' => 'usuariosenha'));
				?>
			</div>
		</div>

		<div class='well'>
			<div class='row-fluid inline'>
				<?php echo $this->BForm->input('rg', array('label' => 'RG (*)', 'class' => 'input', 'type'=>'text'));?>
				<?php echo $this->BForm->input('rg_orgao', array('label' => 'Orgão Expedidor - RG (*)', 'class' => 'input', 'type'=>'text'));?>
				<?php echo $this->BForm->input('rg_data_emissao', array('label' => 'Data de Emissão - RG', 'class' => 'input data', 'type'=>'text'));?>
			</div>
			<div class='row-fluid inline'>
				<?php echo $this->BForm->input('ctps', array('label' => 'Carteira de Trabalho', 'class' => 'input', 'type'=>'text'));?>
				<?php echo $this->BForm->input('ctps_serie', array('label' => 'Série - Carteira de Trabalho', 'class' => 'input', 'type'=>'text'));?>
				<?php echo $this->BForm->input('ctps_uf', array('options' => $estados, 'empty' => 'Selecione', 'label' => 'Estado - Carteira de Trabalho', 'class' => 'input'));?>
				<?php echo $this->BForm->input('ctps_data_emissao', array('label' => 'Data de Emissão - Carteira de Trabalho', 'class' => 'input data', 'type'=>'text'));?>
			</div>
			<div class='row-fluid inline'>
				<?php echo $this->BForm->input('nit', array('label' => 'NIT (PIS/PASEP)', 'class' => 'input', 'type'=>'text'));?>
				<?php echo $this->BForm->input('cns', array('label' => 'Cartão Nacional de Saúde (CNS)', 'class' => 'input', 'type'=>'text'));?>
				<?php echo $this->BForm->input('gfip', array('label' => 'Guia de Recolhimento do FGTS (GFIP)', 'class' => 'input', 'type'=>'text'));?>   
			</div>
		</div>

		<div class='well'>
			<div class='row-fluid inline'>
				<?php echo $this->element('funcionarios_enderecos/fields') ?>
			</div>
		</div>

		<?php if ($edit_mode): ?>
			<div class='well'>
				<div class='row-fluid inline'>
					<?php echo $this->element('funcionarios/contatos') ?>
				</div>
			</div>
		<?php endif; ?>

		<?php if ($incluir): ?>
	  		<div class='well'>
				<div class='row-fluid inline'>
					<div class="pull-left">					
						<?php echo $this->BForm->input('ClienteFuncionario.matricula', array('label' => 'Matrícula (*)')); ?>
					</div>
					<div class="pull-left" style="margin-top: 30px;margin-left:9px;">
						<?php echo $this->Form->input('ClienteFuncionario.sem_matricula',array('type'=>'checkbox', 'class' => 'input-xlarge sem_matricula', 'label' => 'Não possui matrícula <abbr title="Preencher com código da categoria do trabalhador. Informar somente no caso de TSVE sem informação de matrícula no evento S-2300"><h11 style="font-size:0.95em;color: #00b1c4;font-weight:bold;">?</h11></abbr>')) ?>
						
					</div>
					<div class="pull-left" style="margin-top: 30px;margin-left:18px">
                		<?php echo $this->Form->input('ClienteFuncionario.pre_admissional',array('type'=>'checkbox', 'class' => 'input-xlarge pre_admissional', 'label' => 'Pré Admissional <abbr title="O campo matrícula deverá ser atualizado após a efetivação do colaborador para que o arquivo XML seja preenchido corretamente"><h11 style="font-size:0.95em;color: #00b1c4;font-weight:bold;">?</h11></abbr>')) ?>
					</div>
					<div style="clear: both;"></div>
					<div class="msg_nao_possui_matricula" style="position:absolute;margin-top: -18px;display: none;">
						<span style="color:#B94A48;font-size:13px"><p>A matrícula do colaborador será preenchida automaticamente com o número do CPF para que seja posteriormente atualizado.</p></span>
					</div>
				</div>
				<div class='row-fluid inline'>
					<div class="pull-left">
						<?php echo $this->BForm->input('ClienteFuncionario.admissao', array('type' => 'text', 'label' => 'Data de admissão (*)', 'class' => 'data input-small')); ?>
					</div>

                    <div class="pull-left margin-left-10">
                        <?php echo $this->BForm->input('ClienteFuncionario.centro_custo', array('type' => 'text', 'label' => 'Centro de Custo', 'maxlength' => '60')); ?>
                    </div>
                    <div class="pull-left margin-left-10" id="cat_colaborador" style="display: none;">
    					<?php echo $this->BForm->input('ClienteFuncionario.codigo_esocial_01', array('label' => 'Categoria do Colaborar (Tabela 01 - eSocial):', 'options' => $categoria_colaborador,'empty' => 'Selecione', 'class' => 'input-xlarge bselect2 pull-left categoria_colaborador')) ?>                    	
                    </div>
				</div>
				<div class="row-fluid js-line">
					<div class="pull-left">
						<?php echo $this->BForm->input('FuncionarioSetorCargo.codigo_cliente_alocacao', array('options' => $unidades, 'empty' => 'Selecione uma unidade', 'label' => 'Unidade (*)', 'class' => 'input-xlarge js-unidade')); ?>
					</div>
					<div class="pull-left margin-left-10">
						<?php echo $this->BForm->input('FuncionarioSetorCargo.codigo_setor', array('options' => array(), 'empty' => 'Selecione um setor', 'label' => 'Setor (*)', 'class' => 'input-xlarge js-setor')); ?>
					</div>
					<div class="pull-left margin-left-10">
						<?php echo $this->BForm->input('FuncionarioSetorCargo.codigo_cargo', array('options' => array(), 'empty' => 'Selecione', 'label' => 'Cargo (*)', 'class' => 'input-xlarge js-cargo'));?>
					</div>
				</div>
			</div>
		<?php endif; ?>

		<div class='form-actions'>
			<button class="btn btn-primary" id="saveFuncionarios"> Salvar</button>
			<?php //echo $this->BForm->submit('Salvar', array('div' => false, 'id' => 'saveFuncionarios', 'class' => 'btn btn-primary')); ?>
			<?php echo $html->link('Voltar', array('controller' => 'funcionarios', 'action' => 'index', $codigo_matriz, $referencia), array('class' => 'btn')); ?>
		</div>
		<?php echo $this->BForm->end(); ?>
	</div>

	<div class="tab-pane" id="historico">
		<h3>Histórico do Funcionário no grupo</h3>
		<hr>		
		<div class="text-right margin-bottom-10">		
			<?php echo $this->Form->button('Incluir matrícula', array('type' => 'button', 'class' => 'btn btn-success novaMatricula', 'data-toggle' => 'tooltip', 'title' => 'Incluir matrícula', 'escape' => false)); ?>
		</div>

		<div id="lista"></div>


		<div class='form-actions'>
			<?php echo $html->link('Voltar', array('controller' => 'funcionarios', 'action' => 'index', $codigo_matriz, $referencia), array('class' => 'btn')); ?>
		</div>
	</div> 
</div>


<!-- Modal incluir matricula -->
<div id="novaMatricula" class="modal modal-large hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="myModalLabel">Incluir Matrícula</h3>
	</div>
	<div class="modal-body">
		<div class="js-form">
			<div class="row-fluid">
				<div class="pull-left">
					<?php echo $this->BForm->hidden('ClienteFuncionario.codigo_funcionario', array('value' => $codigo)); ?>
					<?php echo $this->BForm->hidden('ClienteFuncionario.codigo_cliente_matricula', array('value' => $codigo_matriz)); ?>
					<?php echo $this->BForm->input('ClienteFuncionario.matricula', array('label' => '* Matrícula:')); ?>
				</div>
				<div class="pull-left" style="margin-top: 30px;margin-left:9px;">
					<?php echo $this->Form->input('ClienteFuncionario.sem_matricula',array('type'=>'checkbox', 'class' => 'input-xlarge sem_matricula', 'label' => 'Não possui matrícula <abbr title="Preencher com código da categoria do trabalhador. Informar somente no caso de TSVE sem informação de matrícula no evento S-2300"><h11 style="font-size:0.95em;color: #00b1c4;font-weight:bold;">?</h11></abbr>')) ?>
						
				</div>
				<div class="pull-left" style="margin-top: 30px;margin-left:18px">
            		<?php echo $this->Form->input('ClienteFuncionario.pre_admissional',array('type'=>'checkbox', 'class' => 'input-xlarge pre_admissional', 'label' => 'Pré Admissional <abbr title="O campo matrícula deverá ser atualizado após a efetivação do colaborador para que o arquivo XML seja preenchido corretamente"><h11 style="font-size:0.95em;color: #00b1c4;font-weight:bold;">?</h11></abbr>')) ?>
				</div>
				<div style="clear: both;"></div>
				<div class="msg_nao_possui_matricula" style="position:absolute;margin-top: -18px;display: none;">
					<span style="color:#B94A48;font-size:13px"><p>A matrícula do colaborador será preenchida automaticamente com o número do CPF para que seja posteriormente atualizado.</p></span>
				</div>
			</div>

			<div class="row-fluid js-line">
				<div class="pull-left">
					<?php echo $this->BForm->input('ClienteFuncionario.admissao', array('type' => 'text', 'label' => '* Data de admissão:', 'class' => 'data input-small')); ?>
				</div>
				<div class="pull-left margin-left-10">
					<?php echo $this->BForm->input('ClienteFuncionario.centro_custo', array('type' => 'text', 'label' => 'Centro de Custo', 'maxlength' => '60')); ?>
				</div>
				<div class="pull-left margin-left-10" id="cat_colaborador" style="display: none;">
					<?php echo $this->BForm->input('ClienteFuncionario.codigo_esocial_01', array('label' => 'Categoria do Colaborar (Tabela 01 - eSocial):', 'options' => $categoria_colaborador,'empty' => 'Selecione', 'class' => 'input-xlarge bselect2 pull-left categoria_colaborador')) ?>                    	
				</div>
			</div>

			<div class="row-fluid js-line">
				<div class="pull-left">
					<?php echo $this->BForm->input('FuncionarioSetorCargo.0.codigo_cliente_alocacao', array('options' => $unidades, 'empty' => 'Selecione uma unidade', 'label' => '* Unidade:', 'class' => 'input-xlarge js-unidade')); ?>
				</div>
				<div class="pull-left margin-left-10">
					<?php echo $this->BForm->input('FuncionarioSetorCargo.0.codigo_setor', array('options' => array(), 'empty' => 'Selecione um setor', 'label' => '* Setor:', 'class' => 'input-xlarge js-setor')); ?>
				</div>
				<div class="pull-left margin-left-10">
					<?php echo $this->BForm->input('FuncionarioSetorCargo.0.codigo_cargo', array('options' => array(), 'empty' => 'Selecione', 'label' => '* Cargo', 'class' => 'input-xlarge js-cargo'));?>
				</div>

			</div>
		</div>
	</div>
	<div class="modal-footer">
		<button type="button" class="btn cancelar" data-dismiss="modal" aria-hidden="true">Cancelar</button>
		<button type="button" class="btn btn-primary salvar">Adicionar</button>
	</div>
</div>

<!-- MODAL HIERARQUIA ALERTA PENDENTE PARA O CLIENTE -->
<!-- <div class="modal fade" id="modal_hierarquia_pendente" data-backdrop="static" data-keyboard="false"> -->
<div class="modal hide fade hierarquia_pendente" id="modal_hierarquia_pendente" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalHierarquiaPendente" aria-hidden="true">
	<div class="modal-dialog modal-lg" style="position: static;">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="myModalHierarquiaPendente">PGR e PCMSO pendentes</h4>
			</div>
			<div class="modal-body" style="height: 350px;font-size: 14px">
				<br>
				<p>Depois de incluir um funcionário no sistema IT.Health, você criou uma nova combinação (ou hierarquia, se preferir esse termo) de unidade + setor + cargo.</p><br>
				<p>Ou seja, você ainda não possui aplicação de risco (PGR) e exame (PCMSO) vinculados a esse novo funcionário e, dessa maneira, não consegue emitir pedidos para gerar um ASO.</p>
				<div style="clear: both;"><br><br>
				<p>Assim que esse risco e exame forem aplicados, <b>você será notificado por e-mail</b> para que possa dar continuidade ao processo.<br>Não será possível emitir um pedido de exame até que essas aplicações sejam realizadas.</p></b><br><br>
				<p>Ficou claro? Se não, não pense duas vezes antes de procurar nosso time (<a href="mailto:relacionamento@rhhealth.com.br">relacionamento@rhhealth.com.br</a>) para mais esclarecimentos.</p>
			
			</div>
			
			</div>
			<div class="form-actions" id="rodape_botoes" style="margin-bottom: 5px;">
				<!--a href="javascript:void(0);" onclick="manipula_modal('modal_hierarquia_pendente', 0);" class="btn btn-danger"><i class="glyphicon glyphicon-fast-backward"></i> FECHAR</a-->
				<button type="button" class="btn cancelar" data-dismiss="modal">Fechar</button>
			</div>
		</div>
	</div>
</div>
<!-- FINAL MODAL HIERARQUIA ALERTA PENDENTE PARA O CLIENTE -->
<?php
//Verifica se na criação do funcionário, um alerta de hierarquia pendente foi gerado
$exibe_alerta_pendente = $this->Session->read('alerta_hierarquia_pendente');
?>
<script type="text/javascript">

	function atualizaLista(codigo_cliente, codigo_funcionario) {
		var div = $('#lista');
		bloquearDiv(div);
		div.load(baseUrl + 'clientes_funcionarios/listagem_matriculas/' + codigo_cliente + '/' + codigo_funcionario);
	}


	$(document).ready(function() {
		atualizaLista('<?php echo $codigo_matriz ?>', '<?php echo $codigo ?>');
		$('.novaMatricula').click(function(event) {
			$('#novaMatricula').modal('show');
		});


<?php  
    if(!empty($exibe_alerta_pendente)) {
    	if($exibe_alerta_pendente  == 1 ){
      ?>
			$('#modal_hierarquia_pendente').modal('show');
<?php	
    	}
    	$this->Session->delete('alerta_hierarquia_pendente');
    }
?>
		/**
		 * [valida_campos_obrigatorios Valida os campos obrigatórios]
		 * @param  {object}  param [objetos do formulario nova matrícula]
		 * @return {boolean}       
		 */
		function valida_campos_obrigatorios(param){			

			var unidade = param.find('.js-unidade').val();
			var setor 	= param.find('.js-setor').val();
			var cargo 	= param.find('.js-cargo').val();
			var matricula = param.find('#ClienteFuncionarioMatricula').val();			
			var type 	= 'warning';
			var title 	= 'Atenção';
			var text 	= '';
			var error 	= false;			

            var flg_nome_social = $('input#FuncionarioFlgNomeSocial').is(':checked');   
            var nome_social = $('input#FuncionarioNomeSocial').val();       
            
            if(flg_nome_social && nome_social.trim() == '') {
                text = 'O campo "Nome Social" é obrigatório.';
                error = true;
            }

			if(matricula == ''){
				$(".sem_matricula").each(function(indice){

					var id = $(this).prop('id');		

					if($('#'+id).prop('checked')) {
						 
						if($(this).val() == 0){
							text = 'O campo Matrícula é obrigatório'
							error = true;						
							$( "#ClienteFuncionarioMatricula").focus();
						}
					}       
				});
			}
                    



			if(unidade == ''){
				text = 'O campo Unidade é obrigatório'
				error = true;
				$( "#FuncionarioSetorCargo0CodigoClienteAlocacao").focus();
			} else if(setor == ''){
				text = 'O campo Setor é obrigatório'
				error = true;
				$( "#FuncionarioSetorCargo0CodigoSetor").focus();
			} else if(cargo == ''){
				text = 'O campo Cargo é obrigatório'
				error = true;
				$( "#FuncionarioSetorCargo0CodigoCargo").focus();
			}


			$(".sem_matricula").each(function(indice){ //Caso o usuário selecione a flag: Não possui matrícula, o campo Categoria do Colaborador é obrigatório.
				var id = $(this).prop('id');						
				if($('#'+id).prop('checked')) {    		
					if(this.value == 1){					      			
						$("#ClienteFuncionarioCodigoEsocial01").each(function(indice){												
							if(this.value == ''){
								$('#ClienteFuncionarioCodigoEsocial01').focus();	
								text = 'O campo Categoria de Colaborador é obrigatório'
								error = true;								
							}
						});
					}       	
				}
			});	

			if(error){
				swal({
					type: type,
					title: title,
					text: text,
				});

				return false;
			}else{
				return true;
			}
		}//FINAL FUNCTION valida_campos_obrigatorios

		$('#novaMatricula .cancelar').click(function(event) {
			$('#FuncionarioSetorCargo0CodigoClienteAlocacao').val('');
			$('#FuncionarioSetorCargo0CodigoSetor').html('<option value="">Selecione um Setor</option>');
			$('#FuncionarioSetorCargo0CodigoCargo').html('<option value="">Selecione um Cargo</option>');
		});

		$('#novaMatricula .salvar').click(function(event) {
			var dados = new Object();
			var este = $('#novaMatricula');
			var botao = $(this);			
			if(!valida_se_existe_data(este.find('.data').val(), 'O campo Data de admissão é obrigatório.')) {
				$( "#ClienteFuncionarioAdmissao").focus();
				return false;
			} else if(!valida_campos_obrigatorios(este)){
				return false;
			}

			var button = $(this).html();
			este.find('[data-dismiss="modal"]').hide();
			$(this).css({height: $(this).outerHeight(), width: $(this).outerWidth() }).html('<img src="' + baseUrl + 'img/default.gif">');
			$('#novaMatricula input, #novaMatricula select').each(function(index, val) {
				dados[val.name.substr(0, val.name.length - 1).substr(5)] = val.value;
			});
			$.ajax({
				url: baseUrl + 'clientes_funcionarios/salva_matricula',
				type: 'POST',
				dataType: 'json',
				data: {dados: dados},
			})
			.done(function(response) {
				$('#novaMatricula').modal('hide');
				botao.html(button);
				$('#ClienteFuncionarioMatricula').val('');
				$('#ClienteFuncionarioAdmissao').val('');
				$('#FuncionarioSetorCargo0CodigoCliente').val('');
				$('#FuncionarioSetorCargo0CodigoSetor').html('<option value="">Selecione um Setor</option>');
				$('#FuncionarioSetorCargo0CodigoCargo').html('<option value="">Selecione um Cargo</option>');
				este.find('[data-dismiss="modal"]').show();

				if(response.type === 'success'){
					alerta_hierarquia_pendente(dados,response.text);
					atualizaLista('<?php echo $codigo_matriz ?>', '<?php echo $codigo ?>');
				} else {
					swal({
						type: response.type,
						title: response.title,
						text: response.text,
					});
				}
				
			})
			.fail(function() {
				$('#novaMatricula').modal('hide');
				botao.html(button);
				$('#ClienteFuncionarioMatricula').val('');
				$('#ClienteFuncionarioAdmissao').val('');
				$('#FuncionarioSetorCargo0CodigoCliente').val('');
				$('#FuncionarioSetorCargo0CodigoSetor').html('<option value="">Selecione um Setor</option>');
				$('#FuncionarioSetorCargo0CodigoCargo').html('<option value="">Selecione um Cargo</option>');
				este.find('[data-dismiss="modal"]').show();
			})
		});//FINAL CLICK #novaMatricula .salvar


		$('#saveFuncionarios').on("click", function(e){
			e.preventDefault();
			
			var retorno = true;

			var edicao = $('#FuncionarioEditMode').val();

			if(edicao != 1){			
				$(".sem_matricula").each(function(indice){ //Caso o usuário selecione a flag: Não possui matrícula, o campo Categoria do Colaborador é obrigatório.
					var id = $(this).prop('id');						
					if($('#'+id).prop('checked')) {    		
						if(this.value == 1){					      			
							$("#ClienteFuncionarioCodigoEsocial01").each(function(indice){												
								if(this.value == ''){
									$('#ClienteFuncionarioCodigoEsocial01').focus();	
									swal({
										type: 'warning',
										title: 'Atenção',
										text: 'O campo Categoria de Colaborador é obrigatório.'
									});
									retorno = false;
								}
							});
						} else if(this.value == 0){					
							$("#ClienteFuncionarioMatricula").each(function(indice){												
								if(this.value == ''){
									$('#ClienteFuncionarioMatricula').focus();	
									swal({
										type: 'warning',
										title: 'Atenção',
										text: 'O campo Matricula é obrigatório.'
									});
									retorno = false;
								}
							});
						}       	
					} 
				});		
			}


			if(retorno == true){

				if(edicao == 1){						
					$("#FuncionarioEditarForm").submit();
				} else {
					$("#FuncionarioIncluirForm").submit();
				}
			}
		});
	});	
</script>


<script type="text/javascript">
	jQuery(document).ready(function() { 
		setup_mascaras(); 
		setup_datepicker(); 
		setup_time(); 


        $('input#FuncionarioFlgNomeSocial').on('change', function(e) {

            if($(this).is(':checked'))
                $('div#row-nome-social').show();
            else {
                $('div#row-nome-social').hide();        
                $('input#FuncionarioNomeSocial').val('');
            }
        });   
        
        if($("input#FuncionarioFlgNomeSocial").is(':checked')) {

            $('div#row-nome-social').show();
        } else {

            $('div#row-nome-social').hide();
        }

		$('#FuncionarioCpf').blur(function(){
			preenche_dados($(this).val());
		});
		// var bloqueado = '<?php // echo ($bloqueado? 1 : 0) ?>';
		// if(bloqueado > 0) {
		// 	var cargos =  JSON.parse('<?php // echo json_encode($cargos) ?>');
		// 	$('.js-setor').change(function(event) {
		// 		var options = '<option value="">Selecione</option>';
		// 		if(this.value != '') {
		// 			options = '<option value="">Selecione</option>';
		// 			$.each(cargos[this.value], function(index, val){
		// 				options += '<option value="' + index + '">' + val + '</option>';
		// 			});
		// 		} 
		// 		$(this).parents('.js-line').find('.js-cargo').html(options);
		// 	});
		// }

        $(document).on('change', '.sem_matricula', function(e) {
        	e.preventDefault();
        	sem_matricula();
        });

        $(".sem_matricula").each(function(indice){
        	sem_matricula();
        });

        function sem_matricula(){
        	var cpf = $('#FuncionarioCpf').val();
        	cpf = cpf.replace(/\.|\-/g, '');         	
        	
        	if ( $('#ClienteFuncionarioSemMatricula').is(":checked") ) {        		
        		$('#ClienteFuncionarioMatricula').attr('readonly', true);        		        		
        		$('#ClienteFuncionarioPreAdmissional').attr('disabled', true);        		        		
        		$("#cat_colaborador").show();        		
        		$('#ClienteFuncionarioMatricula').val(cpf);        		        		
        		$('#ClienteFuncionarioPreAdmissional').val('');
        		$('.msg_nao_possui_matricula').show();        		        		
        	} else {
        		$('#ClienteFuncionarioMatricula').attr('readonly', false);
        		$('#ClienteFuncionarioMatricula').val('');        		        		
        		$('#ClienteFuncionarioPreAdmissional').attr('disabled', false);        		        		
        		$("#cat_colaborador").hide();     		        		        		
        		$('.msg_nao_possui_matricula').hide();        		        		
        	}
        }

        $(document).on('change', '.pre_admissional', function(e) {
        	e.preventDefault();
        	pre_admissional();
        });

        $(".pre_admissional").each(function(indice){
        	pre_admissional();
        });

        function pre_admissional(){
        	var cpf = $('#FuncionarioCpf').val();
        	cpf = cpf.replace(/\.|\-/g, '');

        	if ( $('#ClienteFuncionarioPreAdmissional').is(":checked") ) {
        		$('#ClienteFuncionarioMatricula').attr('readonly', true);
        		$('#ClienteFuncionarioSemMatricula').attr('disabled', true);
        		$('.msg_nao_possui_matricula').show();
        		$('#ClienteFuncionarioMatricula').val(cpf);
        		$("#cat_colaborador").hide();   
        	} else {
        		$('#ClienteFuncionarioMatricula').attr('readonly', false);
        		$('#ClienteFuncionarioSemMatricula').attr('disabled', false);
        		$('.msg_nao_possui_matricula').hide();
        		$('#ClienteFuncionarioMatricula').val('');
        		$("#cat_colaborador").hide();
        	}
        }
	});


    function verifica_email(codigo_funcionario){

      $.ajax({
        type: "POST",
        url: baseUrl + "funcionarios/verifica_email_funcionario/" + codigo_funcionario + "/" + Math.random(),
        dataType: "json",
        success: function(data) {
        	
        	if (data){
        		Usuario.abreModal();
        	} else {
        		swal({
	                type: 'warning',
	                title: 'Atenção',
	                text: 'Para cadastrar um usuário ao funcionário, é necessário que o mesmo possua pelo menos um e-mail de contato!'
	            });
        	}
          
        }
      });

    }

	function preenche_dados(cpf){
		$.ajax({
			type: 'POST',
			url: baseUrl + 'funcionarios/preenche_dados/' + cpf + '/' + Math.random(),
			dataType: 'json',
			success: function(data) {
				if(data != false){
					$.each(data.Funcionario, function() {
						$('#FuncionarioNome').val(data.Funcionario['nome']);
						$('#FuncionarioDataNascimento').val(data.Funcionario['data_nascimento']);
						$('#FuncionarioSexo').val(data.Funcionario['sexo']);
						$('#FuncionarioRg').val(data.Funcionario['rg']);
						$('#FuncionarioRgOrgao').val(data.Funcionario['rg_orgao']);
						$('#FuncionarioRgDataEmissao').val(data.Funcionario['rg_data_emissao']);
						$('#FuncionarioCtps').val(data.Funcionario['ctps']);
						$('#FuncionarioCtpsSerie').val(data.Funcionario['ctps_serie']);
						$('#FuncionarioCtpsUf').val(data.Funcionario['ctps_uf']);
						$('#FuncionarioCtpsDataEmissao').val(data.Funcionario['ctps_data_emissao']);
						$('#FuncionarioNit').val(data.Funcionario['nit']);
						$('#FuncionarioCns').val(data.Funcionario['cns']);
						$('#FuncionarioGfip').val(data.Funcionario['gfip']);
					});
				}
			}
		});    
	}

	function data_fim_menor_inicio_aquisi(data_fim, data_inicio) {	
		if(data_fim != ''){
			data_fim = data_fim.split('/');
			data_inicio = data_inicio.split('/');

			if(parseInt(data_fim[2] + data_fim[1] + data_fim[0]) <= parseInt(data_inicio[2] + data_inicio[1] + data_inicio[0])) {	
				swal({
					type: 'warning',
					title: 'Atenção',
					text: 'Data Fim Período Aquisitivo deve ser maior à Data Início Período Aquisitivo.',
				});
				return false;
			} else {
				return true;
			}
		}
	}
</script>


<style>
	.modal.modal-large{
		width: 860px;
		margin-left: -430px;
	}
</style>