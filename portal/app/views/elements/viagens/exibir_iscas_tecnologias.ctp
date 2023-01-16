<table class='table table-striped iscas tablesorter'>
	<thead>
		<th class='input-mini'><?= $this->Html->link('Tecnologia', 'javascript:void(0)') ?></th>
		<th class='input-mini'><?= $this->Html->link('Terminal', 'javascript:void(0)') ?></th>
	</thead>
	<?php foreach($dados as $iscas): ?>
		<tr> 
			<td><?php echo $iscas['TTecnTecnologia']['tecn_descricao'] ?></td>
			<td><?php echo $iscas['TTermTerminal']['term_numero_terminal'] ?></td>
		</tr>
			
	<?php endforeach ?>	
</table>
<?php echo $this->Buonny->link_css('tablesorter') ?>
<?php echo $this->Buonny->link_js('jquery.tablesorter.min') ?>
<?php echo $this->Javascript->codeBlock("jQuery('table.iscas').tablesorter()") ?>