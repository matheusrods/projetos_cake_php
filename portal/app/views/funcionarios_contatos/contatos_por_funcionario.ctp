<?php if(is_array($this->data) && count($this->data) >= 1) : ?>
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
		  <?php $descricao_contato = $contato['FuncionarioContato']['ddd'].$contato['FuncionarioContato']['descricao']; ?>
		  <?php if (in_array($contato['FuncionarioContato']['codigo_tipo_retorno'], array(1,3,5))): ?>
		  <?php    $descricao_contato = $buonny->telefone($descricao_contato);?>
		  <?php endif; ?>
		  <tr>
		      <td><?php echo $contato['TipoRetorno']['descricao'] ?></td>
		      <td><?php echo $descricao_contato ?></td>
		      <td><?php echo $contato['TipoContato']['descricao'] ?></td>
		      <td><?php echo $contato['FuncionarioContato']['nome'] ?></td>
		      <td>
		          <?php echo $html->link('', array('controller' => 'funcionarios_contatos', 'action' => 'editar', $contato['FuncionarioContato']['codigo']), array('class' => 'icon-edit', 'title' => 'Editar', 'onclick' => 'return open_dialog(this, "Contato", 960)')) ?>
		          <?php echo $html->link('', 'javascript:void(0)', array('class' => 'icon-trash excluir-funcionario-contato', 'title' => 'Excluir', 'onclick' => "excluir_funcionario_contato({$contato['FuncionarioContato']['codigo']}, {$contato['FuncionarioContato']['codigo_funcionario']})")) ?>
		      </td>
		  </tr>
		<?php endforeach; ?>
	</table>
<?php else: ?>
	<div class="alert">Nenhum resultado encontrado.</div>
<?php endif; ?>