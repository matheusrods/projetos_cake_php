<?php if(!empty($medicos)):?>
    <?php echo $paginator->options(array('update' => 'div#busca-lista')); ?>
<table class="table table-striped">
	<thead>
		<tr>
			<th class="input-mini">Código</th>
			<th>Nome</th>
			<th>Conselho</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($medicos as $key => $dados): ?>
			<tr class="resultado-tr" codigo="<?php echo $dados['Medico']['codigo'] ?>" crm="<?php echo $dados['Medico']['numero_conselho']; ?>" uf="<?php echo $dados['Medico']['conselho_uf']; ?>" nome="<?php echo $dados['Medico']['nome']; ?>" >
				<td class="input-mini"><?php echo $dados['Medico']['codigo'] ?></td>
				<td><?php echo $dados['Medico']['nome'] ?></td>
				<td><?php echo $dados['ConselhoProfissional']['descricao']." - ".$dados['Medico']['numero_conselho']."/".$dados['Medico']['conselho_uf'];?></td>
			</tr>
		<?php endforeach ?>	 
	</tbody>
	<tfoot>
            <tr>
                <td colspan = "10"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['Medico']['count']; ?></td>
            </tr>
        </tfoot>    
    </table>
    <div class='row-fluid'>
        <div class='numbers span7'>
            <?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
            <?php echo $this->Paginator->numbers(); ?>
            <?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
        </div>
        <div class='counter span4'>
            <?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
            
        </div>
    </div>
<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?> 

<?php if ($destino == 'buscar_medico_readonly'): ?>
	<?php echo $this->Javascript->codeBlock("jQuery(document).ready(function() {
		$('tbody').attr('class', 'line-selector');
		var double = true;
			
		$('tr.resultado-tr').click(function() {
			
			if(double){
				double = false;
				var codigo = $(this).attr('codigo');
			
				var crm = $(this).attr('crm');
				var uf = $(this).attr('uf');
				var nome = $(this).attr('nome');
			
				var input_codigo = $('#{$input_id}');
				input_codigo.val(codigo).change();
				
				var input_crm_display = $('#{$input_crm_display}');
				input_crm_display.val(crm).change().blur();
				
				var input_uf_display = $('#{$input_uf_display}');
				input_uf_display.val(uf).change().blur();
				
				var input_nome_display = $('#{$input_nome_display}');
				input_nome_display.val(nome).change().blur();
				
				close_dialog();
			}
		})
	})"); ?>
<?php endif ?>
<?php echo $this->Js->writeBuffer(); ?>
