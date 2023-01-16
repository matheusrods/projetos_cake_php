<?php if(is_array($listagem) && count($listagem) >= 1) : ?>
	<?php //echo $paginator->options(array('update' => 'div.lista')); ?>

    <div class="row-fluid inline">

		<?php echo $this->BForm->create('Esocial', array('type' => 'post' ,'url' => array('controller' => 'esocial','action' => 's2240_gerar_zip'))); ?>
    		<div class="row-fluid inline" style="text-align:right; ">

    			<?php if($mensageria): ?>
				
	    			<span id="div_mensageria" style="float: left;">
	    				<a href="#" class="btn btn-primary btn-lg" id="button_mensageria">Integrar ESocial</a>
					</span>

					<script>
					$(document).ready(function(){

						$("#button_mensageria").on('click',function(e){
							e.preventDefault();

					        retorno = false;
					        var codigo_cliente = '';
					        var codigo_funcionario = '';
					        var dados_integracao = [];

							$(".social_id tbody tr :checkbox").each(function(i,o){
								
								if ($(this).is(":checked")) {
									
									codigo_ge = $(this).val();
									codigo_cliente = $('#codigo_cliente_'+$(this).val()).val();
									
									// codigo_funcionario = $('#cod_funcionario_'+$(this).val()).val();
									codigo_funcionario = $('#cod_funcionario_'+i).val();
									dados_integracao.push([codigo_cliente, codigo_ge, codigo_funcionario]);

									var codigo = $(this).attr("id");
									codigo = codigo.substring(11);
									if(codigo){
										retorno = true;
									}
								}
							});

							// console.log(dados_integracao);return false;
							
							if(retorno == false){
								swal('Erro!', 'Para integração, é necessario selecionar pelo menos 1 da listagem. :)', 'error');
							} else {
								// console.log(dados_integracao);
								// return false;
								
								// pega os dados e envia via ajax
								var div = jQuery(".lista");
							 	bloquearDiv(div);
								
								//envia via ajax a data de realizacao
								$.ajax({
									url: baseUrl + 'esocial/setIntegMensageriaEsocial',
									type: 'POST',
									dataType: 'json',
									data: {
										"tipo_evento" : 's2240',
										"dados": dados_integracao
									}
								})
								.done(function(data) {
									if(data.retorno == 'false') {
										swal({
											type: 'warning',
											title: 'Atenção',
											text: data.mensagem,
										});
									} else {
										swal({type: 'success', title: 'Sucesso', text: 'Ir para a tela de integração para acompanhar sua evolução!'});
									}
									// desbloquearDiv(div);
									div.load(baseUrl + "esocial/s2240_listagem/" + Math.random());
								});
							}
						});
					});
					</script>
					<div style='display:inline; float: left; margin-left: 50px;'>
						<!-- Cinza -->
						<span class="badge-empty badge badge-secondary" title="Não integrado">
							<a href="#" style="text-decoration:none; color: #FFF;" onclick="enviaFiltroStatus(1)">Não integrado</a>
						</span>
						<!-- Azul  -->
						<span class="badge-empty badge badge-info" title="Aguardando retorno do Esocial">
							<a href="#" style="text-decoration:none; color: #FFF;" onclick="enviaFiltroStatus(2)">Aguardando retorno do Esocial</a></span>
						<!-- Vermelho -->
						<span class="badge-empty badge badge-important" title="Erro">
							<a href="#" style="text-decoration:none; color: #FFF;" onclick="enviaFiltroStatus(4)">Erro</a>
						</span>
						<!-- Verde -->
						<span class="badge-empty badge badge-success" title="Sucesso">
							<a href="#" style="text-decoration:none; color: #FFF;" onclick="enviaFiltroStatus(3)">Sucesso</a>
						</span>
						<!-- Laranja -->
						<span class="badge-empty badge badge-warning" title="Cancelado">
							<a href="#" style="text-decoration:none; color: #FFF;" onclick="enviaFiltroStatus(7)">Cancelado</a>
						</span>
					</div>		
				<?php endif; ?>
				
				<span id="div_salvar">
					<button class="btn btn-success btn-lg" id="button_submit"><i class="icon-download-alt icon-white"></i> Gerar Zip</button>
				</span>
    		</div>
	    	
	        <table class="table table-striped social_id">
	            <thead>
	                <tr>
	                	<th ><input type="checkbox" class="all" title="Marcar/Desmarcar Todos" /></th>
	                	<th >Unidade</th>
	                    <th >Setor</th>
	                    <th >Cargo</th>
	                    <th >Nome Funcionário</th>
	                    <th >CPF</th>
	                    <th >Matrícula</th>
	                    <th >Cód. Grupo Exposição</th>	                 
	                    <th >Data de início de Vigência</th>	                 
	                    <th >Ação</th>
	                </tr>
	            </thead>
	            <tbody>
	                <?php foreach ($listagem as $key => $linha): ?>
	                    <tr>
	                    	<td id="codigo_esocial" class="esocial_codigo">
	                    		<?php if(empty($linha['validacao'])): ?>
		                    		<?php echo $this->BForm->input('Esocial.'.$key.'.codigo', array('type' => 'checkbox', 'id' => 'id_esocial_'.$key, 'label' => false, 'value' => $linha['GrupoExposicao']['codigo'], 'multiple', 'hiddenField' => false)); ?>
		                    		<?php echo $this->BForm->hidden('codigo_cliente.'.$linha['GrupoExposicao']['codigo'], array('id' => 'codigo_cliente_'.$linha['GrupoExposicao']['codigo'],'value' => $linha['ClienteSetor']['codigo_cliente_alocacao'])); ?>
		                    		<?php //echo $this->BForm->input('Esocial.'.$key.'.codigo_funcionario', array('type' => 'hidden', 'id' => 'cod_funcionario_'.$linha['GrupoExposicao']['codigo'], 'value' => $linha['Funcionario']['codigo'])); ?>
		                    		<?php echo $this->BForm->input('Esocial.'.$key.'.codigo_funcionario', array('type' => 'hidden', 'id' => 'cod_funcionario_'.$key, 'value' => $linha['Funcionario']['codigo'])); ?>
		                    	<?php endif; ?>
	                    	</td>
	                        <td class="input-mini"><?= $linha['ClienteSetor']['codigo_cliente_alocacao']; ?></td>
	                        <td><?= $linha['Setor']['descricao']; ?></td>
	                        <td><?= $linha['Cargo']['descricao']; ?></td>
	                        <td><?= !empty($linha['Funcionario']['nome']) ? $linha['Funcionario']['nome'] : '-'; ?></td>
	                        <td><?= !empty($linha['Funcionario']['cpf']) ? AppModel::formataCpf($linha['Funcionario']['cpf']) : '-'; ?></td>
	                        <td><?php echo $linha['ClienteFuncionario']['matricula']; ?></td>
	                        <td><?php echo $linha['GrupoExposicao']['codigo']; ?></td>
	                        <td>
                        	<?php 
                        	if($linha[0]['data_vigencia']) {
                        		echo date_format(date_create_from_format("Y-m-d", $linha[0]['data_vigencia']), "d/m/Y"); 
                        	}
                    		?>
	                    	</td>         
	                        <td >

	                        	<?php if($mensageria): ?>
	                        		<div>
	                        		<?php //debug($linha['integracao']); ?>
	                        		<?php if (empty($linha['integracao']['codigo_evento'])): ?>
		                        		<!-- Cinza -->
		                        		<span class="badge-empty badge badge-secondary" title="Não integrado"></span>
		                        	<?php else: ?>
	                        		
	                        			<?php if ($linha['integracao']['codigo_esocial_status'] == 2): ?>
			                        		<!-- Azul  -->
			                        		<span class="badge-empty badge badge-info" title="Aguardando retorno do Esocial"></span>
			                        	<?php elseif ($linha['integracao']['codigo_esocial_status'] == 4): ?>
			                        		<!-- Vermelho -->
			                        		<span class="badge-empty badge badge-important" title="Erro"></span>
			                        	<?php elseif ($linha['integracao']['codigo_esocial_status'] == 3): ?>
			                        		<!-- Verde -->
			                        		<span class="badge-empty badge badge-success" title="Sucesso"></span>
			                        	<?php elseif ($linha['integracao']['codigo_esocial_status'] == 7): ?>
			                        		<!-- Laranja -->
			                        		<span class="badge-empty badge badge-warning" title="Cancelado"></span>
			                        	<?php endif; ?>

	                        		<?php endif; ?>
	                        		</div>

	                        	<?php endif;?>

	                        	<?php if(empty($linha['validacao'])): ?>
									<div id="id_img_load_<?= $key ?>" style="display: none">
										<img src="/portal/img/default.gif" style="padding: 10px;">Gerando Xml...	            
									</div>
		                        	<div id="button_gerar_xml_<?= $key ?>" style="display:inline;">
			                        	<a href="javascript:void(0);" onclick="gerar_xml(<?php echo $linha['GrupoExposicao']['codigo']; ?>, <?= $key ?>, <?php echo $linha['Funcionario']['codigo']; ?>);" class="icon-download-alt" title="Gerar XML S-2240"></a>
			                    		<?php //echo $html->link('', array('controller' => 'esocial', 'action' => 's2240_gerar', $linha['GrupoExposicao']['codigo'], $linha['Funcionario']['codigo']), array('class' => 'icon-download-alt', 'title' => 'Gerar XML S-2240')); ?>
		                    		</div>
		                    	<?php else: ?>
		                    		<a href="#" class="icon-warning-sign" title="Erros no Layout ESocial" onclick="exibirErro(1,<?= $linha['GrupoExposicao']['codigo'] ?>,<?= $linha['Funcionario']['codigo'] ?>)"></a>

	                    			<div class="modal fade" id="modal_erro_<?= $linha['GrupoExposicao']['codigo'] ?>_<?= $linha['Funcionario']['codigo'] ?>" data-backdrop="static" style="display: none; width: 750px;">

	                    				<div class="modal-dialog modal-xl" style="position: static;width: 750px;">
											<div class="modal-content" id="modal_data">
												<div class="modal-header" style="text-align: center;">
													<h3>Validação - ESocial S-2240</h3>
												</div>

												<div class="modal-body" style="min-height: 295px;max-height: 360px;">

				                    				<?php
				                    				// debug($linha['validacao']);
				                    				foreach($linha['validacao'] AS $validacao) {
														echo '<div >
															<span style="font-size: 1.2em">
																<b>'.$validacao['titulo'].':</b>
																'.$validacao['descricao'].'
															</span>
														</div>
														<hr>';
				                    					
				                    				}//fim foreach

				                    				?>
												</div>

											    <div class="modal-footer">
											    	<div class="right">
														<a href="javascript:void(0);"onclick="exibirErro(0,<?= $linha['GrupoExposicao']['codigo'] ?>,<?= $linha['Funcionario']['codigo'] ?>);"class="btn btn-danger">FECHAR</a>
													</div>
												</div>
											</div>
										</div>
	                    			</div>
		                    	<?php endif; ?>
	                        </td>
	                    </tr>
	                <?php endforeach; ?>        
	            </tbody>
	        	<!-- <tfoot>
		            <tr>
		                <td colspan = "10"><strong>Total</strong> <?php //echo $this->Paginator->params['paging']['GrupoEconomico']['count']; ?></td>
		            </tr>
		        </tfoot> -->
		    </table>
		    <!-- <div class='row-fluid'>
		        <div class='numbers span6'>
		            <?php // echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
		            <?php // echo $this->Paginator->numbers(); ?>
		            <?php // echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
		        </div>
		        <div class='counter span6'>
		            <?php // echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
		            
		        </div>
		    </div> -->
    	<?php echo $this->BForm->end(); ?>
    </div>
