<?php if(!empty($cbo)):?>
    <?php echo $paginator->options(array('update' => 'div.lista')); ?>
<table class="table table-striped">
	<thead>
		<tr>
			<th class="input-medium">Código</th>
			<th>Descrição</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($cbo as $key => $dados): ?>
			<tr class="resultado-tr" codigo="<?php echo $dados['Cbo']['codigo_cbo'] ?>" nome="<?php echo $dados['Cbo']['descricao'];?>" >
				<td class="input-mini"><?php echo $dados['Cbo']['codigo_cbo'] ?></td>
				<td><?php echo $dados['Cbo']['descricao'] ?></td>
			</tr>
		<?php endforeach ?>	 
	</tbody>
	<tfoot>
            <tr>
                <td colspan = "10"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['Cbo']['count']; ?></td>
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
<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?> 

<?php if ($destino == 'localiza_cbo'): ?>
	<?php echo $this->Javascript->codeBlock("jQuery(document).ready(function() {
		$('tbody').attr('class', 'line-selector');
		var double = true;
		$('tr.resultado-tr').click(function() {
			
			if(double){
				double = false;
				var codigo = $(this).attr('codigo');
				var nome = $(this).attr('nome');

				var input_codigo = $('#{$input_id}');
				input_codigo.val(codigo).change();

				swal({
					title: 'Atenção!',
					text: 'Deseja sobrepor a descrição de atividades com a descrição do CBO?',
					type: 'warning',
					showCancelButton: true,
					confirmButtonClass: 'btn-danger',
					confirmButtonText: 'Sim',
					cancelButtonText: 'Não',
					closeOnConfirm: true,
					closeOnCancel: true
					},
					function(isConfirm) {
						if (isConfirm) {
							var input_display = $('#{$input_display}');
							input_display.val(nome).change().blur();
						}
				});

				


				close_dialog();
			}
		})
	})"); ?>
<?php endif ?>
<?php echo $this->Js->writeBuffer(); ?>