<?php if(!empty($riscos)):?>
    <?php echo $paginator->options(array('update' => 'div.lista')); ?>
<table class="table table-striped">
	<thead>
		<tr>
			<th class="input-medium">Código</th>
			<th>Descrição</th>
			<th>Grupo</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($riscos as $key => $dados): ?>
			<tr class="resultado-tr" codigo="<?php echo $dados['Risco']['codigo'] ?>" nome="<?php echo $dados['Risco']['nome_agente'].' ('.Risco::retorna_grupo($dados['Risco']['codigo_grupo']).')';?>" >
				<td class="input-mini"><?php echo $dados['Risco']['codigo'] ?></td>
				<td><?php echo $dados['Risco']['nome_agente'] ?></td>
				<td><?php echo Risco::retorna_grupo($dados['Risco']['codigo_grupo']);?></td>
			</tr>
		<?php endforeach ?>	 
	</tbody>
	<tfoot>
            <tr>
                <td colspan = "10"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['Risco']['count']; ?></td>
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

<?php if ($destino == 'buscar_risco'): ?>
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
				var input_display = $('#{$input_display}');
				input_display.val(nome).change().blur();
				close_dialog();
			}
		})
	})"); ?>
<?php endif ?>
<?php echo $this->Js->writeBuffer(); ?>