<?php // echo $this->Js->writeBuffer(); ?>
<?php else: ?>
	<div class="alert">Nenhum resultado encontrado.</div>
<?php endif; ?>
<script>
	$(document).ready(function(){
		$('#button_submit').on("click", function(e){
			e.preventDefault();

			var retorno_checked = false;
			var retorno = false;

			$(".social_id tbody tr :checkbox").each(function(){
				if ($(this).is(":checked")) {
					var codigo = $(this).attr("id");					
					codigo = codigo.substring(11);
					if(codigo){					     			
						retorno_checked = true;         
					}															
				}
			});   	     
			
			var checados = [];
			$.each($(".social_id tbody tr :checkbox"), function(){
				if ($(this).is(":checked")) {
					checados.push($(this).val());
				}         
			});

			if(checados != ''){	
				submit();
			} else {
				swal('Erro!', 'Para gerar o zip, é necessario selecionar pelo menos 1 da listagem. :)', 'error');				
			}

			// trecho comentado a pedido do chamado CDCT-424

			// if(checados != ''){	
	  //   		$.ajax({
	  //               url: baseUrl + "esocial/busca_grupo_sem_atividades/" + checados,
	  //               dataType: "json",
	  //               beforeSend: function() {  
	  //                   bloquearDiv($('div.lista'));
	  //               },
	  //               success: function(data) {
	  //               	if(data.return == 1) {
   //              	        swal({
			// 				    type: 'warning',
			// 				    title: 'Atenção',
			// 				    text: data.msg,
			// 				    showCancelButton: true,
			// 				    confirmButtonColor: '#FF0000',
			// 				    cancelButtonColor: '#ADD8E6',
			// 				    cancelButtonText: 'Não',
			// 				    confirmButtonText: 'Sim',
			// 				    showLoaderOnConfirm: true
			// 				}, 
			// 				function(isConfirm){
			// 					if (isConfirm) {														
			// 						desbloquearDiv($('div.lista'));
			// 						submit();
			// 					} else {									
			// 						desbloquearDiv($('div.lista'));
			// 						return;
			// 					}
			// 				});
	  //               	}         
	  //               },
	  //               complete: function(data){
	  //                   desbloquearDiv($('div.lista'));
	  //               }
	  //       	});	
			// } else {
			// 	swal('Erro!', 'Para gerar o zip, é necessario selecionar pelo menos 1 da listagem. :)', 'error');				
			// }
		});//fim
	});

	function mostra_botao(element) {
		if($(element).val()) {
			$("#botao").show();
		} else {
			$("#botao").hide();
		}
	} 


	function submit() {
		if ( $("#EsocialS2240ListagemForm").length ) {
			$("#EsocialS2240ListagemForm").submit();
		}
	}
	//se ele marca todos ou desmarcar pra otimizar
	$('body').on('change', '.all', function() {
  		$('.esocial_codigo').find('input[type="checkbox"]').prop('checked', this.checked);
	});

	function gerar_xml(codigo_grupo_exposicao,x,codigo_funcionario) {
		var url = baseUrl + "/esocial/s2240_gerar/"+codigo_grupo_exposicao+"/"+codigo_funcionario;
		window.location.href = url;																			
		$("#button_gerar_xml_"+x).show();
		$("#id_img_load_"+x).hide();
	}

	// function gerar_xml(codigo_grupo_exposicao,x,codigo_funcionario) {

	// 	$.ajax({
	//         url: baseUrl + "esocial/busca_grupo_sem_atividades/" + codigo_grupo_exposicao + "/" + codigo_funcionario,
	//         dataType: "json",
	//         beforeSend: function() {  
	//             $("#button_gerar_xml_"+x).hide();
	//             $("#id_img_load_"+x).show();
	//         },
	//         success: function(data) {

	//         	var url = baseUrl + "/esocial/s2240_gerar/"+codigo_grupo_exposicao+"/"+codigo_funcionario;
	        	
	//         	if(data.return == 1) {
	//     	        swal({
	// 				    type: 'warning',
	// 				    title: 'Atenção',
	// 				    text: data.msg,
	// 				    showCancelButton: true,
	// 				    confirmButtonColor: '#FF0000',
	// 				    cancelButtonColor: '#ADD8E6',
	// 				    cancelButtonText: 'Não',
	// 				    confirmButtonText: 'Sim',
	// 				    showLoaderOnConfirm: true
	// 				}, 
	// 				function(isConfirm){
	// 					if (isConfirm) {
 //                    		window.location.href = url;																			
	// 						$("#button_gerar_xml_"+x).show();
	//             			$("#id_img_load_"+x).hide();
	// 					} else {									
	// 						$("#button_gerar_xml_"+x).show();
	//             			$("#id_img_load_"+x).hide();
	//             			return;
	// 					}
	// 				});
	//         	} else {
	//         		window.location.href = url;
	//         	}         
	//         },
	//         complete: function(data){
	//         	$("#button_gerar_xml_"+x).show();
	//             $("#id_img_load_"+x).hide();	        
	//         }
	// 	});	
	// }//fim function

	function exibirErro(mostra,codigo_erro,codigo_funcionario) {
		if(mostra) {
			
			$("#modal_erro_"+codigo_erro+"_"+codigo_funcionario).css("z-index", "1050");
			$("#modal_erro_"+codigo_erro+"_"+codigo_funcionario).modal("show");

		} else {
			$(".modal").css("z-index", "-1");
			$("#modal_erro_"+codigo_erro+"_"+codigo_funcionario).modal("hide");
		}
	}

	function enviaFiltroStatus(codigo)
	{
		// console.log(tipo);
        //seta o valor e submete a pagina via ajax
        $('#EsocialBtFiltro').val(codigo);

        $('#EsocialFiltrarForm').submit();

	}
</script>