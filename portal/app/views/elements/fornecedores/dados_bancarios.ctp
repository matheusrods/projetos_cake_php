<div class="well">
    <div class="row-fluid inline">       
        <?php echo $this->BForm->input('Fornecedor.numero_banco', array('label' => 'Banco', 'class' => 'input-xlarge uf', 'empty' => 'Selecione', 'options' => $bancos)) ?>
        <?php echo $this->BForm->input('Fornecedor.agencia', array('class' => 'input-medium', 'label' => 'Agência')); ?>
        <?php echo $this->BForm->input('Fornecedor.numero_conta', array('class' => 'input-medium', 'label' => 'Número da Conta')); ?>
        <?php echo $this->BForm->input('Fornecedor.tipo_conta', array('legend' => false, 'options' => array('1' => 'Conta Corrente', '0' => 'Conta Poupança'), 'type' => 'radio','before' => '<div class="fornecedor_radio_checkbox"><span>Tipo de Conta</span>','after' => '</div>', 'hiddenField' => false,)) ?>
    </div>
    <div class="row-fluid inline">
        <?php echo $this->BForm->input('Fornecedor.favorecido', array('class' => 'input-xxlarge', 'label' => 'Favorecido')); ?>
         <?php echo $this->BForm->input('Fornecedor.cobranca_boleto', array('value' => $this->data['Fornecedor']['cobranca_boleto'], 'legend' => false, 'options' => array('1' => 'Sim', '0' => 'Não'), 'type' => 'radio','before' => '<div class="fornecedor_radio_checkbox"><span>Vou gerar boleto</span>','after' => '</div>', 'hiddenField' => false,)) ?>
    </div>
    <div class="row-fluid inline">
        <div class="span3 control-group" style="margin-left: 8px">
    		<label>Modalidade de pagamento</label>
        	<?php echo $this->BForm->input('Fornecedor.modalidade_pagamento', array('legend' => false, 'options' => array('1' => 'Pagamento Antecipado', '2' => 'Faturamento', '3' => 'Faturamento Diferenciado'), 'type' => 'radio', 'hiddenField' => false)) ?>
    	</div>
    	<div class="span3 control-group" style="margin-left: -52px; margin-top: 0;" id="dias1">
    		<label>Quantos dias?</label>
        	<?php echo $this->BForm->input('Fornecedor.faturamento_dias', array('value' => $this->data['Fornecedor']['faturamento_dias'], 'legend' => false, 'options' => array('30' => '30 dias', '45' => '45 dias', '60' => '60 dias'), 'type' => 'radio', 'hiddenField' => false)) ?>
    	</div>
    	<div class="span3 control-group" style="margin-left: -52px; margin-top: 0;" id="dias2">
    		<label>Quantos dias?</label>
        	<?php echo $this->BForm->input('Fornecedor.faturamento_dias', array('value' => $this->data['Fornecedor']['faturamento_dias'], 'legend' => false, 'options' => array('15' => '15 dias', '25' => '25 dias', null => 'Não definido'), 'type' => 'radio', 'hiddenField' => false)) ?>
    	</div>
    	<div class="span3 control-group" style="margin-left: -140px; margin-top: 0;"  id="detalhesFaturamento">
        	<?php echo $this->Form->input('Fornecedor.faturamento_detalhes', array('type' => 'textarea', 'class' => 'input-large', 'label' => 'Detalhes', 'style' => 'height: 60px; width: 220px; font-size: 11px;')); ?> 
     	</div>
    </div>
    <div id="pag1" class="row-fluid inline">
        <?php echo $this->BForm->input('Fornecedor.dia_pagamento', array('label' => 'Dia de pagamento', 'class' => 'input-medium', 'default' => '','empty' => 'Selecione o dia', 'options' => $dias_pagamento)); ?>
        <?php echo $this->Form->input('Fornecedor.observacao', array('type' => 'textarea', 'class' => 'input-large', 'label' => 'Observação', 'style' => 'height: 60px; width: 220px; font-size: 11px;')); ?>
    </div>
    <b id="titulogestor">Upload da autorização do gestor</b>
    <div id="pag2" class='row-fluid inline'>
        <?php echo $this->BForm->input('Fornecedor.caminho_arquivo', array('type'=>'file', 'label' => false)); ?>
        <?php echo $this->BForm->button('Limpar campo do anexo', array('type'=>'button', 'label' => 'Limpar', 'id' => 'LimparArquivoPrestador', 'class' => 'btn btn-anexos')); ?>
        <?php if(!empty($this->data['Fornecedor']['caminho_arquivo'])): ?>
            <a href="https://api.rhhealth.com.br<?php echo $this->data['Fornecedor']['caminho_arquivo']; ?>" target="_blank" class="icon-file btn-anexos visualiza_anexo" title='Visualizar Anexo'></a>
        <?php endif; ?>
    </div>
