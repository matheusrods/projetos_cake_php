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
	  <?php $descricao_contato = $contato['CorretoraContato']['ddd'].$contato['CorretoraContato']['descricao']; ?>
	  <?php if (in_array($contato['CorretoraContato']['codigo_tipo_retorno'], array(1,3,5))): ?>
	  <?php    $descricao_contato = $buonny->telefone($descricao_contato);?>
	  <?php endif; ?>
	  <tr>
	      <td><?php echo $contato['TipoRetorno']['descricao'] ?></td>
	      <td><?php echo $descricao_contato ?></td>
	      <td><?php echo $contato['TipoContato']['descricao'] ?></td>
	      <td><?php echo $contato['CorretoraContato']['nome'] ?></td>
	      <td>
	          <?php echo $html->link('', array('controller' => 'corretoras_contatos', 'action' => 'editar', $contato['CorretoraContato']['codigo']), array('class' => 'icon-edit', 'title' => 'Editar', 'onclick' => 'return open_dialog(this, "Contato", 960)')) ?>
	          
	          <?php echo $html->link('', 'javascript:void(0)', array('class' => 'icon-trash excluir_corretora_contato', 'title' => 'Excluir', 'onclick' => "excluir_corretora_contato({$contato['CorretoraContato']['codigo']},{$contato['CorretoraContato']['codigo_corretora']},'div#contatos-corretora')"))?>
	      </td>
	  </tr>
	<?php endforeach; ?>
</table>
