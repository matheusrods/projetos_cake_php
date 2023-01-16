<?php 
echo $paginator->options(array('update' => 'div.lista')); 
?>
<table class="table table-striped">
	<thead>
		<tr>
			<th class="span1"><?php echo $this->Paginator->sort('Nº Cotação', 'codigo') ?></th>
			<th class="span2"><?php echo $this->Paginator->sort('Data', 'data_inclusao') ?></th>
			<th class="span1 numeric"><?php echo $this->Paginator->sort('Valor', 'valor_total') ?></th>
			<th class="span4"><?php echo $this->Paginator->sort('Cliente', 'Cliente.nome_fantasia') ?></th>
			<th class="span2"><?php echo $this->Paginator->sort('Vendedor', 'Vendedor.nome') ?></th>
			<th class="span1"><?php echo $this->Paginator->sort('Forma de recebimento', 'FormaPagto.descricao') ?></th>
			<th class="span1">Ações</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($cotacoes as $key => $cotacao) { ?>
		<tr>
			<td><?php echo $cotacao['Cotacao']['codigo'] ?></td>
			<td><?php echo $cotacao['Cotacao']['data_inclusao'] ?></td>
			<td class="numeric"><?php echo $this->Buonny->moeda($cotacao['Cotacao']['valor_total'], array('nozero' => false, 'places' => 2)); ?></td>
			<td><?php echo $cotacao['Cliente']['nome_fantasia'] ?></td>
			<td><?php echo $cotacao['Vendedor']['nome'] ?></td>
			<td><?php echo $cotacao['FormaPagto']['descricao'] ?></td>
			<td>
				<a href="#myModal-<?php echo $key ?>" role="button"  data-toggle="modal"><i class="icon-eye-open" data-toggle="tooltip" title="Detalhar"></i></a>&nbsp;&nbsp;
				<span class="pointer js-cancelar" data-codigo="<?php echo $cotacao['Cotacao']['codigo'] ?>"><i class="icon-remove" data-toggle="tooltip" title="Exluir"></i></span>
			</td>
		</tr>
		<?php } ?>       
	</tbody>
</table>

