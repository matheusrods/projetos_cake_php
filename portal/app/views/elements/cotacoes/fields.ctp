<div class="margin-top-20">
	<h4>Insira os serviços:</h4>
	<div class="js-entrada">
		<div class="row-fluid">
			<div class="span6"><label>Serviço:</label></div>
			<div class="span1"><label>Quantidade:</label></div>
			<div class="span2 numeric"><label>Valor unitário:</label></div>
			<div class="span2 numeric"><label>Valor total:</label></div>
		</div>	
		<div class="row-fluid app-begin padding-top-5">
			<div class="span6">
				<input type="text" id="js-desc" placeholder="Digite o serviço" class="span12 js-servico margin-left-5">
			</div>
			<input type="hidden">
			<div class="span1">
				<input type="text" id="js-quant" class="span12 just-number numeric">
			</div>
			<div class="span2">
				<input type="text" id="js-vl-unit" class="span12 moeda numeric">
			</div>	
			<div class="span2">
				<input type="text" id="js-vl-tot" class="span12 moeda numeric" readonly="readonly">
			</div>	
			<div class="span1">
				<button type="button" class="btn btn-mini btn-success margin-top-4 js-add-resultado">Confirmar</button>
			</div>				
		</div>
	</div>
</div>
<div class="inseridos">

</div>
<div class="row-fluid margin-top-8 hide in">
	<div class="span6 padding-left-10"><strong>TOTAL</strong></div>
	<div class="span1 padding-left-10"></div>
	<div class="span2 padding-left-10"></div>
	<div class="span2 padding-left-10 text-right js-valor-total" style="font-weight: bold;"></div>
</div>

<div class='form-actions'>
	<button type="button" class="btn btn-primary js-salvar">Avançar</button>
	<?php echo $this->BForm->submit('Avançar', array('div' => 'hide', 'class' => 'btn btn-primary js-submeter')); ?>
	<?php echo $this->Html->link('Cancelar', array('action' => 'index'), array('class' => 'btn btn-default')); ?>
</div>

<div id="memoria" class="hide">
	<div class="row-fluid div-table">
		<div class="span6 padding-left-10 desc"></div>
		<div class="span1 padding-left-10 quant"></div>
		<div class="span2 padding-left-10 vl-unit"></div>
		<div class="span2 padding-left-10 vl-tot"></div>
		<div class="span1" style="text-align:inherit;padding-top:initial"><button type="button" class="btn btn-mini btn-danger margin-top-4 js-remover-resultado"><i class="icon-remove icon-white"></i></button></div>
	</div>		
</div>

