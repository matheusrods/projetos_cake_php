<div class = 'form-procurar'>
	
</div>
<div class='actionbar-right margin-bottom-10'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', array('action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar Nova característica'));?>
</div>

<table class="table table-striped">
	<thead>
		<tr>
			<th><?= $this->Paginator->sort('Descrição', 'titulo') ?></th>
			<th><?= $this->Paginator->sort('Alerta', 'alerta') ?></th>
			<th>Ações</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($caracteristicas as $key => $caracteristica) { ?>
		<tr data-codigo="<?php echo $caracteristica['Caracteristica']['codigo'] ?>">
			<td><?= $caracteristica['Caracteristica']['titulo'] ?></td>
			<td><?= 'Desta população quem '.$caracteristica['Caracteristica']['alerta'] ?></td>
			<td class="pagination-centered">
				<?php echo $this->Html->link('', array('action' => 'editar', $caracteristica['Caracteristica']['codigo']), array('class' => 'icon-edit', 'title' => 'Editar', 'data-toggle' => 'tooltip')); ?>&nbsp;&nbsp;
				<?php echo $this->Html->link('', 'javascript:void(0)', array('class' => 'icon-trash', 'title' => 'Excluir', 'data-toggle' => 'tooltip', 'onclick' => "excluir_caracteristica({$caracteristica['Caracteristica']['codigo']})")) ?>
			</td>
		</tr>
		<?php } ?>       
	</tbody>
	<tfoot>
        <tr>
            <td colspan = "10"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['Caracteristica']['count']; ?></td>
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

<script type="text/javascript">
	$(document).ready(function() {
		
	});
	function excluir_caracteristica(codigo) {
		swal({
			type: 'warning',
			title: 'Atenção',
			text: 'Tem certeza que deseja excluir este dado?',
			showCancelButton: true,
			cancelButtonText: 'Cancelar',
			confirmButtonText: 'Excluir',
			showLoaderOnConfirm: true
		}, function(){
			$.ajax({
				url: baseUrl + 'caracteristicas/excluir',
				type: 'POST',
				dataType: 'json',
				data: {codigo: codigo},
			})
			.done(function(response) {
				if(response) {
					location.reload();
				}
			});
		});
	}
</script>