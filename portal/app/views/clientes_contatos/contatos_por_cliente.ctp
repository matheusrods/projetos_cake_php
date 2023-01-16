<table class="table table-striped">
  <thead>
    <tr>
      <th>Retorno</th>
      <th>Contato ( Fone / Email )</th>
      <th>Tipo</th>
      <th>Representante</th>
      <th></th>
    </tr>
  </thead>
	<?php foreach ($this->data as $contato): ?>
	  <?php $descricao_contato = $contato['ClienteContato']['ddd'].$contato['ClienteContato']['descricao']; ?>
	  <?php if (in_array($contato['ClienteContato']['codigo_tipo_retorno'], array(1,3,5))): ?>
	  <?php    $descricao_contato = $buonny->telefone($descricao_contato);?>
	  <?php endif; ?>
	  <tr>
	      <td><?php echo $contato['TipoRetorno']['descricao'] ?></td>
	      <td><?php echo $descricao_contato ?></td>
	      <td><?php echo $contato['TipoContato']['descricao'] ?></td>
	      <td><?php echo $contato['ClienteContato']['nome'] ?></td>
	      <td>
	          <?php echo $html->link('', array('controller' => 'clientes_contatos', 'action' => 'editar', $contato['ClienteContato']['codigo']), array('class' => 'icon-edit', 'title' => 'Editar', 'onclick' => 'return open_dialog(this, "Contato", 960)')) ?>
	          <?php echo $html->link('', 'javascript:void(0)', array('class' => 'icon-trash excluir-cliente-contato', 'title' => 'Excluir', 'onclick' => "excluir_cliente_contato({$contato['ClienteContato']['codigo']}, {$contato['ClienteContato']['codigo_cliente']})")) ?>
	      </td>
	  </tr>
	<?php endforeach; ?>
</table>

