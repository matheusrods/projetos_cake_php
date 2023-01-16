<div class="well">
	<h4>Dados Pessoais:</h4>
    <strong>Nome: </strong><?php echo $this->Html->tag('span', $dados['funcionario_nome']); ?>&emsp;&emsp;
    <strong>Matrícula: </strong><?php echo $this->Html->tag('span', $dados['funcionario_matricula']); ?>&emsp;&emsp;
    <strong>CPF: </strong><?php echo $this->Html->tag('span', $dados['funcionario_cpf']); ?>&emsp;&emsp;
    <strong>Telefone: </strong><?php echo $this->Html->tag('span', $dados['telefone']); ?>&emsp;&emsp;
    <strong>Email: </strong><?php echo $this->Html->tag('span', $dados['email']); ?>
    <hr />
    <h4>Contato de emergência:</h4>
    <strong>Nome Contato de Emergência: </strong><?php echo $this->Html->tag('span', $dados_contato_emergencia['nome']); ?>&emsp;&emsp;
    <strong>E-mail: </strong><?php echo $this->Html->tag('span', $dados_contato_emergencia['email']); ?>&emsp;&emsp;
    <strong>Telefone: </strong><?php echo $this->Html->tag('span', $dados_contato_emergencia['telefone']); ?>&emsp;&emsp;
    <strong>Grau de Parentesco: </strong><?php echo $this->Html->tag('span', $dados_contato_emergencia['grau_parentesco']); ?>
     <hr />
    <?php 
    $cpf = str_replace('.', '', str_replace('-','',$dados['funcionario_cpf']));
    ?>
    <a href="#" class="btn" onclick="retorno('<?php echo $cpf; ?>', 1);"> Retorno ao Trabalho</a>
    &nbsp;&nbsp;
    <a href="#" class="btn" onclick="sintomas('<?php echo $cpf; ?>', 1);"> Sintomas Diários</a>

</div>

