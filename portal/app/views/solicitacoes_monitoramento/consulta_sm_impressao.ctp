<style type="text/css">.page-title{ display: none; }</style>	    
    <div id="topo" style="background:url('/portal/img/logo_buonny_opacidade.jpg') no-repeat left;">
    	<h3 style="text-align:center;">Solicitação de Monitoramento</h3>    	
    </div>
    <hr />
    <style type="text/css">
    	.formtaTbl{ border-top:none; padding:0;}
    </style>   
    <table width="100%" border="0">
    	<tr>
    		<td width="15%"><strong>SM: </strong><?php echo $this->data['Recebsm']['SM'] ?></td>
    		<td width="15%">
    			<strong>Placa: </strong>
				<?php
					if( !Comum::isVeiculo($this->data['Recebsm']['Placa']) ) {
						echo "REMONTA";
					}else
					{ 
						echo $this->Buonny->placa( $this->data['Recebsm']['Placa'], $this->data[0]['dta_inc'], (empty($viagem['data_final_real']) ? Date('d/m/Y H:i:s') : $viagem['data_final_real']) );
					}
				?>
    		</td>
    		<td width="25%"><strong>Status: </strong> <?php echo $msg_status_sm ?></td>
    		<td width="20%"><strong>Guardian: </strong> <?php echo $msg_status_guardian ?></td>
    	</tr>
    </table>

	<hr />

		<!-- ********** INÍCIO DADOS GERAIS ********** -->
		<h5 style="text-align:center;">DADOS GERAIS</h5>

		<h5>Cliente</h5>		
		
		<table width="100%" border="0">
	    	<tr>
	    		<td width="20%"><strong>Codigo: </strong><?php echo isset($this->data['ClientEmpresa'])?$this->data['ClientEmpresa']['codigo_cliente']:NULL ?></td>
	    		<td width="80%"><strong>Empresa: </strong><?php echo isset($this->data['ClientEmpresa'])?$this->data['ClientEmpresa']['Raz_Social']:NULL ?></td>    		
	    	</tr>
	    </table>

		<h5>Embarcador</h5>		
		
		<table width="100%" border="0">
	    	<tr>
	    		<td width="20%"><strong>Codigo: </strong><?php echo isset($this->data['ClientEmpresaEmbarcador'])?$this->data['ClientEmpresaEmbarcador']['codigo_cliente']:NULL ?></td>
	    		<td width="80%"><strong>Empresa: </strong><?php echo isset($this->data['ClientEmpresaEmbarcador'])?$this->data['ClientEmpresaEmbarcador']['Raz_Social']:NULL ?></td>    		
	    	</tr>
	    </table>

	    <h5>Transportador</h5>		
		
		<table width="100%" border="0">
	    	<tr>
	    		<td width="20%"><strong>Codigo: </strong><?php echo isset($this->data['ClientEmpresaTransportador'])?$this->data['ClientEmpresaTransportador']['codigo_cliente']:NULL ?></td>
	    		<td width="80%"><strong>Empresa: </strong><?php echo isset($this->data['ClientEmpresaTransportador'])?$this->data['ClientEmpresaTransportador']['Raz_Social']:NULL ?></td>	
	    	</tr>
	    </table>

	    <br />

	    <table width="100%" border="0">
	    	<tr>
	    		<td width="15%"><strong>Cadastro SM: </strong><?php echo $this->data['0']['dta_receb'] ?></td>
	    		<td width="5%;"><?php echo $this->data['Recebsm']['Hora_Receb'] ?></td>	
	    		<td width="20%;"><strong>Solicitante: </strong><?php echo $this->data['Recebsm']['Solicitante'] ?></td>
	    		<td width="20%;"><strong>Telefone: </strong><?php echo $this->data['Recebsm']['Tel_Solicitante'] ?></td>
	    		<td width="20%;"><strong>Carga(R$): </strong><?php echo $this->Buonny->moeda($this->data['Recebsm']['ValSM']) ?></td>
	    		<td width="20%;"><strong>Pedido Cliente: </strong><?php echo $this->data['Recebsm']['pedido_cliente'] ?></td>
	    		
	    	</tr>

	    	<!--array('label' => 'Previsão Início', 'readonly' => true, 'value' => $this->data['0']['dta_inc'], 
			array('label' => '&nbsp', 'readonly' => true, 'value' => $this->data['Recebsm']['Hora_Inc'], '
			array('label' => 'Previsão Fim', 'readonly' => true, 'value' => $this->data['0']['dta_fim'], 
			array('label' => '&nbsp', 'readonly' => true, 'value' => $this->data['Recebsm']['Hora_Fim'], '-->
	    </table>
	    
			<?php 
				$tipoIni = '';
				$tipoFim = '';
				if( !is_null($viagem['data_inicio_real']) ){
					$tipoIni = ( $tipoInicioFimViagem['inicio'] ) ? ' (auto)' : ' (manual)';
				}						
				if( !is_null($viagem['data_final_real']) ){
					$tipoFim = ( $tipoInicioFimViagem['fim'] ) ? ' (auto)' : ' (manual)';
				}
			?>
	    <table width="100%" border="0">
	    	<tr>
	    		<td width="65%"><strong>Previsão Início: </strong><?php echo $this->data['0']['dta_inc'] ?></td>	    		
	    	</tr>	    	
	    </table>
	    <table width="100%" border="0">
	    	<tr>
	    		<td width="35%"><strong>Previsão Fim: </strong><?php echo $this->data['0']['dta_fim'] ?></td>	
	    		<td width="65%"><strong><?php echo 'Início Real'.$tipoIni ?>: </strong><?php echo $viagem['data_inicio_real'] ?></td>
	    	</tr>
	    </table>
	    <table width="100%" border="0">
	    	<tr>
	    		<td width="40%"><strong><?php echo 'Fim Real'.$tipoFim ?>: </strong><?php echo $viagem['data_final_real'] ?></td>	
	    		<td width="60%"><strong>Temperatura: </strong><?php echo $this->data['Recebsm']['Temperatura'] ?> a <?php echo $this->data['Recebsm']['Temperatura2'] ?></td>	
	    	</tr>
	    </table>
	    <h5 style="text-align:center;">ORIGEM VIAGEM</h5>
	    <table>
	    	<td width="20%;"><strong>Origem: </strong><?php echo $this->data['CidadeOrigem']['Descricao'] ?></td>
	    	<td width="20%;"><?php echo $this->data['CidadeOrigem']['Estado'] ?></td>
	    	<td width="20%;"><strong>Destino: </strong><?php echo $this->data['CidadeDestino']['Descricao'] ?></td>
	    	<td width="20%;"><?php echo $this->data['CidadeDestino']['Estado'] ?></td>
		</table>
		<table>
			<td width="20%;"><strong>Empresa: </strong><?php echo $this->data['MWebsm']['origemviagem_empresa'] ?></td>
			<td width="20%;"><strong>Telefone: </strong><?php echo $this->data['MWebsm']['origemviagem_telefone'] ?></td>
			<td width="20%;"><strong>Contato: </strong><?php echo $this->data['MWebsm']['origemviagem_contato'] ?></td>
		</table>

		<div class="row">						
			
			<div class="span5">
				<?php if (isset($authUsuario['Usuario']['codigo_uperfil']) && in_array($authUsuario['Usuario']['codigo_uperfil'], array(3, 20))): ?>
					<?php echo "<strong>Sistema Origem:</strong> " . $this->data['Recebsm']['sistema_origem'] ?>
				<?php endif ?>
			</div>
		</div>



		<!-- ********** FIM DADOS GERAIS ********** -->

		<hr />

		<!-- ********** INÍCIO INFO VEÍCULO ********** -->		
		<h5 style="text-align:center;">INFORMAÇÕES DO VEÍCULO</h5>
	    
    	<h5>Motorista</h5>
    	<table width="100%" border="0">
	    	<tr>	
	    		<td width="50%"><strong>Nome: </strong><?php echo $this->data['Motorista']['Nome'] ?></td>
    			<td width="25%"><strong>CPF: </strong><?php echo $this->data['Motorista']['CPF'] ?></td>	
    			<td width="25%"><strong>RG: </strong><?php echo $this->data['Motorista']['RG'] ?></td>    		
	    	</tr>
	    </table>
	    <table width="100%" border="0">
	    	<tr>    			
	    		<td width="30%"><strong>Vencimento CNH: </strong><?php echo $this->data['0']['cnh_validade'] ?></td>
	    		<td width="30%"><strong>Telefone: </strong><?php echo $this->data['Motorista']['Telefone'] ?></td>
	    		<td width="30%"><strong>Celular: </strong><?php echo $this->data['Motorista']['Celular'] ?></td>
	    	</tr>
	    </table>
	    <table width="100%" border="0">
	    	<tr>    			
	    		<td width="60%"><strong>Gerenciadora: </strong><?php echo $this->data['Recebsm']['NOME_GERENCIADORA'] ?></td>
	    		<td width="20%"><strong>Liberação: </strong><?php echo $this->data['Recebsm']['N_LIBERACAO'] ?></td>
	    	</tr>
	    </table>
    	
    	<h5>Cavalo</h5>
    	<table width="100%" border="0">
	    	<tr>    			
	    		<td width="15%">
	    			<?php if( !Comum::isVeiculo($this->data['Recebsm']['Placa'] )) 
						echo '<strong>Placa: </strong>REMONTA<br />';				
					 else 
		    			echo '<strong>Placa: </strong>'.$this->data['Recebsm']['Placa'].'<br />';
		    		?>
	    		</td>
	    		<td width="15%"><strong>Fabricante: </strong><?php echo $this->data['MCaminhao']['Fabricante'] ?></td>	    		
	    		<td width="20%"><strong>Modelo: </strong><?php echo $this->data['MCaminhao']['Modelo'] ?></td>
	    		<td width="15%"><strong>Ano Fabricação: </strong><?php echo $this->data['MCaminhao']['Ano_Fab'] ?></td>	 
	    		<td width="15%"><strong>Ano Modelo: </strong><?php echo $this->data['MCaminhao']['Ano_Modelo'] ?></td>
	    		<td width="15%"><strong>Cor: </strong><?php echo $this->data['MCaminhao']['Cor'] ?></td>   		
	    	</tr>
	    </table>
	    <table width="100%" border="0">
	    	<tr>   
	    		<td width="30%"><strong>Tecnologia: </strong><?php echo $this->data['MCaminhao']['Tipo_Equip'] ?></td>
	    		<td width="30%"><strong>Chassi: </strong><?php echo $this->data['MCaminhao']['Chassi'] ?></td>
	    		<td width="30%"><strong>Tipo: </strong><?php echo $this->data['MMonTipocavalocarreta']['TIP_Descricao'] ?></td>
	    	</tr>
	    </table>
    	 		    		    		   		
    	<h5>Carreta</h5>	

    	<table width="100%" border="0">
	    	<tr>    			
	    		<td width="20%"><strong>Placa: </strong><?php echo $this->data['Recebsm']['Placa_Carreta'] ?></td>
	    		<td width="20%"><strong>Ano: </strong><?php echo $this->data['MCarreta']['Ano'] ?></td>
	    		<td width="40%"><strong>Local Emplacamento: </strong><?php echo $this->data['CidadeEmplacamentoCarreta']['Descricao'] ?> - <?php echo $this->data['CidadeEmplacamentoCarreta']['Estado'] ?></td>
	    		<td width="20%"><strong>Tipo: </strong><?php echo $this->data['MMonTipocarroceria']['TCA_Descricao'] ?></td>
	    	</tr>
	    </table>    	

	    <!-- ********** FIM INFO VEÍCULO ********** -->

	    <hr />
		
		<!-- ********** INÍCIO ESCOLTA ********** -->
		<h5 style="text-align:center;">ESCOLTA</h5>

		<?php if(!empty($this->data['Recebsm']['ESCOLTA_EMPRESA1'])): ?>
			<table class="table">
				<thead>
					<tr>
						<th colspan="3">Empresa: <?php echo $this->data['Recebsm']['ESCOLTA_EMPRESA1'] ?></th>
					</tr>
					<tr>
						<th class="input-large">Equipe</th>
						<th class="input-large">Telefone</th>
						<th>Placa</th>
					</tr>
				</thead>
		    	<tbody>
		    		<?php if(!empty($this->data['Recebsm']['ESCOLTA_EQUIPE1']) && !empty($this->data['Recebsm']['ESCOLTA_PLACA_EQUIPE1'])): ?>
						<tr>
							<td><?php echo $this->data['Recebsm']['ESCOLTA_EQUIPE1'] ?></td>
							<td><?php echo $this->data['Recebsm']['ESCOLTA_TELEFONE_EQUIPE1'] ?></td>
							<td><?php echo strtoupper($this->data['Recebsm']['ESCOLTA_PLACA_EQUIPE1']) ?></td>
						</tr>
					<?php endif; ?>
					<?php if(!empty($this->data['Recebsm']['ESCOLTA_EQUIPE2']) && !empty($this->data['Recebsm']['ESCOLTA_PLACA_EQUIPE2'])): ?>
						<tr>
							<td><?php echo $this->data['Recebsm']['ESCOLTA_EQUIPE2'] ?></td>
							<td><?php echo $this->data['Recebsm']['ESCOLTA_TELEFONE_EQUIPE2'] ?></td>
							<td><?php echo strtoupper($this->data['Recebsm']['ESCOLTA_PLACA_EQUIPE2']) ?></td>
						</tr>
					<?php endif; ?>
					<?php if(!empty($this->data['Recebsm']['ESCOLTA_EQUIPE3']) && !empty($this->data['Recebsm']['ESCOLTA_PLACA_EQUIPE3'])): ?>
						<tr>
							<td><?php echo $this->data['Recebsm']['ESCOLTA_EQUIPE3'] ?></td>
							<td><?php echo $this->data['Recebsm']['ESCOLTA_TELEFONE_EQUIPE3'] ?></td>
							<td><?php echo strtoupper($this->data['Recebsm']['ESCOLTA_PLACA_EQUIPE3']) ?></td>
						</tr>
					<?php endif; ?>
					<?php if(!empty($this->data['Recebsm']['ESCOLTA_EQUIPE4']) && !empty($this->data['Recebsm']['ESCOLTA_PLACA_EQUIPE4'])): ?>
						<tr>
							<td><?php echo $this->data['Recebsm']['ESCOLTA_EQUIPE4'] ?></td>
							<td><?php echo $this->data['Recebsm']['ESCOLTA_TELEFONE_EQUIPE4'] ?></td>
							<td><?php echo strtoupper($this->data['Recebsm']['ESCOLTA_PLACA_EQUIPE4']) ?></td>
						</tr>
					<?php endif; ?>
				</tbody>
		    </table>
		<?php endif; ?>

		<?php if(!empty($this->data['Recebsm']['ESCOLTA1'])): ?>
		    <table class="table">
				<thead>
					<tr>
						<th colspan="3">Empresa: <?php echo $this->data['Recebsm']['ESCOLTA1'] ?></th>
					</tr>
					<tr>
						<th class="input-large">Equipe</th>
						<th class="input-large">Telefone</th>
						<th>Placa</th>
					</tr>
				</thead>
		    	<tbody>
					<?php if(!empty($this->data['Recebsm']['ESCOLTA1_EQUIPE1']) && !empty($this->data['Recebsm']['ESCOLTA1_PLACA_EQUIPE1'])): ?>
						<tr>
							<td><?php echo $this->data['Recebsm']['ESCOLTA1_EQUIPE1'] ?></td>
							<td><?php echo $this->data['Recebsm']['ESCOLTA1_TELEFONE_EQUIPE1'] ?></td>
							<td><?php echo strtoupper($this->data['Recebsm']['ESCOLTA1_PLACA_EQUIPE1']) ?></td>
						</tr>
					<?php endif; ?>
					<?php if(!empty($this->data['Recebsm']['ESCOLTA1_EQUIPE2']) && !empty($this->data['Recebsm']['ESCOLTA1_PLACA_EQUIPE2'])): ?>
						<tr>
							<td><?php echo $this->data['Recebsm']['ESCOLTA1_EQUIPE2'] ?></td>
							<td><?php echo $this->data['Recebsm']['ESCOLTA1_TELEFONE_EQUIPE2'] ?></td>
							<td><?php echo strtoupper($this->data['Recebsm']['ESCOLTA1_PLACA_EQUIPE2']) ?></td>
						</tr>
					<?php endif; ?>
					<?php if(!empty($this->data['Recebsm']['ESCOLTA1_EQUIPE3']) && !empty($this->data['Recebsm']['ESCOLTA1_PLACA_EQUIPE3'])): ?>
						<tr>
							<td><?php echo $this->data['Recebsm']['ESCOLTA1_EQUIPE3'] ?></td>
							<td><?php echo $this->data['Recebsm']['ESCOLTA1_TELEFONE_EQUIPE3'] ?></td>
							<td><?php echo strtoupper($this->data['Recebsm']['ESCOLTA1_PLACA_EQUIPE3']) ?></td>
						</tr>
					<?php endif; ?>
					<?php if(!empty($this->data['Recebsm']['ESCOLTA1_EQUIPE4']) && !empty($this->data['Recebsm']['ESCOLTA1_PLACA_EQUIPE4'])): ?>
						<tr>
							<td><?php echo $this->data['Recebsm']['ESCOLTA1_EQUIPE4'] ?></td>
							<td><?php echo $this->data['Recebsm']['ESCOLTA1_TELEFONE_EQUIPE4'] ?></td>
							<td><?php echo strtoupper($this->data['Recebsm']['ESCOLTA1_PLACA_EQUIPE4']) ?></td>
						</tr>
					<?php endif; ?>
				</tbody>
		    </table>
	    <?php endif; ?>	    

	    <!-- ********** FIM ESCOLTA ********** -->

	    <hr />
		
		<!-- ********** INÍCIO ITINERÁRIO ********** -->
		<h5 style="text-align:center;">ITINERÁRIOS</h5>

    	<div class='row-fluid inline' style='display:none' id='tempo_restante'>
    		<table class='table'>
    			<thead>
    				<th>Posicao Atual</th>
    				<th>Destino</th>
    				<th class='numeric'>Distancia restante</th>
    				<th>Tempo restante</th>
    			</thead>
    			<tr>
    				<td id="PosicaoAtual"></td>
    				<td id="PosicaoDestino"></td>
    				<td id="RestanteDistancia" class='numeric'></td>
    				<td id="RestanteTempo"></td>
    			</tr>
    		</table>
    		<?php $this->addScript($this->Javascript->codeBlock("tempo_restante_sm({$this->data['Recebsm']['SM']})")) ?>
    	</div>
    	
    	<h5>ITINERÁRIO</h5>
    	<div class='row'>
    		<div class="span12">
    			<table class='table'>
    			<thead>
    				<th class="input-small"></th>
    				<th>Dados</th>
    				<th></th>
    			</thead>
    			<tbody>
	    			<?php $linha = true; $n = 0;  foreach($itinerario as $entrega): ?>
	    				<?php $n++; ?>
		    			<?php if( $linha ): ?>
		    				<tr><td class="input-small" rowspan="2"><strong>ITINERÁRIO <?php echo $n; ?></strong></td></tr>
		    				<tr>
		    				<td colspan="2">
		    				<?php $linha = false; ?>
		    			<?php else: ?>
		    				<tr bgcolor="#F5F5F5"><td class="input-small" rowspan="2"><strong>ITINERÁRIO <?php echo $n; ?></strong></td></tr>
		    				<tr bgcolor="#F5F5F5">
		    				<td colspan="2">
		    				<?php $linha = true; ?>
		    			<?php endif; ?>		    						    					
	    						
	    						<table width="100%" border="0">
							    	<tr>    			
							    		<td width="50%" style="border-top:none;padding:0;">
							    			<strong>Nome do Alvo: </strong>
	    							<?php echo (isset($entrega['ViagemLocal']['refe_latitude']) && !empty($entrega['ViagemLocal']['refe_latitude']) ? $this->Buonny->posicao_geografica($entrega['MSmitinerario']['empresa'], $entrega['ViagemLocal']['refe_latitude'], $entrega['ViagemLocal']['refe_longitude']) : $entrega['MSmitinerario']['empresa']) ?>
							    		</td>
							    		<td width="50%"style="border-top:none;padding:0;">
							    			<strong>Endereço: </strong>
	    							<?php echo $entrega['MSmitinerario']['endereco'] ?>
							    		</td>							    			    		
							    	</tr>
							    </table>

							    <table width="100%" border="0">
							    	<tr>    			
							    		<td width="50%" style="border-top:none;padding:0;">
							    			<strong>Bairro: </strong>
	    									<?php echo $entrega['MSmitinerario']['bairro'] ?>
							    		</td>
							    		<td width="30%" style="border-top:none;padding:0;">
							    			<strong>Cidade: </strong>
	    									<?php echo $entrega['Cidade']['descricao'] ?>
							    		</td>
							    		<td width="20%" style="border-top:none;padding:0;">
							    			<strong>Estado: </strong>
	    									<?php echo $entrega['Cidade']['estado'] ?>
							    		</td>							    		
							    	</tr>
							    </table>    						
	    						
	    						<table width="100%" border="0">
							    	<tr>
							    		<td width="100%" style="border-top:none;padding:0;">
							    			<strong>Volume: </strong>
	    							        <?php echo isset($entrega['MSmitinerario']['volume']) ? $entrega['MSmitinerario']['volume']: '' ?>
							    		</td>							    		
							    	</tr>
							    </table>   

							    <table width="100%" border="0">
							    	<tr>    			
							    		<td width="25%" style="border-top:none;padding:0;">							    			
							    			<strong>Nota Fiscal: </strong>
	    							<?php echo $entrega['MSmitinerario']['nf'] ?>						    		
							    		</td>
							    		<td width="25%" style="border-top:none;padding:0;">
							    			<strong>LoadPlan: </strong>
	    							<?php echo $entrega['MSmitinerario']['loadplan'] ?>
							    		</td>
							    		<td width="25%" style="border-top:none;padding:0;">
							    			<strong>Série NF: </strong>
	    							<?php echo isset($entrega['MSmitinerario']['notaserie']) ? $entrega['MSmitinerario']['notaserie']: '' ?>
							    		</td>	
							    		<td width="25%" style="border-top:none;padding:0;">
							    			<strong>Valor Nota: </strong>
	    									<?php echo number_format($entrega['MSmitinerario']['valor_nf'], 2, ',', '.') ?>
							    		</td>							    		
							    	</tr>
							    </table>  

							    <table width="100%" border="0">
							    	<tr>    			
							    		
							    		<td width="50%" style="border-top:none;padding:0;">							    			
							    			<strong>Data Previsão: </strong>
	    							<?php echo AppModel::dbDateToDate($entrega['0']['vlev_data_previsao_entrada']) ?>					    		
							    		</td>
							    	</tr>
							    </table> 						

	    						
	    						<table width="100%" border="0">
							    	<tr>    			
							    		<td width="50%" style="border-top:none;padding:0;">						    			
							    	<strong>Início Janela: </strong>
	    							<?php echo AppModel::dbDateToDate($entrega['0']['vloc_data_janela_inicio']) ?>				    		
							    		</td>
							    		<td width="50%" style="border-top:none;padding:0;">							    			
							    	<strong>Final Janela: </strong>
	    							<?php echo AppModel::dbDateToDate($entrega['0']['vloc_data_janela_fim']) ?>	    		
							    		</td>
							    	</tr>
							    </table> 

							    <table width="100%" border="0">
							    	<tr>    			
							    		<td width="50%" style="border-top:none;padding:0;">							    			
							    	<strong>Status Chegada: </strong>
	    							<?php echo $entrega['0']['status_chegada'] == MSmitinerario::STATUS_CHEGADA_NO_PRAZO ? 'No Prazo' : ($entrega['0']['status_chegada'] == MSmitinerario::STATUS_CHEGADA_ATRASADO ? 'Atrasado' : 'Adiantado')?>  		
							    		</td>
							    		<td width="50%" style="border-top:none;padding:0;">							    			
							    	<strong>Status Janela: </strong>
	    							<?php echo (empty($entrega['0']['vloc_data_janela_inicio']) ? '' : ($entrega['0']['status_janela'] == MSmitinerario::STATUS_CHEGADA_NO_PRAZO ? 'No Prazo' : ($entrega['0']['status_janela'] == MSmitinerario::STATUS_CHEGADA_ATRASADO ? 'Atrasado' : 'Adiantado')) )?>			    		
							    		</td>
							    	</tr>
							    </table> 
	    						
	    					 	<table width="100%" border="0">
							    	<tr>    			
							    		<td width="50%" style="border-top:none;padding:0;">							    			
							    	<strong>Data Entrada: </strong>
	    							<?php echo AppModel::dbDateToDate($entrega['0']['vlev_data_entrada']) ?>	    		
							    		</td>
							    		<td width="50%" style="border-top:none;padding:0;">							    			
							    	<strong>Data Saída: </strong>
	    							<?php echo AppModel::dbDateToDate($entrega['0']['vlev_data_saida']) ?>		
							    		</td>
							    	</tr>
							    </table>    					

		    				</td>		    				
		    			</tr>

		    			
	    			<?php endforeach ?>
    			</tbody>
    			
    			</table>
    		</div>
    		<?php //$this->addScript($this->Javascript->codeBlock("tempo_restante_sm({$this->data['TViagViagem']['viag_codigo_sm']})")) ?>
    	</div>

    	<?php //echo $this->element('viagens/itinerario') ?>

    	<!-- ********** FIM ITINERÁRIO ********** -->
	    
	    <hr />
		
		<!-- ********** INÍCIO OBSERVAÇÕES ********** -->
		<h5 style="text-align:center;">OBSERVAÇÕES</h5>
	    
	    <strong>Observação:</strong>
	    <div class="row">
	    	<div class="span12">
	    		<?php echo $this->data['Recebsm']['OBSERVACAO']; ?>
	    	</div>
	    </div>
	    
	    
	    <!-- ********** FIM OBSERVAÇÕES ********** -->
	    

<?php  echo $this->BForm->end() ?>
<?php echo $this->Buonny->link_js('estatisticas') ?>
<?php echo $this->Javascript->codeBlock("
	jQuery(document).ready(function(){
	
		$('#a-itinerario').on('shown', function (e) {
	        $('.horizontal-scroll').tableScroll({width:2500, height:200}); 
		});
		
		window.setTimeout(function(){ window.print(); }, 5000);
	});

	function consulta_sm_impressao(codigo_viagem) {
		
		var newwindow = window.open('/portal/viagens/consulta_sm2/newwindow/print','_blank','scrollbars=yes,top=0,left=0,width=1000,height=800');								
		newwindow.document.write(
			'<div id=\"postlink\"><form accept-charset=\"utf-8\" method=\"post\" id=\"TViagViagem\" action=\"/portal/viagens/consulta_sm2/newwindow/print\"><input type=\"text\" id=\"TViagViagemCodigo\" value='+'\"'+codigo_viagem+'\"'+' name=\"data[TViagViagem][viag_codigo_sm]\"></form></div>'
		);
		newwindow.document.getElementById('postlink').style.display = 'none';
		newwindow.document.getElementById('TViagViagem').submit();	
	}

", false);
?>