<?php echo $this->Javascript->codeBlock("
	$(document).ready(function() {
		setup_mascaras();
		var i = ".((isset($key))? $key+1 : 0 ).";
		var valor_total = 0.00;
		function calcula(este) {
			var quant = este.parents('.app-begin')
			.find('#js-quant')
			.val();
			var valor = parseFloat(este.parents('.app-begin')
				.find('#js-vl-unit')
				.val()
				.replace(/\./g, '')
				.replace(/,/g, '.'));
			total = new Object();
			total.value = String((quant * valor).toFixed(2));
			este.parents('.app-begin')
			.find('#js-vl-tot')
			.val(moeda(total));
		}
		$('.js-add-resultado').click(function(event) {
			var este = $(this);
			var descricao = este.parents('.app-begin').find('#js-desc').val();
			var codigo = este.parents('.app-begin').find('input[type=\"hidden\"]').val();
			var quantidade = este.parents('.app-begin').find('#js-quant').val();
			var valor_unitario = este.parents('.app-begin').find('#js-vl-unit').val().replace(/\./g, '').replace(/,/g, '.');

			if(descricao != '' && codigo != '' && quantidade != '' && valor_unitario != '') {
				var quantidade = parseInt(quantidade);
				var valor_unitario = parseFloat(valor_unitario).toFixed(2);
				var unit_total = parseFloat(quantidade*valor_unitario).toFixed(2);
				var html = $('#memoria').clone();
				html.find('.div-table').append($('<input>', {type: 'hidden', name: 'data[ItemCotacao]['+i+'][codigo_servico]', value: codigo}));
				html.find('.div-table').append($('<input>', {type: 'hidden', name: 'data[ItemCotacao]['+i+'][quantidade]', value: quantidade}));
				html.find('.div-table').append($('<input>', {type: 'hidden', name: 'data[ItemCotacao]['+i+'][valor_unitario]', value: valor_unitario}));
				html.find('.desc').text(descricao);
				html.find('.quant').text(quantidade);
				text_valor_unitario = new Object();
				text_valor_unitario.value = String(valor_unitario);
				html.find('.vl-unit').text(moeda(text_valor_unitario));
				html.find('.vl-unit').attr('data-preco', valor_unitario);
				text_valor_total = new Object();
				text_valor_total.value = String(unit_total);
				html.find('.vl-tot').text(moeda(text_valor_total));
				html = html.html();
				valor_total = (parseFloat(quantidade*valor_unitario) + parseFloat(valor_total)).toFixed(2);
				text_valor_total = new Object();
				text_valor_total.value = String(valor_total);
				$('.js-valor-total').text(moeda(text_valor_total)).parents('.in').removeClass('hide');
				$('.inseridos').append(html);
				este.parents('.app-begin').find('#js-desc').val('');
				este.parents('.app-begin').find('input[type=\"hidden\"]').val('');
				este.parents('.app-begin').find('#js-quant').val('');
				este.parents('.app-begin').find('#js-vl-unit').val('');
				este.parents('.app-begin').find('#js-vl-tot').val('');
				$('#js-desc').focus();
				i++;
			} else {
				swal({
					type: 'warning',
					title: 'Atenção',
					text: 'Preencha todos os campos para inserir o serviço.'
				});
			}
		});
		$('body').on('click', '.js-remover-resultado', function(event) {
			var este = $(this);
			var valor = parseFloat(este.parents('.div-table').find('.vl-unit').attr('data-preco'));
			este.parents('.div-table').remove();
			valor_total = (valor_total - valor).toFixed(2);
			text_valor_total = new Object();
			text_valor_total.value = String(valor_total);
			$('.js-valor-total').text(moeda(text_valor_total));
			i--;
			if(i < 1) {
				$('.js-valor-total').text(moeda(text_valor_total)).parents('.in').addClass('hide');
			}
		});

		// ajax auto complete
		var timer;
		$('body').on('keyup', '.js-servico', function() {
			var este = $(this);
			var string = this.value;
			if(string.length >= 2) {
				este.parent().css('position', 'relative');
				$('.loader-gif').remove();
				este.tooltip('destroy');
				este.parent().append(' <img src=\"'+baseUrl+'img/default.gif\" style=\"position:absolute;top:4px;right:0px;\" class=\"loader-gif\">');
				$('.seleciona-servico').remove();
				clearTimeout(timer); 
				timer = setTimeout(function() {
					$.ajax({
						url: baseUrl + 'servicos/carrega_servicos_por_ajax',
						type: 'POST',
						dataType: 'json',
						data: {string: string},
					})
					.done(function(response) {
						if(response) {
							$('.seleciona-servico').remove();
							var canvas = $('<div>', {class: 'seleciona-servico'}).html(response);
							este.parent().append(canvas);
						} else {
							este.tooltip({title: '<span style=\"font-size-16\">Serviço não localizado no sistema</span>', html: true, trigger: 'hover', placement: 'top'});
						}
					})
					.always(function() {
						$('.loader-gif').remove();
					});
				}, 500);
			} else {
				$('.seleciona-servico').remove();
				$('.loader-gif').remove();
			}
		});
		
		$('body').on('click', '.js-click', function() {
			var valor_unitario = parseFloat($(this).attr('data-valor')).toFixed(2);
			text_valor_unitario = new Object();
			text_valor_unitario.value = String(valor_unitario);
			$(this).parents('.app-begin').find('.js-servico').val($(this).find('td:last-child').text());
			$(this).parents('.app-begin').find('#js-vl-unit').val( moeda(text_valor_unitario) );
			$(this).parents('.app-begin').find('input[type=\"hidden\"]').val($(this).attr('data-codigo'));
			$(this).parents('.app-begin').find('#js-quant').val(1);
			calcula($('.js-add-resultado'));
			$('.seleciona-servico').remove();
		});
		$('body').click(function(event) {
			$('.seleciona-servico').remove();
		});
		$('body').on('keyup', '#js-vl-unit', function(event) {
			calcula($(this));
		});
		$('body').on('keyup', '#js-quant', function(event) {
			calcula($(this));
		});
		//========
		
		$('.js-salvar').click(function(event) {
			if(i < 1) {
				swal({
					type: 'warning',
					title: 'Atenção',
					text: 'Você precisa inserir ao menos um serviço para continuar'
				});
			} else {
				$('.js-submeter').click();
				$('body').append($('<div>', {class: 'ajax-loader'}));
			}
		});
	});
", false) ?>
<style type="text/css">
	.input-prepend .help-block.error-message{
		font-size: 14px;
		margin-top: 10px;
	}
	.ajax-loader{
		position: fixed;
		top: 0;
		left: 0;
		bottom: 0;
		right: 0;
		background: rgba(0,0,0,0.75) url('/portal/img/load-gear.gif') center center no-repeat;
		z-index: 99999;
	}
	.control-group{
		margin-bottom: 0px !important;
	}
	input[type="text"]{
		margin-bottom: 5px !important;
	}
	.app-begin{
		border-top: 2px solid #e2e2e2;
		background: whitesmoke;
	}
	.margin-left-5{
		margin-left: 5px !important;
	}
	label{
		font-weight: bold;
	}
	.div-table div[class^="span"]{
		text-align: right;
		padding-top: 7px;
	}
	.div-table div[class^="span"]:first-child{
		text-align: left;
	}
	.div-table div[class^="span"]:last-child{
		padding-top: inherit;
		text-align: inherit;
	}
	.inseridos div.div-table:last-child {
		border-bottom: 1px #ccc solid !important;
	}
	.div-table{
		margin-top: 2px;
		border-top: 1px #ccc solid;
	}
	.inseridos div.div-table:first-child{
		margin-top: 20px;
	}
</style>