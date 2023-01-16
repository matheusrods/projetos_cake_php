<div class="row-fluid inline">
    <?php echo $this->BForm->input('regiao_tipo_faturamento', array('label' => 'Faturamento', 'options' => array(1 => 'Total', 0 => 'Parcial'), 'class' => 'text-small')); ?>
    <?php echo $this->BForm->input('iss', array('label' => 'ISS', 'class' => 'text-small numeric', 'value' => $buonny->moeda((isset($this->data['Cliente']['iss']) ? $this->data['Cliente']['iss'] : 0), array('edit' => true)))); ?>
    <?php if ($edit_mode): ?>
        <?php echo ($opcao_fatura_email) ? '<label>Optante por receber informações de faturamento somente por email</label>' : ''; ?>
    <?php endif; ?>
</div>
<div class="row-fluid inline">
 		<?php echo $this->Form->input('Cliente.aguardar_liberacao',array('type'=>'checkbox', 'class' => 'input-xlarge', 'label' => 'Aguardar Liberação')) ?>
</div>
<div class="row-fluid inline">
	<?php echo $this->Form->input('Cliente.validar_pre_faturamento',array('type'=>'checkbox', 'class' => 'input-xlarge', 'label' => 'Liberar para validação de Pré-Faturamento')) ?>
 </div>
 <div class="row-fluid inline" style="margin-top: 17px;">
 	 <?php echo $this->BForm->input('Cliente.dia_util_faturamento', array('label' => 'Dia util para validar o faturamento', 'class' => 'input-medium', 'default' => '','empty' => 'Selecione o dia', 'options' => $dias_faturamento)); ?>
 </div>

<div class="col-sm-8">
	<h4 class="modal-title" id="gridSystemModalLabel"> Títulos Remessa Bancária</h4>
</div>
<?php if( !empty($remessa_bancaria) ): ?>

	<style type="text/css">
	.table {
	    table-layout:fixed;
	    max-width:1170px;
	    display: block;
	}


	.table td {
	  white-space: nowrap;
	  overflow-y: auto;
	  /*text-overflow: ellipsis;*/

	}

	.table th {
	  white-space: nowrap;
	  /*overflow-y: auto;*/
	  /*text-overflow: ellipsis;*/

	}
	</style>

	<table class='table'>
		<thead>
			<th>Nosso Numero</th>
			<th>Dt. Emissão</th>
			<th>Dt. Vencimento</th>
			<th>Dt. Pagamento</th>
			<th>Status</th>
			<th>Status Retorno</th>
			<th class='input-small numeric'>Valor Juros/Multa</th>
			<th class='input-small numeric'>Valor Pago</th>
			<th class='input-small numeric'>Total</th>
		</thead>
		<tbody>			
			<?php foreach($remessa_bancaria as $key => $value):?>

				<?php
				$retorno = (!empty($value['RemessaRetorno']['codigo'])) ? $value['RemessaRetorno']['codigo']."-".$value['RemessaRetorno']['descricao'] : '';
				?>
				<tr>
					<td><?php echo $value['RemessaBancaria']['nosso_numero']; ?></td>
					<td><?php echo $value['RemessaBancaria']['data_emissao']; ?></td>
					<td><?php echo $value['RemessaBancaria']['data_vencimento']; ?></td>
					<td><?php echo $value['RemessaBancaria']['data_pagamento']; ?></td>
					<td><?php echo $value['RemessaStatus']['descricao']; ?></td>
					<td><?php echo $retorno; ?></td>
					<td class='input-small numeric'><?php echo $this->Buonny->moeda($value['RemessaBancaria']['valor_juros']); ?></td>
					<td class='input-small numeric'><?php echo $this->Buonny->moeda($value['RemessaBancaria']['valor_pago']); ?></td>
					<td class='input-small numeric'><?php echo $this->Buonny->moeda($value['RemessaBancaria']['valor']); ?></td>
				</tr>
			<?php endforeach; ?>
			
		</tbody>
		
	</table>

<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?>  