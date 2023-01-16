<?php echo $this->BForm->create('TViagViagem', array('autocomplete' => 'off', 'url' => array('controller' => 'viagens', 'action' => 'altera_sm_para_escolta'))) ?>
	<div class="row-fluid inline">
		<?php echo $this->BForm->input('viag_codigo_sm', array('label' => false, 'placeholder' => 'Código SM', 'class' => 'input-small just-number', 'type' => 'text')); ?>
	</div>
	<?php echo $this->BForm->submit('Consultar', array('div' => false, 'class' => 'btn')); ?>
	<?php echo $this->BForm->end();?>
	<?php echo $this->Javascript->codeBlock('
	$(document).ready(function() {
		setup_mascaras();
	});
'); 
?>

<?php if(isset($dadosSm)){ ?>
<?php echo $this->BForm->create('TVescViagemEscolta', array('autocomplete' => 'off', 'url' => array('controller' => 'viagens', 'action' => 'altera_sm_para_escolta'))) ?>
	<div class="row-fluid inline">
		<?php echo $this->BForm->input('TViagViagem.viag_codigo_sm', array('label' => 'SM', 'readonly' => true,  'class' => 'input-small')); ?>
		<?php echo $this->BForm->input('TTveiTipoVeiculo.tvei_descricao', array('value' => $dadosSm['TTveiTipoVeiculo']['tvei_descricao'], 'label' => 'Tipo do Veículo', 'readonly' => true, 'class' => 'input-small')); ?>
		<div class='control-group input'>
			<label>Placa</label>
			<?php
				if( !Comum::isVeiculo($dadosSm['TVeicVeiculo']['veic_placa'])) {
					echo "REMONTA";
				}else
				{ 
					echo $this->BForm->input('TVeicVeiculo.veic_placa', array('value' => $dadosSm['TVeicVeiculo']['veic_placa'], 'readonly' => true, 'label' => false, 'class' => 'input-small'));
				}
			?>
		</div>
	</div>
	<div class="tab-content">
		<div class="tab-pane active" id="gerais">
			<div class="row-fluid inline">
				<?php echo $this->BForm->input('TPjurEmbarcador.codigo_cliente', array('value' => $dadosSm['TPjurEmbarcador']['codigo_cliente'], 'type' => 'text', 'label' => 'Embarcador', 'readonly' => true, 'class' => 'input-small')); ?>
				<?php echo $this->BForm->input('TPjurEmbarcador.pjur_razao_social', array('value' => $dadosSm['TPjurEmbarcador']['pjur_razao_social'], 'label' => '&nbsp', 'readonly' => true, 'class' => 'input-xxlarge')); ?>
			</div>
			<div class="row-fluid inline">
				<?php echo $this->BForm->input('TPjurTransportador.codigo_cliente', array('value' => $dadosSm['TPjurTransportador']['codigo_cliente'], 'type' => 'text','label' => 'Transportador', 'readonly' => true, 'class' => 'input-small')); ?>
				<?php echo $this->BForm->input('TPjurTransportador.pjur_razao_social', array('value' => $dadosSm['TPjurTransportador']['pjur_razao_social'], 'label' => '&nbsp', 'readonly' => true, 'class' => 'input-xxlarge')); ?>
			</div>
		</div>
	</div>
	<h4>Escolta</h4>

<div class='actionbar-right'>
	<?php echo $this->Html->link('Incluir', array('action' => 'adicionar_escolta', $dadosSm['TViagViagem']['viag_codigo'], rand()), array('onclick' => 'return open_dialog(this, "Adicionar Escolta", 560)', 'title' => 'Adicionar Escolta', 'class' => 'btn btn-success'));?>
</div>
<div class="viagem_escoltas">
</div>
<?php echo $this->Javascript->codeBlock('
	$(document).ready(function() {
		setup_mascaras();
		atualizaViagemEscoltas('.$dadosSm['TViagViagem']['viag_codigo'].');	
		
	});
	
');
?>
<?php }?>