<?php echo $this->BForm->create('UsuarioGrupoCovid', array('type' => 'post', 'enctype' => 'multipart/form-data', 'url' => array('controller' => 'usuario_grupo_covid','action' => 'editar', $codigo_usuario, $codigo_cliente_funcionario, $codigo_funcionario_setor_cargo, ))); ?>
	
	<?php echo $this->BForm->input('UsuarioGrupoCovid.data_corrente', array('type' => 'hidden', 'id' => 'data_corrente', 'value' => date('d/m/Y'))); ?>
	<?php echo $this->BForm->input('UsuarioGrupoCovid.codigo_usuario', array('type' => 'hidden', 'id' => 'codigo_usuario', 'value' => $codigo_usuario)); ?>
	<?php echo $this->BForm->input('UsuarioGrupoCovid.codigo', array('type' => 'hidden', 'id' => 'codigo_usuario_grupo_covid', 'value' => $codigoUsuarioGrupoCovid)); ?>
	<?php echo $this->BForm->input('UsuarioGrupoCovid.cpf', array('type' => 'hidden', 'id' => 'cpf', 'value' => $dados['funcionario_cpf'])); ?>
	
	
	<?php 
	// if(!empty($dados_atestado)){
	// 	echo $this->BForm->input('AnexoAtestado.codigo_atestado', array('type' => 'hidden', 'id' => 'codigo_usuario_exames', 'value' => $dados_atestado['Atestado']['codigo']));
	// }
	?>

	<div class="well">

		<div class='row-fluid inline'>
			<?php echo $this->BForm->input('UsuarioGca.data_resultado_exame', array('type' => 'text', 'label' => 'Data do Resultado do Exame', 'class' => 'data input-medium', 'value' => '')); ?>
			
			<?php echo $this->BForm->input('UsuarioGca.resultado_exame', array('label' => 'Resultado do exame', 'class' => 'input-large resultado', 'options' => $resultado_covid, 'empty' => 'Selecione', 'default' => '', 'type' => 'select', 'value' => '')) ?>
			
			
			<!-- <div class="control-group input text">
				<label>Afastamento</label>
				<?php //echo $html->link('Atestado',array('controller'=>'atestados','action'=>'incluir',$codigo_cliente_funcionario,$codigo_funcionario_setor_cargo, $dados['codigo']) , array('class' => 'btn','target' => '_blank')); ?>
			</div> -->
		
		</div>
		<div class='row-fluid inline'>
			<label><b>Reversão de passaporte do dia</b></label>
		</div>
		<div class='row-fluid inline'>			
			<?php echo $this->BForm->input('UsuarioGca.volta_grupo',array('type'=>'checkbox','value'=>'S', 'class' => 'input-xlarge', 'label' => 'Reverter o passaporte vermelho para verde e retornar o colaborador para o grupo anterior: ' . $label_grupo_anterior)) ?>
		</div>
		
		<div class='row-fluid inline'>
			
			<?php 
			if($not_afastamento) {
				echo $this->Form->input('UsuarioGca.afastamento_sintomas_hidden',array('type'=>'checkbox','value'=>'S', 'class' => 'input-xlarge','disabled' => 'disabled', 'label' => 'Afastar colaborador com sintomas (Reverter passaporte do dia para vermelho e retornar ao grupo Laranja):'));
				echo '<div style="width: 200px;float: left;margin-top: -29px;">';
					echo $this->BForm->input('UsuarioGca.data_fim_afastamento_hidden', array('type' => 'text', 'label' => 'Data fim do afastamento', 'class' => 'data input-medium', 'disabled' => 'disabled', 'value' => ''));
				echo '</div>';
			}
			else {

				echo $this->Form->input('UsuarioGca.afastamento_sintomas',array('type'=>'checkbox','value'=>'S', 'class' => 'input-xlarge', 'label' => 'Afastar colaborador com sintomas (Reverter passaporte do dia para vermelho e retornar ao grupo Laranja):'));
				echo '<div style="width: 200px;float: left;margin-top: -29px;">';
					echo $this->BForm->input('UsuarioGca.data_fim_afastamento', array('type' => 'text', 'label' => 'Data fim do afastamento', 'class' => 'data input-medium', 'value' => ''));
				echo '</div>';
			}
			?>
		</div>

		<div class='row-fluid inline'>
			
			<?php 
			if($not_afastamento) {
				echo $this->Form->input('UsuarioGca.afastamento_positivado_hidden',array('type'=>'checkbox','value'=>'S', 'class' => 'input-xlarge','disabled' => 'disabled', 'label' => 'Afastar colaborador positivado (Reverter passaporte do dia para vermelho e retornar ao grupo Vermelho)'));
			}
			else {
				echo $this->Form->input('UsuarioGca.afastamento_positivado',array('type'=>'checkbox','value'=>'S', 'class' => 'input-xlarge', 'label' => 'Afastar colaborador positivado (Reverter passaporte do dia para vermelho e retornar ao grupo Vermelho)'));
			}
			?>
		</div>

		<div class='row-fluid inline'>
			<label><b>Óbito</b></label>
			<?php 
				echo $this->BForm->input('UsuarioGca.data_fim_obito', array('type' => 'text', 'label' => "Data de Óbito", 'class' => 'data input-medium', 'value' => ''));
			?>
		</div>

		<div class='row-fluid inline'>
			<?php echo $this->Form->input('UsuarioGca.solicita_exame',array('type'=>'checkbox','value'=>'S', 'class' => 'input-xlarge', 'label' => 'Solicitado Teste Covid-19')) ?>
		</div>
		<div class='row-fluid inline' id='div_aguardar_resultado'>
			<?php // echo $this->Form->input('UsuarioGca.dias_aguardar_resultado', array('label' => false, 'class' => 'form-control span3', 'id' => 'dias_aguardar_resultado', 'before' => 'Dias Aguardar Resultado ')); ?>			
			<?php echo $this->Form->input('UsuarioGca.data_aguardar_resultado', array('type' => 'text', 'label' => false, 'class' => 'data input-small', 'value' => '', 'id' => 'data_aguardar_resultado', 'before' => 'Data Prevista para Liberação de Exame: ')); ?>


		</div>
		<br>
		<label><b>Upload de arquivos</b></label>
		<div class='row-fluid '>
			<?php
				echo $this->BForm->input('UsuarioGcaAnexos.exame', array('type'=>'file', 'label' => 'Upload de resultado de exame'));
				echo $this->BForm->button('Limpar campo do anexo', array('type'=>'button', 'label' => 'Limpar', 'id' => 'UsuarioExameImagemExame', 'class' => 'btn btn-anexos'));
			?>
		</div>
		<br>
		<div class='row-fluid '>
			<?php echo $this->BForm->input('UsuarioGcaAnexos.exame2', array('type'=>'file', 'label' => 'Upload Outro Arquivos')); ?>
			<?php echo $this->BForm->button('Limpar campo do anexo', array('type'=>'button', 'label' => 'Limpar', 'id' => 'UsuarioExameImagemExame2', 'class' => 'btn btn-anexos')); ?>
		</div>
		<div>
			<label>Observação:</label>
			<?php echo $this->BForm->textarea('UsuarioGca.observacao',array('style' => 'min-height:150px; min-width:450px')) ?>
		</div>
	</div>

<div class="form-actions">	
	<a id="ItemPedidoExameBaixaOk" href="javascript:void(0);" class="btn btn-success">Salvar</a>

	<?php echo $html->link('Voltar',array('controller'=>'usuario_grupo_covid','action'=>'index') , array('class' => 'btn')); ?>
