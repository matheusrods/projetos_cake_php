<div class="well">
	<div class="row-fluid">
		<div class="span3">
			<strong>Nº COTAÇÃO: </strong> <?php echo $cotacao['Cotacao']['codigo'] ?>
		</div>
		<div class="span3">
			<strong>DATA COTAÇÃO: </strong> <?php echo $cotacao['Cotacao']['data_inclusao'] ?> 
		</div>
	</div>
	<div class="row-fluid margin-top-10 app-begin">
		<div class="span4">
			<?php echo $this->BForm->input('nome', array('label' => 'Cliente:', 'class' => 'js-cliente span12 js-val', 'placeholder' => 'digite seu nome')); ?>
			<?php echo $this->BForm->hidden('codigo_cliente', array('id' => 'codigoCliente')); ?>

		</div>
		<div class="span4">
			<?php echo $this->BForm->input('vendedor', array('label' => 'Vendedor:', 'class' => 'js-vendedor span12 js-val', 'placeholder' => 'digite o nome do vendedor')); ?>
			<?php echo $this->BForm->hidden('codigo_vendedor', array('id' => 'codigoVendedor')); ?>
		</div>
		<div class="span4">
			<?php echo $this->BForm->input('codigo_forma_pagto', array('label' => 'Forma de recebimento:', 'class' => 'span12 js-val', 'options' => $formas_pagto, 'empty' => 'Selecione a forma de recebimento')); ?>
		</div>
	</div>
</div>

<table class="table">
	<thead>
		<tr>
			<th>Serviços</th>
			<th>Quantidade</th>
			<th  class="numeric">Valor Unitário</th>
			<th  class="numeric">Valor Total</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($cotacao['ItemCotacao'] as $key => $valor) { ?>
		<tr>
			<td><?php echo $valor['Servico']['descricao']; ?></td>
			<td><?php echo $valor['quantidade']; ?></td>
			<td class="numeric"><?php echo $this->Buonny->moeda($valor['valor_unitario'], array('nozero' => true, 'places' => 2)); ?></td>
			<td class="numeric"><?php echo $this->Buonny->moeda($valor['valor_unitario'] * $valor['quantidade'], array('nozero' => true, 'places' => 2)); ?></td>
		</tr>
		<?php } ?>
	</tbody>
</table>

<?php foreach ($cotacao['ItemCotacao'] as $key => $valor) { ?>
<!-- LAYOUT PARA IMPRESSAO -->
<div class="print hide" style="float:left;width:100%;padding:15px;font-family:Arial">
	<div>
		<div>
			<?php echo $this->Html->image('logo-rhhealth.png', array('style' => 'max-width:180px')); ?>
		</div>
		<div style="float:left;width:100%;font-family:Arial">
			<h4>Detalhes da Cotação</h4>
		</div>
		<div style="float:left;width:50%;font-family:Arial">
			<strong>Nº Cotação: </strong> <?php echo $cotacao['Cotacao']['codigo'] ?>
		</div>
		<div style="float:left;width:50%;font-family:Arial">
			<strong>Data: </strong> <?php echo $cotacao['Cotacao']['data_inclusao'] ?> 
		</div>
		<div style="float:left;width:50%;font-family:Arial">
			<strong>Nome do cliente: </strong> <span class="js-nome"></span>
		</div>
		<div style="float:left;width:50%;font-family:Arial">
			<strong>Vendedor: </strong> <span class="js-vendedor"></span>
		</div>
		<div style="float:left;width:50%;font-family:Arial">
			<strong>Forma de recebimento: </strong> <span class="js-forma_pagto"></span>
		</div>
		<div style="clear:both;"></div>
		<div>&nbsp;</div>
		<table style="width:100%">
			<thead>
				<tr style="background-color:#DBEAF9">
					<th style="text-align:left;font-family:Arial">Serviço</th>
					<th style="text-align:left;font-family:Arial">Quantidade</th>
					<th style="text-align:left;font-family:Arial;text-align:right">Valor Unitário</th>
					<th style="text-align:left;font-family:Arial;text-align:right">Valor Total</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($cotacao['ItemCotacao'] as $key3 => $item_cotacao) { ?>
				<tr>
					<td style="font-family:Arial"><?php echo $item_cotacao['Servico']['descricao'] ?></td>
					<td style="font-family:Arial"><?php echo $item_cotacao['quantidade'] ?></td>
					<td style="font-family:Arial;text-align:right"><?php echo $this->Buonny->moeda($item_cotacao['valor_unitario'], array('nozero' => true, 'places' => 2)); ?></td>
					<td style="font-family:Arial;text-align:right"><?php echo $this->Buonny->moeda(($item_cotacao['valor_unitario'] * $item_cotacao['quantidade']), array('nozero' => true, 'places' => 2));  ?></td>
				</tr>		
				<?php } ?>
				<tr>
					<td colspan="4" style="font-family:Arial;text-align:right;font-weight:bold;text-align:right">Total: <?php echo $this->Buonny->moeda($cotacao['Cotacao']['valor_total'], array('nozero' => false, 'places' => 2)); ?></td>	
				</tr>
			</tbody>
		</table>
	</div>
</div>
<!-- FIM LAYOUT IMPRESSAO -->
<?php } ?>

