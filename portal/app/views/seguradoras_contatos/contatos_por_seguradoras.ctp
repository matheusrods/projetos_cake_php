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
	<?php foreach ($contatos as $contato): ?>
	  <?php $descricao_contato = $contato['SeguradoraContato']['ddd'].$contato['SeguradoraContato']['descricao']; ?>
	  <?php if (in_array($contato['SeguradoraContato']['codigo_tipo_retorno'], array(1,3,5))): ?>
	  <?php    $descricao_contato = $buonny->telefone($descricao_contato);?>
	  <?php endif; ?>
	  <tr>
	      <td><?php echo $contato['TipoRetorno']['descricao'] ?></td>
	      <td><?php echo $descricao_contato ?></td>
	      <td><?php echo $contato['TipoContato']['descricao'] ?></td>
	      <td><?php echo $contato['SeguradoraContato']['nome'] ?></td>
	      <td>
	          <?php echo $html->link('', array('controller' => 'seguradoras_contatos', 'action' => 'editar', $contato['SeguradoraContato']['codigo']), array('class' => 'icon-edit', 'title' => 'Editar', 'onclick' => 'return open_dialog(this, "Contato", 960)')) ?>
	          <?php echo $html->link('', 'javascript:void(0)', array('class' => 'icon-trash excluir-cliente-contato', 'title' => 'Excluir', 'onclick' => "excluir_seguradora_contatos({$contato['SeguradoraContato']['codigo']},{$contato['SeguradoraContato']['codigo_seguradora']},'div#contatos-seguradoras')")) ?>
	      </td>
	  </tr>
	<?php endforeach; ?>
</table>
