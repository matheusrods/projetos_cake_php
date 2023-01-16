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
    		<td width="15%"><strong>SM: </strong><?php echo $this->data['QViagViagem']['viag_codigo_sm'] ?></td>
    		<td width="25%"><strong>Status: </strong> <?php echo $msg_status_sm ?></td>
    		<td width="20%"><strong>Checklist: </strong> <?php echo $msg_checklist ?></td>
    	</tr>
    </table>

	<hr />

		<!-- ********** INÍCIO DADOS GERAIS ********** -->
		<h5 style="text-align:center;">DADOS GERAIS</h5>

		<h5>Embarcador</h5>		
		
		<table width="100%" border="0">
	    	<tr>
	    		<td width="20%"><strong>Codigo: </strong><?php echo isset($this->data['ClienteEmbarcador'])?$this->data['ClienteEmbarcador']['codigo']:NULL ?></td>
	    		<td width="80%"><strong>Empresa: </strong><?php echo isset($this->data['ClienteEmbarcador'])?$this->data['ClienteEmbarcador']['razao_social']:NULL ?></td>    		
	    	</tr>
	    </table>

	    <h5>Transportador</h5>		
		
		<table width="100%" border="0">
	    	<tr>
	    		<td width="20%"><strong>Codigo: </strong><?php echo isset($this->data['ClienteTransportador'])?$this->data['ClienteTransportador']['codigo']:NULL ?></td>
	    		<td width="80%"><strong>Empresa: </strong><?php echo isset($this->data['ClienteTransportador'])?$this->data['ClienteTransportador']['razao_social']:NULL ?></td>	
	    	</tr>
	    </table>

	    <br />

	    <table width="100%" border="0">
	    	<tr>
	    		<td width="35%"><strong>Cadastro SM: </strong><?php echo $this->data['QViagViagem']['viag_data_cadastro'] ?></td>
	    		<td width="45%;"><strong>Solicitante: </strong><?php echo  $this->data['QViagViagem']['viag_usuario_adicionou'] ?></td>	
	    		<td width="20%;"><strong>Carga(R$): </strong><?php echo $this->data['QViagViagem']['viag_valor_carga']; ?></td>
	    	</tr>
	    </table>
	    
			<?php 
				$tipoIni = '';
				$tipoFim = '';
				if( !is_null($this->data['QViagViagem']['viag_data_inicio']) ){
					$tipoIni = ( $this->data['tipoInicioFimViagem']['inicio'] ) ? ' (auto)' : ' (manual)';
				}						
				if( !is_null($this->data['QViagViagem']['viag_data_fim']) ){
					$tipoFim = ( $this->data['tipoInicioFimViagem']['fim'] ) ? ' (auto)' : ' (manual)';
				}
			?>
	    <table width="100%" border="0">
	    	<tr>
	    		<td width="35%"><strong>Pedido Cliente: </strong><?php echo $this->data['QViagViagem']['viag_pedido_cliente'] ?></td>
	    		<td width="65%"><strong>Previsão Início: </strong><?php echo $this->data['QViagViagem']['viag_previsao_inicio'] ?></td>	    		
	    	</tr>	    	
	    </table>
	    <table width="100%" border="0">
	    	<tr>
	    		<td width="35%"><strong>Previsão Fim: </strong><?php echo $this->data['QViagViagem']['viag_previsao_fim'] ?></td>	
	    		<td width="65%"><strong><?php echo 'Início Real'.$tipoIni ?>: </strong><?php echo $this->data['QViagViagem']['viag_data_inicio'] ?></td>
	    	</tr>
	    </table>
	    <table width="100%" border="0">
	    	<tr>
	    		<td width="40%"><strong><?php echo 'Fim Real'.$tipoFim ?>: </strong><?php echo $this->data['QViagViagem']['viag_data_fim'] ?></td>	
	    		<td width="60%"><strong>Temperatura: </strong><?php echo $this->data['QVtemViagemTemperatura']['vtem_valor_minimo'] ?> a <?php echo $this->data['QVtemViagemTemperatura']['vtem_valor_maximo'] ?></td>	
	    	</tr>
	    </table>

		
		<div class="row">						
			
			<div class="span5">
				<?php echo "<strong>Sistema Origem:</strong> ".$this->data['QViagViagem']['viag_sistema_origem'];
					if(!empty($this->data['QViagViagem']['viag_sm_reprogramada'])){
						echo '&nbsp;'.' <strong>SM :</strong> REPROGRAMADA';
					}
				?>
			</div>
		</div>

		<!-- ********** FIM DADOS GERAIS ********** -->

		<hr />

		<!-- ********** INÍCIO INFO VEÍCULO ********** -->		
		<h5 style="text-align:center;">INFORMAÇÕES DO MOTORISTA</h5>
	    
    	<h5>Motorista</h5>
    	<table class='table'>
	    	<tr>	
	    		<td width="50%"><strong>Nome: </strong><?php echo $this->data['Motorista']['nome'] ?></td>
    			<?php if(!empty($estrangeiro)): ?>
	    			<td width="40%"><strong>CPF/RNE: </strong><?php echo $this->data['QPfisPessoaFisica']['pfis_cpf'] ?></td>
	    		<?php else:?>		    			
	    			<td width="25%"><strong>CPF: </strong><?php echo $this->data['QPfisPessoaFisica']['pfis_cpf'] ?></td>	
	    			<td width="25%"><strong>RG: </strong><?php echo $this->data['QPfisPessoaFisica']['pfis_rg'] ?></td>
				<?php endif?>	    		
	    	</tr>
	    </table>
	    <table width="100%" border="0">
	    	<tr>    			
	    		<td width="30%"><strong>Vencimento CNH: </strong><?php echo $this->data['Motorista']['cnh_vencimento'] ?></td>
	    		<td width="30%"><strong>Telefone: </strong><?php echo $this->data['Motorista']['ProfissionalTelefone']['descricao'] ?></td>
	    		<td width="30%"><strong>Celular: </strong><?php echo $this->data['Motorista']['ProfissionalCelular']['descricao'] ?></td>
	    		<td width="10%"><strong>Radio: </strong><?php echo $this->data['Motorista']['ProfissionalRadio']['descricao'] ?></td>
	    	</tr>
	    </table>
	    <table width="100%" border="0">
	    	<tr>    			
	    		<td width="60%"><strong>Gerenciadora: </strong><?php echo $this->data['QPjurGerenciadora']['pjur_razao_social'] ?></td>
	    		<td width="20%"><strong>Liberação: </strong><?php echo $this->data['QViagViagem']['viag_numero_liberacao'] ?></td>	    		
	    		<td width="20%">
	    			<strong>Nacionalidade: </strong><?php echo $this->data['Motorista']['estrangeiro_txt'] ?>
	    		</td>
	    	</tr>
	    </table>   

	    <h5 style="text-align:center;">VEÍCULOS</h5>
	    <?php if($this->data['Recebsm']['Placa'] == 'REMONTA'):?>
	    	 <table class='table'>
		    	<thead>
					<th>Tipo</th>
					<th>Chassi</th>
				</thead>	
				<tbody>
					<?php foreach ($this->data['Veiculos'] as $veiculo):?>
						<tr>    			
				    		<td><?= $veiculo['TTveiTipoVeiculo']['tvei_descricao'];?></td>
				    		<td><?= $veiculo['QVeicVeiculo']['veic_chassi'];?></td>
				    	</tr>
				    <?php endforeach;?>		
				</tbody>    		
		    </table>
    	<?php else:?>		
		    <table class='table'>
		    	<thead>
					<th>Tipo</th>
					<th>Placa</th>
					<th class="numeric">Ano</th>
					<th>Local Emplacamento</th>
					<th class="numeric">Ano Modelo</th>
					<th>Cor</th>
					<th>Tecnologia</th>
					<th>Chassi</th>
					<th>Fabricante</th>
					<th>Modelo</th>
					<th class="numeric">Ano Fabricação</th>
					<th class="numeric">Telefone</th>
					<th class="numeric">Radio</th>
	    		</thead>
	    		<tbody>    		
		    	<?php foreach ($this->data['Veiculos'] as $veiculo):?>
		    		<?php $placa = ($this->data['Recebsm']['Placa'] == 'REMONTA')? 'REMONTA' : $veiculo['QVeicVeiculo']['veic_placa'];?>
			    	<tr>    			
			    		<td><?php echo $veiculo['QTveiTipoVeiculo']['tvei_descricao'] ?></td>
			    		<td><?php echo $placa ?></td>
			    		<td class="numeric"><?php echo $veiculo['QVeicVeiculo']['veic_ano_fabricacao'] ?></td>
			    		<td><?php echo $veiculo['QCidaCidade']['cida_descricao'] ?> - <?php echo $veiculo['QEstaEstado']['esta_sigla'] ?></td>
			    		<td class="numeric"><?php echo $veiculo['QVeicVeiculo']['veic_ano_modelo'] ?></td>
			    		<td><?php echo $veiculo['QVeicVeiculo']['veic_cor'] ?></td>
			    		<td><?php echo $veiculo['QTecnTecnologia']['tecn_descricao'] ?></td>
			    		<td><?php echo $veiculo['QVeicVeiculo']['veic_chassi'] ?></td>
			    		<td><?php echo $veiculo['QMveiMarcaVeiculo']['mvei_descricao'] ?></td>
			    		<td><?php echo $veiculo['QMvecModeloVeiculo']['mvec_descricao'] ?></td>
			    		<td class="numeric"><?php echo $veiculo['QVeicVeiculo']['veic_ano_fabricacao'] ?></td>
			    		<td class="numeric"><?php echo $veiculo['QVeicVeiculo']['veic_telefone'] ?></td>
			    		<td class="numeric"><?php echo $veiculo['QVeicVeiculo']['veic_radio'] ?></td>
			    	</tr>
		    	<?php endforeach;?>
			    </tbody>	
		    </table>
		<?php endif;?>       
	    <!-- ********** FIM INFO VEÍCULO ********** -->
	    <hr />		
		<!-- ********** INÍCIO ESCOLTA ********** -->
		<h5 style="text-align:center;">ESCOLTA</h5>
    	<?php foreach ($escoltas as $escolta): ?>
    		<table class="table" >
    			<thead>
    				<tr>
    					<th colspan="3">Empresa: <?php echo $escolta['TPjurPessoaJuridica']['pjur_razao_social'] ?></th>
    				</tr>
    				<tr>
    					<th class="input-xxlarge">Equipe</th>
    					<th class="input-large">Telefone</th>
    					<th>Placa</th>
    				</tr>
    			</thead>

    			<tbody>
    				<?php foreach ($escolta['Equipes'] as $equipe): ?>
	    				<tr>
	    					<td><?php echo $equipe['TVescViagemEscolta']['vesc_equipe'] ?></td>
	    					<td><?php echo $equipe['TVescViagemEscolta']['vesc_telefone'] ?></td>
	    					<td><?php echo strtoupper($equipe['TVescViagemEscolta']['vesc_placa']) ?></td>
	    				</tr>
    				<?php endforeach; ?>
    			</tbody>

    		</table>
    	<?php endforeach; ?>	    

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
    		<?php //$this->addScript($this->Javascript->codeBlock("tempo_restante_sm({$this->data['QViagViagem']['viag_codigo_sm']})")) ?>
    	</div>

    	<h5>ORIGEM/DESTINO</h5>
    	<div class='row'>
    		<div class="span12">
    			<table class='table'>
    			<thead>
    				<th class="input-small">Local</th>
    				<th>Dados</th>
    				<th></th>
    			</thead>
    			
    			<tbody>
	    			<?php $linha = true; foreach($origem_destino as $tipo_parada): ?>
		    			<?php if( $linha ): ?>
		    				<tr><td class="input-small" rowspan="2"><strong><?php echo $tipo_parada['TTparTipoParada']['tpar_descricao'] ?></strong></td></tr>
		    				<tr>
		    				<td colspan="2">	
		    				<?php $linha = false; ?>
		    			<?php else: ?>
		    				<tr bgcolor="#F5F5F5"><td class="input-small" rowspan="2"><strong><?php echo $tipo_parada['TTparTipoParada']['tpar_descricao'] ?></strong></td></tr>		    				
		    				<tr bgcolor="#F5F5F5">
		    				<td colspan="2">	
		    				<?php $linha = true; ?>
		    			<?php endif; ?>
								
		    					<table width="100%" border="0">
							    	<tr>    			
							    		<td width="50%" style="border-top:none;padding:0;">
							    			<strong>Nome do Alvo: </strong>
	    							<?php echo (isset($tipo_parada['TRefeReferencia']['refe_latitude']) && !empty($tipo_parada['TRefeReferencia']['refe_latitude']) ? $this->Buonny->posicao_geografica($tipo_parada['TRefeReferencia']['refe_descricao'], $tipo_parada['TRefeReferencia']['refe_latitude'], $tipo_parada['TRefeReferencia']['refe_longitude']) : $tipo_parada['TRefeReferencia']['refe_descricao']) ?>
							    		</td>
							    		<td width="50%" style="border-top:none;padding:0;">
							    			<strong>Endereço: </strong>
	    									<?php echo $tipo_parada['TRefeReferencia']['refe_endereco_empresa_terceiro'] ?>
							    		</td>							    			    		
							    	</tr>
							    </table>

							    <table width="100%" border="0">
							    	<tr>    			
							    		<td width="50%" style="border-top:none;padding:0;">
							    			<strong>Bairro: </strong>
	    									<?php echo $tipo_parada['TRefeReferencia']['refe_bairro_empresa_terceiro'] ?>
							    		</td>
							    		<td width="30%" style="border-top:none;padding:0;">
							    			<strong>Cidade: </strong>
	    									<?php echo $tipo_parada['TCidaCidade']['cida_descricao'] ?>
							    		</td>
							    		<td width="20%" style="border-top:none;padding:0;">
							    			<strong>Estado: </strong>
	    									<?php echo $tipo_parada['TEstaEstado']['esta_sigla'] ?>
							    		</td>							    		
							    	</tr>
							    </table>
	    						<table width="100%" border="0">
							    	<tr>    			
							    		<td width="50%" style="border-top:none;padding:0;">							    			
							    			<strong>Produto: </strong>
	    									<?php echo $tipo_parada['TProdProduto']['prod_descricao'] ?>							    	
							    		</td>
							    		<td width="25%" style="border-top:none;padding:0;">
							    			<strong>Peso: </strong>
	    									<?php echo isset($tipo_parada['TVnfiViagemNotaFiscal']['vnfi_peso']) ? $tipo_parada['TVnfiViagemNotaFiscal']['vnfi_peso']: '' ?>
							    		</td>
							    		<td width="25%" style="border-top:none;padding:0;">
							    			<strong>Volume: </strong>
	    							        <?php echo isset($tipo_parada['TVnfiViagemNotaFiscal']['vnfi_volume']) ? $tipo_parada['TVnfiViagemNotaFiscal']['vnfi_volume']: '' ?>
							    		</td>							    		
							    	</tr>
							    </table>   

							    <table width="100%" border="0">
							    	<tr>    			
							    		<td width="25%" style="border-top:none;padding:0;">							    			
							    			<strong>Nota Fiscal: </strong>
	    							<?php echo $tipo_parada['TVnfiViagemNotaFiscal']['vnfi_numero'] ?>							    		
							    		</td>
							    		<td width="25%" style="border-top:none;padding:0;">
							    			<strong>LoadPlan: </strong>
	    							<?php echo $tipo_parada['TVnfiViagemNotaFiscal']['vnfi_pedido'] ?>
							    		</td>
							    		<td width="25%" style="border-top:none;padding:0;">
							    			<strong>Série NF: </strong>
	    							<?php echo isset($tipo_parada['TVnfiViagemNotaFiscal']['vnfi_serie']) ? $tipo_parada['TVnfiViagemNotaFiscal']['vnfi_serie']: '' ?>
							    		</td>	
							    		<td width="25%" style="border-top:none;padding:0;">
							    			<strong>Valor Nota: </strong>
	    									<?php echo number_format($tipo_parada['TVnfiViagemNotaFiscal']['vnfi_valor'], 2, ',', '.') ?>
							    		</td>							    		
							    	</tr>
							    </table>	    						
	    						
	    						<table width="100%" border="0">
							    	<tr>    			
							    		<td width="50%" style="border-top:none;padding:0;">							    			
							    			<strong>Data Previsão: </strong>
	    									<?php echo AppModel::dbDateToDate($tipo_parada['TVlevViagemLocalEventoEntrada']['vlev_data_previsao']) ?>
							    		</td>
							    		
							    		<td width="50%" style="border-top:none;padding:0;">
							    			<strong>Status Chegada: </strong>
	    									<?php echo $tipo_parada['status_chegada']?>
							    		</td>							    						    		
							    	</tr>
							    </table>

							    <table width="100%" border="0">
							    	<tr>    			
							    		<td width="50%" style="border-top:none;padding:0;">							    			
							    			<strong>Data Entrada: </strong>
	    							<?php echo AppModel::dbDateToDate($tipo_parada['TVlevViagemLocalEventoEntrada']['vlev_data']) ?>
							    		</td>
							    		
							    		<td width="50%" style="border-top:none;padding:0;">
							    			<strong>Data Saída: </strong>
	    							<?php echo AppModel::dbDateToDate($tipo_parada['TVlevViagemLocalEventoSaida']['vlev_data']) ?>
							    		</td>							    						    		
							    	</tr>
							    </table>	    					
		    					
		    				</td>		    				
		    			</tr>

		    			
	    			<?php endforeach ?>
    			</tbody>
    			
    			</table>
    		</div>
    		<?php //$this->addScript($this->Javascript->codeBlock("tempo_restante_sm({$this->data['QViagViagem']['viag_codigo_sm']})")) ?>
    	</div>

    	<?php //echo $this->element('viagens/origem_destino') ?>
    	
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
	    							<?php echo (isset($entrega['TRefeReferencia']['refe_latitude']) && !empty($entrega['TRefeReferencia']['refe_latitude']) ? $this->Buonny->posicao_geografica($entrega['TRefeReferencia']['refe_descricao'], $entrega['TRefeReferencia']['refe_latitude'], $entrega['TRefeReferencia']['refe_longitude']) : $entrega['TRefeReferencia']['refe_descricao']) ?>
							    		</td>
							    		<td width="50%"style="border-top:none;padding:0;">
							    			<strong>Endereço: </strong>
	    							<?php echo $entrega['TRefeReferencia']['refe_endereco_empresa_terceiro'] ?>
							    		</td>							    			    		
							    	</tr>
							    </table>

							    <table width="100%" border="0">
							    	<tr>    			
							    		<td width="50%" style="border-top:none;padding:0;">
							    			<strong>Bairro: </strong>
	    									<?php echo $entrega['TRefeReferencia']['refe_bairro_empresa_terceiro'] ?>
							    		</td>
							    		<td width="30%" style="border-top:none;padding:0;">
							    			<strong>Cidade: </strong>
	    									<?php echo $entrega['TCidaCidade']['cida_descricao'] ?>
							    		</td>
							    		<td width="20%" style="border-top:none;padding:0;">
							    			<strong>Estado: </strong>
	    									<?php echo $entrega['TEstaEstado']['esta_sigla'] ?>
							    		</td>							    		
							    	</tr>
							    </table>    						
	    						
	    						<table width="100%" border="0">
							    	<tr>    			
							    		<td width="50%" style="border-top:none;padding:0;">						    			
							    			<strong>Produto: </strong>
	    									<?php echo $entrega['TProdProduto']['prod_descricao'] ?>						    	
							    		</td>
							    		<td width="25%" style="border-top:none;padding:0;">
							    			<strong>Peso: </strong>
	    									<?php echo isset($entrega['TVnfiViagemNotaFiscal']['vnfi_peso']) ? $entrega['TVnfiViagemNotaFiscal']['vnfi_peso']: '' ?>
							    		</td>
							    		<td width="25%" style="border-top:none;padding:0;">
							    			<strong>Volume: </strong>
	    							        <?php echo isset($entrega['TVnfiViagemNotaFiscal']['vnfi_volume']) ? $entrega['TVnfiViagemNotaFiscal']['vnfi_volume']: '' ?>
							    		</td>							    		
							    	</tr>
							    </table>   

							    <table width="100%" border="0">
							    	<tr>    			
							    		<td width="25%" style="border-top:none;padding:0;">							    			
							    			<strong>Nota Fiscal: </strong>
	    							<?php echo $entrega['TVnfiViagemNotaFiscal']['vnfi_numero'] ?>						    		
							    		</td>
							    		<td width="25%" style="border-top:none;padding:0;">
							    			<strong>LoadPlan: </strong>
	    							<?php echo $entrega['TVnfiViagemNotaFiscal']['vnfi_pedido'] ?>
							    		</td>
							    		<td width="25%" style="border-top:none;padding:0;">
							    			<strong>Série NF: </strong>
	    							<?php echo isset($entrega['TVnfiViagemNotaFiscal']['vnfi_serie']) ? $entrega['TVnfiViagemNotaFiscal']['vnfi_serie']: '' ?>
							    		</td>	
							    		<td width="25%" style="border-top:none;padding:0;">
							    			<strong>Valor Nota: </strong>
	    									<?php echo number_format($entrega['TVnfiViagemNotaFiscal']['vnfi_valor'], 2, ',', '.') ?>
							    		</td>							    		
							    	</tr>
							    </table>  

							    <table width="100%" border="0">
							    	<tr>    			
							    		<td width="50%" style="border-top:none;padding:0;">						    			
							    			<strong>Tipo Itin.: </strong>
	    							<?php echo $entrega['TTparTipoParada']['tpar_descricao'] ?>					    		
							    		</td>
							    		<td width="50%" style="border-top:none;padding:0;">							    			
							    			<strong>Data Previsão: </strong>
	    							<?php echo AppModel::dbDateToDate($entrega['TVlevViagemLocalEventoEntrada']['vlev_data_previsao']) ?>					    		
							    		</td>
							    	</tr>
							    </table> 						

	    						
	    						<table width="100%" border="0">
							    	<tr>    			
							    		<td width="50%" style="border-top:none;padding:0;">						    			
							    	<strong>Início Janela: </strong>
	    							<?php echo AppModel::dbDateToDate($entrega['TVlocViagemLocal']['vloc_data_janela_inicio']) ?>				    		
							    		</td>
							    		<td width="50%" style="border-top:none;padding:0;">							    			
							    	<strong>Final Janela: </strong>
	    							<?php echo AppModel::dbDateToDate($entrega['TVlocViagemLocal']['vloc_data_janela_fim']) ?>	    		
							    		</td>
							    	</tr>
							    </table> 

							    <table width="100%" border="0">
							    	<tr>    			
							    		<td width="50%" style="border-top:none;padding:0;">							    			
							    	<strong>Status Chegada: </strong>
	    							<?php echo $entrega['status_chegada']?>			    		
							    		</td>
							    		<td width="50%" style="border-top:none;padding:0;">							    			
							    	<strong>Status Janela: </strong>
	    							<?php echo empty($entrega['TVlocViagemLocal']['vloc_data_janela_inicio']) ? '': $entrega['status_janela']?>  		
							    		</td>
							    	</tr>
							    </table> 
	    						
	    					 	<table width="100%" border="0">
							    	<tr>    			
							    		<td width="50%" style="border-top:none;padding:0;">							    			
							    	<strong>Data Entrada: </strong>
	    							<?php echo AppModel::dbDateToDate($entrega['TVlevViagemLocalEventoEntrada']['vlev_data']) ?>	    		
							    		</td>
							    		<td width="50%" style="border-top:none;padding:0;">							    			
							    	<strong>Data Saída: </strong>
	    							<?php echo AppModel::dbDateToDate($entrega['TVlevViagemLocalEventoSaida']['vlev_data']) ?>		
							    		</td>
							    	</tr>
							    </table>    					

		    				</td>		    				
		    			</tr>

		    			
	    			<?php endforeach ?>
    			</tbody>
    			
    			</table>
    		</div>
    		<?php //$this->addScript($this->Javascript->codeBlock("tempo_restante_sm({$this->data['QViagViagem']['viag_codigo_sm']})")) ?>
    	</div>

    	<?php //echo $this->element('viagens/itinerario') ?>

    	<!-- ********** FIM ITINERÁRIO ********** -->
	    
	    <hr />
		
		<!-- ********** INÍCIO OBSERVAÇÕES ********** -->
		<h5 style="text-align:center;">OBSERVAÇÕES</h5>
	    
	    <strong>Observação:</strong>
	    <div class="row">
	    	<div class="span12">
	    		<?php echo $this->data['QViagViagem']['viag_observacao']; ?>
	    	</div>
	    </div>
	    
	    
	    <!-- ********** FIM OBSERVAÇÕES ********** -->
	    
	    <hr />
		
		<!-- ********** INÍCIO ISCAS ********** -->
		<h5 style="text-align:center;">ISCAS</h5>    

	    <div class="tab-pane isca" id="iscas">
	      	 <?php echo $this->addScript($this->Javascript->codeBlock("listaIscasTecnologia({$this->data['QViagViagem']['viag_codigo']})"));?>
	    </div>	
		
	    <!-- ********** FIM ISCAS ********** -->
	    
	    <hr />

	    <!-- ********** INÍCIO OCORRENCIAS ********** -->
		<h5 style="text-align:center;">OCORRENCIAS</h5>    

	    <div class="tab-pane ocorrencia" id="ocorrencias">
	      	<?php echo $this->addScript($this->Javascript->codeBlock("listaOcorrencias({$this->data['QViagViagem']['viag_codigo']})"));?>
	    </div>
		
	    <!-- ********** FIM OCORRENCIAS ********** -->
        <?php if(!isset($nova_janela)): ?>
            <div class="form-actions">
                <?php echo $html->link('Voltar', array('action' => 'consulta_sm2'), array('class' => 'btn')); ?>
            </div>    
        <?php endif; ?>

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
			'<div id=\"postlink\"><form accept-charset=\"utf-8\" method=\"post\" id=\"QViagViagem\" action=\"/portal/viagens/consulta_sm2/newwindow/print\"><input type=\"text\" id=\"QViagViagemCodigo\" value='+'\"'+codigo_viagem+'\"'+' name=\"data[QViagViagem][viag_codigo_sm]\"></form></div>'
		);
		newwindow.document.getElementById('postlink').style.display = 'none';
		newwindow.document.getElementById('QViagViagem').submit();	
	}

", false);
?>
