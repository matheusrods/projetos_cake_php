<table class="table table-striped">
  <thead>
    <tr>
      <th>Retorno</th>
      <th>Contato ( Fone / Email )</th>
      <th>Tipo</th>
      <th>Representante</th>
    </tr>
  </thead>
	<?php foreach ($this->data as $contato): ?>
	  <?php $descricao_contato = $contato['FuncionarioContato']['ddd'].$contato['FuncionarioContato']['descricao']; ?>
	  <?php if (in_array($contato['FuncionarioContato']['codigo_tipo_retorno'], array(1,3,5))): ?>
	  <?php    $descricao_contato = $buonny->telefone($descricao_contato);?>
	  <?php endif; ?>
	  <tr>
	      <td><?php echo $contato['TipoRetorno']['descricao'] ?></td>
	      <td><?php echo $descricao_contato ?></td>
	      <td><?php echo $contato['TipoContato']['descricao'] ?></td>
	      <td><?php echo $contato['FuncionarioContato']['nome'] ?></td>
	  </tr>
	<?php endforeach; ?>
</table>
