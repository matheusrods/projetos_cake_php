<?php if ($titulos_a_receber): ?>
	<?= $paginator->options(array('update' => 'div.lista')) ?>
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th class='input-medium'><?php echo $this->Paginator->sort('Codigo','codigo_cliente');?></th>
				<th class='input-xxlarge'><?php echo $this->Paginator->sort('Cliente','cliente')?></th>
				<th class='input-large'><?php echo $this->Paginator->sort('Valor R$','valor_tranrec')?></th>
				<th class='input-xxlarge'><?php echo $this->Paginator->sort('Data Emissão','data_emissao')?></th>
				<th class='input-medium'><?php echo $this->Paginator->sort('Data Venc.','data_vencto')?></th>
				<th class='input-xxlarge'><?php echo $this->Paginator->sort('Data Pgto.','Tranrec.dtpagto')?></th>
				<th class='input-xxlarge'><?php echo $this->Paginator->sort('Filial','EnderecoRegiao.descricao')?></th>
				<th class='input-xxlarge'><?php echo $this->Paginator->sort('Corretora','Corretora')?></th>
				<th class='input-xxlarge'><?php echo $this->Paginator->sort('Seguradora','Seguradora')?></th>
				<th class='numeric'><?php echo $this->Paginator->sort('N.RPS','NRPS')?></th>
				<th class='numeric'><?php echo $this->Paginator->sort('Nº NF','NotaFiscal')?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($titulos_a_receber as $key => $titulo_a_receber): ?>
				<tr>
					<td><?= $titulo_a_receber['Cliente']['codigo'] ?></td>
					<td><?= $titulo_a_receber['Cliente']['razao_social'] ?></td>
					<td class='numeric'><?= $this->Buonny->moeda($titulo_a_receber['Tranrec']['valor'], array('nozero' => true)) ?></td>
					<td><?= substr($titulo_a_receber['Tranrec']['dtemiss'],0,10) ?></td>
					<td><?= substr($titulo_a_receber['Tranrec']['dtvencto'],0,10) ?></td>
					<td><?= substr(AppModel::dbDateToDate($titulo_a_receber['0']['dtpgto']),0,10) ?></td>
					<td><?= $titulo_a_receber['Seguradora']['nome'] ?></td>
					<td><?= $titulo_a_receber['EnderecoRegiao']['descricao'] ?></td>
					<td><?= $titulo_a_receber['Corretora']['nome'] ?></td>
					<td><?= $titulo_a_receber['Notafis']['numero'] ?></td>
					<td><?= $titulo_a_receber['Gernfe']['numnfe'] ?></td>
				</tr>
			<?php endforeach ?>
		</tbody>
		<tfoot>
			<tr>
				<td></td>
				<td></td>
				<td class='numeric'><?= $this->Buonny->moeda($totais[0]['valor'], array('nozero' => true)) ?></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
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
<?php endif ?>