</div>
<?php echo $this->BForm->end();?>


<h3>Lista de atendimentos:</h3>
<table class="table table-striped">
    <thead>
        <tr>
            <th>Usuário Inclusão</th>
            <th>Data Inclusão</th>
            <th>Volta Ao Grupo</th>
            <th>Solicitou Exame</th>
            <th>Dias Aguardar Resultado</th>
            <th>Data Aguardar Resultado</th>
            <th>Data Resultado</th>
            <th>Resultado</th>
            <th>Afastamento Sintomas</th>
            <th>Data Fim Afastamento</th>
            <th>Afastamento Positivado</th>
            <th>Data Óbito</th>
            <th>Atestado</th>
            <th>Observação</th>
            <th>Anexos</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        if($lista_atendimento){
        	foreach ($lista_atendimento as $dados){ 
        		?>
		    <tr>
				<td><?php echo $dados['UsuarioGca']['nome']; ?></td>
				<td><?php echo $dados['UsuarioGca']['data_inclusao']; ?></td>
				<td><?php echo ($dados['UsuarioGca']['volta_grupo'] == 1) ? 'Sim':'Não'; ?></td>
				<td><?php echo ($dados['UsuarioGca']['solicita_exame'] == 1) ? 'Sim' : 'Não'; ?></td>
				<td><?php echo $dados['UsuarioGca']['dias_aguardar_resultado']; ?></td>
				<td><?php echo $dados['UsuarioGca']['data_aguardar_resultado']; ?></td>
				<td><?php echo $dados['UsuarioGca']['data_resultado_exame']; ?></td>
				<td><?php echo (!empty($dados['UsuarioGca']['resultado_exame'])) ? $resultado_covid[$dados['UsuarioGca']['resultado_exame']] : ''; ?></td>
				<td><?php echo ($dados['UsuarioGca']['afastamento_sintomas'] == 1) ? 'Sim' : 'Não'; ?></td>
				<td><?php echo $dados['UsuarioGca']['data_fim_afastamento']; ?></td>
				<td><?php echo ($dados['UsuarioGca']['afastamento_positivado'] == 1) ? 'Sim' : 'Não'; ?></td>
				<td><?php echo $dados['UsuarioGca']['data_fim_obito']; ?></td>
				<td><?php echo $dados['UsuarioGca']['codigo_atestado']; ?></td>
				<td><?php echo $dados['UsuarioGca']['observacao']; ?></td>
				<td>
					<?php
					if(!empty($dados['UsuarioGca']['UsuarioGcaAnexos'])){
        				foreach ($dados['UsuarioGca']['UsuarioGcaAnexos'] as $anexo){ 
        					echo "<a href='".$anexo['anexo']."' target='_blank'>Anexo Exame</a>";
        				}
        			}
        			?>
				</td>
		    </tr>
	        <?php 
	    	} 
	    }else{ ?>
	    	<tr><td colspan="13">Nenhum</td></tr>
	    <?php } ?>
	</tbody>       
</table>

<div class="modal fade" id="modal_retorno" data-backdrop="static"></div>
<div class="modal fade" id="modal_sintomas" data-backdrop="static"></div>

