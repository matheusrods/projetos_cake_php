<?php if(is_array($listagem) && count($listagem) >= 1) : ?>

    <div class="row-fluid inline">

		<?php echo $this->BForm->create('Esocial', array('type' => 'post' ,'url' => array('controller' => 'esocial','action' => 's2230_gerar_zip'))); ?>
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
					        var dados_integracao = [];

							$(".social_id tbody tr :checkbox").each(function(){
								
								if ($(this).is(":checked")) {
									
									codigo_cliente = $('#codigo_cliente_'+$(this).val()).val();
									dados_integracao.push([codigo_cliente, $(this).val()]);

									var codigo = $(this).attr("id");
									codigo = codigo.substring(11);
									if(codigo){
										retorno = true;
									}
								}
							});
							
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
										"tipo_evento" : 's2230',
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
									div.load(baseUrl + "esocial/s2230_listagem/" + Math.random());
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
	    	<div style="overflow-x: auto;">
		        <table class="table table-striped social_id" style="max-width: 100% !important; width: 100% !important;">
		            <thead>
		                <tr>
		                	<th ><input type="checkbox" class="all" title="Marcar/Desmarcar Todos" /></th>
		                	<th >Codigo Unidade</th>
		                	<th >Unidade</th>
		                    <th >Setor</th>
		                    <th >Cargo</th>
		                    <th >Nome Funcionário</th>
		                    <th >CPF</th>	                   
		                    <th >Matricula</th>	                 
		                    <th >Código Atestado</th>	                 
		                    <th >Ação</th>
		                </tr>
		            </thead>
		            <tbody>
		                <?php foreach ($listagem as $key => $linha): ?>
		                    <tr>
		                    	<td id="codigo_esocial" class="esocial_codigo">
		                    		<?php if(empty($linha['validacao'])): ?>
		                    			<?php echo $this->BForm->hidden('codigo_cliente.'.$linha['Atestado']['codigo'], array('id' => 'codigo_cliente_'.$linha['Atestado']['codigo'],'value' => $linha['FuncionarioSetorCargo']['codigo_cliente_alocacao'])); ?>
		                    			<?php echo $this->BForm->input('Esocial.'.$key.'.codigo', array('type' => 'checkbox', 'id' => 'id_esocial_'.$key, 'label' => false, 'value' => $linha['Atestado']['codigo'], 'multiple', 'hiddenField' => false)); ?>
		                    		<?php endif; ?>
		                    	</td>
		                        <td class="input-mini"><?= $linha['FuncionarioSetorCargo']['codigo_cliente_alocacao']; ?></td>
		                        <td class="input-mini"><?= $linha['Cliente']['nome_fantasia']; ?></td>
		                        <td><?= $linha['Setor']['descricao']; ?></td>
		                        <td><?= $linha['Cargo']['descricao']; ?></td>
		                        <td><?= !empty($linha['Funcionario']['nome']) ? $linha['Funcionario']['nome'] : '-'; ?></td>
		                        <td><?= !empty($linha['Funcionario']['cpf']) ? AppModel::formataCpf($linha['Funcionario']['cpf']) : '-'; ?></td>
		                        <td><?php echo $linha['ClienteFuncionario']['matricula']; ?></td>         
		                        <td><?php echo $linha['Atestado']['codigo']; ?></td>	                                           
		                        <td>
									<?php if($mensageria): ?>
		                        		<div>
		                        		
		                        		<?php if(empty($linha['integracao']['codigo_evento'])): ?>
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
										<?php echo $html->link('', array('controller' => 'esocial', 'action' => 's2230_gerar', $linha['Atestado']['codigo']), array('class' => 'icon-download-alt', 'title' => 'Gerar XML S-2230')); ?>		               
		                       
									<?php else: ?>
										<a href="#" class="icon-warning-sign" title="Erros no Layout ESocial" onclick="exibirErro(1,<?= $linha['Atestado']['codigo'] ?>)"></a>

										<div class="modal fade" id="modal_erro_<?= $linha['Atestado']['codigo'] ?>" data-backdrop="static" style="display: none; width: 750px;">

											<div class="modal-dialog modal-xl" style="position: static;width: 750px;">
												<div class="modal-content" id="modal_data">
													<div class="modal-header" style="text-align: center;">
														<h3>Validação - ESocial S-2230</h3>
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
															<a href="javascript:void(0);"onclick="exibirErro(0,<?= $linha['Atestado']['codigo'] ?>);"class="btn btn-danger">FECHAR</a>
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
			    </table>

		    </div>
    	<?php echo $this->BForm->end(); ?>
    </div>
<?php else: ?>
	<div class="alert">Nenhum resultado encontrado.</div>
<?php endif; ?>


<script>
	$(document).ready(function(){
		$('#button_submit').on("click", function(e){
			e.preventDefault();      	     

	        retorno = false;  

			$(".social_id tbody tr :checkbox").each(function(){
				if ($(this).is(":checked")) {
					var codigo = $(this).attr("id");
					codigo = codigo.substring(11);
					if(codigo){					     			
						retorno = true;         
					}																
				}
			});
			
			if(retorno == false){
				swal('Erro!', 'Para gerar o zip, é necessario selecionar pelo menos 1 da listagem. :)', 'error');
			} else {
				if ( $("#EsocialS2230ListagemForm").length ) {
					$("#EsocialS2230ListagemForm").submit();
				}
			}
		});//fim
	});

	function mostra_botao(element) {
		if($(element).val()) {
			$("#botao").show();
		} else {
			$("#botao").hide();
		}
	}
	//se ele marca todos ou desmarcar pra otimizar
	$('body').on('change', '.all', function() {
  		$('.esocial_codigo').find('input[type="checkbox"]').prop('checked', this.checked);
	});

	function exibirErro(mostra,codigo_erro) {
		if(mostra) {
			
			$("#modal_erro_"+codigo_erro).css("z-index", "1050");
			$("#modal_erro_"+codigo_erro).modal("show");

		} else {
			$(".modal").css("z-index", "-1");
			$("#modal_erro_"+codigo_erro).modal("hide");
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


