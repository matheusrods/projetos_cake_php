<?php echo $paginator->options(array('update' => 'div#lista-referencias-visualizar')); ?>
<div class='row-fluid inline'>
	<table class='table table-striped referencias-table'>
		<thead>
			<th class='input-small'><?php echo $this->Paginator->sort('Codigo', 'refe_codigo') ?></th>
			<th ><?php echo $this->Paginator->sort('Descrição', 'refe_descricao') ?></th>
			<th class='input-small'><?php echo $this->Paginator->sort('Classe', 'cref') ?></th>
			<th class='input-xlarge'><?php echo $this->Paginator->sort('Endereco', 'refe_endereco_empresa_terceiro') ?></th>
			<th class='input-small'><?php echo $this->Paginator->sort('Cidade', 'cida_descricao') ?></th>
			<th class='input-small'><?php echo $this->Paginator->sort('Estado', 'esta_sigla') ?></th>
		</thead>
		<tbody>
			<?php foreach ($listagem as $referencia):?>
				<tr class="referencias-tr" codigo="<?php echo $referencia['TRefeReferencia']['refe_codigo'] ?>" descricao='<?php echo $referencia['TRefeReferencia']['refe_descricao'] ?>' >
					<td><?php echo $referencia['TRefeReferencia']['refe_codigo'] ?></td>
					<td title = "<?php echo $referencia['TRefeReferencia']['refe_descricao']?>" ><?php echo mb_substr($referencia['TRefeReferencia']['refe_descricao'],0,30,'utf-8')?></td>
					<td><?php echo $referencia['TCrefClasseReferencia']['cref_descricao']?></td>
					<td><?php echo $referencia['TRefeReferencia']['refe_endereco_empresa_terceiro']?></td>
					<td><?php echo $referencia['TCidaCidade']['cida_descricao']?></td>
					<td><?php echo $referencia['TEstaEstado']['esta_sigla']?></td>
				</tr>
			<?php endforeach;?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan = "11"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['TRefeReferencia']['count']; ?></td>
			</tr>
		</tfoot>
	</table>
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
	<?php echo $this->Js->writeBuffer(); ?>
</div>
<?php echo $this->Javascript->codeBlock("jQuery(document).ready(function() {
	$('.referencias-table tbody').attr('class', 'line-selector');
	var double = true;
	$('tr.referencias-tr').click(function() {
		if(double){
			double = false;
			var codigo = $(this).attr('codigo');
			var descricao = $(this).attr('descricao');
			var input = $('#{$this->passedArgs['searcher']}');
			input.val(codigo).change();
			var display = $('#{$this->passedArgs['display']}');
			display.val(descricao);
			close_dialog();
		}
	})
})"); ?>