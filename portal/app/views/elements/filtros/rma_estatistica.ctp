<?php $filtrado = (!empty($this->data['TOrmaOcorrenciaRma']['codigo_embarcador']) || !empty($this->data['TOrmaOcorrenciaRma']['codigo_transportador']) || !empty($this->data['TOrmaOcorrenciaRma']['codigo_cliente']) ); ?>
<div class='well' style="<?echo ($filtrado ? 'display: none;' : '');?>">
	<h5><?= $this->Html->link(($filtrado ? 'Listagem Filtrada' : 'Definir Filtros'), 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show')) ?></h5>
    <div id='filtros'>
	    <?php echo $this->Bajax->form('TOrmaOcorrenciaRma', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'TOrmaOcorrenciaRma', 'element_name' => 'rma_estatistica'), 'divupdate' => '.form-procurar')) ?>
		<div class="row-fluid inline">
			<div id='DivPeriodoInicioFim'>
		    	<?php echo $this->Buonny->input_periodo($this, 'TOrmaOcorrenciaRma','data_inicial','data_final',false,60); ?>
		    	<?php echo $this->BForm->input('TOrmaOcorrenciaRma.tipo_data', array('type' => 'hidden', 'value'=>2, 'legend' => false, 'label' => false)) ?>
			    <?php echo $this->Buonny->input_embarcador_transportador($this, $embarcadores, $transportadores, 'codigo_cliente', 'Cliente', false, 'TOrmaOcorrenciaRma'); ?>
		    </div>
		</div>
		<div class="row-fluid inline">
			<span class="label label-info">Status da Viagem</span>
			 <span class='pull-right'>
		        <?= $html->link('Desmarcar todas', 'javascript:void(0)', array('onclick' => 'desmarcarTodos("status_viagem")')) ?>
		        <?= $html->link('Marcar todas', 'javascript:void(0)', array('onclick' => 'marcarTodos("status_viagem")')) ?>
		    </span>
		    <div id='status_viagem'>
				<?php echo $this->BForm->input('status_atual_viagem', array('label'=>false, 'options'=>$status_viagens_atual, 'multiple'=>'checkbox', 'class' => 'checkbox inline input-xlarge')); ?>
			</div>
		</div>
	    <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
	    <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
	    <?php echo $this->BForm->end() ?>
	</div>
</div>
<?php echo $this->Javascript->codeBlock("jQuery(document).ready(function() {
	verifica_sm();
	setup_mascaras();
	//$.placeholder.shim();
	var div = jQuery('div.lista');
	bloquearDiv(div);
	div.load(baseUrl + 'rma/analitico_estatistica_listagem/0/0'); 

	function verifica_sm() {
		var variavel = $('#TOrmaOcorrenciaRmaViagCodigoSm').val(); 
		if(variavel != '') {
			$('#DivPeriodoInicioFim').hide();
		}else {
			$('#DivPeriodoInicioFim').show();
		}
	}

	$(document).on('change','#TOrmaOcorrenciaRmaViagCodigoSm',function(){
	    	verifica_sm() ;
	});

	jQuery('#limpar-filtro').click(function(){
            bloquearDiv(jQuery('.form-procurar'));
            jQuery('.form-procurar').load(baseUrl + '/filtros/limpar/model:TOrmaOcorrenciaRma/element_name:rma_estatistica/' + Math.random())
        });
	jQuery('a#filtros').click(function(){
            jQuery('div#filtros').slideToggle('slow');
        });
})") ?>
<?php if (!empty($filtrado)): ?>
    <?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){jQuery("div#filtros").hide()})');?>
<?php endif; ?>