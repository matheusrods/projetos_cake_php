<?php echo $this->BForm->create('TViagViagem', array('type' => 'post','url' => array('controller' => 'Viagens','action' => 'alterar_rota2', $this->data['TViagViagem']['viag_codigo_sm'])));?>
	<div class='row-fluid inline'>
		<div id="destino" class='well'>
			<div class="row-fluid inline" >
				<?php echo $this->BForm->input('TViagViagem.viag_transportador', array('type' => 'hidden' ,'value' => $codigo_transportador_buonny)) ?>
				<?php echo $this->BForm->input('TViagViagem.viag_codigo', array('type' => 'hidden')) ?>
				<?php echo $this->BForm->input('TViagViagem.viag_embarcador', array('type' => 'hidden','value' => $codigo_embarcador_buonny)) ?>
				<?php echo $this->BForm->input('TViagViagem.viag_codigo_sm', array('class' => 'input-small', 'label' => 'SM', 'readonly' => true)) ?>
				<?php echo $this->BForm->input('TViagViagem.viag_previsao_inicio', array('class' => 'input-medium', 'label' => 'Previsão de Inicio', 'readonly' => true, 'type' => 'text')) ?>
				<?php echo $this->BForm->input('TViagViagem.viag_previsao_fim', array('class' => 'input-medium', 'label' => 'Previsão de fim', 'readonly' => true, 'type' => 'text')) ?>
				<?php echo $this->BForm->input('TViagViagem.viag_data_inicio', array('class' => 'input-medium', 'label' => 'Inicio Real', 'readonly' => true, 'type' => 'text')) ?>
				<?php echo $this->BForm->input('TViagViagem.viag_data_fim', array('class' => 'input-medium', 'label' => 'Fim Real', 'readonly' => true, 'type' => 'text')) ?>
			</div>
			<div class="row-fluid inline" >
				<?php echo $this->BForm->input('TRotaRota.rota_descricao', array('class' => 'input-medium', 'label' => 'Rota Atual', 'readonly' => true, 'type' => 'text')) ?>
				<?php echo $this->BForm->input('TViagViagem.ralo_ultima_data_alteracao', array('class' => 'input-medium', 'label' => 'Ultima alteração de rota', 'readonly' => true, 'type' => 'text', 'value' => $this->data[0]['ralo_data_cadastro'])) ?>
			</div>
				<h3> Itinerário </h3>
		    	<?php echo $this->element('viagens/origem_destino') ?>
				<?php echo $this->element('viagens/itinerario') ?>
		</div>
		<h3>Nova Rota</h3>
		<div> 
			<?php echo $this->Buonny->input_rota_emb_transp($this, "#TViagViagemViagEmbarcador", "#TViagViagemViagTransportador", "TRotaRota", "rota_codigo", false, 'Rota', false, true); ?>
		</div>
	</div>
<div class="form-actions">
	  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-success')); ?>
	  <?php echo $html->link('Voltar', 'itinerarios', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
</div>
<?php echo $this->BForm->end(); ?>