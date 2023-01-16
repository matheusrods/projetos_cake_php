<?php if(isset($listagem) && count($listagem)) : ?>
	<?php //echo $paginator->options(array('update' => 'div.lista')); ?>

    <div class="row-fluid inline">

		<?php echo $this->BForm->create('Esocial', array('type' => 'post' ,'url' => array('controller' => 'esocial','action' => 's2220_gerar_zip'))); ?>
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
										"tipo_evento" : 's2220',
										"dados": dados_integracao
									}
								})
								.done(function(data) {

									// console.log(data);

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
									div.load(baseUrl + "esocial/s2220_listagem/" + Math.random());
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


    			<button id="botao" type="submit" class="btn btn-success btn-lg" ><i class="glyphicon glyphicon-share"></i> <i class="icon-download-alt icon-white"></i> Gerar Zip </button>
    		</div>
	    	
	        <table class="table table-striped social_id">
	            <thead>
	                <tr>
	                	<th ><input type="checkbox" class="all" title="Marcar/Desmarcar Todos" /></th>
	                	<th >Unidade</th>
	                    <th >Nome Funcionário</th>
	                    <th >CPF</th>
	                    <th >Matrícula</th>
	                    <th >Numero Pedido</th>
	                    <th >Data da Baixa</th>
	                    <th >Data de Conclusão do Exame</th>
	                    <th >Ação</th>
	                </tr>
	            </thead>
	            <tbody>
	                <?php foreach ($listagem as $key => $linha): ?>
	                    <tr>
	                    	<td class="esocial_codigo" >

	                    		<?php if(empty($linha['validacao'])): ?>

		                    		<?php echo $this->BForm->hidden('codigo_cliente.'.$linha['PedidoExame']['codigo'], array('id' => 'codigo_cliente_'.$linha['PedidoExame']['codigo'],'value' => $linha['PedidoExame']['codigo_cliente'])); ?>
		                    		<?php echo $this->BForm->input('Esocial.'.$key.'.codigo', array('type' => 'checkbox', 'label' => false, 'value' => $linha['PedidoExame']['codigo'], 'multiple', 'hiddenField' => false)); ?>
		                    	<?php endif; ?>
	                    	</td>
	                        <td class="input-mini"><?= $linha['PedidoExame']['codigo_cliente']; ?></td>
	                        <td><?= $linha['Funcionario']['nome']; ?></td>
	                        <td><?= Comum::formatarDocumento($linha['Funcionario']['cpf']); ?></td>
	                        <td><?php echo $linha['ClienteFuncionario']['matricula']; ?></td>
	                        <td><?php echo $linha['PedidoExame']['codigo']; ?></td>
	                        <td><?php echo $linha[0]['data_baixa']; ?></td>	                        
	                        <td><?php echo $linha['ItemPedidoExameBaixa']; ?></td>                      
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
	                    			<?php echo $html->link('', array('controller' => 'esocial', 'action' => 's2220_gerar', $linha['PedidoExame']['codigo']), array('class' => 'icon-download-alt', 'title' => 'Gerar XML S-2220')); ?>
	                    		<?php else: ?>
	                    			<a href="#" class="icon-warning-sign" title="Erros no Layout ESocial" onclick="exibirErro(1,<?= $linha['PedidoExame']['codigo'] ?>)"></a>

	                    			<div class="modal fade" id="modal_erro_<?= $linha['PedidoExame']['codigo'] ?>" data-backdrop="static" style="display: none; width: 750px;">

	                    				<div class="modal-dialog modal-xl" style="position: static;width: 750px;">
											<div class="modal-content" id="modal_data">
												<div class="modal-header" style="text-align: center;">
													<h3>Validação - ESocial S-2220</h3>
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
														<a href="javascript:void(0);"onclick="exibirErro(0,<?= $linha['PedidoExame']['codigo'] ?>);"class="btn btn-danger">FECHAR</a>
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
		                <td colspan = "10"><strong>Total</strong> <?php //echo $this->Paginator->params['paging']['PedidoExame']['count']; ?></td>
		            </tr>
		        </tfoot> -->
		    </table>
		    <!-- <div class='row-fluid'>
		        <div class='numbers span6'>
		            <?php //echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
		            <?php //echo $this->Paginator->numbers(); ?>
		            <?php //echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
		        </div>
		        <div class='counter span6'>
		            <?php //echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
		            
		        </div>
		    </div> -->
    	<?php echo $this->BForm->end(); ?>
    </div>
<?php //echo $this->Js->writeBuffer(); ?>
<?php else: ?>
	<div class="alert">Nenhum resultado encontrado.</div>
<?php endif; ?>

<?php echo $this->Javascript->codeBlock('
	function mostra_botao(element) {
		if($(element).val()) {
			$("#botao").show();
		} else {
			$("#botao").hide();
		}
	}

'); ?>

<script>
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
