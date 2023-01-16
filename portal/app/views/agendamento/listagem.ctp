<?php echo $this->Buonny->link_css('jqueryui/hot-sneaks/jquery-ui-1.9.2.custom.css'); ?>
<?php echo $this->Buonny->link_js('jqueryui/jquery-ui-1.9.2.custom'); ?>
<style>
	.btn-day { border: 3px solid #2f96b4; border-radius: 15px; margin: 10px; padding: 3px; text-align: center; width: 70px; float: left; }
	.btn-day:hover, .pinta { background: #db4865 url("/portal/css/jqueryui/hot-sneaks/images/ui-bg_diagonals-small_40_db4865_40x40.png") repeat scroll 50% 50%; font-weight: bold; color: #FFF; }	
	.pinta-amarelo { background: #ffff38 url("/portal/css/jqueryui/hot-sneaks/images/ui-bg_diagonals-small_75_ccd232_40x40.png") repeat scroll 50% 50%; font-weight: bold; color: #888 }
	.error{ border: 1px solid red !important; }
	.error-message{ display: block; position: relative; top: -9px; clear: both; color: red; }
</style>
<?php 
    echo $paginator->options(array('update' => 'div.lista')); 
?>
<?php if(!empty($sugestoes)) : ?>

	<table class="table table-striped">
	    <thead>
	        <tr>
	            <th class="input-mini">Pedido</th>
	            <th>Item</th>
	            <th>Data Pedido</th>
	            <th>Cliente</th>
	            <th>Funcionário</th>
	            <th>Exame</th>
	            <th>Fornecedor</th>
	            <th>Agendar</th>
	        </tr>
	    </thead>
	    
	    <tbody>
        	<?php foreach ($sugestoes as $chave => $pedido) : ?>
		    	<tr class="item_<?php echo $pedido['ItemPedidoExame']['codigo']; ?>">
		            <td class="input-mini"><?php echo $pedido['PedidoExame']['codigo']; ?></td>
		            <td><?php echo $pedido['ItemPedidoExame']['codigo']; ?></td>
		            <td><?php echo $pedido['PedidoExame']['data_inclusao']; ?></td>
 		            <td><?php echo $pedido['Cliente']['razao_social']; ?></td>
		            <td><?php echo $pedido['Funcionario']['nome']; ?></td>
 		            <td><?php echo $pedido['Exame']['descricao']; ?></td>
		            <td><?php echo $pedido['Fornecedor']['razao_social']; ?></td>
		            <td>
		            
		            	<?php if((isset($pedido[0]['qtd_sugestoes']) && $pedido[0]['qtd_sugestoes'])) : ?>

			            	<?php if($pedido['ItemPedidoExame']['data_agendamento']) : ?>
			            	
			            		<a href="javascript:void(0);" onclick="manipula_modal('modal_mostra_agendamento_<?php echo $pedido['ItemPedidoExame']['codigo']; ?>', 1); carrega_dados_cliente_funcionario(<?php echo $pedido['PedidoExame']['codigo_func_setor_cargo']; ?>, <?php echo $pedido['ItemPedidoExame']['codigo']; ?>); carrega_telefone(<?php echo $pedido['Fornecedor']['codigo']; ?>, <?php echo $pedido['ItemPedidoExame']['codigo']; ?>);" class="icon-calendar" title="Ver Data Agendamento"></a>
			            		<a href="javascript:void(0);" onclick="mostra_modal_notificacao(<?php echo $pedido['PedidoExame']['codigo']; ?>, <?php echo $pedido['ItemPedidoExame']['codigo']; ?>);"><i class="icon-envelope"></i></a>
			            		
			            		
			            		
								<!-- MODAL p/ Agendamento de Sugestões para Exames sem Agenda RHHealth. -->
								<div class="modal fade" id="modal_mostra_agendamento_<?php echo $pedido['ItemPedidoExame']['codigo']; ?>" data-backdrop="static" style="width: 85%; left: 8%; top: 15%; margin: 0 auto;">
								
		           					<div class="modal-dialog modal-sm" style="position: static;">
		           						<div class="modal-content">
		           							<div class="modal-header" style="text-align: center;">
		           								<h3>AGENDAMENTO DE EXAMES:</h3>
		           							</div>
		           							<div id="box_<?php echo $pedido['ItemPedidoExame']['codigo']; ?>" style="margin: 5px 20px; background: #F5F5F5;">
		           								<img src="/portal/img/default.gif" style="padding: 15px;">Carregando dados do funcionário...
		           							</div>
		           							<div class="modal-body" style="min-height: 295px;">
		           								<div class="span5" style="border-right: 1px solid #CCC;">
													<br />
													<span style="font-size: 1.2em">
														<b>Fornecedor:</b><br />
														<?php echo $pedido['Fornecedor']['razao_social']; ?>
													</span>
		
													<br /><br />
				           							<div id="box_telefone_<?php echo $pedido['ItemPedidoExame']['codigo']; ?>" style="font-size: 15px; font-weight: bold;">
				           								<img src="/portal/img/default.gif" style="padding: 15px;">Carregando contato...
				           							</div>
		           							
													<br /><br />
													<span style="font-size: 1.2em">
														<b style="font-size: 1.2em">Exame:</b><br />
														<?php echo $pedido['Exame']['descricao']; ?>
													</span>
		
													<br /><br />
													<span style="font-size: 1.1em">
														<b>Dias Sugeridos pelo Cliente</b><br />
														<?php if(isset($pedido['ItemPedidoExame']['sugestoes']) && trim($pedido['ItemPedidoExame']['sugestoes'])) : ?>
															<?php if(trim($pedido['ItemPedidoExame']['sugestoes'])) : ?>
																<?php foreach(explode("|", $pedido['ItemPedidoExame']['sugestoes']) as $k => $sugestao) : ?>
																	<?php list($data, $hora) = explode(" ", trim($sugestao)); ?>
																	- <a href="javascript:void(0);" onclick="atualiza_data('<?php echo $data; ?>', '<?php echo $hora ? (substr(str_pad($hora, 4, 0, STR_PAD_LEFT), 0, 2) . ":" . substr(str_pad($hora, 4, 0, STR_PAD_LEFT), 2, 2)) : ''; ?>', '<?php echo $pedido['ItemPedidoExame']['codigo']; ?>', '<?php echo $pedido['Exame']['codigo']; ?>');">
																		<?php echo $data; ?>
																		<?php echo $hora ? " - " . (substr(str_pad($hora, 4, 0, STR_PAD_LEFT), 0, 2) . ":" . substr(str_pad($hora, 4, 0, STR_PAD_LEFT), 2, 2)) : ''; ?><br />
																	</a>	
																<?php endforeach; ?>													
															<?php else : ?>
																(sem sugestão)
															<?php endif; ?>
														<?php else : ?>
															(sem sugestão)
														<?php endif; ?>
													</span>
		           								</div>
		           								
		           								<div class="span5">
													<br />
													<span style="font-size: 1.1em; border-bottom: 1px solid #CCC; background: #EFEFEF; padding: 1px;">
														<b>ATENÇÃO:</b> Este fornecedor não utiliza nossa agenda, ligar<br />
														para agendar o horário conforme sua disponibilidade.
													</span>
													<br /><br /><br />
													<?php echo $this->BForm->input('ItemPedidoExame.' . $pedido['ItemPedidoExame']['codigo'] . '.data_agendamento', array('value' => $pedido['ItemPedidoExame']['data_agendamento'], 'class' => 'input-small data form-control', 'type' => 'text', 'disabled' => true)); ?>
								           			<?php echo $this->BForm->input('ItemPedidoExame.' . $pedido['ItemPedidoExame']['codigo'] . '.hora_agendamento', array('value' => $pedido['ItemPedidoExame']['hora_agendamento'], 'class' => 'input-small hora form-control', 'type' => 'text', 'disabled' => true)); ?>
								           			
								           			<a href="javascript:void(0);" onclick="habilita_alteracao_data(<?php echo $pedido['ItemPedidoExame']['codigo']; ?>);" class="btn btn-danger">Alterar Data de Agendamento!</a>
		           								</div>
		           							</div>
									    </div>
									    <div class="modal-footer">
							    			<div class="right">
							    				<a href="javascript:void(0);" onclick="manipula_modal('modal_mostra_agendamento_<?php echo $pedido['ItemPedidoExame']['codigo']; ?>', 0);" class="btn btn-danger">CANCELAR</a>
							    				<a href="javascript:void(0);" id="botao_mostra_<?php echo $pedido['ItemPedidoExame']['codigo']; ?>_<?php echo $pedido['Exame']['codigo']; ?>" onclick="grava_agenda(this, '<?php echo $pedido['Exame']['codigo']; ?>', '<?php echo $pedido['ItemPedidoExame']['codigo']; ?>', '<?php echo $pedido['PedidoExame']['codigo']; ?>', '1');" class="btn btn-success">CONFIRMAR</a>
							    			</div>
							    		</div>									    
									</div>
								</div>			            		
			            	
			            	<?php else : ?>
			            	
				            	<a href="javascript:void(0);" onclick="manipula_modal('modal_agendamento_<?php echo $pedido['ItemPedidoExame']['codigo']; ?>', 1); carrega_dados_cliente_funcionario(<?php echo $pedido['PedidoExame']['codigo_func_setor_cargo']; ?>, <?php echo $pedido['ItemPedidoExame']['codigo']; ?>); carrega_telefone(<?php echo $pedido['Fornecedor']['codigo']; ?>, <?php echo $pedido['ItemPedidoExame']['codigo']; ?>);" class="icon-calendar" title="Realizar Agendamento"></a>
				            	
				            	<!-- MODAL p/ Agendamento de Sugestões para Exames sem Agenda RHHealth. -->
								<div class="modal fade" id="modal_agendamento_<?php echo $pedido['ItemPedidoExame']['codigo']; ?>" data-backdrop="static" style="width: 85%; left: 8%; top: 15%; margin: 0 auto;">
		           					<div class="modal-dialog modal-sm" style="position: static;">
		           						<div class="modal-content">
		           							<div class="modal-header" style="text-align: center;">
		           								<h3>AGENDAMENTO DE EXAMES:</h3>
		           							</div>
		           							<div id="box_<?php echo $pedido['ItemPedidoExame']['codigo']; ?>" style="margin: 5px 20px; background: #F5F5F5;">
		           								<img src="/portal/img/default.gif" style="padding: 15px;">Carregando dados do funcionário...
		           							</div>
		           							<div class="modal-body" style="min-height: 295px;">
		           								<div class="span5" style="border-right: 1px solid #CCC;">
													<br />
													<span style="font-size: 1.2em">
														<b>Fornecedor:</b><br />
														<?php echo $pedido['Fornecedor']['razao_social']; ?>
													</span>
		
													<br /><br />
				           							<div id="box_telefone_<?php echo $pedido['ItemPedidoExame']['codigo']; ?>" style="font-size: 15px; font-weight: bold;">
				           								<img src="/portal/img/default.gif" style="padding: 15px;">Carregando contato...
				           							</div>
		           							
													<br /><br />
													<span style="font-size: 1.2em">
														<b style="font-size: 1.2em">Exame:</b><br />
														<?php echo $pedido['Exame']['descricao']; ?>
													</span>
		
													<br /><br />
													<span style="font-size: 1.1em">
														<b>Dias Sugeridos pelo Cliente</b><br />
														<?php if(isset($pedido['ItemPedidoExame']['sugestoes']) && trim($pedido['ItemPedidoExame']['sugestoes'])) : ?>
															<?php if(trim($pedido['ItemPedidoExame']['sugestoes'])) : ?>
																<?php foreach(explode("|", $pedido['ItemPedidoExame']['sugestoes']) as $k => $sugestao) : ?>
																	<?php list($data, $hora) = explode(" ", trim($sugestao)); ?>
																	- <a href="javascript:void(0);" onclick="atualiza_data('<?php echo $data; ?>', '<?php echo $hora ? (substr(str_pad($hora, 4, 0, STR_PAD_LEFT), 0, 2) . ":" . substr(str_pad($hora, 4, 0, STR_PAD_LEFT), 2, 2)) : ''; ?>', '<?php echo $pedido['ItemPedidoExame']['codigo']; ?>', '<?php echo $pedido['Exame']['codigo']; ?>');">
																		<?php echo $data; ?>
																		<?php echo $hora ? " - " . (substr(str_pad($hora, 4, 0, STR_PAD_LEFT), 0, 2) . ":" . substr(str_pad($hora, 4, 0, STR_PAD_LEFT), 2, 2)) : ''; ?><br />
																	</a>	
																<?php endforeach; ?>													
															<?php else : ?>
																(sem sugestão)
															<?php endif; ?>
														<?php else : ?>
															(sem sugestão)
														<?php endif; ?>
													</span>
		           								</div>
		           								
		           								<div class="span5">
													<br />
													<span style="font-size: 1.1em; border-bottom: 1px solid #CCC; background: #EFEFEF; padding: 1px;">
														<b>ATENÇÃO:</b> Este fornecedor não utiliza nossa agenda, ligar <br />
														para agendar o horário conforme sua disponibilidade.
													</span>
													<br /><br /><br />
													<?php echo $this->BForm->input('ItemPedidoExame.' . $pedido['ItemPedidoExame']['codigo'] . '.data_agendamento', array('class' => 'input-small data form-control', 'type' => 'text')); ?>
								           			<?php echo $this->BForm->input('ItemPedidoExame.' . $pedido['ItemPedidoExame']['codigo'] . '.hora_agendamento', array('class' => 'input-small hora form-control', 'type' => 'text')); ?>
		           								</div>
		           							</div>
									    </div>
									    <div class="modal-footer">
							    			<div class="right">
							    				<a href="javascript:void(0);" onclick="manipula_modal('modal_agendamento_<?php echo $pedido['ItemPedidoExame']['codigo']; ?>', 0);" class="btn btn-danger">CANCELAR</a>
							    				<a href="javascript:void(0);" id="botao_<?php echo $pedido['ItemPedidoExame']['codigo']; ?>_<?php echo $pedido['Exame']['codigo']; ?>" onclick="grava_agenda(this, '<?php echo $pedido['Exame']['codigo']; ?>', '<?php echo $pedido['ItemPedidoExame']['codigo']; ?>', '<?php echo $pedido['PedidoExame']['codigo']; ?>', '0');" class="btn btn-success">CONFIRMAR</a>
							    			</div>
							    		</div>									    
									</div>
								</div>
											            	
			            	<?php endif; ?>
		            	
		            	

							
							<div class="modal fade" id="modal_notificacao_<?php echo $pedido['ItemPedidoExame']['codigo']; ?>" data-backdrop="static">
								<div class="modal-dialog modal-sm" style="position: static;">
									<div class="modal-content">
										<div class="modal-header" style="text-align: center;">
											<h4>NOTIFICAÇÃO DE PEDIDOS:</h4>
										</div>
								    	<div class="modal-body" style="min-height: 340px;">
								    	
										</div>
								    </div>
								    <div class="modal-footer">
										<a href="javascript:void(0);" onclick="$('.item_<?php echo $pedido['ItemPedidoExame']['codigo']; ?>').fadeOut(); manipula_modal('modal_notificacao_<?php echo $pedido['ItemPedidoExame']['codigo']; ?>', 0);" class="btn btn-success btnc">FECHAR</a>								    
							   		</div>
								</div>
							</div>
								            	
		            	<?php elseif($pedido['Fornecedor']['utiliza_sistema_agendamento'] == '1') : ?>

							<div id="botao_seleciona_agenda_<?php echo $pedido['ItemPedidoExame']['codigo']; ?>">
								<a href="javascript:void(0);" onclick="mostra_modal_agendamento(this, <?php echo $pedido['ItemPedidoExame']['codigo']; ?>, <?php echo $pedido['ClienteFuncionario']['codigo']; ?>, <?php echo $pedido['Fornecedor']['codigo']; ?>, <?php echo $pedido['Exame']['codigo']; ?>, <?php echo $pedido['Exame']['codigo_servico']; ?>, <?php echo $pedido['PedidoExame']['codigo_func_setor_cargo']; ?>);"><label class="label label-important">ESCOLHER DATA!</label></a>
							</div>

							<div class="modal fade" id="modal_agenda_<?php echo $pedido['ItemPedidoExame']['codigo']; ?>" data-backdrop="static" style="width: 85%; left: 8%; top: 15%; margin: 0 auto;">
								<div class="modal-dialog modal-sm" style="position: static;">
									<div class="modal-content">
										<div class="modal-header">
											<h3>AGENDAMENTO DE EXAMES (AGENDA RHHEALTH):</h3>
										</div>

	           							<div id="box_<?php echo $pedido['ItemPedidoExame']['codigo']; ?>" style="margin: 5px 20px; background: #F5F5F5;">
	           								<img src="/portal/img/default.gif" style="padding: 15px;">Carregando dados do funcionário...
	           							</div>
	           							
										<div class="modal-body" style="min-height: 390px;">
										
											<div class="span5" style="border-right: 1px solid #CCC;">
												<label><b>FORNECEDOR:</b></label>
												<?php echo $pedido['Fornecedor']['razao_social']; ?>
												<br /><br />

												<label><b>EXAME:</b></label>
												<?php echo $pedido['Exame']['descricao']; ?>
												<br /><br />

												<label><b>DATAS COM AGENDA DISPONÍVEIS:</b></label>
												<div class="pull-left">
													<div class="pull-left margin-right-15"><span class="legenda-dia-disponivel"></span>data disponível</div>
													<div class="clear"></div>
													<div class="pull-left margin-right-15"><span class="legenda-dia-atual"></span>data atual</div>
												</div>
												<div class="pull-left">
													<div class="pull-left"><span class="legenda-dia-indisponivel"></span>data indisponível</div>
													<div class="clear"></div>
													<div class="pull-left"><span class="legenda-dia-selecionado"></span>data selecionada</div>			
												</div>

												<div class="clear"></div>	    		
												<div id="datepicker" class="margin-top-10"></div>
											</div>
											<div class="span5">
												<b style="font-size: 18px;">Horários Disponíveis <span id="texto_dia">:</span></b>
												<div class="well">
													<div id="dias_disponiveis_<?php echo $pedido['ItemPedidoExame']['codigo']; ?>">
														<label class="label label-info">Escolher o melhor dia, ao lado!</label>									    				
													</div>
													<div style="clear: both;"></div>
													<?php echo $this->BForm->input('ItemPedidoExame.' . $pedido['ItemPedidoExame']['codigo'] . '.data_agendamento', array('class' => 'input-small form-control', 'type' => 'hidden')); ?>
							           				<?php echo $this->BForm->input('ItemPedidoExame.' . $pedido['ItemPedidoExame']['codigo'] . '.hora_agendamento', array('class' => 'input-small form-control', 'type' => 'hidden')); ?>
												</div>

												<br />
											</div>
										</div>
									</div>
									<div class="modal-footer">
										<div class="right">
											<a href="javascript:void(0);" id="botao_<?php echo $pedido['ItemPedidoExame']['codigo']; ?>" style="display: none;" onclick="confirma_agendamento('<?php echo $pedido['ItemPedidoExame']['codigo']; ?>');" class="btn btn-success">CONFIRMAR</a>
										</div>
									</div>									    
								</div>
							</div>		            		
		            	<?php else : ?>
		            		Não utiliza Agenda RHHealth!!!
		            	<?php endif; ?>
		            </td>
		        </tr>
        	<?php endforeach; ?>
        </tbody>
	</table>
	<div class='row-fluid'>
	    <div class='numbers span6'>
	    	<?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
	        <?php echo $this->Paginator->numbers(); ?>
	    	<?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
	    </div>
	    <div class='counter span6'>
	        <?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
	    </div>
	</div>
		
<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?>

<div class="modal fade" id="modal_carregando">
	<div class="modal-dialog modal-sm" style="position: static;">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="gridSystemModalLabel">Aguarde, buscando pedidos para notificação...</h4>
			</div>
			<div class="modal-body">
				<img src="/portal/img/ajax-loader.gif" style="padding: 10px;">
			</div>
		</div>
	</div>
</div>

<?php echo $this->Js->writeBuffer(); ?>
<?php echo $this->Javascript->codeBlock('
		
	var customCode = {
		tooltip: "Agendamento de Exames",
		init: function() {
		
	    	// Define o texto padrão exibido no tooltip
			this.tooltip = "Confira a programação para o dia";

	        // Tradução do datepicker para pt-BR (Brasil)
	        
			$.datepicker.regional["pt-BR"] = {
				minDate: new Date(),
				closeText: "Fechar"
				, prevText: "< Anterior"
				, nextText: "Próximo >"
				, currentText: "Hoje"
				, monthNames: ["Janeiro", "Fevereiro", "Mar&ccedil;o", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"]
				, monthNamesShort: ["Jan" , "Fev", "Mar", "Abr", "Mai", "Jun", "Jul", "Ago", "Set", "Out", "Nov", "Dez"]
				, dayNames: ["Domingo", "Segunda-feira", "Terça-feira", "Quarta-feira", "Quinta-feira", "Sexta-feira", "Sábado"]
				, dayNamesShort: ["Dom", "Seg", "Ter", "Qua", "Qui", "Sex", "Sab"]
				, dayNamesMin: ["Dom", "Seg", "Ter", "Qua", "Qui", "Sex", "Sab"]
				, weekHeader: "Sm"
				, dateFormat: "yy-mm-dd"
				, firstDay: 0
				, isRTL: false
				, showMonthAfterYear: false
				, yearSuffix: ""
			};

		    // Configura idioma do Datepicker
			$.datepicker.setDefaults($.datepicker.regional["pt-BR"]);
		},

	    // Configura o Datepicker
		setupDatepicker: function(ranges, codigo_pedido_item_exame) {

			var datas_liberadas = [];
			jQuery.each(ranges, function(index, range) {
				datas_liberadas.push(range.start.split("/")[2] + "-" + range.start.split("/")[1] + "-" + range.start.split("/")[0]);
			});		
			
			$( "#datepicker" ).datepicker({
				onSelect: function(date, inst) {
					carregaHorarios(date, ranges, codigo_pedido_item_exame);
				},		
				beforeShowDay: function(date) {
					data_comparacao = $.datepicker.formatDate("yy-mm-dd", date);

					if(datas_liberadas.indexOf(data_comparacao) >= 0) {
						return [true, "ui-highlight", ""];
					} else {
						return [false, "", ""];
					}
				},
				minDate: customCode.getDateRange(ranges, "min"),
				maxDate: customCode.getDateRange(ranges, "max")
			});
		},
		
		getDateRange: function(ranges, type) {
			var date = new Date(), dates = [], retDate;

			if(!type)
				type = "min";

			$.each(ranges, function(index, range){
				dates.push(new Date(index.split("/")[2], Math.round(index.split("/")[1] - 1), index.split("/")[0]));
			});
			
			if (type == "max") {
				retDate = new Date(Math.max.apply(null, dates));
			}

			if (type == "min")
				retDate = new Date(Math.min.apply(null, dates));

			return retDate;
		}		
	};			
		
	jQuery(document).ready(function() {
		customCode.init();
		
		$(".modal").css("z-index", "-1");
		setup_mascaras(); setup_time(); setup_datepicker(); 
		
		$(".data").datepicker({
			minDate: new Date(),
			dateFormat: \'dd/mm/yy\',
			showOn : \'button\',
			buttonImage : baseUrl + \'img/calendar.gif\',
			buttonImageOnly : true,
			buttonText : \'Escolha uma data\',
			dayNames : [\'Domingo\',\'Segunda\',\'Terça\',\'Quarta\',\'Quinta\',\'Sexta\',\'Sabado\'],
			dayNamesShort : [\'Dom\',\'Seg\',\'Ter\',\'Qua\',\'Qui\',\'Sex\',\'Sab\'],
			dayNamesMin : [\'D\',\'S\',\'T\',\'Q\',\'Q\',\'S\',\'S\'],
			monthNames : [\'Janeiro\',\'Fevereiro\',\'Março\',\'Abril\',\'Maio\',\'Junho\',\'Julho\',\'Agosto\',\'Setembro\',\'Outubro\',\'Novembro\',\'Dezembro\'],
			monthNamesShort : [\'Jan\',\'Fev\',\'Mar\',\'Abr\',\'Mai\',\'Jun\',\'Jul\',\'Ago\',\'Set\',\'Out\',\'Nov\',\'Dez\']
		}).mask("99/99/9999").addClass(\'binded\');
		
		$(".data").focusout(function() {
			$(this).removeClass("error").parent().parent().find(".error-message").remove();

			if( !  $(this).parents(".modal-dialog").find(".data").hasClass("error") ) {
				$(this).parents(".modal-dialog").find(".btnc").show();
				$(".btn-ok").show();
			}
		
			dataAtual = new Date();
			ano = dataAtual.getFullYear().toString();
			mes = (dataAtual.getMonth() + 1).toString();
			dia = dataAtual.getDate().toString();
		
			if(mes.length != 2)
				mes = "0" + mes;
	
			if(dia.length != 2)
				dia = "0" + dia;
		
			var data_atual = ano + mes + dia;
			var data_digitada = this.value.split(\'/\')[2] + this.value.split(\'/\')[1] + this.value.split(\'/\')[0];
		
			if(parseInt(data_digitada) < parseInt(data_atual)) {
				$(this).addClass("error").parent().parent().append( $("<span>", {text: "A data não pode ser menor que a atual.", class: "error-message"}) );
				$(this).parents(".modal-dialog").find(".btnc").hide();
				$(".btn-ok").hide();
			}
		});		
	});
		
	function habilita_alteracao_data(codigo_item_pedido) {
		$("input[name=\"data[ItemPedidoExame][" + codigo_item_pedido + "][data_agendamento]\"]").removeAttr("disabled");
		$("input[name=\"data[ItemPedidoExame][" + codigo_item_pedido + "][hora_agendamento]\"]").removeAttr("disabled");
	}
		
	function manipula_modal(id, mostra) {
		if(mostra) {
			$("#" + id).css("z-index", "1050");
			$("#" + id).modal("show");
		} else {
			$(".modal").css("z-index", "-1");
			$("#" + id).modal("hide");
		}
	}
		
	function atualiza_data(data, hora, codigo_item, codigo_exame) {
		$("input[name=\"data[ItemPedidoExame][" + codigo_item + "][data_agendamento]\"]").val(data);
		$("input[name=\"data[ItemPedidoExame][" + codigo_item + "][hora_agendamento]\"]").val(hora);
	}
		
	function grava_agenda(elemento, codigo_exame, codigo_item, codigo_pedido, alteracao) {
		
		var data_origin = $("input[name=\"data[ItemPedidoExame][" + codigo_item + "][data_agendamento]\"]").val();
		var hora_origin = $("input[name=\"data[ItemPedidoExame][" + codigo_item + "][hora_agendamento]\"]").val();
		
		var data_agendamento = data_origin.replaceAll("/", "-");
		var hora_agendamento = hora_origin;
		
		if(data_agendamento == "" && hora_agendamento == "") {
			$("input[name=\"data[ItemPedidoExame][" + codigo_item + "][data_agendamento]\"]").css("border", "1px solid red");
			$("input[name=\"data[ItemPedidoExame][" + codigo_item + "][data_agendamento]\"]").change(function() {
				$(this).css("border", "1px solid #CCC");
			});
		
			$("input[name=\"data[ItemPedidoExame][" + codigo_item + "][hora_agendamento]\"]").css("border", "1px solid red");
			$("input[name=\"data[ItemPedidoExame][" + codigo_item + "][hora_agendamento]\"]").change(function() {
				$(this).css("border", "1px solid #CCC");
			});
		
			return false;
		} else if(data_agendamento == "") {
			$("input[name=\"data[ItemPedidoExame][" + codigo_item + "][data_agendamento]\"]").css("border", "1px solid red");
			$("input[name=\"data[ItemPedidoExame][" + codigo_item + "][data_agendamento]\"]").change(function() {
				$(this).css("border", "1px solid #CCC");
			});		
			return false;
		} else if(hora_agendamento == "") {
			$("input[name=\"data[ItemPedidoExame][" + codigo_item + "][" + codigo_exame + "][hora_agendamento]\"]").css("border", "1px solid red");
			$("input[name=\"data[ItemPedidoExame][" + codigo_item + "][" + codigo_exame + "][hora_agendamento]\"]").change(function() {
				$(this).css("border", "1px solid #CCC");
			});		
			return false;
		}
		
		var bkp_element = $(elemento).parent("div").html();
		
	    $.ajax({
	        type: "POST",
	        url: "/portal/agendamento/grava_agenda",
	        dataType: "json",
	        data: { "data_agendamento" : data_agendamento, "hora_agendamento" : hora_agendamento, "codigo_item_pedido" : codigo_item},
	        beforeSend: function() {
				$(elemento).parent("div").html("<img src=\"/portal/img/default.gif\" style=\"padding: 10px;\"> <b>Aguarde! Gravando as informações!</b>");
			},
	        success: function(json) {
		
				if(json) {
				    $.ajax({
				        type: "POST",
				        url: "/portal/agendamento/consulta_disponibilidade_notificacao",
				        dataType: "json",
				        data: { "codigo_pedido" : codigo_pedido },
				        beforeSend: function() {
		
						},
				        success: function(resposta) {
							if(parseInt(alteracao)) {
								manipula_modal("modal_mostra_agendamento_" + codigo_item, 0);
								$("#modal_mostra_agendamento_" + codigo_item + " .modal-footer .right").html(bkp_element);
							} else {
								manipula_modal("modal_agendamento_" + codigo_item, 0);
								$("#modal_agendamento_" + codigo_item + " .modal-footer .right").html(bkp_element);
							}
		
							if(resposta) {
								mostra_modal_notificacao(codigo_pedido, codigo_item);
							} else {
								$(".item_" + codigo_item).fadeOut();
							}
						},
						complete: function() {
							
						}
					});
				}
			},
	        complete: function() {

			}
	    });
	}
		
	function mostra_modal_notificacao(codigo_pedido, codigo_pedido_item) {

	    $.ajax({
	        type: "POST",
	        url: "/portal/agendamento/mostra_notificacao",
	        dataType: "html",
	        data: { "codigo_pedido" : codigo_pedido },
	        beforeSend: function() {
				manipula_modal("modal_carregando", 1);
			},
	        success: function(html) {
				$("#modal_notificacao_" + codigo_pedido_item + " .modal-body").html(html);
			},
			complete: function() {
				manipula_modal("modal_carregando", 0);
				manipula_modal("modal_notificacao_" + codigo_pedido_item, 1);
			}
		});		
		
	}
		
	function carrega_dados_cliente_funcionario(codigo_func_setor_cargo, codigo_pedido_item, codigo_fornecedor) {
		$("#box_" + codigo_pedido_item).load("/portal/agendamento/box_dados_cliente_funcionario/" + codigo_func_setor_cargo);
	} 	
		
	function carrega_telefone(codigo_fornecedor, codigo_pedido_item) {
		$("#box_telefone_" + codigo_pedido_item).load("/portal/agendamento/busca_telefone/" + codigo_fornecedor);
	}
		
	function carregaHorarios(data, ranges, codigo_item_pedido_exame) {
		
		var data_formatada = data.split("-")[2] + "/" + data.split("-")[1] + "/" + data.split("-")[0];
		
		$("input[name=\"data[ItemPedidoExame][" + codigo_item_pedido_exame + "][data_agendamento]\"]").val(data_formatada);
		$("input[name=\"data[ItemPedidoExame][" + codigo_item_pedido_exame + "][hora_agendamento]\"]").val("");
		
		$("#dias_disponiveis_" + codigo_item_pedido_exame).html("");
		$("#texto_dia").html(" em " + data_formatada + ":");
		
		jQuery.each(ranges[data_formatada].horas_disponiveis, function(i, value) {
			if(i.length < 4)
				i = "0" + i;

			$("#dias_disponiveis_" + codigo_item_pedido_exame).append("<label class=\"btn-day\" style=\"margin: 10px;\">" + (i.substr(0, 2) + ":" + i.substr(2, 2)) + "</label>");
		});
		
		$("#botao_" + codigo_item_pedido_exame).hide();
		$(".btn-day").removeClass("pinta");
		
		$("body").on("click", ".btn-day", function() {
			marca_horario(this, codigo_item_pedido_exame);
		});
	}		

	function marca_horario(element, codigo_item_pedido_exame) {
		
		$("input[name=\"data[ItemPedidoExame][" + codigo_item_pedido_exame + "][hora_agendamento]\"]").val($(element).text());
		
		$("#botao_" + codigo_item_pedido_exame).show();
		
		$(".btn-day").removeClass("pinta");
		$(element).addClass("pinta");
	}
		
	function  mostra_modal_agendamento(element, codigo_item_pedido_exame, codigo_cliente_funcionario, codigo_fornecedor, codigo_exame, codigo_servico, codigo_func_setor_cargo) {
		
		// mostra modal loading
		manipula_modal("modal_carregando", 1);
				
		// procura dados disponiveis na agenda
		$.getJSON("/portal/pedidos_exames/datas_disponiveis/" + codigo_cliente_funcionario + "/" + codigo_fornecedor + "/" + codigo_exame  + "/" + codigo_servico, function(data) {
			manipula_modal("modal_carregando", 0);
			manipula_modal("modal_agenda_" + codigo_item_pedido_exame, 1);
			customCode.setupDatepicker(data, codigo_item_pedido_exame);
		});
		
		// carrega dados do cliente
		carrega_dados_cliente_funcionario(codigo_func_setor_cargo, codigo_item_pedido_exame);
	}	
		
'); ?>		
