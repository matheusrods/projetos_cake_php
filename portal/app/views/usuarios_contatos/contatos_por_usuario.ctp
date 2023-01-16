<table class="table table-striped">
  <thead>
    <tr>
      <th>Retorno</th>
      <th>Contato</th>
      <th width="50"></th>
    </tr>
  </thead>
	<?php foreach ($this->data as $contato): ?>
	  <?php $descricao_contato = $contato['UsuarioContato']['ddd'].$contato['UsuarioContato']['descricao']; ?>
	  <?php if (in_array($contato['UsuarioContato']['codigo_tipo_retorno'], array(1,3,5,7,8,9,11))): ?>
	  <?php $descricao_contato = $buonny->telefone($descricao_contato);?>
	  <?php endif; ?>
	  <tr>
	      <td><?php echo $contato['TipoRetorno']['descricao'] ?></td>
	      <td><?php echo $descricao_contato ?></td>
	      <td>
	          <?php echo $html->link('', array('controller' => 'usuarios_contatos', 'action' => 'editar', $contato['UsuarioContato']['codigo']), array('class' => 'icon-edit', 'title' => 'Editar', 'onclick' => 'return open_dialog(this, "Contato", 960)')) ?>
	          <?php echo $html->link('', 'javascript:void(0)', array('class' => 'icon-trash excluir-usuario-contato', 'title' => 'Excluir', 'onclick' => "excluir_usuario_contato({$contato['UsuarioContato']['codigo']}, {$contato['UsuarioContato']['codigo_usuario']})")) ?>
	      </td>
	  </tr>
	<?php endforeach; ?>
</table>
<?php
    if($session->read('Message.flash.params.type') == MSGT_SUCCESS){
        $session->delete('Message.flash');
    }
?>