<?php 
    echo $this->Paginator->options(array('update' => 'div.lista')); 
?>
<table class='table table-striped'>
	<thead>
		<th class='input-medium'><?php echo $this->Paginator->sort('Data', 'data_inclusao') ?></th>
		<th class="input-mini">Cliente</th>
		<th>Conteúdo</th>
		<th class='input-small'>Sistema</th>
		<th></th>
		<th class='input-mini'>Tratado</th>
		<th class='input-mini'>Tipo</th>
	</thead>
	<tbody>
		<?php foreach ($logs_aplicacoes as $log_aplicacao): ?>			
			<?php 
			switch ($log_aplicacao['LogAplicacao']['tipo']) {
				case LogAplicacao::WARN:
					$tipo = 'badge-warning';
					$titulo = 'Atenção';
					break;
				case LogAplicacao::ERROR:
					$tipo = 'badge-important';
					$titulo = 'Erro';
					break;
				default:
					$tipo = 'badge-info';
					$titulo = 'Informação';
					break;
			}
			if ($log_aplicacao['LogAplicacao']['tratado']) {
				$tipo_tratado = 'badge-success';
				$titulo_tratado = 'Tratado';
			} else {
				$tipo_tratado = 'badge-important';
				$titulo_tratado = 'Não tratado';
			}
			?>
			<tr>
				<td><?= $log_aplicacao['LogAplicacao']['data_inclusao'] ?></td>
				<td><?= $log_aplicacao['LogAplicacao']['codigo_cliente'] ?></td>
				<td><?php echo '<PRE>'.htmlspecialchars($log_aplicacao['LogAplicacao']['conteudo'], ENT_QUOTES).'</PRE>'; ?></td>
				<td><?php echo $log_aplicacao['LogAplicacao']['sistema']; ?></td>
				<td><div style='float:left'><span id="<?php echo 'btn'.$log_aplicacao['LogAplicacao']['codigo'] ?>" onclick="mudar_status(<?php echo $log_aplicacao['LogAplicacao']['codigo'] ?>)" class="icon-random" title="Mudar status"></span></div></td>
				<td><div style='float:left'><span id="<?php echo $log_aplicacao['LogAplicacao']['codigo'] ?>" class="badge-empty badge <?php echo $tipo_tratado; ?>" title="<?php echo $titulo_tratado; ?>"></span></div></td>
				<td><span class="badge-empty badge <?php echo $tipo; ?>" title="<?php echo $titulo; ?>"></span></td>
			</tr>
		<?php endforeach ?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="11"><strong>Total:</strong> <?php echo $this->Paginator->params['paging']['LogAplicacao']['count']; ?></td>
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
<?php echo $this->Javascript->codeBlock("function mudar_status(codigo) {
	$.ajax({
		url: baseUrl + 'logs_aplicacoes/mudar_status_tratado/' + codigo + '/'+Math.random(),
		type:'post',
		dataType:'json',
		data:{  
            'data[LogAplicacao][codigo]' : codigo,
        },
        beforeSend: function() {
        	bloquearDiv($('span#btn'+codigo).parent());
        },
		success: function(data){
			var class_definition = 'badge-empty badge ';
			if (data == 1) {
				class_definition += 'badge-success';
			} else {
				class_definition += 'badge-important';
			}
			$('span#'+codigo).attr('class', class_definition);
			$('span#btn'+codigo).parent().unblock();
		},
	})
}") ?>