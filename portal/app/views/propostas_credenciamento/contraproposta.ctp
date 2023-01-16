				<h3>Serviços Incluídos na Proposta:</h3>
				
				<div class="alert alert-warning" style="display: none;">
				  <strong>Atenção!</strong> Você já enviou o posicionamento referente aos serviços prestados.<br />
				  Aguarde e em breve entraremos em contato!
				  <a href="#" data-dismiss="alert" aria-label="close" title="close" class="close">×</a>
				</div>
						
				<div class="alert alert-warning" id="msg_negociacao" style="display: none;">
				  <strong>Atenção!</strong> Temos uma contra-proposta para você, verifique os valores e envie seu feedback!
				  <a href="#" data-dismiss="alert" aria-label="close" title="close" class="close">×</a>
				</div>
				
				<table style="width: 100%">
					<?php foreach( $exames as $key => $exame ): ?>
						<?php $label = $exame['servico']['tipo_servico'] == 'E' ? 'Serviço de Saúde' : 'Serviço de Segurança'; ?>
						<tr>
			    			<td style="width: 50%">
			    				<?php echo $this->BForm->input('Servico.' . $exame['PropostaCredExame']['codigo'] . '.descricao', array('value' => $exame['servico']['descricao'], 'class' => 'form-control', 'label' => $label, 'style' => 'float: left; width: 95%;', 'readonly' => 'readonly')); ?>
			    			</td>
			    			<td>
			    				<?php if($exame['PropostaCredExame']['valor']) : ?>
			    					<?php echo $this->BForm->input('PropostaCredExame.' . $exame['PropostaCredExame']['codigo'] . '.valor', array('value' => $exame['PropostaCredExame']['valor'], 'class' => 'form-control moeda', 'label' => 'Valor Proposta: (R$)', 'style' => "float: left; width: 130px; text-align: right; {$exame['Style']['valor_1']}", 'readonly' => 'readonly')); ?>
			    				<?php endif; ?>
			    			</td>
			    			<td>
			    				<?php if($exame['PropostaCredExame']['valor']) : ?>
			    					<?php echo $this->BForm->input('PropostaCredExame.' . $exame['PropostaCredExame']['codigo'] . '.valor_contra_proposta', array('value' => ($exame['PropostaCredExame']['valor_contra_proposta'] ? $exame['PropostaCredExame']['valor_contra_proposta'] : ''), 'class' => 'form-control moeda', 'label' => 'Contra Proposta: (R$)', 'style' => "float: left; width: 130px; text-align: right; {$exame['Style']['valor_2']}", 'readonly' => 'readonly')); ?>
			    				<?php endif; ?>
			    			</td>			    			
			    			<td id="exame_<?php echo $exame['PropostaCredExame']['codigo']; ?>" style="padding: 15px 10px 0; text-align: center;">
			    				<?php if($exame['PropostaCredExame']['valor_contra_proposta'] && is_null($exame['PropostaCredExame']['valor_minimo']) && is_null($exame['PropostaCredExame']['aceito'])) : ?>
				    				<a href="javascript:void(0);" onclick="aprova_exame('<?php echo $exame['PropostaCredExame']['codigo']; ?>', '1', <?php echo $exame['PropostaCredExame']['codigo_proposta_credenciamento']; ?>);" class="btn btn-info"><i class="icon-white icon-thumbs-up"></i> Aceitar!</a>
				    				<a class="btn btn-danger" onclick="$('#valor_minimo_<?php echo $exame['PropostaCredExame']['codigo']; ?>').attr('class', 'moeda text-right'); setup_mascaras(); $('#modal_<?php echo $exame['PropostaCredExame']['codigo']; ?>').modal('show');  setup_mascaras();"><i class="icon-white icon-thumbs-down"></i> Não Aceitar!</a>
				    			<?php elseif($exame['PropostaCredExame']['aceito'] == '1'): ?>
				    				<?php if($exame['PropostaCredExame']['valor_minimo']) : ?>
				    					<a href="javascript:void(0);" class="btn btn-success disabled"><i class="icon-white icon-ok-sign"></i> Aprovado Mínimo: <?php echo $exame['PropostaCredExame']['valor_minimo']; ?></a>
				    				<?php else : ?>
				    					<?php if($exame['PropostaCredExame']['valor']) : ?>
				    						<a href="javascript:void(0);" class="btn btn-success disabled"><i class="icon-white icon-ok-sign"></i> Valor Aprovado!</a><br />
				    						<?php if(($exame['PropostaCredExame']['usuario_aprovou'] == $authUsuario['Usuario']['codigo']) && (($dadosProposta['PropostaCredenciamento']['codigo_status_proposta_credenciamento'] == StatusPropostaCred::AGUARDANDO_AVALIACAO_CONTRA_PROPOSTA))) : ?>
				    							<a href="javascript:void(0);" id="reverter" onclick="volta_status_exame(<?php echo $exame['PropostaCredExame']['codigo']; ?>, <?php echo $exame['PropostaCredExame']['codigo_proposta_credenciamento']; ?>);">Reverter</a>	
				    						<?php endif; ?>
				    					<?php else : ?>
				    						<a href="javascript:void(0);" class="btn btn-success disabled"><i class="icon-white icon-ok-sign"></i> Serviço Aprovado!</a>
				    					<?php endif; ?>
				    				<?php endif; ?>
				    			<?php elseif($exame['PropostaCredExame']['aceito'] == '0'): ?>
				    				<?php if($exame['PropostaCredExame']['valor_minimo']) : ?>
				    					<a href="javascript:void(0);" class="btn btn-inverse disabled" style="cursor: default;"><i class="icon-white icon-remove-sign"></i> Reprovado Mínimo: <?php echo $exame['PropostaCredExame']['valor_minimo']; ?></a>	
				    				<?php else : ?>
				    					<a href="javascript:void(0);" class="btn btn-inverse" style="cursor: default;"><i class="icon-white icon-remove-sign"></i> Reprovado!</a>
				    				<?php endif; ?>
				    			<?php elseif(is_null($exame['PropostaCredExame']['aceito']) && $exame['PropostaCredExame']['valor_minimo']) : ?>
									<a href="javascript:void(0);" class="btn btn-warning disabled" style="cursor: default;"><i class="icon-white icon-remove-sign"></i> Enviado Valor Mínimo: <?php echo $exame['PropostaCredExame']['valor_minimo']; ?></a><br />
									<?php if(($dadosProposta['PropostaCredenciamento']['codigo_status_proposta_credenciamento'] == StatusPropostaCred::AGUARDANDO_AVALIACAO_CONTRA_PROPOSTA)) : ?>
										<a href="javascript:void(0);" onclick="volta_status_exame(<?php echo $exame['PropostaCredExame']['codigo']; ?>, <?php echo $exame['PropostaCredExame']['codigo_proposta_credenciamento']; ?>);">Reverter</a>
									<?php endif; ?>				    			
				    			<?php elseif(is_null($exame['PropostaCredExame']['valor_contra_proposta']) && is_null($exame['PropostaCredExame']['aceito']) && !is_null($exame['PropostaCredExame']['valor'])): ?>
				    				<a href="javascript:void(0);" class="label" style="border: 2px solid #666; padding: 5px; cursor: default; font-size: 12px; font-weight: normal;"><i class="icon-white icon-remove-sign"></i> Ainda não Avaliado!</a>
				    			<?php elseif(is_null($exame['PropostaCredExame']['aceito'])) : ?>
				    				<a href="javascript:void(0);" class="label" style="border: 2px solid #666; padding: 5px; cursor: default; font-size: 12px; font-weight: normal;"><i class="icon-white icon-remove-sign"></i> Ainda não Avaliado!</a>
				    			<?php endif; ?>

								<?php if(($dadosProposta['PropostaCredenciamento']['codigo_status_proposta_credenciamento'] <= 2) && ($exame['PropostaCredExame']['aceito'] != '1') && ($dadosProposta['PropostaCredenciamento']['ativo'] != '1')) : ?>
				    				<a href="javascript:void(0);" class="btn btn-danger" style="padding: 5px; font-size: 12px; font-weight: normal;" onclick="removeExame(this, '<?php echo $exame['PropostaCredExame']['codigo']; ?>');"><i class="icon-white icon-remove-sign"></i> Remover este Serviço!</a>
				    			<?php endif; ?>
			    			</td>
			    			<td id="carregando_<?php echo $exame['PropostaCredExame']['codigo']; ?>" style="display: none; text-align: center;">
			    				<img src="/portal/img/hourglass.gif">
			    			</td>
						</tr>
					<?php endforeach; ?>
				</table>
			
				<?php if(($dadosProposta['PropostaCredenciamento']['codigo_status_proposta_credenciamento'] == StatusPropostaCred::AGUARDANDO_AVALIACAO_CONTRA_PROPOSTA)) : ?>
					<div class="modal fade" id="modal_enviar_valores_para_aprovacao" data-backdrop="static">
						<div class="modal-dialog modal-sm" style="position: static;">
							<div class="modal-content">
								<div class="modal-header">
									<h4 class="modal-title" id="gridSystemModalLabel">
										<b>Enviar Valores para RHHealth!</b><br />
									</h4>
								</div>
						    	<div class="modal-body">
									<?php echo $this->BForm->create('PropostaCredenciamento', array('type' => 'post', 'url' => array('controller' => 'propostas_credenciamento', 'action' => 'envia_retorno_de_valores', $this->passedArgs[0]))); ?>
										<?php echo $this->BForm->hidden('codigo', array('value' => $dadosProposta['PropostaCredenciamento']['codigo'])); ?>
										<p>(Você acabou de definir seus custos para os serviços propostos, remeter os mesmo para avaliação da RHhealth)</p>
										<a href="javascript:void(0);" onclick="$('#PropostaCredenciamentoContrapropostaForm').submit();" class="btn btn-success"><i class="icon-white icon-thumbs-up"></i> Enviar Agora</a>
									<?php echo $this->BForm->end(); ?>
						    	</div>
						    </div>
						</div>
					</div>
				<?php endif; ?>
			
				<?php foreach($exames as $key => $exame) : ?>
					<div class="modal fade" id="modal_<?php echo $exame['PropostaCredExame']['codigo']; ?>">
						<div class="modal-dialog modal-sm">
					    	<div class="modal-content">
					    		<div class="modal-header">
					    			<h4 class="modal-title" id="gridSystemModalLabel">Qual seria o valor mínimo para este serviço?</h4>
					    		</div>
					    		<div class="modal-body right" >
					    			<label for="message-text" class="control-label"><b><?php echo $authUsuario['Usuario']['nome']; ?></b>, agradecemos sua proposta e interesse, poderia nos informar o valor mínimo para fornecimento deste exame, para que possamos avaliar melhor sua proposta.</label>
	            					<div style="text-align: left">R$ <input type="text" class="form-control" id="valor_minimo_<?php echo $exame['PropostaCredExame']['codigo']; ?>" class="moeda" style="width: 100px;"/></div>
					    		</div>
					    		<div class="modal-footer">
					    			<a href="javascript:void(0);" class="btn btn-danger" onclick="$('#modal_<?php echo $exame['PropostaCredExame']['codigo']; ?>').modal('hide')">X</a>
					    			<a href="javascript:void(0);" class="btn btn-success" onclick="aprova_exame('<?php echo $exame['PropostaCredExame']['codigo']; ?>', '0', <?php echo $exame['PropostaCredExame']['codigo_proposta_credenciamento']; ?>);"><i class="icon-white icon-thumbs-up"></i> Enviar Valor Mínimo!</a>
					    		</div>
					    	</div>
					  	</div>
					</div>
				<?php endforeach; ?>
			
				<div id="aprovado" style="display:none;">
					<a href="javascript:void(0);" class="btn btn-success disabled"><i class="icon-white icon-ok-sign"></i> Valor Aprovado!</a>
				</div>
				<div id="reprovado" style="display:none;">
					<a href="javascript:void(0);" class="btn btn-inverse disabled" style="cursor: default;"><i class="icon-white icon-remove-sign"></i> Reprovado!</a>
				</div>
				<div id="enviado_valor_minimo" style="display:none;">
					<a href="javascript:void(0);" class="btn btn-warning disabled" style="cursor: default;"><i class="icon-white icon-remove-sign"></i> Enviado Valor Mínimo: <span id="valor_minimo"></span></a>
				</div>

				<?php $url = Ambiente::getUrl(); ?> 
			
				<?php echo $this->Javascript->codeBlock('
					$(document).ready(function() {
						setup_mascaras(); 
						setup_time();
						verifica_DEFINICAO_exames('.$this->passedArgs[0].');
					});
					
					function aprova_exame(codigo, status, proposta) {
						var valor_minimo = (status == 0) ? $("#valor_minimo_" + codigo).val() : "";
						var salva = false;
						
						if(status == 1) {
							if(confirm("Aceitar o valor de R$" + $("input[name=\'data[PropostaCredExame][" + codigo + "][valor_contra_proposta]\']").val() + ", para o serviço: \n - " + $("input[name=\'data[Servico][" + codigo + "][descricao]\']").val() + "?")) {
								salva = true;
							}
						} else {
							salva = true;
						}
		
						if(salva) {
						    $.ajax({
						        type: "POST",
						        url: "/portal/propostas_credenciamento/status_exame",
						        dataType: "json",
						        data: "codigo=" + codigo + "&status=" + status + "&valor_minimo=" + valor_minimo,
						        beforeSend: function() {
						        	$("#exame_" + codigo).hide(); $("#carregando_" + codigo).fadeIn();
						        	
						        	if(status == "0")
						        		$("#modal_" + codigo).modal("hide");
						        },
						        success: function(json) {
							        if(json) {
							        	if(status == "1") {
							        		$("#exame_" + codigo).html($("#aprovado").clone().show()).append("<a href=\'javascript:void(0);\' id=\'reverter\' onclick=\'volta_status_exame(" + codigo + ", " + proposta + ");\'>Reverter</a>");
							        		$("input[name=\'data[PropostaCredExame][" + codigo + "][valor_contra_proposta]\']").attr("style", "float: left; width: 130px; text-align: right; border: 2px solid green;");
							        	} else {
						
							        		$("#enviado_valor_minimo #valor_minimo").html(valor_minimo);
							        		$("#exame_" + codigo).html($("#enviado_valor_minimo a").clone().show()).append("<a href=\'javascript:void(0);\' id=\'reverter\' onclick=\'volta_status_exame(" + codigo + ", " + proposta + ");\'><br />Reverter</a>");
							        	}
							        	
							        	verifica_STATUS_exames(proposta);
							        	verifica_DEFINICAO_exames(proposta);
							        }
						        },
						        complete: function() {
						        	$("#carregando_" + codigo).hide();  $("#exame_" + codigo).fadeIn();
						        }
						    });							
						}
					}
					
					function volta_status_exame(codigo, proposta) {
					    $.ajax({
					        type: "POST",
					        url: "/portal/propostas_credenciamento/volta_status_exame",
					        dataType: "json",
					        data: "codigo=" + codigo + "&codigo_proposta=" + proposta,
					        beforeSend: function() { $("#exame_" + codigo).hide(); $("#carregando_" + codigo).fadeIn(); },
					        success: function(json) {
						        if(json == "1") {
					        		$("input[name=\'data[PropostaCredExame][" + codigo + "][valor_contra_proposta]\']").removeAttr("disabled");
					        		$("input[name=\'data[PropostaCredExame][" + codigo + "][valor_contra_proposta]\']").attr("style", "float: left; width: 130px; text-align: right; border: 1px solid #ccc;");
						        		
					        		$("#exame_" + codigo).html("<a class=\'btn btn-info\' onclick=\'aprova_exame("+codigo+", 1, "+proposta+");\' href=\'javascript:void(0);\'><i class=\'icon-white icon-thumbs-up\'></i>Aceitar!</a> <a class=\'btn btn-danger\' onclick=\"$(\'#modal_"+codigo+"\').modal(\'show\'); setup_mascaras(); \"><i class=\'icon-white icon-thumbs-down\'></i> Não Aceitar!</a>").show();
						        	$("#carregando_" + codigo).hide();
						        	// $("#form-actions").show();
						        }
					        },
					        complete: function() { 
					        	verifica_STATUS_exames(proposta);
					        	verifica_DEFINICAO_exames(proposta);				        	 
					        }
					    });					
					}
					
					function verifica_DEFINICAO_exames(proposta) {
						$.ajax({
					    	type: "POST",
					    	url: "/portal/propostas_credenciamento/verifica_definicao_exames",
					    	dataType: "json",
					    	data: "proposta=" + proposta,
					    	beforeSend: function() {},
					    	success: function(json) {
					    		if(json == "1") {
					    			// $(".botao-status").show();
						
									$("#modal_enviar_valores_para_aprovacao").modal("show");
						
					    			// $("#reverter").hide();
					    		} else {
					    			$(".botao-status").hide();
					    			return false;
					    		}
					    	},
					    	complete: function() {}
					    });				
					}
					
					function verifica_STATUS_exames(proposta) {
						$.ajax({
					    	type: "POST",
					    	url: "/portal/propostas_credenciamento/verifica_exames_proposta",
					    	dataType: "json",
					    	data: "proposta=" + proposta + "&fornecedor=1",
					    	beforeSend: function() {},
					    	success: function(json) {
					    		if(json == "1") {
					    			window.location.href = "' . $url . '/portal/propostas_credenciamento/minha_proposta";
					    		} else {
					    			return false;
					    		}
					    	},
					    	complete: function() {}
					    });	
					}				
	
					function removeExame(element, codigo) {
						$(element).prev().hide();
						$(element).hide();
						$("#exame_" + codigo).prepend($("#carregando_" + codigo).show());
					
						$.ajax({
					        type: "POST",
					        url: "/portal/propostas_credenciamento/remove_servico/",
					        dataType: "json",
					        data: "codigo=" + codigo,
					        beforeSend: function() { },
					        
					        success: function(result) {
					        	if(result == 1)
					        		$(element).parents("tr").fadeOut( 2500, "linear" ).remove()
					        },
					        complete: function() { }
					    });		
					}		
				'); ?>