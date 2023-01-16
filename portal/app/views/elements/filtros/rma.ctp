<?php $filtrado = (!empty($this->data['TOrmaOcorrenciaRma']['codigo_embarcador']) || !empty($this->data['TOrmaOcorrenciaRma']['codigo_transportador'])); ?>
<div class='well'>
	<h5><?= $this->Html->link(($filtrado ? 'Listagem Filtrada' : 'Definir Filtros'), 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show')) ?></h5>
    <div id='filtros'>
	    <?php echo $this->Bajax->form('TOrmaOcorrenciaRma', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'TOrmaOcorrenciaRma', 'element_name' => 'rma'), 'divupdate' => '.form-procurar')) ?>
    <?php echo $this->element('rma/fields-filtros') ?>
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
	div.load(baseUrl + 'rma/analitico_listagem'); 

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
            jQuery('.form-procurar').load(baseUrl + '/filtros/limpar/model:TOrmaOcorrenciaRma/element_name:rma/' + Math.random())
        });
	jQuery('a#filtros').click(function(){
            jQuery('div#filtros').slideToggle('slow');
        });
})") ?>
<?php if (!empty($filtrado)): ?>
    <?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){jQuery("div#filtros").hide()})');?>
<?php endif; ?>