<?php foreach ($cotacoes as $key => $cotacao) { ?>
<!-- Modal -->
<div id="myModal-<?php echo $key ?>" class="modal config hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	
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
				<strong>Nome do cliente: </strong> <?php echo $cotacao['Cliente']['nome_fantasia'] ?>
			</div>
			<div style="float:left;width:50%;font-family:Arial">
				<strong>Vendedor: </strong> <?php echo $cotacao['Vendedor']['nome'] ?>
			</div>
			<div style="float:left;width:50%;font-family:Arial">
				<strong>Forma de recebimento: </strong> <?php echo $cotacao['FormaPagto']['descricao'] ?>
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
						<td style="font-family:Arial;text-align:right"><?php echo $this->Buonny->moeda($item_cotacao['valor_unitario'], array('nozero' => false, 'places' => 2)); ?></td>
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

	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3>Detalhes da Cotação</h3>
	</div>
	<div class="modal-body">
		<div class="well">
			<div class="row-fluid">
				<div class="span6">
					<strong>Nº Cotação: </strong> <?php echo $cotacao['Cotacao']['codigo'] ?>
				</div>
				<div class="span6">
					<strong>Data: </strong> <?php echo $cotacao['Cotacao']['data_inclusao'] ?>
				</div>
			</div>
			<div class="row-fluid">
				<div class="span6">
					<strong>Cliente: </strong> <?php echo $cotacao['Cliente']['nome_fantasia'] ?>
				</div>
				<div class="span6">
					<strong>Vendedor: </strong> <?php echo $cotacao['Vendedor']['nome'] ?>
				</div>
			</div>
			<div class="row-fluid">
				<div class="span6">
					<strong>Forma de recebimento: </strong> <?php echo $cotacao['FormaPagto']['descricao'] ?>
				</div>
			</div>
		</div>
		<table class="table">
			<thead>
				<tr>
					<th>Serviço</th>
					<th>Quantidade</th>
					<th class="numeric">Valor Unitário</th>
					<th class="numeric">Valor Total</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($cotacao['ItemCotacao'] as $key2 => $item_cotacao) { ?>
				<tr>
					<td><?php echo $item_cotacao['Servico']['descricao'] ?></td>
					<td><?php echo $item_cotacao['quantidade'] ?></td>
					<td class="numeric"><?php echo $this->Buonny->moeda($item_cotacao['valor_unitario'], array('nozero' => false, 'places' => 2)); ?></td>
					<td class="numeric"><?php echo $this->Buonny->moeda(($item_cotacao['valor_unitario'] * $item_cotacao['quantidade']), array('nozero' => false, 'places' => 2));  ?></td>
				</tr>		
				<?php } ?>
				<tr>
					<td colspan="4">
						<span class="pull-left"><strong>Total: </strong><?php echo count($cotacao['ItemCotacao']) ?></span>  
						<span class="pull-right"><strong>Valor total: </strong><?php echo $this->Buonny->moeda($cotacao['Cotacao']['valor_total'], array('nozero' => false, 'places' => 2)); ?></span>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="modal-footer">
		<button class="btn btn-danger" data-dismiss="modal" aria-hidden="true">Fechar</button>
		<button class="btn btn-info imprimir">Imprimir</button>
		<button class="btn btn-success js-enviar-email" data-email="<?php echo $cotacao['Cotacao']['emails'] ?>" data-codigo="<?php echo $cotacao['Cotacao']['codigo'] ?>" data-cursor="<?php echo $key ?>">Enviar e-mail</button>
	</div>
</div>
<!-- fim modal -->

<!-- modal email -->
<div id="myModalEmail-<?php echo $key ?>" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="myModalLabel">Enviar Cotação por e-mail</h3>
	</div>
	<?php echo $this->Form->create('Cotacao', array('url' => 'disparar_email')) ?>
	<?php echo $this->Form->hidden('codigo', array('value' => $cotacao['Cotacao']['codigo'])); ?>
	<div class="modal-body">
		<p>
			<?php if(trim($cotacao['Cotacao']['emails'])!='') { ?>
			<strong>Este e-mail está correto? </strong>
			<?php }	else { ?>
			<strong>Informe o e-mail: </strong>
			<?php } ?>
		</p>
		<?php echo $this->Form->input('email', array('label' => 'E-mail:', 'style' => 'width:98%', 'value' => $cotacao['Cotacao']['emails'])); ?>
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true">Fechar</button>
		<button class="btn btn-primary">Enviar</button>
	</div>
	<?php echo $this->Form->end(); ?>
</div>
<!-- fim modal email -->
<?php } ?>

<div class='row-fluid'>
	<div class='numbers span6'>
		<?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
		<?php echo $this->Paginator->numbers(); ?>
		<?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
	</div>
	<div class='counter span6'>
		<?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function() {
		$('.imprimir').click(function(event) {
			var conteudo = $(this).parents('.modal.fade').find('.print').html();
			tela_impressao = window.open('about:blank');
			tela_impressao.document.write(conteudo);
			tela_impressao.window.print();
			tela_impressao.window.close();
		});
		$('.js-cancelar').click(function(event) {
			var codigo = $(this).attr('data-codigo');
			swal({
				title: "Atenção",
				text: "Tem certeza que deseja excluir esta cotação?",
				type: "info",
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
		$('.js-enviar-email').click(function(event) {
			var email = $(this).attr('data-email');
			var cursor = $(this).attr('data-cursor');
			$('.modal').modal('hide');
			$('#myModalEmail-'+cursor).modal('show');

		});
	});
</script>
<?php echo $this->Js->writeBuffer(); ?>
<?php echo $this->Javascript->codeblock("$(document).ready(function() { $('[data-toggle=\"tooltip\"]').tooltip() })") ?>
<style type="text/css">
	.modal.config
	{
		width: 1000px; 
		/*margin-top: -300px !important;*/
		margin-left:  -500px !important; 
	} 
	.modal.config .modal-body {
		max-height: 525px;
	}
</style>

