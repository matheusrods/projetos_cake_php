<?php echo $this->BForm->create('TRefeReferencia', array('url' => array('controller' => 'Referencias','action' => 'incluir_configuracao_origem_destino')));?>
<div class='row-fluid inline'>
	<div id="cliente" class='well'>
		<strong>Código: </strong><?=$cliente['Cliente']['codigo'] ?>
		<strong>Cliente: </strong><?=$cliente['Cliente']['razao_social'] ?>
	</div>
</div>
<div class="row-fluid inline">
    <?php echo $this->BForm->hidden('codigo_cliente'); ?>
	<?php echo $this->Buonny->input_referencia($this, '#TRefeReferenciaCodigoCliente', 'TRefeReferencia', 'refe_codigo_origem',FALSE,'Alvo Origem',TRUE); ?>
    <?php echo $this->Buonny->input_referencia($this, '#TRefeReferenciaCodigoCliente', 'TRefeReferencia', 'refe_codigo_destino',FALSE,'Alvo Destino',TRUE); ?>
</div>
<div class="form-actions" style="clear:both;">
	<?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
	<?php echo $html->link('Voltar',array('controller' => 'Referencias', 'action' => 'configurar_origem_destino'), array('class' => 'btn')) ;?>
</div>
<?php echo $this->BForm->end(); ?>
<?php echo $this->Javascript->codeBlock("
	jQuery(document).ready(function(){
		setup_mascaras();
		carrega_listagem();
		$('#ReferenciasRefeCodigoDestino').change(function(){
			if( $('#ReferenciasRefeCodigoDestino').val() ==  $('#TRefeReferenciaRefeCodigoOrigem').val() ){
				$('#ReferenciasRefeCodigoDestino').val('');
				$('#ReferenciasRefeCodigoDestinoVisual').attr('value', ''); 
			}
		});
        function carrega_listagem(){
            var div = jQuery('div.lista');
            bloquearDiv(div);
            div.load(baseUrl + 'referencias/listagem_configuracao_origem_destino/1' + Math.random());
        }

	});", false);?>
<div class='lista'></div>