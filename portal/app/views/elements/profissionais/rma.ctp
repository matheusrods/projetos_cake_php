<h5>RMA</h5>

<div id="cockpit-rma" >
	<table class='table table-striped'>
		<thead>
			<tr>
				<th class='input-xlarge'><?= $this->Html->link('Tipo RMA', 'javascript:void(0)') ?></th>
				<th class='input-small numeric'><?= $this->Html->link('Qtd', 'javascript:void(0)') ?></th>
			</tr>
		</thead>
		<tbody></tbody>
	</table>
</div>
<?php echo $this->Javascript->codeBlock("carregarCockpitRMA('#cockpit-rma');"); ?>