</div>
<script type="text/javascript">
	$(document).ready(function() {
        setup_mascaras();
        setup_datepicker();
        setup_time();
    });
    $("#LimparArquivoPrestador").click(function(){
        $("#FornecedorCaminhoArquivo").val("");                
    });
    $('.datepickerjs').datepicker({
        dateFormat: 'dd/mm/yy',
        showOn : 'button',
        buttonImage : baseUrl + 'img/calendar.gif',
        buttonImageOnly : true,
        buttonText : 'Escolha uma data',
        dayNames : ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sabado'],
        dayNamesShort : ['Dom','Seg','Ter','Qua','Qui','Sex','Sab'],
        dayNamesMin : ['D','S','T','Q','Q','S','S'],
        monthNames : ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
        monthNamesShort : ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
        onClose : function() {}
    }).mask("99/99/9999");
    $('#dias1').hide();
    $('#dias2').hide();
    $('#detalhesFaturamento').hide();
    $('#pag1').hide();
    $('#titulogestor').hide();
    $('#pag2').hide();
    $("input:radio[id='FornecedorModalidadePagamento2']").click(function() {
        $('#dias1').show();
        $('#dias2').hide();
        $('#detalhesFaturamento').hide();
        $('#pag1').hide();
        $('#titulogestor').hide();
        $('#pag2').hide();
        $("#FornecedorFaturamentoDias15").removeAttr("checked");
        $("#FornecedorFaturamentoDias25").removeAttr("checked");
        $("#FornecedorFaturamentoDias").removeAttr("checked");
    });
    $("input:radio[id='FornecedorModalidadePagamento3']").click(function() {
        $('#dias1').hide();
        $('#detalhesFaturamento').hide();
        $('#pag1').show();
        $('#titulogestor').show();
        $('#pag2').show();
        $('#dias2').show();
        $("#FornecedorFaturamentoDias30").removeAttr("checked");
        $("#FornecedorFaturamentoDias60").removeAttr("checked");
    });
    $("input:radio[id='FornecedorModalidadePagamento1']").click(function() {
        $('#dias1').hide();
        $('#dias2').hide();
        $('#pag1').hide();
        $('#titulogestor').hide();
        $('#pag2').hide();
        $('#detalhesFaturamento').hide();
        $('#FornecedorFaturamentoDetalhes').val('');
        $("#FornecedorFaturamentoDias30").removeAttr("checked");
        $("#FornecedorFaturamentoDias60").removeAttr("checked");
        $("#FornecedorFaturamentoDias15").removeAttr("checked");
        $("#FornecedorFaturamentoDias25").removeAttr("checked");
        $("#FornecedorFaturamentoDias").removeAttr("checked");
    });
    $("input:radio[id='FornecedorFaturamentoDias30']").click(function() {
        $('#detalhesFaturamento').show();
    });
    $("input:radio[id='FornecedorFaturamentoDias60']").click(function() {
        $('#detalhesFaturamento').show();
    });
    $("input:radio[id='FornecedorFaturamentoDias15']").click(function() {
        $('#detalhesFaturamento').show();
    });
    $("input:radio[id='FornecedorFaturamentoDias25']").click(function() {
        $('#detalhesFaturamento').show();
    });
    $("input:radio[id='FornecedorFaturamentoDias']").click(function() {
        $('#detalhesFaturamento').show();
    });
    if( $('#FornecedorModalidadePagamento2').is(":checked") ){
		$('#dias1').show();
	} else if( $('#FornecedorModalidadePagamento3').is(":checked") ){
		$('#dias2').show();
        $('#pag1').show();
        $('#titulogestor').show();
        $('#pag2').show();
	} else {
		$('#dias1').hide();
		$('#dias2').hide();
        $('#pag1').hide();
        $('#titulogestor').hide();
        $('#pag2').hide();
	}
	if( $('#FornecedorModalidadePagamento1').is(":checked") ){
		$('#dias1').hide();
		$('#dias2').hide();
		$('#detalhesFaturamento').hide();
        $('#pag1').hide();
        $('#titulogestor').hide();
        $('#pag2').hide();
	}
	if( $('#FornecedorFaturamentoDias30').is(":checked") || $('#FornecedorFaturamentoDias60').is(":checked") ){
		$('#detalhesFaturamento').show();
	} else if( $('#FornecedorFaturamentoDias15').is(":checked") || $('#FornecedorFaturamentoDias25').is(":checked") || $('#FornecedorFaturamentoDias').is(":checked") ) {
		$('#detalhesFaturamento').show();
	} else {
		$('#detalhesFaturamento').hide();
	}
</script>
