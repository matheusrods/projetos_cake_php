<div class="dados-carga">            
	<legend>Carga</legend>
	<div class="actionbar-right">
		<?= $this->BForm->button('Limpar', array('div' => false, 'class' => 'btn btn-info btn-limpar', 'type'=>'button')); ?>
	</div>
	<div class="row-fluid inline">
		<?php echo $this->BForm->input("FichaScorecard.codigo_carga_tipo", array('label' => 'Tipo', 'class' => 'input-large', 'empty' => 'Tipo', 'options'=>$carga_tipos)) ?>
		<?php echo $this->BForm->input("FichaScorecard.codigo_carga_valor", array('label' => 'Valor', 'class' => 'input-large', 'empty' => 'Valor', 'options'=>$carga_valores)) ?>
	</div>
	<h5>Origem</h5>
	<div>
		<div class="row-fluid inline">	
		<?php echo $this->BForm->input('FichaScorecard.cidade_origem', array('class' => 'input-large ui-autocomplete-input', 'placeholder' => 'Informe uma Cidade', 'empty' => 'Cidade', 'label' => 'Cidade', 'for' =>'FichaScorecardCodigoEnderecoCidadeCargaOrigem')) ?>
		<?php echo $this->BForm->input('FichaScorecard.codigo_endereco_cidade_carga_origem',    array('class' => 'input-large', 'id'=>'codigo_cidade', 'type' => 'hidden', 'empty' => 'Cidade', 'label' => false)) ?>
		</div>
	</div>
	<h5>Destino</h5>
	<div>
		<div class="row-fluid inline">
			<?php echo $this->BForm->input('FichaScorecard.cidade_destino', array('class' => 'input-large ui-autocomplete-input', 'placeholder' => 'Informe uma Cidade', 'empty' => 'Cidade', 'label' => 'Cidade', 'for' =>'FichaScorecardCodigoEnderecoCidadeCargaDestino')) ?>
	        <?php echo $this->BForm->input('FichaScorecard.codigo_endereco_cidade_carga_destino',    array('class' => 'input-large', 'id'=>'codigo_cidade', 'type' => 'hidden', 'empty' => 'Cidade', 'label' => false)) ?>
		</div>
	</div>
</div>
<?php echo $this->Javascript->codeBlock('
$(document).ready(function() {
	$(".dados-carga :button").click(function(){
		$(".dados-carga :input").not(":button, :submit, :reset, :hidden").val("");			
	});
});', false);?>