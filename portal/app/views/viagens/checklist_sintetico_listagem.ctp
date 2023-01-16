<table class='table table-striped' >
	<thead>
		<tr style="border:none">
			<th class='input-small' style="vertical-align: middle;" rowspan='2'>CD</th>
			<th class='input-xxlarge' style="text-align: center;" colspan='3'>Entradas</th>
			<th class='input-small numeric' style="vertical-align: middle;" rowspan='2'>Saídas</th>
			<th class='input-small numeric' style="vertical-align: middle;" rowspan='2'>Total</th>
			<th class='input-small numeric' style="vertical-align: middle;" rowspan='2'>Veículos</th>
		</tr>
		<tr style="border:none;box-shadow:none">
			<th class='input-small numeric' style="border-top: none">Aprovadas</th>
			<th class='input-small numeric' style="border-top: none">Reprovadas</th>
			<th class='input-small numeric' style="border-top: none">Total</th>
		</tr>
	</thead>
	<tbody>
		<?php $total = 0 ?>
		<?php foreach ($checklists as $checklist): ?>
			<?php
				Comum::somaTotalizador($totais,$checklist[0]);
				$total++; 
			?>
			<tr>
				<td>
					<?php echo $this->Html->link($checklist[0]['refe_descricao'], array('action'=>'checklist_analitico', 'popup' , $checklist[0]['refe_codigo']), array('onclick'=>'return open_popup(this);'))?>
				</td>
				<td class='numeric'><?php echo (!empty($checklist[0]['qtd_aprovadas']) ? $this->Html->link($checklist[0]['qtd_aprovadas'], array('action'=>'checklist_analitico', 'popup' , $checklist[0]['refe_codigo'], 2, 'S'), array('onclick'=>'return open_popup(this);')): '')?></td>
				<td class='numeric'><?php echo (!empty($checklist[0]['qtd_reprovadas']) ? $this->Html->link($checklist[0]['qtd_reprovadas'], array('action'=>'checklist_analitico', 'popup' , $checklist[0]['refe_codigo'], 2, 'N'), array('onclick'=>'return open_popup(this);')): '')?></td>
				<td class='numeric'><?php echo (!empty($checklist[0]['qtd_entradas']) ? $this->Html->link($checklist[0]['qtd_entradas'], array('action'=>'checklist_analitico', 'popup' , $checklist[0]['refe_codigo'], 2), array('onclick'=>'return open_popup(this);')): '')?></td>
				<td class='numeric'><?php echo (!empty($checklist[0]['qtd_saidas']) ? $this->Html->link($checklist[0]['qtd_saidas'], array('action'=>'checklist_analitico', 'popup' , $checklist[0]['refe_codigo'], 1), array('onclick'=>'return open_popup(this);')): '')?></td>
				<td class='numeric'><?php echo (!empty($checklist[0]['qtd_total']) ? $this->Html->link($checklist[0]['qtd_total'], array('action'=>'checklist_analitico', 'popup' , $checklist[0]['refe_codigo']), array('onclick'=>'return open_popup(this);')): '')?></td>
				<td class='numeric'><?php echo (!empty($checklist[0]['qtd_veiculos']) ? $checklist[0]['qtd_veiculos'] : '')?></td>
		</tr>
			
		<?php endforeach; ?>
		
	</tbody>
    <tfoot>
        <tr>
            <td><strong>Total</strong></td>
			<td class='numeric'><?php echo $totais['qtd_aprovadas']?></td>
			<td class='numeric'><?php echo $totais['qtd_reprovadas']?></td>				
			<td class='numeric'><?php echo $totais['qtd_entradas']?></td>
			<td class='numeric'><?php echo $totais['qtd_saidas']?></td>
			<td class='numeric'><?php echo $totais['qtd_total']?></td>
			<td class='numeric'>&nbsp;</td>
        </tr>
    </tfoot>
</table>

<?php echo $this->Js->writeBuffer(); ?>