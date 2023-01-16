<table class="table table-striped">
  <thead>
    <tr>
      <th>Retorno</th>
      <th>Contato ( Fone / Email )</th>
    </tr>
  </thead>
	<?php foreach ($this->data as $contato): ?>
	  <?php $descricao_contato = $contato['UsuarioContato']['ddd'].$contato['UsuarioContato']['descricao']; ?>
	  <?php if (in_array($contato['UsuarioContato']['codigo_tipo_retorno'], array(1,3,5))): ?>
	  <?php    $descricao_contato = $buonny->telefone($descricao_contato);?>
	  <?php endif; ?>
	  <tr>
	      <td><?php echo $contato['TipoRetorno']['descricao'] ?></td>
	      <td><?php echo $descricao_contato ?></td>
	  </tr>
	<?php endforeach; ?>
</table>