<?php echo $this->Javascript->codeBlock('
	$(document).ready(function(){
		setup_mascaras();
		setup_datepicker();
		setup_time();

		// $("#div_aguardar_resultado").hide();

		var botaoOk = $("#ItemPedidoExameBaixaOk");
		var habilitarBotao = false;
		botaoOk.addClass( "disabled" );

		validaBotaoSub = function() {
			
			var checado=false;
			$("input[type=checkbox]").each(function(){				
			    if($(this).prop("checked")) {
			       checado=true;
			    }
			});

			$("input[type=text]").each(function(){				
			    if($(this).val() != "") {
			       checado=true;
			    }
			});

			$("input[type=file]").each(function(){				
			    if($(this).val() != "") {
			       checado=true;
			    }
			});
			
			
			if(checado){
				botaoOk.removeClass( "disabled" );
				habilitarBotao = true;
			}
			else {

				if($("#UsuarioGcaObservacao").val() != "") {
					botaoOk.removeClass( "disabled" );
					habilitarBotao = true;
				}
				else {
					botaoOk.addClass( "disabled" );
					habilitarBotao = false;
				}
			}

		}
		validaBotaoSub();

		$("input[type=checkbox]").change(function() {
			validaBotaoSub();
	  	});

	  	$("input[type=text]").change(function() {
			validaBotaoSub();
	  	});

	  	$("input[type=file]").change(function() {
			validaBotaoSub();
	  	});
	  	
	  	$("#UsuarioGcaObservacao").change(function() {
	  		validaBotaoSub();
	  	});

		botaoOk.click(function() {
			if( true == habilitarBotao ){

				//verifica se o afastamento_sintomas esta selecionado precisa da data do afastamento como obrigatorio
				if($("#UsuarioGcaAfastamentoSintomas").prop("checked")) {
					//verfica se a data do afastamento tem valor
					if($("#UsuarioGcaDataFimAfastamento").val() == "") {
						swal("Atenção", "Ao selecionar esta opção, a data fim do afastamento deve ser preenchida.", "warning");
						$("#UsuarioGcaDataFimAfastamento").focus();
						return false;
					}
					//verfifica se a data fim afastamento e menor que a data corrente
					if($("#UsuarioGcaDataFimAfastamento").val() != "") {
						
						var inicio = $("#UsuarioGcaDataFimAfastamento").val();
					    var fim = $("#data_corrente").val();
					    
					    if (gerarData(inicio) < gerarData(fim)) {
							$("#UsuarioGcaDataFimAfastamento").val("");
							swal("Atenção", "Data inferior à data atual, você deve informar uma data de fim de afastamento igual ou maior que a data de hoje.", "warning");
							return false;
						}
					}

				}
				$("#UsuarioGrupoCovidEditarForm").submit();

				//salvar_realizacao(<?php echo $codigo_item_pedido; ?>);
			}
	  	});

		$("#UsuarioGcaVoltaGrupo").click(function(){
			if($("#UsuarioGcaVoltaGrupo").prop("checked")){
				
				$("#UsuarioGcaAfastamentoSintomas").attr("disabled", true);
				$("#UsuarioGcaDataFimAfastamento").attr("disabled", true);

				$("#UsuarioGcaAfastamentoPositivado").attr("disabled", true);

				swal("Atenção", "Ao selecionar esta opção, o colaborador receberá no APP LYN um passaporte verde com validade para a data de hoje e retornará ao grupo anterior.", "warning");
			}
			else {
				$("#UsuarioGcaAfastamentoSintomas").removeAttr("disabled");
				$("#UsuarioGcaDataFimAfastamento").removeAttr("disabled");

				$("#UsuarioGcaAfastamentoPositivado").removeAttr("disabled");
			}
		});

		$("#UsuarioGcaAfastamentoSintomas").click(function(){
			if($("#UsuarioGcaAfastamentoSintomas").prop("checked")){
				$("#UsuarioGcaVoltaGrupo").attr("disabled", true);
				$("#UsuarioGcaAfastamentoPositivado").attr("disabled", true);
			}
			else {
				$("#UsuarioGcaVoltaGrupo").removeAttr("disabled");
				$("#UsuarioGcaAfastamentoPositivado").removeAttr("disabled");
			}
		});

		$("#UsuarioGcaAfastamentoPositivado").click(function(){
			if($("#UsuarioGcaAfastamentoPositivado").prop("checked")){
				
				$("#UsuarioGcaAfastamentoSintomas").attr("disabled", true);
				$("#UsuarioGcaDataFimAfastamento").attr("disabled", true);

				$("#UsuarioGcaVoltaGrupo").attr("disabled", true);

				
			}
			else {
				$("#UsuarioGcaAfastamentoSintomas").removeAttr("disabled");
				$("#UsuarioGcaDataFimAfastamento").removeAttr("disabled");

				$("#UsuarioGcaVoltaGrupo").removeAttr("disabled");
			}
		});

		gerarData = function(str) {
		    var partes = str.split("/");
		    return new Date(partes[2], partes[1] - 1, partes[0]);
		}

		retorno = function(cpf,mostra) {
			if(mostra) {
				var div = jQuery("div#modal_retorno");
				bloquearDiv(div);
				div.load(baseUrl + "usuario_grupo_covid/modal_retorno/" + cpf + "/" + Math.random());
		
				$("#modal_retorno").css("z-index", "1050");
				$("#modal_retorno").modal("show");

			} else {
				$(".modal").css("z-index", "-1");
				$("#modal_retorno").modal("hide");
			}
		}

		sintomas = function(cpf,mostra) {
			if(mostra) {
				var div = jQuery("div#modal_sintomas");
				bloquearDiv(div);
				div.load(baseUrl + "usuario_grupo_covid/modal_sintomas/" + cpf + "/" + Math.random());
		
				$("#modal_sintomas").css("z-index", "1050");
				$("#modal_sintomas").modal("show");

			} else {
				$(".modal").css("z-index", "-1");
				$("#modal_sintomas").modal("hide");
			}
		}

	});

	$("#UsuarioExameImagemExame").click(function(){
		$("#UsuarioGcaAnexosExame").val("");
		validaBotaoSub();
    });

    $("#UsuarioExameImagemExame2").click(function(){
        $("#UsuarioGcaAnexosExame2").val("");                
    	validaBotaoSub();
    }); 
'); ?>
