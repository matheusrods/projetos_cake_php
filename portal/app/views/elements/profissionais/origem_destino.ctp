<h5> Cidades Origens / Destinos</h5>
<div id="cockpit-origem-destino" >
	<table class='table table-striped'>
		
		<thead>
			<th><?= $this->Html->link('Origem', 'javascript:void(0)') ?></th>
			<th><?= $this->Html->link('Destino', 'javascript:void(0)') ?></th>
			<th class='input-small numeric'><?= $this->Html->link('Qtd', 'javascript:void(0)') ?></th>
		</thead>
		<tbody></tbody>

	</table>
</div>
<?php echo $this->Javascript->codeBlock("carregarCockpitOrigemDestino('#cockpit-origem-destino');"); ?>