<div class='form-actions'>
	<div class="hide">
		<?php echo $this->BForm->submit('Salvar', array('div' => false,'class' => 'js-salvar')); ?>
	</div>
	<button type="button" class="btn btn-primary js-botao-salvar">Salvar</button>
	<button type="button" class="btn btn-info imprimir">Salvar e imprimir</button>
	<button type="button" class="btn btn-success js-salvar-email">Salvar e enviar e-mail</button>
	<?php // echo $this->BForm->submit('Salvar e enviar e-mail', array('div' => false,'class' => 'btn btn-success js-salvar')); ?>
	<button type="button" class="btn btn-danger js-cancelar" data-codigo="<?php echo $codigo ?>">Cancelar</button>

	<?php // echo $html->link('Cancelar', array('action' => 'cancelar', $codigo), array('class' => 'btn btn-danger')); ?>
</div> 

<?php echo $this->Javascript->codeBlock("
	function valida_campos() {
		$('.js-val').css({borderColor: '#ccc'});
		var mensagem = 'Verifique os seguintes campos para continuar: <br><strong>';
		var erro = false;
		if($('#codigoCliente').val() == '') {
			erro = true;
			$('#CotacaoNome').css({borderColor: 'red'}).val('');
			mensagem += '- Cliente<br>';
		}	
		if($('#codigoVendedor').val() == '') {
			erro = true;
			$('#CotacaoVendedor').css({borderColor: 'red'}).val('');
			mensagem += '- Vendedor<br>';
		}
		if($('#CotacaoCodigoFormaPagto').val() == '') {
			erro = true;
			$('#CotacaoCodigoFormaPagto').css({borderColor: 'red'});
			mensagem += '- Forma de recebimento<br>';
		}	
		mensagem += '</strong>';
		if(erro) {
			swal({
				html: true,
				type: 'warning',
				title: 'Atenção',
				text: mensagem
			});
			return false;
		} else {
			$('.js-salvar').click();
			return true;
		}
	}
	$(document).ready(function() {
		$('.imprimir').click(function(event) {
			if(valida_campos()) {
				$('.js-nome').text($('#CotacaoNome').val());
				$('.js-vendedor').text($('#CotacaoVendedor').val());
				$('.js-forma_pagto').text($('#CotacaoCodigoFormaPagto option:selected').text());
				var conteudo = $('.print').html();
				tela_impressao = window.open('about:blank');
				tela_impressao.document.write(conteudo);
				tela_impressao.window.print();
				tela_impressao.window.close();
			}
		});
		$('.js-salvar').click(function(event) {
			$('body').append($('<div>', {class: 'ajax-loader'}));
		});
		setup_mascaras();
		$('.js-cancelar').click(function(event) {
			var codigo = $(this).attr('data-codigo');
			swal({
				title: \"Atenção\",
				text: \"Tem certeza que deseja excluir esta cotação?\",
				type: \"info\",
				showCancelButton: true,
				closeOnConfirm: false,
				showLoaderOnConfirm: true,
				confirmButtonText: 'Sim',
				cancelButtonText: 'Não',
				confirmButtonColor: '#006dcc',
			},
			function(){
				window.location.replace(baseUrl + 'cotacoes/excluir/' + codigo);
			});
		});
		$('.js-salvar-email').click(function(event) {
			$(this).parents('form').append( $('<input>', {type: 'hidden', name: 'data[Cotacao][enviar_email]', value: '1'}) );
			valida_campos()
		});

		$('.js-botao-salvar').click(function(event) {
			valida_campos();
		});

		// ajax auto complete cliente
		var timer;
		$('body').on('keyup', '.js-cliente', function() {
			var este = $(this);
			var string = this.value;
			if(string.length >= 2) {
				este.parent().css('position', 'relative');
				$('.loader-gif').remove();
				este.tooltip('destroy');
				este.parent().append(' <img src=\"'+baseUrl+'img/default.gif\" style=\"position:absolute;top:28px;right:0px;\" class=\"loader-gif\">');
				$('.seleciona-cliente').remove();
				clearTimeout(timer); 
				timer = setTimeout(function() {
					$.ajax({
						url: baseUrl + 'clientes/carrega_clientes_por_ajax',
						type: 'POST',
						dataType: 'json',
						data: {string: string},
					})
					.done(function(response) {
						if(response) {
							$('.seleciona-cliente').remove();
							var canvas = $('<div>', {class: 'seleciona-cliente'}).html(response);
							este.parent().append(canvas);
						} else {
							este.tooltip({title: '<span style=\"font-size-16\">Cliente não localizado no sistema</span>', html: true, trigger: 'hover', placement: 'top'});
						}
					})
					.always(function() {
						$('.loader-gif').remove();
					});
				}, 500);
			} else {
				$('.seleciona-cliente').remove();
				$('.loader-gif').remove();
			}
		});
		$('body').on('click', '.js-click-cliente', function() {
			var valor_unitario = parseFloat($(this).attr('data-valor')).toFixed(2);
			text_valor_unitario = new Object();
			text_valor_unitario.value = String(valor_unitario);
			$(this).parents('.app-begin').find('.js-cliente').val($(this).find('td:last-child').text());
			$(this).parents('.app-begin').find('#codigoCliente').val($(this).attr('data-codigo'));
			$('.seleciona-cliente').remove();
		});
		$('body').click(function(event) {
			$('.seleciona-cliente').remove();
		});
		//========
		
		// ajax auto complete vendedor
		var timer;
		$('body').on('keyup', '.js-vendedor', function() {
			var este = $(this);
			var string = this.value;
			if(string.length >= 2) {
				este.parent().css('position', 'relative');
				$('.loader-gif').remove();
				este.tooltip('destroy');
				este.parent().append(' <img src=\"'+baseUrl+'img/default.gif\" style=\"position:absolute;top:28px;right:0px;\" class=\"loader-gif\">');
				$('.seleciona-vendedor').remove();
				clearTimeout(timer); 
				timer = setTimeout(function() {
					$.ajax({
						url: baseUrl + 'vendedores/carrega_vendedores_por_ajax',
						type: 'POST',
						dataType: 'json',
						data: {string: string},
					})
					.done(function(response) {
						if(response) {
							$('.seleciona-vendedor').remove();
							var canvas = $('<div>', {class: 'seleciona-vendedor'}).html(response);
							este.parent().append(canvas);
						} else {
							este.tooltip({title: '<span style=\"font-size-16\">Vendedor não localizado no sistema</span>', html: true, trigger: 'hover', placement: 'top'});
						}
					})
					.always(function() {
						$('.loader-gif').remove();
					});
				}, 500);
			} else {
				$('.seleciona-vendedor').remove();
				$('.loader-gif').remove();
			}
		});
		$('body').on('click', '.js-click-vendedor', function() {
			var valor_unitario = parseFloat($(this).attr('data-valor')).toFixed(2);
			text_valor_unitario = new Object();
			text_valor_unitario.value = String(valor_unitario);
			$(this).parents('.app-begin').find('.js-vendedor').val($(this).find('td:last-child').text());
			$(this).parents('.app-begin').find('#codigoVendedor').val($(this).attr('data-codigo'));
			$('.seleciona-vendedor').remove();
		});
		$('body').click(function(event) {
			$('.seleciona-vendedor').remove();
		});
		//========

	});
	", false) ?>

<style type="text/css">
	.ajax-loader{
		position: fixed;
		top: 0;
		left: 0;
		bottom: 0;
		right: 0;
		background: rgba(0,0,0,0.75) url('/portal/img/load-gear.gif') center center no-repeat;
		z-index: 99999;
	}
</style>
