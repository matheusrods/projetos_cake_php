<?php if (empty($this->data)): ?>

	<?php echo $this->BForm->create('Recebsm', array('autocomplete' => 'off', 'url' => array('controller' => 'solicitacoes_monitoramento', 'action' => 'consulta_sm', $nova_janela))) ?>
		<div class="row-fluid inline">
			<?php echo $this->BForm->input('codigo_sm', array('label' => false, 'placeholder' => 'Código SM','class' => 'input-small', 'type' => 'text', 'maxlength' => 12 )); ?>
		</div>
		<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
	<?php echo $this->BForm->end();?>
<?php else: ?>
	
	<div id="print">
        <a style="display:block; float:right;" href="#" onclick="consulta_sm_impressao('<?php echo $this->data['Recebsm']['SM'] ?>')" title="Imprimir Solicitação de Monitoramento"><i class="icon-print icon-black"></i></a>
    </div>

	<ul class="nav nav-tabs">
	  <li class="active"><a href="#gerais" data-toggle="tab">Dados Gerais</a></li>
	  <li><a href="#veiculo" data-toggle="tab">Informações do Veículo</a></li>
	  <li><a href="#escolta" data-toggle="tab">Escolta</a></li>
	  <li><a href="#itinerario" data-toggle="tab" id="a-itinerario">Itinerário</a></li>
	  <li><a href="#observacoes" data-toggle="tab">Observações</a></li>
	</ul>
    <div class="row-fluid inline">
		<span class='span2'><span class="badge-empty badge <?php echo $status_sm ?>" title="<?php echo $msg_status_sm ?>"></span>&nbsp;Status</span>
		<span class='span2'><span class="badge-empty badge <?php echo $status_guardian ?>" title="<?php echo $msg_status_guardian ?>"></span>&nbsp;Guardian</span>
		<?php echo $this->Html->link('Detalhar Itinerário no Mapa', array('controller'=>'solicitacoes_monitoramento', 'action'=>'itinerario_mapa', $this->data['Recebsm']['SM']), array('onclick'=>'return open_popup(this);')); ?>
    </div>
	<div class="row-fluid inline">
            <?= $this->BForm->input('Recebsm.sm', array('label' => 'SM', 'readonly' => true, 'value' => $this->data['Recebsm']['SM'], 'class' => 'input-small')); ?>
            <?= $this->BForm->input('ClientEmpresa.codigo_cliente', array('label' => 'Cliente', 'readonly' => true, 'value' => $this->data['ClientEmpresa']['codigo_cliente'], 'title' => 'Código Monitora: '.$this->data['Recebsm']['Cliente'], 'class' => 'input-small')); ?>
            <?= $this->BForm->input('ClientEmpresa.raz_social', array('label' => '&nbsp', 'readonly' => true, 'value' => $this->data['ClientEmpresa']['Raz_Social'], 'class' => 'input-xxlarge')); ?>
            <div class='control-group input'>
            	<label>Placa</label>
            	<?= $this->Buonny->placa($this->data['Recebsm']['Placa'], $this->data[0]['dta_inc'], (empty($viagem['data_final_real']) ? Date('d/m/Y H:i:s') : $viagem['data_final_real']) ) ?>
	        </div>
	</div>
	<div class="tab-content">
		<div class="tab-pane active" id="gerais">
			<div class="row-fluid inline">
				<?= $this->BForm->input('ClientEmpresaEmbarcador.codigo_cliente', array('label' => 'Embarcador', 'readonly' => true, 'value' => $this->data['ClientEmpresaEmbarcador']['codigo_cliente'], 'title' => 'Código Monitora: '.$this->data['Recebsm']['cliente_embarcador'], 'class' => 'input-small')); ?>
				<?= $this->BForm->input('ClientEmpresaEmbarcador.raz_social', array('label' => '&nbsp', 'readonly' => true, 'value' => $this->data['ClientEmpresaEmbarcador']['Raz_Social'], 'class' => 'input-xxlarge')); ?>
				<?= $this->BForm->input('Recebsm.Produtor', array('label' => 'Produtor/Embarcador', 'readonly' => true, 'value' => $this->data['Recebsm']['Produtor'], 'class' => 'input-large')); ?>
			</div>
			<div class="row-fluid inline">
				<?= $this->BForm->input('ClientEmpresaTransportador.codigo_cliente', array('label' => 'Transportadora', 'readonly' => true, 'value' => $this->data['ClientEmpresaTransportador']['codigo_cliente'], 'title' => 'Código Monitora: '.$this->data['Recebsm']['cliente_transportador'], 'class' => 'input-small')); ?>
				<?= $this->BForm->input('ClientEmpresaTransportador.raz_social', array('label' => '&nbsp', 'readonly' => true, 'value' => $this->data['ClientEmpresaTransportador']['Raz_Social'], 'class' => 'input-xxlarge')); ?>
			</div>
			<div class="row-fluid inline">
				<?= $this->BForm->input('Recebsm.dta_receb', array('label' => 'Cadastro SM', 'readonly' => true, 'value' => $this->data['0']['dta_receb'], 'class' => 'input-small')); ?>
				<?= $this->BForm->input('Recebsm.hora_receb', array('label' => '&nbsp', 'readonly' => true, 'value' => $this->data['Recebsm']['Hora_Receb'], 'class' => 'input-mini')); ?>
				<?= $this->BForm->input('Recebsm.solicitante', array('label' => 'Solicitante', 'readonly' => true, 'value' => $this->data['Recebsm']['Solicitante'], 'class' => 'input-medium')); ?>
				<?= $this->BForm->input('Recebsm.tel_solicitante', array('label' => 'Telefone', 'readonly' => true, 'value' => $this->data['Recebsm']['Tel_Solicitante'], 'class' => 'input-small')); ?>
				<?= $this->BForm->input('Recebsm.valor', array('label' => 'Carga(R$)', 'readonly' => true, 'value' => $this->Buonny->moeda($this->data['Recebsm']['ValSM']), 'class' => 'input-small numeric')); ?>
				<?= $this->BForm->input('Recebsm.pedido_cliente', array('label' => 'Pedido Cliente', 'readonly' => true, 'value' => $this->data['Recebsm']['pedido_cliente'], 'class' => 'input-small')); ?>
			</div>
			<div class="row-fluid inline">
				<?= $this->BForm->input('Recebsm.dta_inc', array('label' => 'Previsão Início', 'readonly' => true, 'value' => $this->data['0']['dta_inc'], 'class' => 'input-small')); ?>
				<?= $this->BForm->input('Recebsm.hora_inc', array('label' => '&nbsp', 'readonly' => true, 'value' => $this->data['Recebsm']['Hora_Inc'], 'class' => 'input-mini')); ?>
				<?= $this->BForm->input('Recebsm.dta_fim', array('label' => 'Previsão Fim', 'readonly' => true, 'value' => $this->data['0']['dta_fim'], 'class' => 'input-small')); ?>
				<?= $this->BForm->input('Recebsm.hora_fim', array('label' => '&nbsp', 'readonly' => true, 'value' => $this->data['Recebsm']['Hora_Fim'], 'class' => 'input-mini')); ?>
				<?php if (isset($viagem)): ?>
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
					<?= $this->BForm->input('TViagViagem.viag_data_inicio', array('label' => 'Início Real'.$tipoIni, 'readonly' => true, 'value' => $viagem['data_inicio_real'], 'class' => 'input-medium', 'type' => 'text', 'title' => $viagem['origem_real'])); ?>
					<?= $this->BForm->input('TViagViagem.viag_data_fim', array('label' => 'Fim Real'.$tipoFim, 'readonly' => true, 'value' => $viagem['data_final_real'], 'class' => 'input-medium', 'type' => 'text', 'title' => $viagem['origem_real'])); ?>
				<?php endif ?>
				<?= $this->BForm->input('Recebsm.temperatura', array('label' => 'Temperatura', 'readonly' => true, 'value' => $this->data['Recebsm']['Temperatura'], 'class' => 'input-mini numeric')); ?>
				<div class='input'><label>&nbsp</label>a</div>
				<?= $this->BForm->input('Recebsm.temperatura2', array('label' => '&nbsp', 'readonly' => true, 'value' => $this->data['Recebsm']['Temperatura2'], 'class' => 'input-mini numeric')); ?>
			</div>
			<div class="row-fluid inline">
				<?= $this->BForm->input('CidadeOrigem.descricao', array('label' => 'Origem', 'readonly' => true, 'value' => $this->data['CidadeOrigem']['Descricao'], 'class' => 'input-large')); ?>
				<?= $this->BForm->input('CidadeOrigem.estado', array('label' => '&nbsp', 'readonly' => true, 'value' => $this->data['CidadeOrigem']['Estado'], 'class' => 'input-mini')); ?>
				<?= $this->BForm->input('CidadeDestino.descricao', array('label' => 'Destino', 'readonly' => true, 'value' => $this->data['CidadeDestino']['Descricao'], 'class' => 'input-large')); ?>
				<?= $this->BForm->input('CidadeDestino.estado', array('label' => '&nbsp', 'readonly' => true, 'value' => $this->data['CidadeDestino']['Estado'], 'class' => 'input-mini')); ?>
			</div>
			<h4>Origem Viagem</h4>
			<div class="row-fluid inline">
				<?= $this->BForm->input('MWebsm.origemviagem_empresa', array('label' => 'Empresa', 'readonly' => true, 'value' => $this->data['MWebsm']['origemviagem_empresa'], 'class' => 'input-medium')); ?>
				<?= $this->BForm->input('MWebsm.origemviagem_telefone', array('label' => 'Telefone', 'readonly' => true, 'value' => $this->data['MWebsm']['origemviagem_telefone'], 'class' => 'input-small')); ?>
				<?= $this->BForm->input('MWebsm.origemviagem_contato', array('label' => 'Contato', 'readonly' => true, 'value' => $this->data['MWebsm']['origemviagem_contato'], 'class' => 'input-small')); ?>
			</div>
			<?php if (isset($authUsuario['Usuario']['codigo_uperfil']) && in_array($authUsuario['Usuario']['codigo_uperfil'], array(3, 20))): ?>
				<div class="row-fluid inline">
			 	<?php echo 'Sistema Origem: ' . $this->data['Recebsm']['sistema_origem'] ?>
			 	</div>
			<?php endif ?>
		</div>
	    <div class="tab-pane" id="veiculo">
	    	<h5>Motorista</h5>
	    	<div class="row-fluid inline">
	    		<?= $this->BForm->input('Motorista.nome', array('label' => 'Nome', 'readonly' => true, 'value' => $this->data['Motorista']['Nome'], 'class' => 'input-large')); ?>
	    		<?= $this->BForm->input('Motorista.cpf', array('label' => 'CPF', 'readonly' => true, 'value' => $this->data['Motorista']['CPF'], 'class' => 'input-small')); ?>
	    		<?= $this->BForm->input('Motorista.rg', array('label' => 'RG', 'readonly' => true, 'value' => $this->data['Motorista']['RG'], 'class' => 'input-small')); ?>
	    		<?= $this->BForm->input('Motorista.cnh_validade', array('label' => 'Vencimento CNH', 'readonly' => true, 'value' => $this->data['0']['cnh_validade'], 'class' => 'input-small')); ?>
	    		<?= $this->BForm->input('Motorista.telefone', array('label' => 'Telefone', 'readonly' => true, 'value' => $this->data['Motorista']['Telefone'], 'class' => 'input-small')); ?>
	    		<?= $this->BForm->input('Motorista.celular', array('label' => 'Celular', 'readonly' => true, 'value' => $this->data['Motorista']['Celular'], 'class' => 'input-small')); ?>
	    	</div>
	    	<div class="row-fluid inline">
	    		<?= $this->BForm->input('Recebsm.nome_gerenciadora', array('label' => 'Gerenciadora', 'readonly' => true, 'value' => $this->data['Recebsm']['NOME_GERENCIADORA'], 'class' => 'input-xlarge')); ?>
	    		<?= $this->BForm->input('Recebsm.n_liberacao', array('label' => 'Liberação', 'readonly' => true, 'value' => $this->data['Recebsm']['N_LIBERACAO'], 'class' => 'input-medium')); ?>
	    	</div>
	    	<h5>Cavalo</h5>
	    	<div class="row-fluid inline">
	    		<?= $this->BForm->input('Recebsm.placa', array('label' => 'Placa', 'readonly' => true, 'value' => $this->data['Recebsm']['Placa'], 'class' => 'input-small')); ?>
	    		<?= $this->BForm->input('MCaminhao.fabricante', array('label' => 'Fabricante', 'readonly' => true, 'value' => $this->data['MCaminhao']['Fabricante'], 'class' => 'input-small')); ?>
	    		<?= $this->BForm->input('MCaminhao.modelo', array('label' => 'Modelo', 'readonly' => true, 'value' => $this->data['MCaminhao']['Modelo'], 'class' => 'input-small')); ?>
	    		<?= $this->BForm->input('MCaminhao.ano_fab', array('label' => 'Ano Fabricação', 'readonly' => true, 'value' => $this->data['MCaminhao']['Ano_Fab'], 'class' => 'input-small numeric')); ?>
	    		<?= $this->BForm->input('MCaminhao.ano_mod', array('label' => 'Ano Modelo', 'readonly' => true, 'value' => $this->data['MCaminhao']['Ano_Modelo'], 'class' => 'input-small numeric')); ?>
	    		<?= $this->BForm->input('MCaminhao.cor', array('label' => 'Cor', 'readonly' => true, 'value' => $this->data['MCaminhao']['Cor'], 'class' => 'input-small')); ?>
	    	</div>
			<div class="row-fluid inline">
	    		<?= $this->BForm->input('Equipamento.Descricao', array('label' => 'Tecnologia', 'readonly' => true, 'value' => $this->data['MCaminhao']['Tipo_Equip'], 'class' => 'input-medium')); ?>
	    		<?= $this->BForm->input('MCaminhao.chassi', array('label' => 'Chassi', 'readonly' => true, 'value' => $this->data['MCaminhao']['Chassi'], 'class' => 'input-medium')); ?>
	    		<?= $this->BForm->input('MMonTipocavalocarreta.tip_descricao', array('label' => 'Tipo', 'readonly' => true, 'value' => $this->data['MMonTipocavalocarreta']['TIP_Descricao'], 'class' => 'input-small')); ?>
	    		
	    	</div>
	    	<h5>Carreta</h5>
	    	<div class="row-fluid inline">
	    		<?= $this->BForm->input('Recebsm.placa_carreta', array('label' => 'Placa', 'readonly' => true, 'value' => $this->data['Recebsm']['Placa_Carreta'], 'class' => 'input-small')); ?>
	    		<?= $this->BForm->input('MCarreta.ano', array('label' => 'Ano', 'readonly' => true, 'value' => $this->data['MCarreta']['Ano'], 'class' => 'input-small numeric')); ?>
	    		<?= $this->BForm->input('CidadeEmplacamentoCarreta.descricao', array('label' => 'Local Emplacamento', 'readonly' => true, 'value' => $this->data['CidadeEmplacamentoCarreta']['Descricao'], 'class' => 'input-large')); ?>
	    		<?= $this->BForm->input('CidadeEmplacamentoCarreta.estado', array('label' => '&nbsp', 'readonly' => true, 'value' => $this->data['CidadeEmplacamentoCarreta']['Estado'], 'class' => 'input-mini')); ?>
	    		<?= $this->BForm->input('MMonTipocarroceria.tca_descricao', array('label' => 'Tipo', 'readonly' => true, 'value' => $this->data['MMonTipocarroceria']['TCA_Descricao'], 'class' => 'input-small')); ?>
	    	</div>
	    </div>
	    <div class="tab-pane" id="escolta">
	    	<div class="row-fluid inline">
		    	<?= $this->BForm->input('Recebsm.escolta_empresa1', array('label' => 'Empresa', 'readonly' => true, 'value' => $this->data['Recebsm']['ESCOLTA_EMPRESA1'], 'class' => 'input-large')); ?>
		    	<?= $this->BForm->input('Recebsm.escolta_contato1', array('label' => 'Contato', 'readonly' => true, 'value' => $this->data['Recebsm']['ESCOLTA_CONTATO1'], 'class' => 'input-small')); ?>
		    	<?= $this->BForm->input('Recebsm.escolta_telefone1', array('label' => 'Telefone', 'readonly' => true, 'value' => $this->data['Recebsm']['ESCOLTA_TELEFONE1'], 'class' => 'input-small')); ?>
		    </div>
	    	<div class="row-fluid inline">
	    		<span class="span6">
			    	<?= $this->BForm->input('Recebsm.escolta_equipe1', array('label' => 'Equipe 1', 'readonly' => true, 'value' => $this->data['Recebsm']['ESCOLTA_EQUIPE1'], 'class' => 'input-small')); ?>
			    	<?= $this->BForm->input('Recebsm.escolta_telefone_equipe1', array('label' => 'Telefone', 'readonly' => true, 'value' => $this->data['Recebsm']['ESCOLTA_TELEFONE_EQUIPE1'], 'class' => 'input-small')); ?>
			    	<?= $this->BForm->input('Recebsm.escolta_placa_equipe1', array('label' => 'Placa', 'readonly' => true, 'value' => $this->data['Recebsm']['ESCOLTA_PLACA_EQUIPE1'], 'class' => 'input-small')); ?>
			    </span>
			    <span class="span6">
			    	<?= $this->BForm->input('Recebsm.escolta_equipe2', array('label' => 'Equipe 2', 'readonly' => true, 'value' => $this->data['Recebsm']['ESCOLTA_EQUIPE2'], 'class' => 'input-small')); ?>
			    	<?= $this->BForm->input('Recebsm.escolta_telefone_equipe2', array('label' => 'Telefone', 'readonly' => true, 'value' => $this->data['Recebsm']['ESCOLTA_TELEFONE_EQUIPE2'], 'class' => 'input-small')); ?>
			    	<?= $this->BForm->input('Recebsm.escolta_placa_equipe2', array('label' => 'Placa', 'readonly' => true, 'value' => $this->data['Recebsm']['ESCOLTA_PLACA_EQUIPE2'], 'class' => 'input-small')); ?>
			    </span>
		    </div>
		    <div class="row-fluid inline">
		    	<span class="span6">
			    	<?= $this->BForm->input('Recebsm.escolta_equipe3', array('label' => 'Equipe 3', 'readonly' => true, 'value' => $this->data['Recebsm']['ESCOLTA_EQUIPE3'], 'class' => 'input-small')); ?>
			    	<?= $this->BForm->input('Recebsm.escolta_telefone_equipe3', array('label' => 'Telefone', 'readonly' => true, 'value' => $this->data['Recebsm']['ESCOLTA_TELEFONE_EQUIPE3'], 'class' => 'input-small')); ?>
			    	<?= $this->BForm->input('Recebsm.escolta_placa_equipe3', array('label' => 'Placa', 'readonly' => true, 'value' => $this->data['Recebsm']['ESCOLTA_PLACA_EQUIPE3'], 'class' => 'input-small')); ?>
			    </span>
			    <span class="span6">
			    	<?= $this->BForm->input('Recebsm.escolta_equipe4', array('label' => 'Equipe 4', 'readonly' => true, 'value' => $this->data['Recebsm']['ESCOLTA_EQUIPE4'], 'class' => 'input-small')); ?>
			    	<?= $this->BForm->input('Recebsm.escolta_telefone_equipe4', array('label' => 'Telefone', 'readonly' => true, 'value' => $this->data['Recebsm']['ESCOLTA_TELEFONE_EQUIPE4'], 'class' => 'input-small')); ?>
			    	<?= $this->BForm->input('Recebsm.escolta_placa_equipe4', array('label' => 'Placa', 'readonly' => true, 'value' => $this->data['Recebsm']['ESCOLTA_PLACA_EQUIPE4'], 'class' => 'input-small')); ?>
			    </span>
		    </div>
		    <div class="row-fluid inline">
		    	<?= $this->BForm->input('Recebsm.escolta1', array('label' => 'Empresa', 'readonly' => true, 'value' => $this->data['Recebsm']['ESCOLTA1'], 'class' => 'input-large')); ?>
		    </div>
	    	<div class="row-fluid inline">
	    		<span class="span6">
			    	<?= $this->BForm->input('Recebsm.escolta1_equipe1', array('label' => 'Equipe 1', 'readonly' => true, 'value' => $this->data['Recebsm']['ESCOLTA1_EQUIPE1'], 'class' => 'input-small')); ?>
			    	<?= $this->BForm->input('Recebsm.escolta1_telefone_equipe1', array('label' => 'Telefone', 'readonly' => true, 'value' => $this->data['Recebsm']['ESCOLTA1_TELEFONE_EQUIPE1'], 'class' => 'input-small')); ?>
			    	<?= $this->BForm->input('Recebsm.escolta1_placa_equipe1', array('label' => 'Placa', 'readonly' => true, 'value' => $this->data['Recebsm']['ESCOLTA1_PLACA_EQUIPE1'], 'class' => 'input-small')); ?>
			    </span>
			    <span class="span6">
			    	<?= $this->BForm->input('Recebsm.escolta1_equipe2', array('label' => 'Equipe 2', 'readonly' => true, 'value' => $this->data['Recebsm']['ESCOLTA1_EQUIPE2'], 'class' => 'input-small')); ?>
			    	<?= $this->BForm->input('Recebsm.escolta1_telefone_equipe2', array('label' => 'Telefone', 'readonly' => true, 'value' => $this->data['Recebsm']['ESCOLTA1_TELEFONE_EQUIPE2'], 'class' => 'input-small')); ?>
			    	<?= $this->BForm->input('Recebsm.escolta1_placa_equipe2', array('label' => 'Placa', 'readonly' => true, 'value' => $this->data['Recebsm']['ESCOLTA1_PLACA_EQUIPE2'], 'class' => 'input-small')); ?>
			    </span>
		    </div>
		    <div class="row-fluid inline">
		    	<span class="span6">
			    	<?= $this->BForm->input('Recebsm.escolta1_equipe3', array('label' => 'Equipe 3', 'readonly' => true, 'value' => $this->data['Recebsm']['ESCOLTA1_EQUIPE3'], 'class' => 'input-small')); ?>
			    	<?= $this->BForm->input('Recebsm.escolta1_telefone_equipe3', array('label' => 'Telefone', 'readonly' => true, 'value' => $this->data['Recebsm']['ESCOLTA1_TELEFONE_EQUIPE3'], 'class' => 'input-small')); ?>
			    	<?= $this->BForm->input('Recebsm.escolta1_placa_equipe3', array('label' => 'Placa', 'readonly' => true, 'value' => $this->data['Recebsm']['ESCOLTA1_PLACA_EQUIPE3'], 'class' => 'input-small')); ?>
			    </span>
			    <span class="span6">
			    	<?= $this->BForm->input('Recebsm.escolta1_equipe4', array('label' => 'Equipe 4', 'readonly' => true, 'value' => $this->data['Recebsm']['ESCOLTA1_EQUIPE4'], 'class' => 'input-small')); ?>
			    	<?= $this->BForm->input('Recebsm.escolta1_telefone_equipe4', array('label' => 'Telefone', 'readonly' => true, 'value' => $this->data['Recebsm']['ESCOLTA1_TELEFONE_EQUIPE4'], 'class' => 'input-small')); ?>
			    	<?= $this->BForm->input('Recebsm.escolta1_placa_equipe4', array('label' => 'Placa', 'readonly' => true, 'value' => $this->data['Recebsm']['ESCOLTA1_PLACA_EQUIPE4'], 'class' => 'input-small')); ?>
			    </span>
		    </div>
	    </div>
	    <div class="tab-pane" id="itinerario">
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
	    	<?php echo $this->element('solicitacoes_monitoramento/itinerario') ?>
	    </div>
	    <div class="tab-pane" id="observacoes">
	    	<?= $this->BForm->input('Recebsm.observacao', array('label' => 'Observações', 'readonly' => true, 'value' => $this->data['Recebsm']['OBSERVACAO'], 'class' => 'input-xxlarge', 'type' => 'textarea')); ?>
	    </div>
	</div>
        <?php if(!isset($nova_janela)): ?>
            <div class="form-actions">
                <?= $html->link('Voltar', array('action' => 'consulta_sm'), array('class' => 'btn')); ?>
            </div>    
        <?php endif; ?>
