<h5> Embarcador / Transportador</h5>

<div id="cockpit-emba-tran" >
	<table class='table table-striped'>
		<thead>
			<tr>
				<th class='input-xlarge'><?= $this->Html->link('Transportador', 'javascript:void(0)') ?></th>
				<th class='input-xlarge'><?= $this->Html->link('Embarcador', 'javascript:void(0)') ?></th>
				<th class='input-small'><?= $this->Html->link('Tipo Veiculo', 'javascript:void(0)') ?></th>
				<th class='input-small numeric'><?= $this->Html->link('Qtd Viagens', 'javascript:void(0)') ?></th>
				<th class='input-medium numeric'><?= $this->Html->link('Maior Valor', 'javascript:void(0)') ?></th>
				<th class='input-medium numeric'><?= $this->Html->link('Total Transportado', 'javascript:void(0)') ?></th>
			</tr>
		</thead>
		<tbody></tbody>
	</table>
</div>
<?php echo $this->Javascript->codeBlock("carregarCockpitEmbaTran('#cockpit-emba-tran');"); ?>