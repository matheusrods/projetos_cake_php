<h5>TELECONSULT</h5>

<div id="cockpit-teleconsult" >
	<table class='table table-striped'>
		<thead>
			<tr>
				<th class='input-small numeric'><?= $this->Html->link('Qtd de Cadastros', 'javascript:void(0)') ?></th>
				<th class='input-small numeric'><?= $this->Html->link('Qtd de Consultas', 'javascript:void(0)') ?></th>
				<th class='input-small numeric'><?= $this->Html->link('Qtd de Alterações', 'javascript:void(0)') ?></th>
				<th class='input-small numeric'><?= $this->Html->link('Qtd de Renovações', 'javascript:void(0)') ?></th>
			</tr>
		</thead>
		<tbody></tbody>
	</table>
</div>
<?php echo $this->Javascript->codeBlock("carregarCockpitTELECONSULT('#cockpit-teleconsult');"); ?>