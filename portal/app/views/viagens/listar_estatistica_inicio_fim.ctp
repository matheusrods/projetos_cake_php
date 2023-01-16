<?php if(isset($listagem) && !empty($listagem)):?>
    <div class="well">
        <?php if(!empty($consulta_cliente['Cliente']['razao_social'])):?>  
            <strong> Cliente:</strong>&nbsp;&nbsp;<?php echo $consulta_cliente['Cliente']['razao_social'];?>  
        <?php else:?>
            <strong> Cliente:</strong>&nbsp;&nbsp;<?php echo "Todos";?>
       <?php endif;?>     
    </div>
    <div class='row-fluid inline'>
        <table class="table table-striped">
            <thead>
                <th class='input-large'>Estatísticas inicio e fim de SM</th>
                <th class='input-large'></th>
                <th class='input-large'></th>  
                <th class='input-large'></th>
                <th class='input-large'></th>
            </thead>
            <tbody>
				<?php
					$total_sm = 0;
					$totais = array(
						array(
							'total_inicio_automatico'	=> 0
							,'total_inicio_manual'		=> 0
							,'total_fim_automatico'		=> 0
							,'total_fim_manual'			=> 0
							,'total_sem_inicio'			=> 0
							,'total_sem_fim'			=> 0
							,'total_com_checklist'		=> 0
							,'total_sem_checklist'		=> 0
							,'total_sm'					=> 0
						)
					);
					array_push($totais, $totais[0]);
					foreach($listagem as $elemento) {
						$i = $elemento[0]['categoria'] == 'Cavalo' ? 0: 1;
						$totais[$i]['total_inicio_automatico']	+= $elemento[0]['total_inicio_automatico'];
						$totais[$i]['total_inicio_manual']		+= $elemento[0]['total_inicio_manual'];
						$totais[$i]['total_fim_automatico']		+= $elemento[0]['total_fim_automatico'];
						$totais[$i]['total_fim_manual']			+= $elemento[0]['total_fim_manual'];
						$totais[$i]['total_sem_inicio']			+= $elemento[0]['total_sem_inicio'];
						$totais[$i]['total_sem_fim']			+= $elemento[0]['total_sem_fim'];
						$totais[$i]['total_com_checklist']		+= $elemento[0]['total_com_checklist'];
						$totais[$i]['total_sem_checklist']		+= $elemento[0]['total_sem_checklist'];
						$totais[$i]['total_sm']					+= $elemento[0]['total_sm'];
					}
				?>
				<tr rowspan="4">
					<td colspan="5">
							<strong>TOTAL SM :</strong>&nbsp;&nbsp;<?php echo array_sum(Set::extract($listagem, '/0/total_sm'));?>
					</td>
				</tr>    
				<tr>
					<td colspan="5"><h5>Cavalo</h5></td>
				</tr>
				<thead>
					<th class='numeric'>Inicio automatico</th>
					<th class='numeric'>Inicio manual    </th>
					<th class='numeric'>Checklist        </th>
					<th class='numeric'>Sem inicio       </th>
					<th class='numeric'>Total            </th>
				</thead>
						<tr rowspan="3"> 
							<td class='numeric'>
								<?php echo $this->Html->link($totais[0]['total_inicio_automatico'], 'javascript:void(0)', array('onclick' => "listagem_acompanhamento_viagens_analitico( 1, false, 2, false, false, false, false)")); ?>
							</td>
							<td class='numeric'>								
								<?php echo $this->Html->link($totais[0]['total_inicio_manual'], 'javascript:void(0)', array('onclick' => "listagem_acompanhamento_viagens_analitico(2, false, 2, false, false, false, false)")); ?>
							</td> 
							 <td class='numeric'>
								<?php echo $totais[0]['total_com_checklist'] ?>
							</td>
							<td class='numeric'>
								<?php echo $this->Html->link($totais[0]['total_sem_inicio'], 'javascript:void(0)', array('onclick' => "listagem_acompanhamento_viagens_analitico(false, false, 2, 3, false, false, false)")); ?>
							</td>
							<td class='numeric'>
								<?php echo $totais[0]['total_sm'] ?>
							</td>
						</tr>                       
				<thead>
					<th class='numeric'>Fim automatico</th>
					<th class='numeric'>Fim manual    </th>
					<th class='numeric'>Sem Checklist </th>
					<th class='numeric'>Sem fim       </th>
					<th class='numeric'>Total         </th>
				</thead>
				<tr rowspan="3"> 
					<td class='numeric'>
						<?php echo $this->Html->link($totais[0]['total_fim_automatico'], 'javascript:void(0)', array('onclick' => "listagem_acompanhamento_viagens_analitico( false, 1, 2, false, false, false, false)")); ?>
					</td>
					<td class='numeric'>
						<?php echo $this->Html->link($totais[0]['total_fim_manual'], 'javascript:void(0)', array('onclick' => "listagem_acompanhamento_viagens_analitico( false, 2, 2, false, false, false, false)")); ?>
					</td>
					 <td class='numeric'>
						<?php echo $totais[0]['total_sem_checklist'] ?>
					</td>    
					<td class='numeric'>
						<?php echo $this->Html->link($totais[0]['total_sem_fim'], 'javascript:void(0)', array('onclick' => "listagem_acompanhamento_viagens_analitico( false, false, 2, false, false, 7, false)")); ?>
					</td>
					<td class='numeric'>
						<?php echo $totais[0]['total_sm'] ?>
					</td>
				</tr>
			
				<tr>
					<td colspan="5"><h5>Outros</h5></td>
				</tr>
				<thead>
					<th class='numeric'>Inicio automatico</th>
					<th class='numeric'>Inicio manual    </th>
					<th class='numeric'>Checklist        </th>
					<th class='numeric'>Sem inicio       </th>
					<th class='numeric'>Total            </th>
				</thead>
						<tr rowspan="3"> 
							<td class='numeric'>
								<?php echo $this->Html->link($totais[1]['total_inicio_automatico'], 'javascript:void(0)', array('onclick' => "listagem_acompanhamento_viagens_analitico( 1, false, false, false, false, false, 2)")); ?>
							</td>
							<td class='numeric'>
								<?php echo $this->Html->link($totais[1]['total_inicio_manual'], 'javascript:void(0)', array('onclick' => "listagem_acompanhamento_viagens_analitico( 2, false, false, false, false, false, 2)")); ?>
							</td> 
							 <td class='numeric'>
								<?php echo $totais[1]['total_com_checklist'] ?>
							</td>
							<td class='numeric'>
								<?php echo $this->Html->link($totais[1]['total_sem_inicio'], 'javascript:void(0)', array('onclick' => "listagem_acompanhamento_viagens_analitico( false, false, false, 3, false, false, 2)")); ?>																			
							</td>
							<td class='numeric'>
								<?php echo $totais[1]['total_sm'] ?>
							</td>
						</tr>                       
				<thead>
					<th class='numeric'>Fim automatico</th>
					<th class='numeric'>Fim manual    </th>
					<th class='numeric'>Sem Checklist </th>
					<th class='numeric'>Sem fim       </th>
					<th class='numeric'>Total         </th>
				</thead>
				<tr rowspan="3"> 
					<td class='numeric'>
						<?php echo $this->Html->link($totais[1]['total_fim_automatico'], 'javascript:void(0)', array('onclick' => "listagem_acompanhamento_viagens_analitico( false, 1, false, false, false, false, 2)")); ?>
					</td>
					<td class='numeric'>
						<?php echo $this->Html->link($totais[1]['total_fim_manual'], 'javascript:void(0)', array('onclick' => "listagem_acompanhamento_viagens_analitico( false, 2, false, false, false, false, 2)")); ?>
					</td>
					 <td class='numeric'>
							<?php echo $totais[1]['total_sem_checklist'] ?>
						</td>    
					<td class='numeric'>
						<?php echo $this->Html->link($totais[1]['total_sem_fim'], 'javascript:void(0)', array('onclick' => "listagem_acompanhamento_viagens_analitico( false, false, false, false, false, 7, 2)")); ?>
					</td>
					<td class='numeric'>
						<?php echo $totais[1]['total_sm'] ?>
					</td>
				</tr>
				
				<!-- TOTAL GERAL -->
				<tr>
					<td colspan="5"><h5>Total Geral</h5></td>
				</tr>
				<thead>
					<th class='numeric'>Total Inicio automatico</th>
					<th class='numeric'>Total Inicio manual    </th>
					<th class='numeric'>Total Checklist        </th>
					<th class='numeric'>Total Sem inicio       </th>
					<th></th>
				</thead>
						<tr rowspan="3"> 
							<td class='numeric'>
								<?php echo array_sum(Set::extract($totais, '{n}/total_inicio_automatico'));?>
							</td>
							<td class='numeric'>
								<?php echo array_sum(Set::extract($totais, '{n}/total_inicio_manual'));?>
							</td> 
							<td class='numeric'>
								<?php echo array_sum(Set::extract($totais, '{n}/total_com_checklist'));?>
							</td>
							<td class='numeric'>
								<?php echo array_sum(Set::extract($totais, '{n}/total_sem_inicio'));?>
							</td>
							<td></td>
						</tr>                       
				<thead>
					<th class='numeric'>Total Fim automatico</th>
					<th class='numeric'>Total Fim manual    </th>
					<th class='numeric'>Total Sem Checklist </th>
					<th class='numeric'>Total Sem fim       </th>
					<th class='numeric'>Total SM            </th>
				</thead>
				<tr rowspan="3"> 
					<td class='numeric'>
						<?php echo array_sum(Set::extract($totais, '{n}/total_fim_automatico'));?>
					</td>
					<td class='numeric'>
						<?php echo array_sum(Set::extract($totais, '{n}/total_fim_manual'));?>
					</td>
					<td class='numeric'>
						<?php echo array_sum(Set::extract($totais, '{n}/total_sem_checklist'));?>
					</td>    
					<td class='numeric'>
						<?php echo array_sum(Set::extract($totais, '{n}/total_sem_fim'));?>
					</td>
					<td class='numeric'>
						<?php echo array_sum(Set::extract($totais, '{n}/total_sm'));?>
					</td>
				</tr>
				<!-- FIM TOTAL GERAL -->
				
				<?php $tecn_codigo = 0; ?>
				<?php $cavalo_tecnologia = 0; ?>
				<?php $outros_tecnologia = 0; ?>
				
                <?php foreach($listagem as $elemento): ?>
				
					<tr>
						<td colspan="5"><h5><?php echo $elemento[0]['tecn_descricao'] ?>&nbsp;&nbsp;&nbsp;Tipo Veículo:&nbsp;<?php echo $elemento[0]['categoria'] ?></h5></td>
					</tr>
                    <thead>
                        <th class='numeric'>Inicio automatico</th>
                        <th class='numeric'>Inicio manual    </th>
                        <th class='numeric'>Checklist        </th>
                        <th class='numeric'>Sem inicio       </th>
                        <th class='numeric'>Total            </th>
                    </thead>
                            <tr rowspan="3"> 
                                <td class='numeric'>
									<?if( $elemento[0]['categoria'] == 'Cavalo' ):?>									
										<?php echo $this->Html->link($elemento[0]['total_inicio_automatico'], 'javascript:void(0)', array('onclick' => "listagem_acompanhamento_viagens_analitico( 1, false, 2, false, {$elemento[0]['tecn_codigo']}, false, false)")); ?>
									<?else:?>  
										<?php echo $this->Html->link($elemento[0]['total_inicio_automatico'], 'javascript:void(0)', array('onclick' => "listagem_acompanhamento_viagens_analitico( 1, false, false, false, {$elemento[0]['tecn_codigo']}, false, 2)")); ?> 
									<?endif;?>
                                </td>
                                <td class='numeric'>
                                    <?if( $elemento[0]['categoria'] == 'Cavalo' ):?>									
										<?php echo $this->Html->link($elemento[0]['total_inicio_manual'], 'javascript:void(0)', array('onclick' => "listagem_acompanhamento_viagens_analitico( 2, false, 2, false, {$elemento[0]['tecn_codigo']}, false, false)")); ?>
									<?else:?>  
										<?php echo $this->Html->link($elemento[0]['total_inicio_manual'], 'javascript:void(0)', array('onclick' => "listagem_acompanhamento_viagens_analitico( 2, false, false, false, {$elemento[0]['tecn_codigo']}, false, 2)")); ?> 
									<?endif;?>									
                                </td> 
                                 <td class='numeric'>
                                    <?php echo $elemento[0]['total_com_checklist'] ?>
                                </td>
                                <td class='numeric'>
									<?if( $elemento[0]['categoria'] == 'Cavalo' ):?>									
										<?php echo $this->Html->link($elemento[0]['total_sem_inicio'], 'javascript:void(0)', array('onclick' => "listagem_acompanhamento_viagens_analitico( false, false, 2, 3, {$elemento[0]['tecn_codigo']}, false, false)")); ?>
									<?else:?>  
										<?php echo $this->Html->link($elemento[0]['total_sem_inicio'], 'javascript:void(0)', array('onclick' => "listagem_acompanhamento_viagens_analitico( false, false, false, 3, {$elemento[0]['tecn_codigo']}, false, 2)")); ?> 
									<?endif;?>
                                </td>
                                <td class='numeric'>
                                    <?php echo $elemento[0]['total_sm'] ?>
                                </td>
                            </tr>                       
                    <thead>
                        <th class='numeric'>Fim automatico</th>
                        <th class='numeric'>Fim manual    </th>
                        <th class='numeric'>Sem Checklist </th>
                        <th class='numeric'>Sem fim       </th>
                        <th class='numeric'>Total         </th>
                    </thead>
                        <tr rowspan="3"> 
                            <td class='numeric'>
								<?if( $elemento[0]['categoria'] == 'Cavalo' ):?>									
									<?php echo $this->Html->link($elemento[0]['total_fim_automatico'], 'javascript:void(0)', array('onclick' => "listagem_acompanhamento_viagens_analitico( false, 1, 2, false, {$elemento[0]['tecn_codigo']}, false, false)")); ?>
								<?else:?>  
									<?php echo $this->Html->link($elemento[0]['total_fim_automatico'], 'javascript:void(0)', array('onclick' => "listagem_acompanhamento_viagens_analitico( false, 1, false, false, {$elemento[0]['tecn_codigo']}, false, 2)")); ?> 
								<?endif;?>
                            </td>
                            <td class='numeric'>
								<?if( $elemento[0]['categoria'] == 'Cavalo' ):?>									
									<?php echo $this->Html->link($elemento[0]['total_fim_manual'], 'javascript:void(0)', array('onclick' => "listagem_acompanhamento_viagens_analitico( false, 2, 2, false, {$elemento[0]['tecn_codigo']}, false, false)")); ?>
								<?else:?>  
									<?php echo $this->Html->link($elemento[0]['total_fim_manual'], 'javascript:void(0)', array('onclick' => "listagem_acompanhamento_viagens_analitico( false, 2, false, false, {$elemento[0]['tecn_codigo']}, false, 2)")); ?> 
								<?endif;?>
                            </td>
                             <td class='numeric'>
                                <?php echo $elemento[0]['total_sem_checklist'] ?>
                            </td>    
                            <td class='numeric'>
								<?if( $elemento[0]['categoria'] == 'Cavalo' ):?>									
									<?php echo $this->Html->link($elemento[0]['total_sem_fim'], 'javascript:void(0)', array('onclick' => "listagem_acompanhamento_viagens_analitico( false, false, 2, false, {$elemento[0]['tecn_codigo']}, 7, false)")); ?>
								<?else:?>  
									<?php echo $this->Html->link($elemento[0]['total_sem_fim'], 'javascript:void(0)', array('onclick' => "listagem_acompanhamento_viagens_analitico( false, false, false, false, {$elemento[0]['tecn_codigo']}, 7, 2)")); ?> 
								<?endif;?>
                            </td>
                            <td class='numeric'>
                                <?php echo $elemento[0]['total_sm'] ?>
                            </td>
                        </tr>
						
						<?php
							if ($tecn_codigo != $elemento[0]['tecn_codigo']):
								$tecn_codigo = $elemento[0]['tecn_codigo'];
								$cavalo_tecnologia = $elemento[0];
							else:
								$outros_tecnologia = $elemento[0];
								$total_tecnologia = array(
									$cavalo_tecnologia,
									$outros_tecnologia
								);
						?>
							<!-- TOTAL GERAL -->
							<tr>
								<td colspan="5"><h5>Total <?php echo $elemento[0]['tecn_descricao']?></h5></td>
							</tr>
							<thead>
								<th class='numeric'>Inicio automatico</th>
								<th class='numeric'>Inicio manual    </th>
								<th class='numeric'>Checklist        </th>
								<th class='numeric'>Sem inicio       </th>
								<th></th>
							</thead>
								<tr rowspan="3"> 
									<td class='numeric'>
										<?php echo array_sum(Set::extract($total_tecnologia, '{n}/total_inicio_automatico'));?>
									</td>
									<td class='numeric'>
										<?php echo array_sum(Set::extract($total_tecnologia, '{n}/total_inicio_manual'));?>
									</td> 
									<td class='numeric'>
										<?php echo array_sum(Set::extract($total_tecnologia, '{n}/total_com_checklist'));?>
									</td>
									<td class='numeric'>
										<?php echo array_sum(Set::extract($total_tecnologia, '{n}/total_sem_inicio'));?>
									</td>
									<td></td>
								</tr>                       
							<thead>
								<th class='numeric'>Fim automatico</th>
								<th class='numeric'>Fim manual    </th>
								<th class='numeric'>Sem Checklist </th>
								<th class='numeric'>Sem fim       </th>
								<th class='numeric'>SM            </th>
							</thead>
							<tr rowspan="3"> 
								<td class='numeric'>
									<?php echo array_sum(Set::extract($total_tecnologia, '{n}/total_fim_automatico'));?>
								</td>
								<td class='numeric'>
									<?php echo array_sum(Set::extract($total_tecnologia, '{n}/total_fim_manual'));?>
								</td>
								<td class='numeric'>
									<?php echo array_sum(Set::extract($total_tecnologia, '{n}/total_sem_checklist'));?>
								</td>    
								<td class='numeric'>
									<?php echo array_sum(Set::extract($total_tecnologia, '{n}/total_sem_fim'));?>
								</td>
								<td class='numeric'>
									<?php echo array_sum(Set::extract($total_tecnologia, '{n}/total_sm'));?>
								</td>
							</tr>
							<!-- FIM TOTAL GERAL -->
						<?php endif; ?>
						
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>    
<?php endif;?>
<?php echo $this->Javascript->codeBlock("
	function listagem_acompanhamento_viagens_analitico( inicializacao, finalizacao, codigo_tipo_veiculo, codigo_status_viagem, tecn_codigo, nega_codigo_status_viagem, nega_codigo_tipo_veiculo ) {
		var field = null;
		var form = null;
		var form = document.createElement('form');
	    var form_id = ('formresult' + Math.random()).replace('.','');
	    var codigo_cliente = ". (!empty($filtros['codigo_cliente']) ? $filtros['codigo_cliente'] : 'false' ).";
		form.setAttribute('method', 'post');
		form.setAttribute('target', form_id);
	    form.setAttribute('action', '/portal/relatorios_sm/listagem_acompanhamento_viagens_analitico/popup/' + Math.random());
	    
		if(codigo_cliente != false ){
			field = document.createElement('input');
			field.setAttribute('name', 'data[RelatorioSm][codigo_cliente]');
			field.setAttribute('value', codigo_cliente);
			field.setAttribute('type', 'hidden');
			form.appendChild(field);
		}

	    field = document.createElement('input');
	    field.setAttribute('name', 'data[RelatorioSm][base_cnpj]');
	    field.setAttribute('value', 0);
	    field.setAttribute('type', 'hidden');
	    form.appendChild(field);
	    field = document.createElement('input');
	    field.setAttribute('name', 'data[RelatorioSm][data_inicial]');
	    field.setAttribute('value', ".AppModel::dateToDbDate($filtros['data_inicial']).");
	    field.setAttribute('type', 'hidden');
	    form.appendChild(field);
	    field = document.createElement('input');
	    field.setAttribute('name', 'data[RelatorioSm][data_final]');
	    field.setAttribute('value', ".AppModel::dateToDbDate($filtros['data_final']).");
	    field.setAttribute('type', 'hidden');
	    form.appendChild(field);

		if ( inicializacao != false ) {
			field = document.createElement('input');
			field.setAttribute('name', 'data[RelatorioSm][inicializacao]');
			field.setAttribute('value', inicializacao );
			field.setAttribute('type', 'hidden');
			form.appendChild(field);		
		}
		
		if ( finalizacao != false ) {
			field = document.createElement('input');
			field.setAttribute('name', 'data[RelatorioSm][finalizacao]');
			field.setAttribute('value', finalizacao );
			field.setAttribute('type', 'hidden');
			form.appendChild(field);		
		}

		if( codigo_status_viagem != false ){
			field = document.createElement('input');
			field.setAttribute('name', 'data[RelatorioSm][codigo_status_viagem]');
			field.setAttribute('value', codigo_status_viagem );
			field.setAttribute('type', 'hidden');
			form.appendChild(field);
		}


		if( nega_codigo_status_viagem != false ){
			field = document.createElement('input');
			field.setAttribute('name', 'data[RelatorioSm][!codigo_status_viagem]');
			field.setAttribute('value', nega_codigo_status_viagem );
			field.setAttribute('type', 'hidden');
			form.appendChild(field);
		}
		
		if ( codigo_tipo_veiculo != false  ){
			field = document.createElement('input');
			field.setAttribute('name', 'data[RelatorioSm][codigo_tipo_veiculo]');
			field.setAttribute('value', codigo_tipo_veiculo );
			field.setAttribute('type', 'hidden');
			form.appendChild(field);
		}

		if( nega_codigo_tipo_veiculo != false ){
			field = document.createElement('input');
			field.setAttribute('name', 'data[RelatorioSm][!codigo_tipo_veiculo]');
			field.setAttribute('value', nega_codigo_tipo_veiculo );
			field.setAttribute('type', 'hidden');
			form.appendChild(field);
		}

		if ( codigo_tipo_veiculo != false  ){
			field = document.createElement('input');
			field.setAttribute('name', 'data[RelatorioSm][codigo_tipo_veiculo]');
			field.setAttribute('value', codigo_tipo_veiculo );
			field.setAttribute('type', 'hidden');
			form.appendChild(field);
		}

		if ( tecn_codigo != false  ){
			field = document.createElement('input');
			field.setAttribute('name', 'data[RelatorioSm][tecn_codigo]');
			field.setAttribute('value', tecn_codigo );
			field.setAttribute('type', 'hidden');
			form.appendChild(field);
		}
		
        var janela = window_sizes();
	    window.open('', form_id, 'scrollbars=yes,menubar=no,height='+(janela.height-200)+',width='+(janela.width-80)+',resizable=yes,toolbar=no,status=no');
        document.body.appendChild(form);
        form.submit();
	}
") ?>