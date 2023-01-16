
<div class="well">
	<?php echo $this->BForm->create('TRefeReferencia', array('action' => 'post', 'url' => array('controller' => 'Referencias','action' => 'consultar_sistema_origem')));?>
		<?php echo $this->BForm->input('refe_codigo', array('class' => 'input-small', 'label' => 'Codigo do Alvo','type' => 'text')) ?>
		<?php echo $this->BForm->submit('Consultar', array('div' => false, 'class' => 'btn btn-success')) ?>
	<?php echo $this->BForm->end() ?>
</div>

<?php if($listagem): ?>
<div class='row-fluid inline'>
	<table class='table table-striped'>
		<thead>
			<th class='input-xxlarge'>Sistema Origem</th>
			<th class='input-small'>Total</th>
		</thead>
		<tbody>
			<?php foreach ($listagem as $origem):?>
				<tr>
					<td><?php echo $origem[0]['sistema_origem']?$origem[0]['sistema_origem']:"<i>(NULL)</i>" ?></td>
					<td><?php echo $origem[0]['total'] ?></td>
				</tr>
			<?php endforeach;?>
		</tbody>
	</table>
</div>
<?php endif; ?>