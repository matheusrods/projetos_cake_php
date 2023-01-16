<?php 
    echo $paginator->options(array('update' => 'div.lista')); 
    $total_paginas = $this->Paginator->numbers();
?>
<table class='table table-striped horizontal-scroll' style='width:2000px;max-width:none;'>
	<thead>
		<th>Código</th>
        <th>Razão Social</th>
		<th>Usuário</th>
		<th>Operação</th>
		<th>Categoria</th>
		<th>CPF</th>
		<th>Data</th>
		<th>Nº Consulta</th>
		<th>Placa</th>
		<th>Carreta</th>
		<th>Bitrem</th>
		<th>Origem</th>
		<th>Destino</th>
		<th>Carga</th> 
	</thead>
	<tbody>		
	<?php $id_label=0; 
	if( $fichasScorecard ) :
		foreach ($fichasScorecard as $key => $ficha):?>		
		<tr>
			<td><?php echo $ficha['Cliente']['codigo']?></td>
			<td><?php echo $ficha['Cliente']['nome_fantasia']?></td>
			<td><?php echo $ficha['Usuario']['apelido'] ?></td>
			<td><?php echo $ficha['TipoOperacao']['descricao']; ?></td>
			<td><?php echo $ficha['ProfissionalTipo']['descricao'];?></td>
			<td><?php echo comum::formatarDocumento($ficha['Profissional']['codigo_documento']);?></td>
			<td><?php echo $ficha['LogFaturamentoTeleconsult']['data_inclusao'];?></td>
			<td><?php echo ($ficha['TipoOperacao']['codigo'] == 1 ? $ficha['LogFaturamentoTeleconsult']['codigo'] : NULL);?></td>
			<td><?php echo strtoupper($ficha['LogFaturamentoTeleconsult']['placa']);?></td>
			<td><?php echo strtoupper($ficha['LogFaturamentoTeleconsult']['placa_carreta']);?></td>
			<td><?php echo strtoupper($ficha['LogFaturamentoTeleconsult']['placa_veiculo_bitrem']);?></td>
			<td><?php echo ($ficha['EnderecoCidadeOrigem']['descricao'] ? $ficha['EnderecoCidadeOrigem']['descricao'].'/'.$ficha['EnderecoEstadoOrigem']['descricao'] : NULL) ?></td>
			<td><?php echo ($ficha['EnderecoCidadeDestino']['descricao']? $ficha['EnderecoCidadeDestino']['descricao'].'/'.$ficha['EnderecoEstadoDestino']['descricao']: NULL) ?></td>
			<td><?php echo $ficha['CargaTipo']['descricao'] ?></td>			
		</tr>
	<?php endforeach;
	endif;?>
	</tbody>
    <tfoot>
    <?php if( isset($fichasScorecard) ): ?>
        <tr>
            <td colspan="15" class="input-xlarge"><strong>Total: <?php echo $this->Paginator->counter('{:count}')?></strong></td>
        </tr>
    <?php  endif;?>
	</tfoot>
</table>
<div class='row-fluid'>
    <div class='numbers span6'>
        <?php if($this->Paginator->counter('{:pages}') > 1): ?>
            <?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
            <?php echo $this->Paginator->numbers(); ?>
            <?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
        <?php endif; ?>
    </div>
    <div class='counter span6'>
        <?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
    </div>
</div>
<?php echo $this->Js->writeBuffer(); ?>