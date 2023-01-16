		<div class="modal fade" id="agenda_horarios">
			<div class="modal-dialog modal-lg" style="position: static;">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title" id="gridSystemModalLabel">Disponibilidade de Agenda</h4>
						<div class="clear"></div>
					</div>
			    	<div class="modal-body" style="height: 600px; overflow: scroll;">
					    <table class="table table-bordered" style="width: 700px;">
							<tr>
					        	<td>Dia</td>
					        	<td>Hora</td>
					        	<td>Vaga</td>
					        </tr>
						    <?php foreach($horario_formatado as $k_dia => $dados_dia) : ?>
						    	<tr>
					        		<td colspan="3" style="background: #CCC;"><?php echo $dias_semana[$k_dia]; ?></td>
						        </tr>	        
						        <?php foreach($dados_dia as $k_hora => $dados_hora) : ?>
						        	<?php foreach($dados_hora as $k_minuto => $dados_minuto) : ?>
								       	<tr>
								       		<td><?php echo $dias_semana[$k_dia]; ?></td>
								       		<td><?php echo str_pad($k_hora, 2, 0, STR_PAD_LEFT); ?>:<?php echo str_pad($k_minuto, 2, 0, STR_PAD_LEFT); ?></td>
								       		<td>(<?php echo $dados_minuto; ?> vagas disponíveis)</td>
								       	</tr>
						        	<?php endforeach; ?>	
							       	<tr>
						        		<td colspan="3" style="background: #EFEFEF;"><br /></td>
							       	</tr>	        		
						        <?php endforeach; ?>
					        <?php endforeach; ?>        	
						</table> 	    	
			    	</div>
					<div class="modal-footer">
						<span id="carregando_modal" style="display: none;">
							Aguarde enquanto gravamos a agenda... <img src="/portal/img/default.gif">
						</span>
						<span id="botao_gravar">
							<a href="javascript:void(0);" class="btn btn-success" onclick="FornecedoresCapacidadeAgenda.aprova_agenda('<?php echo $codigo_fornecedor; ?>', '<?php echo $codigo_lista_de_preco_produto_servico; ?>');">
								Salva Grade de Agendamento
							</a>
						</span>						
					</div>			    	
			    </div>
			</div>
		</div>  