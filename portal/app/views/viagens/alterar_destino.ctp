
<?php echo $this->BForm->create('TViagViagem', array('type' => 'post','url' => array('controller' => 'Viagens','action' => 'alterar_destino',$cliente['Cliente']['codigo'],$this->data['TViagViagem']['viag_codigo'])));?>
	<div class='row-fluid inline'>
		<div id="cliente" class='well'>
			<strong>Código: </strong><?= $cliente['Cliente']['codigo'] ?>
			<strong>Cliente: </strong><?= $cliente['Cliente']['razao_social'] ?>
		</div>

		<div id="destino" class='well'>
			<div class="row-fluid inline" >
				<?php echo $this->BForm->hidden('viag_codigo') ?>
				<?php echo $this->BForm->input('viag_codigo_sm', array('class' => 'input-small', 'label' => 'SM', 'readonly' => true)) ?>
				<?php echo $this->BForm->input('viag_previsao_inicio', array('class' => 'input-medium', 'label' => 'Previsão Viagem', 'readonly' => true, 'type' => 'text')) ?>
			</div>
			<div class="row-fluid inline" >
				<?php echo $this->BForm->input('TRefeReferencia.refe_descricao', array('class' => 'input-xlarge', 'label' => 'Destino', 'readonly' => true)) ?>
			</div>
		</div>
	</div>
	
	<div id="hidden" class="row-fluid inline">
		<?php echo $this->BForm->hidden('TVestViagemEstatus.vest_estatus') ?>
		<?php echo $this->BForm->hidden('viag_data_fim') ?>
		<?php echo $this->BForm->hidden('viag_tran_pess_oras_codigo') ?>
		<?php echo $this->BForm->hidden('viag_emba_pjur_pess_oras_codigo') ?>
		<?php echo $this->BForm->hidden('ClienteTran.codigo',array('value' => $tran_cliente['Cliente']['codigo'])) ?>
		<?php echo $this->BForm->hidden('CleinteEmba.codigo',array('value' => $emba_cliente['Cliente']['codigo'])) ?>
		<?php echo $this->Buonny->input_referencia($this, '#ClienteTranCodigo', 'TVlocViagemLocal', 'vloc_refe_codigo', false, 'Novo Destino', true, true, '#CleinteEmbaCodigo') ?>
		<?php echo $this->BForm->hidden('TVlocViagemLocal.vloc_codigo') ?>

		<div class="control-group input text">
			<?php echo $this->BForm->input("TVlevViagemLocalEvento.vlev_previsao_data", array('label' => 'Previsão Chegada', 'class' => 'data input-small')) ?>
			<?php echo $this->BForm->input("TVlevViagemLocalEvento.vlev_previsao_hora", array('label' => 'Hora', 'class' => 'hora input-mini')) ?>
			<?php echo $this->BForm->error('TVlevViagemLocalEvento.data_error', null, array('style'=>"color:#b94a48; clear:both; margin-top: 60px; position: absolute;")) ?>		
		</div>
	</div>
	

	<?php if($menssagem): ?>
	<div class="form-actions alert-error veiculo-error" >
		<h5>Erros:</h5>
		<?php echo $menssagem ?>
	</div>
	<?php endif; ?>

<div class="form-actions">
	  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-success')); ?>
	  <?php echo $html->link('Voltar', 'itinerarios', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
</div>

<?php echo $this->BForm->end(); ?>
<?php echo $this->Javascript->codeBlock('
	jQuery(document).ready(function(){
		setup_datepicker();
		setup_time();
	});', false);
?>