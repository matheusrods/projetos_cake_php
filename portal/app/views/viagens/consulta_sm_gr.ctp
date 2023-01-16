<?php if (!isset($this->data['QViagViagem'])): ?>
	<?php echo $this->BForm->create('QViagViagem', array('autocomplete' => 'off', 'url' => array('controller' => 'viagens', 'action' => 'consulta_sm_gr', $nova_janela))) ?>
		<div class="row-fluid inline">
			<?php echo $this->BForm->input('viag_codigo_sm', array('label' => false, 'placeholder' => 'Código SM', 'class' => 'input-small just-number', 'type' => 'text')); ?>
		</div>
		<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
	<?php echo $this->BForm->end();?>
<?php else:?>
	
	<div id="print">
        <a style="display:block; float:right;" href="#" onclick="consulta_sm_impressao('<?php echo $this->data['QViagViagem']['viag_codigo_sm'] ?>')" title="Imprimir Solicitação de Monitoramento"><i class="icon-print icon-black"></i></a>
    </div>
	
	<ul class="nav nav-tabs">
		<li class="active"><a href="#gerais" data-toggle="tab">Dados Gerais</a></li>
		<li><a href="#veiculo" data-toggle="tab">Informações do Veículo</a></li>
		<li><a href="#escolta" data-toggle="tab" class="escolta">Escolta</a></li>
		<li><a href="#itinerario" data-toggle="tab" id="a-itinerario" class="itinerario">Itinerário</a></li>
		<!-- <li><a href="#rota" data-toggle="tab">Rota</a></li> -->
		<li><a href="#observacoes" data-toggle="tab">Observações</a></li>
		<li><a href="#iscas" data-toggle="tab" class="iscas">Iscas</a></li>
		<li><a href="#ocorrencias" data-toggle="tab" class="ocorrencias">Ocorrencias</a></li>
		<li><a href="#rmas" data-toggle="tab" class="rmas">RMA´s</a></li>
		<?php //if($exibe_eventos): ?>
			<li><a href="#eventos" data-toggle="tab" class="eventos">Eventos</a></li>
		<?php //endif; ?>
		<li><a href="#cronologia" data-toggle="tab" class="cronologia">Cronologia</a></li>
		<?php if($comboio): ?>
			<li><a href="#comboio" data-toggle="tab">Comboio</a></li>
		<?php endif; ?>
		<?php if( isset($exibe_operadores_em_monitoramento) && $exibe_operadores_em_monitoramento == TRUE ): ?>
			<li><a href="#monitoramento" class="monitoramento" data-toggle="tab">Monitoramento</a></li>
		<?php endif; ?>
	</ul>	    
    <div class="row-fluid inline">
		<span class='span2'>
			<span class="badge-empty badge <?php echo $status_sm ?>" title="<?php echo $msg_status_sm ?>"></span> <?php echo $msg_status_sm ?>
		</span> 
    	<?php //if ($this->data['TRacsRegraAceiteSm']['racs_verificar_checklist']): ?>
			<span class='span2'>
				<span class="badge-empty badge <?php echo $checklist ?>" title="<?php echo $msg_checklist_tooltip ?>"></span>
				<?php if(!empty($this->data['QVcheViagemChecklist']['vche_refe_codigo'])):?>
					<?php $data_inicial_checklist = substr($this->data['QViagViagem']['viag_data_cadastro'],0,10);?>
					<?php $data_final_checklist = date('d/m/Y');?>
					<?= $this->Html->link($msg_checklist, 'javascript:void(0)', array('onclick' => "consulta_checklist('{$this->data['QVcheViagemChecklist']['vche_refe_codigo']}','{$this->data['QViagViagem']['viag_codigo_sm']}','{$this->data['ClienteEmbarcador']['codigo']}','{$data_inicial_checklist}','{$data_final_checklist}')")) ?>
				<?php else:?>
					<?=  $msg_checklist;?>
				<?php endif;?>	
			</span>
			<span class='span4'>
				<span class="badge-empty badge <?php echo $checklist_veiculo ?>" title="<?php echo $msg_checklist_tooltip_veiculo ?>"></span>
				<?php echo $msg_checklist_veiculo ?>
			</span>
		<?php //endif ?>
		<div style='float:right'>
			<?php if(!$authUsuario['Usuario']['codigo_cliente']): ?>
				<?php echo $html->link(
										$html->image("icon-log.png", array('title' => 'Histórico da Monitoração')), 
										array(
											'controller'=>'logs_distribuicao', 
											'action' => 'log_viagem', 
											$this->data['QViagViagem']['viag_codigo']
										), 
										array('escape' => false, 
										'onclick' => 'return open_popup(this);'));?>&nbsp;&nbsp;&nbsp;
		
			<?php endif; ?>
			<?php if($permissao && $msg_status_sm != 'Cancelado'): ?>

			<?php echo $html->link(
									$html->image("icon-cancel.png", array('title' => 'Cancelar Viagem')), 
									"/{$this->name}/cancelarSm?key=".urlencode(Comum::encriptarLink($this->data['QViagViagem']['viag_codigo'])), 
									array('class' => 'sm-cancelar', 'escape' => false));?>&nbsp;&nbsp;&nbsp;

			<?php endif; ?>
			<?php if (!$chamada_guardian):?>
			<?php echo $html->link(
									$html->image("icon-map.jpg", array('title' => 'Detalhar Itinerário no Mapa')), 
									array(
										'controller'=>'solicitacoes_monitoramento', 
										'action' => 'itinerario_mapa', 
										$this->data['QViagViagem']['viag_codigo_sm']
									), 
									array('escape' => false, 
											'onclick' => 'return open_popup(this);'
									)
				);
			?>
			<?php else: ?>
			<?php echo $html->link(
									$html->image("icon-map.jpg", array('title' => 'Detalhar Itinerário no Mapa')), 
									array(
										'controller'=>'solicitacoes_monitoramento', 
										'action' => 'itinerario_mapa', 
										$this->data['QViagViagem']['viag_codigo_sm']
									), 
									array('escape' => false)
				);
			?>

			<?php endif; ?>
		</div>

    </div>
	<div class="row-fluid inline">
		<?php echo $this->BForm->input('QViagViagem.viag_codigo_sm', array('label' => 'SM', 'readonly' => true,  'class' => 'input-small')); ?>
		<?php echo $this->BForm->input('QTveiTipoVeiculo.tvei_descricao', array('label' => 'Tipo do Veículo', 'readonly' => true, 'class' => 'input-small')); ?>
		<div class='control-group input'>
			<label>Placa</label>
			<?php 
				
				if( !Comum::isVeiculo($this->data['Veiculos'][0]['QVeicVeiculo']['veic_placa'])) {
					echo "REMONTA";
				}else
				{ 
					echo $this->Buonny->placa( $this->data['Veiculos'][0]['QVeicVeiculo']['veic_placa'], $this->data['QViagViagem']['viag_data_cadastro'], (empty($this->data['QViagViagem']['viag_data_fim']) ? Date('d/m/Y H:i:s') : $this->data['QViagViagem']['viag_data_fim']) );
				}
			?>
		</div>
		<?php if ($this->data['QVestViagemEstatus']['vest_estatus'] == '2'): ?>
			<?php echo $this->BForm->input('QVestViagemEstatus.vest_usuario_adicionou', array('label' => 'Usuário Cancelamento', 'readonly' => true, 'class' => 'input-medium')) ?>
			<?php echo $this->BForm->input('QMotiMotivo.moti_descricao', array('label' => 'Motivo', 'readonly' => true, 'class' => 'input-xlarge')) ?>
		<?php endif ?>
	</div>
	<div class="tab-content">
		<div class="tab-pane active" id="gerais">
			<div class="row-fluid inline">
				<?php echo $this->BForm->input('ClienteEmbarcador.codigo', array('label' => 'Embarcadora', 'readonly' => true, 'class' => 'input-small')); ?>
				<?php echo $this->BForm->input('ClienteEmbarcador.razao_social', array('label' => '&nbsp', 'readonly' => true, 'class' => 'input-xxlarge')); ?>
			</div>
			<div class="row-fluid inline">
				<?php echo $this->BForm->input('ClienteTransportador.codigo', array('label' => 'Transportadora', 'readonly' => true, 'class' => 'input-small')); ?>
				<?php echo $this->BForm->input('ClienteTransportador.razao_social', array('label' => '&nbsp', 'readonly' => true, 'class' => 'input-xxlarge')); ?>
			</div>
			<?php if (isset($this->data['ClientePagador']['codigo'])): ?>
				<div class="row-fluid inline">
					<?php echo $this->BForm->input('ClientePagador.codigo', array('label' => 'Pagador', 'readonly' => true, 'class' => 'input-small')); ?>
					<?php echo $this->BForm->input('ClientePagador.razao_social', array('label' => '&nbsp', 'readonly' => true, 'class' => 'input-xxlarge')); ?>
				</div>
			<?php endif ?>
			<div class="row-fluid inline">
				<?php echo $this->BForm->input('QViagViagem.viag_data_cadastro', array('label' => 'Data Cadastro', 'readonly' => true, 'type' => 'text', 'class' => 'input-medium')); ?>
				<?php echo $this->BForm->input('QViagViagem.viag_usuario_adicionou', array('label' => 'Solicitante', 'readonly' => true, 'type' => 'text', 'class' => 'input-medium')); ?>
				<?php echo $this->BForm->input('QViagViagem.viag_valor_carga', array('label' => 'Carga(R$)', 'readonly' => true, 'type' => 'text', 'class' => 'input-medium')); ?>
				<?php echo $this->BForm->input('QViagViagem.viag_pedido_cliente', array('label' => 'Pedido Cliente', 'readonly' => true, 'type' => 'text', 'class' => 'input-small')); ?>
				<?php if ($visualizar_pgr): ?>
					<div class="control-group input text required">
						<label>Código PGR</label>
						<?php echo $this->Html->link($this->data['QViagViagem']['viag_pgpg_codigo'], 'javascript:void(0)', array('onclick' => "visualizar_pgr('{$this->data['QViagViagem']['viag_pgpg_codigo']}', '{$this->data['QViagViagem']['viag_racs_codigo']}')")) ?>
					</div>
				<?php else: ?>
					<?php echo $this->BForm->input('QViagViagem.viag_pgpg_codigo', array('label' => 'Código PGR', 'readonly' => true, 'type' => 'text', 'class' => 'input-mini')); ?>
				<?php endif ?>

				<?php echo $this->BForm->input('QTtraTipoTransporte.ttra_descricao', array('label' => 'Tipo Transporte', 'readonly' => true, 'type' => 'text', 'class' => 'input-medium')); ?>
			</div>
			<div class="row-fluid inline">
				<?php echo $this->BForm->input('QViagViagem.viag_previsao_inicio', array('label' => 'Previsão Início', 'readonly' => true, 'class' => 'input-medium', 'type' => 'text')); ?>
				<?php echo $this->BForm->input('QViagViagem.viag_previsao_fim', array('label' => 'Previsão Fim', 'readonly' => true, 'class' => 'input-medium', 'type' => 'text')); ?>
				<?php 
					$tipoIni = '';
					$tipoFim = '';
					if( !is_null($this->data['QViagViagem']['viag_data_inicio']) ){
						$tipoIni = ( $this->data['tipoInicioFimViagem']['inicio'] ) ? ' (auto)' : " ({$this->data['QViagViagem']['vag_usuario_efetivou']})";
					}						
					if( !is_null($this->data['QViagViagem']['viag_data_fim']) ){
						$tipoFim = ( $this->data['tipoInicioFimViagem']['fim'] ) ? ' (auto)' : " ({$this->data['QViagViagem']['viag_usuario_finalizou']})";
					}
				?>
				<?php echo $this->BForm->input('QViagViagem.viag_data_inicio', array('label' => 'Início Real'.$tipoIni, 'readonly' => true,'class' => 'input-medium', 'type' => 'text')); ?>
				<?php echo $this->BForm->input('QViagViagem.viag_data_fim', array('label' => 'Fim Real'.$tipoFim, 'readonly' => true, 'class' => 'input-medium', 'type' => 'text')); ?>
				<?php echo $this->BForm->input('QVtemViagemTemperatura.vtem_valor_minimo', array('label' => 'Temperatura', 'readonly' => true, 'class' => 'input-mini numeric')); ?>
				<div class='input'><label>&nbsp;</label>a</div>
				<?php echo $this->BForm->input('QVtemViagemTemperatura.vtem_valor_maximo', array('label' => '&nbsp', 'readonly' => true, 'class' => 'input-mini numeric')); ?>
				<div class='input'><label>&nbsp;</label><?php echo $this->Html->link($this->Html->image('/img/icon-thermometer.jpg'), array('controller'=>'viagens', 'action'=>'jornada_temperatura', $this->data['QViagViagem']['viag_codigo'], rand()), array('onclick'=>'return open_popup(this);','escape' => false)); ?></div>
				
			</div>
			<div class="row-fluid inline">
				<h5>Terminal da Viagem</h5>
				<?php echo $this->BForm->input('QVterViagemTerminal.tecn_descricao', array('label' => 'Tecnologia', 'readonly' => true, 'class' => 'input-medium', 'type' => 'text')); ?>
				<?php echo $this->BForm->input('QVterViagemTerminal.term_numero_terminal', array('label' => 'Numero', 'readonly' => true, 'class' => 'input-medium', 'type' => 'text')); ?>
				<?php echo $this->BForm->input('QVterViagemTerminal.term_numero_serie', array('label' => 'Série', 'readonly' => true, 'class' => 'input-medium', 'type' => 'text')); ?>
			</div>
			<div class="row-fluid inline">
				<?php echo  "<strong>Sistema Origem:</strong> {$this->data['QViagViagem']['viag_sistema_origem']}" ?>
				<?php if($this->data['QViagViagem']['viag_sm_reprogramada']): ?>
					<strong>SM :</strong> REPROGRAMADA
				<?php endif ?>
			</div>
		</div>

	    <div class="tab-pane" id="veiculo">
	    	<h5>Motorista </h5>
	    	<div class="row-fluid inline"> 
	    		<?php echo $this->BForm->input('Motorista.nome', array('label' => 'Nome', 'readonly' => true, 'class' => 'input-large')); ?>
	    		<?php if(!empty($estrangeiro)): ?>
	    			<?php echo $this->BForm->input('QPfisPessoaFisica.pfis_cpf', array('label' => 'CPF/RNE', 'readonly' => true, 'class' => 'input-small')); ?>
	    		<?php else:?>
	    			<?php echo $this->BForm->input('QPfisPessoaFisica.pfis_cpf', array('label' => 'CPF', 'readonly' => true, 'class' => 'input-small')); ?>
	    			<?php echo $this->BForm->input('QPfisPessoaFisica.pfis_rg', array('label' => 'RG', 'readonly' => true, 'class' => 'input-small')); ?>
				<?php endif?>	    		
	    		<?php echo $this->BForm->input('Motorista.validade_cnh', array('label' => 'Vencimento CNH', 'readonly' => true, 'type' => 'text','class' => 'input-small')); ?>
	    		<?php echo $this->BForm->input('Motorista.ProfissionalTelefone.descricao', array('label' => 'Telefone', 'readonly' => true, 'class' => 'input-small')); ?>
	    		<?php echo $this->BForm->input('Motorista.ProfissionalCelular.descricao', array('label' => 'Celular', 'readonly' => true, 'class' => 'input-small')); ?>
	    		<?php echo $this->BForm->input('Motorista.ProfissionalRadio.descricao', array('label' => 'Radio', 'readonly' => true, 'class' => 'input-small')); ?>
	    	</div>
	    	<?php if (!empty($this->data['Motorista']['TelefonesTeleConsultDataUltAtualizacao'])) : ?>
	    	<h6>Telefones pesquisados <span style="margin: 0 0 0 90px;">Data da Última Atualização:</span> <span style="font-weight: normal; margin:0 0 0 3px;"><?php echo $this->data['Motorista']['TelefonesTeleConsultDataUltAtualizacao']; ?></span></h6>
	    	<div class="row-fluid inline lstTeleConsult">
				<?php foreach ($this->data['Motorista']['TelefonesTeleConsult'] as $key => $Telefone) : ?>
	    			<?php echo $this->BForm->input("Motorista.TelefonesTeleConsult.{$key}", array('label'=>false,'readonly' => true, 'class' => 'input-small')); ?>
	    		<?php endforeach ?>
	    	</div>
	    	<?php endif ?>
	    	<div class="row-fluid inline">
	    		<?php echo $this->BForm->input('QPjurGerenciadora.pjur_razao_social', array('label' => 'Gerenciadora', 'readonly' => true, 'class' => 'input-xlarge')); ?>
	    		<?php if($this->data['QViagViagem']['viag_gris_pjur_pess_oras_codigo'] == QGrisGerenciadoraRisco::BUONNY): ?>
	    			<?php echo $this->BForm->input('QViagViagem.viag_codigo_log_faturamento', array('label' => 'Liberação', 'readonly' => true, 'class' => 'input-medium')); ?>
	    		<?php else: ?>
	    			<?php echo $this->BForm->input('QViagViagem.viag_numero_liberacao', array('label' => 'Liberação', 'readonly' => true, 'class' => 'input-medium')); ?>
	    		<?php endif; ?>
	    		<?php echo $this->BForm->input('Motorista.estrangeiro_txt', array('label' => 'Nacionalidade', 'readonly' => true, 'class' => 'input-small')); ?>
	    	</div>
	    	<h5>Placas</h5>
	    	<?php if($this->data['Recebsm']['Placa'] == 'REMONTA'):?>
	    	 <table class='table'>
		    	<thead>
					<th>Tipo</th>
					<th>Chassi</th>
				</thead>	
				<tbody>
					<?php foreach ($this->data['Veiculos'] as $veiculo):?>
						<tr>    			
				    		<td><?= $veiculo['QTveiTipoVeiculo']['tvei_descricao'];?></td>
				    		<td><?= $veiculo['QVeicVeiculo']['veic_chassi'];?></td>
				    	</tr>
				    <?php endforeach;?>		
				</tbody>    		
		    </table>
    		<?php else:?>
		    	<table class='table table-striped'>
		    		<thead>
		    			<th>Placa</th>
		    			<th>Seq</th>
		    			<th>Tipo</th>
		    			<th>Tecnologia</th>
		    			<th>Chassi</th>
		    			<th>Telefone</th>
		    			<th>Radio</th>
		    			<th>Cor</th>
		    			<th>Fabricante</th>
		    			<th>Modelo</th>
		    			<th>Ano Fabricação</th>
		    			<th>Ano Modelo</th>
		    		</thead>
		    		<tbody>
		    			<?php foreach ($this->data['Veiculos'] as $viagem_veiculo): ?>
			    			<tr>
			    				<td><?= $viagem_veiculo['QVeicVeiculo']['veic_placa'] ?></td>
			    				<td><?= $viagem_veiculo['QVveiViagemVeiculo']['vvei_precedencia'] ?></td>
			    				<td><?= $viagem_veiculo['QTveiTipoVeiculo']['tvei_descricao'] ?></td>
			    				<td><?= $viagem_veiculo['QTecnTecnologia']['tecn_descricao'] ?></td>
			    				<td><?= $viagem_veiculo['QVeicVeiculo']['veic_chassi'] ?></td>
			    				<td><?= $viagem_veiculo['QVeicVeiculo']['veic_telefone'] ?></td>
			    				<td><?= $viagem_veiculo['QVeicVeiculo']['veic_radio'] ?></td>
			    				<td><?= $viagem_veiculo['QVeicVeiculo']['veic_cor'] ?></td>
			    				<td><?= $viagem_veiculo['QMveiMarcaVeiculo']['mvei_descricao'] ?></td>
			    				<td><?= $viagem_veiculo['QMvecModeloVeiculo']['mvec_descricao'] ?></td>
			    				<td><?= $viagem_veiculo['QVeicVeiculo']['veic_ano_fabricacao'] ?></td>
			    				<td><?= $viagem_veiculo['QVeicVeiculo']['veic_ano_modelo'] ?></td>
			    			</tr>
		    			<?php endforeach ?>
		    		</tbody>
		    	</table>
		    <?php endif;?>	
	    </div>
		<div class="tab-pane" id="escolta">&nbsp;</div>
	    <div class="tab-pane" id="itinerario">&nbsp;</div>

	    <div class="tab-pane" id="observacoes">
	    	<?php echo $this->BForm->input('QViagViagem.viag_observacao', array('label' => 'Observações', 'readonly' => true, 'class' => 'input-xxlarge', 'type' => 'textarea')); ?>
	    </div>
	    <div class="tab-pane isca" id="iscas">&nbsp;</div>
	    <div class="tab-pane" id="ocorrencias">
	    	<div class="actionbar-right" >
	    		<?php echo $this->BMenu->linkOnClick('Incluir', array('controller' => 'Ocorrencias', 'action' => 'incluir_viagem_ocorrencia',$this->data['QViagViagem']['viag_codigo'],rand()), array('onclick' => 'return open_dialog(this, "Incluir Ocorrencia", 600)', 'title' => 'Incluir Ocorrencia', 'class' => 'btn btn-success')); ?>
	    	</div>
	    	
	    	<div class="ocorrencia">&nbsp;</div>
	    	<?php //echo $this->addScript($this->Javascript->codeBlock("listaOcorrencias({$this->data['QViagViagem']['viag_codigo']})"));?>
	    </div>	
	    <div class="tab-pane" id="rmas">
	    	<div id="rma-list" >&nbsp;</div>	    	
	    </div>		
		<?php //if($exibe_eventos): ?>
	    <div class="tab-pane" id="eventos">&nbsp;</div>		
	    <div class="tab-pane" id="cronologia">&nbsp;</div>		
		<? //endif; ?>
		<?php if($comboio) : ?>
	    <div class="tab-pane" id="comboio">
	    	<div id="comboio-list" >
			    <div class='row-fluid' style='overflow-x:auto'>
			        <table class='table table-striped' > 
			            <thead >
			                <tr>
			                    <th class='input-small'  title="Codigo SM">SM</th>
			                    <th class='input-medium' title="Placa">Placa</th>
			                    <th class='input-medium' title="Previsão de Inicio">Previsão de Inicio</th>
			                    <th class='input-medium' title="Previsão de Fim">Previsão de Fim</th>
			                    <th class='input-xlarge' title="Alvo Origem">Alvo Origem</th>			                    
			                    <th class='input-xlarge' title="Alvo Destino">Alvo Destino</th>
			                </tr>
			            </thead>
			            <tbody >
			                <?php foreach ($comboio as $key => $dados_comboio ) :  ?>
			                <tr>
			                    <td>
			                    	<?php 
			                    	if( $this->data['QViagViagem']['viag_codigo_sm'] != $dados_comboio['QViagViagem']['viag_codigo_sm'] )
			                    		echo $this->Buonny->codigo_sm($dados_comboio['QViagViagem']['viag_codigo_sm']); 
			                    	else
			                    		echo $dados_comboio['QViagViagem']['viag_codigo_sm'];
			                    	?>
			                    </td>
			                    <td><?=Comum::formatarPlaca($dados_comboio['QVeicVeiculo']['veic_placa']); ?></td>
			                    <td><?=AppModel::dbDateToDate($dados_comboio['QViagViagem']['viag_previsao_inicio']) ?></td>
			                    <td><?=AppModel::dbDateToDate($dados_comboio['QViagViagem']['viag_previsao_fim']) ?></td>
								<td>
									<?php 
									if (isset($dados_comboio['QRefeOrigem']['refe_latitude']) && !empty($dados_comboio['QRefeOrigem']['refe_latitude']) )
										echo $this->Buonny->posicao_geografica($dados_comboio['QRefeOrigem']['refe_descricao'], $dados_comboio['QRefeOrigem']['refe_latitude'], $dados_comboio['QRefeOrigem']['refe_longitude']);
			                    	else 
			                    		echo $dados_comboio['QRefeOrigem']['refe_descricao'];?>									
								</td>
								<td>
									<?php 
									if (isset($dados_comboio['QRefeDestino']['refe_latitude']) && !empty($dados_comboio['QRefeDestino']['refe_latitude']) )
										echo $this->Buonny->posicao_geografica($dados_comboio['QRefeDestino']['refe_descricao'], $dados_comboio['QRefeDestino']['refe_latitude'], $dados_comboio['QRefeDestino']['refe_longitude']) ;
			                    	else 
			                    		echo $dados_comboio['QRefeDestino']['refe_descricao'];?>
								</td>			                    
			                </tr>
			            <?php  endforeach; ?>
			        	</tbody>
			    	</table>
				</div>
	    	</div>	    	
	    </div>				
		<? endif; ?>
		<?php if( isset($exibe_operadores_em_monitoramento) && $exibe_operadores_em_monitoramento == TRUE ): ?>
		<div class="tab-pane" id="monitoramento">&nbsp;</div>
		<? endif; ?>
	</div>
        <?php if(!isset($nova_janela)): ?>
            <div class="form-actions">
                <?php echo $html->link('Voltar', array('action' => 'consulta_sm_gr'), array('class' => 'btn')); ?>
            </div>    
        <?php endif; ?>