<?php endif ?>
<?php  echo $this->BForm->end() ?>
<?= $this->Buonny->link_js('estatisticas') ?>
<?php echo $this->Buonny->link_css('jquery.tablescroll'); ?>
<?php echo $this->Buonny->link_js('jquery.tablescroll'); ?>
<?php echo $this->Javascript->codeBlock("
	jQuery(document).ready(function(){
		$('#a-itinerario').on('shown', function (e) {
	        $('.horizontal-scroll').tableScroll({width:2500, height:200}); 
		});
	});

	function consulta_sm_impressao(codigo_viagem) {
		
		var newwindow = window.open('/portal/solicitacoes_monitoramento/consulta_sm/newwindow/print','_blank','scrollbars=yes,top=0,left=0,width=1000,height=800');								
		newwindow.document.write(
			'<div id=\"postlink\"><form accept-charset=\"utf-8\" method=\"post\" id=\"TViagViagem\" action=\"/portal/solicitacoes_monitoramento/consulta_sm/newwindow/print\"><input type=\"text\" id=\"TViagViagemCodigo\" value='+'\"'+codigo_viagem+'\"'+' name=\"data[Recebsm][codigo_sm]\"></form></div>'
		);
		newwindow.document.getElementById('postlink').style.display = 'none';
		newwindow.document.getElementById('TViagViagem').submit();	
	}
", false);
?>