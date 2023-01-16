<div id="prestador-contatos">
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
		  <?php $descricao_contato = $contato['PrestadorContato']['ddd'].$contato['PrestadorContato']['descricao']; ?>
		  <?php if (in_array($contato['PrestadorContato']['codigo_tipo_retorno'], array(1,3,5))): ?>
		  <?php    $descricao_contato = $buonny->telefone($descricao_contato);?>
		  <?php endif; ?>
		  <tr>
		      <td><?php echo $contato['TipoRetorno']['descricao'] ?></td>
		      <td><?php echo $descricao_contato ?></td>
		      <td><?php echo $contato['TipoContato']['descricao'] ?></td>
		      <td><?php echo $contato['PrestadorContato']['nome'] ?></td>
		      <td>
		          <?php echo $html->link('', array('controller' => 'prestadores_contatos', 'action' => 'editar', $contato['PrestadorContato']['codigo']), array('class' => 'icon-edit', 'title' => 'Editar', 'onclick' => 'return open_dialog(this, "Contato", 960)')) ?>
		          <?php echo $html->link('', 'javascript:void(0)', array('class' => 'icon-trash excluir-cliente-contato', 'title' => 'Excluir', 'onclick' => "excluir_prestador_contato({$contato['PrestadorContato']['codigo']},{$contato['PrestadorContato']['codigo_prestador']})")) ?>
		      </td>
		  </tr>
		<?php endforeach; ?>
	</table>
</div>
<?php echo $javascript->codeBlock("
function excluir_prestador_contato(codigo_prestador_contato, codigo_prestador ) {
    if (confirm('Deseja realmente excluir ?'))
		jQuery.ajax({
		    type: 'POST',
			url: baseUrl + 'prestadores_contatos/excluir/' + codigo_prestador_contato + '/' + Math.random(),
			success: function(data) {
				var div = jQuery('#prestador-contatos');
				bloquearDiv(div);
				div.load(baseUrl + 'prestadores_contatos/contatos_por_prestador/' + codigo_prestador + '/' + Math.random() );
			}
		});
	}");?>