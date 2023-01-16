<h5> Sinístro</h5>
<div id="cockpit-sinistro" >
	<table class='table table-striped'>
		<thead>
			<th><?= $this->Html->link('Natureza', 'javascript:void(0)') ?></th>
			<th class='input-small numeric '><?= $this->Html->link('Qtd Eventos', 'javascript:void(0)') ?></th>
			<th class='input-xlarge '><?= $this->Html->link('Data Último Sinístro', 'javascript:void(0)') ?></th>
			
		</thead>
		<tbody></tbody>

	</table>
</div>
<?php echo $this->Javascript->codeBlock("carregarCockpitSinistro('#cockpit-sinistro');"); ?>