<?php endif ?>
<?php  echo $this->BForm->end() ?>
<?php echo $this->Buonny->link_js('estatisticas') ?>
<?php echo $this->Buonny->link_css('jquery.tablescroll'); ?>
<?php echo $this->Buonny->link_js('jquery.tablescroll'); ?>

<?php echo $this->Javascript->codeBlock("
	jQuery(document).ready(function(){
		//list_rmas();
		setup_mascaras();
		$('#a-itinerario').on('shown', function (e) {
	        $('.horizontal-scroll').tableScroll({width:2500, height:200}); 
		});	
		
		$('#a-itinerario').on('shown', function (e) {
	        $('.horizontal-scroll').tableScroll({width:2500, height:200}); 
		});	
		
		$('.sm-cancelar').click(function(){
			if(!confirm('Deseja realmente efetuar o cancelamento da SM?')){
				return false;
			}
		});

		$('.monitoramento').click(function(){
			if( $('#monitoramento').html() == '&nbsp;' ){
				var div_monitoramento = jQuery('#monitoramento');
				bloquearDiv(div_monitoramento);
				div_monitoramento.load(baseUrl + '/operadores/listagem_operadores_por_sm/{$this->data['QViagViagem']['viag_codigo_sm']}');
			}
		});
		
		$('.cronologia').click(function(){
			if( $('#cronologia').html() == '&nbsp;' ){
				var div_cronologia = jQuery('#cronologia');
				bloquearDiv(div_cronologia);
				div_cronologia.load(baseUrl + '/viagens/consulta_sm_gr_cronologia/{$this->data['QViagViagem']['viag_codigo_sm']}');
			}
		});
		
		$('.itinerario').click(function(){
			if( $('#itinerario').html() == '&nbsp;' ){
				var div_itinerario = jQuery('#itinerario');
				bloquearDiv(div_itinerario);
				div_itinerario.load(baseUrl + '/viagens/consulta_sm_gr_itinerario/{$this->data['QViagViagem']['viag_codigo_sm']}');
			}
		});

		$('.eventos').click(function(){
			if( $('#eventos').html() == '&nbsp;' ){
				var div_eventos = jQuery('#eventos');
				bloquearDiv(div_eventos);
				div_eventos.load(baseUrl + '/viagens/consulta_sm_gr_eventos/{$this->data['QViagViagem']['viag_codigo_sm']}');
			}
		});
		
		$('.escolta').click(function(){
			if( $('#escolta').html() == '&nbsp;' ){
				var div_escolta = jQuery('#escolta');
				bloquearDiv(div_escolta);
				div_escolta.load(baseUrl + '/viagens/consulta_sm_gr_escolta/{$this->data['QViagViagem']['viag_codigo_sm']}');
			}
		});

		$('.rmas').click(function(){
			if( $('#rma-list').html() == '&nbsp;' ){
				var div_rma = jQuery('#rma-list');
				bloquearDiv(div_rma);
				list_rmas();
			}
		});

		$('.iscas').click(function(){
			if( $('#iscas').html() == '&nbsp;' ){
				var div_iscas = jQuery('#iscas');
				bloquearDiv(div_iscas);
				listaIscasTecnologia({$this->data['QViagViagem']['viag_codigo']});
			}
		});

		$('.ocorrencias').click(function(){
			if( $('.ocorrencia').html() == '&nbsp;' ){
				var div_ocorrencia = jQuery('.ocorrencia');
				bloquearDiv(div_ocorrencia);
				listaOcorrencias({$this->data['QViagViagem']['viag_codigo']});
			}
		});
	});


	function list_rmas()
	{
		$.ajax({
			type: 'POST',
			url: baseUrl + 'filtros/filtrar/model:TOrmaOcorrenciaRma/element_name:rma',
			data: { 
					'_method' : 'post',
					'data[TOrmaOcorrenciaRma][data_inicial]': '" . date("d/m/Y") . "',
					'data[TOrmaOcorrenciaRma][data_final]': '" . date("d/m/Y") . "',
					'data[TOrmaOcorrenciaRma][codigo_cliente]': '',
					'data[TOrmaOcorrenciaRma][codigo_embarcador]': '',
					'data[TOrmaOcorrenciaRma][codigo_transportador]': '',
					'data[TOrmaOcorrenciaRma][grma_codigo]': '',
					'data[TOrmaOcorrenciaRma][trma_codigo]': '',
					'data[TOrmaOcorrenciaRma][pfis_cpf]': '',
					'data[TOrmaOcorrenciaRma][viag_codigo_sm]': '". $this->data['QViagViagem']['viag_codigo_sm'] ."',
					
				  }
		})
		.done(function( return_rma ) {
			$('#rma-list').load(baseUrl + 'rma/analitico_listagem/'+Math.random(), function() {
				$.ajax({
					type: 'GET',
					url: baseUrl + 'filtros/limpar/model:TOrmaOcorrenciaRma/element_name:rma',
				});
			}); 
		});

	}

	function consulta_sm_impressao(codigo_viagem) {
		
		var newwindow = window.open('/portal/viagens/consulta_sm_gr/newwindow/print','_blank','scrollbars=yes,top=0,left=0,width=1000,height=800');								
		newwindow.document.write(
			'<div id=\"postlink\"><form accept-charset=\"utf-8\" method=\"post\" id=\"QViagViagem\" action=\"/portal/viagens/consulta_sm_gr/newwindow/print\"><input type=\"text\" id=\"QViagViagemCodigo\" value='+'\"'+codigo_viagem+'\"'+' name=\"data[QViagViagem][viag_codigo_sm]\"></form></div>'
		);
		newwindow.document.getElementById('postlink').style.display = 'none';
		newwindow.document.getElementById('QViagViagem').submit();	
	}

	function visualizar_pgr(codigo_pgr, racs_codigo) {
		var form = document.createElement('form');
		var form_id = ('formresult' + Math.random()).replace('.','');
		form.setAttribute('method', 'post');
		form.setAttribute('action', '/portal/pgpg_pgs/consulta_pgr/');
		form.setAttribute('target', form_id);
		field = document.createElement('input');
		field.setAttribute('name', 'data[TPgpgPg][pgpg_codigo]');
		field.setAttribute('value', codigo_pgr);
		field.setAttribute('type', 'hidden');
		form.appendChild(field);
		field = document.createElement('input');
		field.setAttribute('name', 'data[TRacsRegraAceiteSm][racs_codigo]');
		field.setAttribute('value', racs_codigo);
		field.setAttribute('type', 'hidden');
		form.appendChild(field);
		document.body.appendChild(form);
		var janela = window_sizes();
		window.open('', form_id, 'scrollbars=yes,menubar=no,height='+(janela.height-100)+',width='+(janela.width-80).toString()+',resizable=yes,toolbar=no,status=no');
		form.submit();
	}

	function consulta_checklist(refe_referencia,codigo_sm,codigo_cliente,data_inicial,data_final) {	
		var form = document.createElement('form');
		var form_id = ('formresult' + Math.random()).replace('.','');
		form.setAttribute('method', 'post');
		form.setAttribute('action', '/portal/viagens/checklist_analitico/popup/'+refe_referencia+'/1/');
		form.setAttribute('target', form_id);

		field = document.createElement('input');
		field.setAttribute('name', 'data[ChecklistViagem][viag_codigo_sm]');
		field.setAttribute('value', codigo_sm);
		field.setAttribute('type', 'hidden');
		form.appendChild(field);

		field = document.createElement('input');
		field.setAttribute('name', 'data[ChecklistViagem][codigo_cliente]');
		field.setAttribute('value', codigo_cliente);
		field.setAttribute('type', 'hidden');
		form.appendChild(field);

		field = document.createElement('input');
		field.setAttribute('name', 'data[ChecklistViagem][data_inicial]');
		field.setAttribute('value', data_inicial);
		field.setAttribute('type', 'hidden');
		form.appendChild(field);

		field = document.createElement('input');
		field.setAttribute('name', 'data[ChecklistViagem][consulta_sm]');
		field.setAttribute('value', 1);
		field.setAttribute('type', 'hidden');
		form.appendChild(field);		

		document.body.appendChild(form);
		var janela = window_sizes();
		window.open('', form_id, 'scrollbars=yes,menubar=no,height='+(janela.height-100)+',width='+(janela.width-80).toString()+',resizable=yes,toolbar=no,status=no');
		form.submit();
	}

", false);
?>
