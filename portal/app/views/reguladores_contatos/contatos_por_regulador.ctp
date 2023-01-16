<div id="regulador-contatos">
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
		  <?php $descricao_contato = $contato['ReguladorContato']['ddd'].$contato['ReguladorContato']['descricao']; ?>
		  <?php if (in_array($contato['ReguladorContato']['codigo_tipo_retorno'], array(1,3,5))): ?>
		  <?php    $descricao_contato = $buonny->telefone($descricao_contato);?>
		  <?php endif; ?>
		  <tr>
		      <td><?php echo $contato['TipoRetorno']['descricao'] ?></td>
		      <td><?php echo $descricao_contato ?></td>
		      <td><?php echo $contato['TipoContato']['descricao'] ?></td>
		      <td><?php echo $contato['ReguladorContato']['nome'] ?></td>
		      <td>
		          <?php echo $html->link('', array('controller' => 'reguladores_contatos', 'action' => 'editar', $contato['ReguladorContato']['codigo']), array('class' => 'icon-edit', 'title' => 'Editar', 'onclick' => 'return open_dialog(this, "Contato", 960)')) ?>
		          <?php echo $html->link('', 'javascript:void(0)', array('class' => 'icon-trash excluir-cliente-contato', 'title' => 'Excluir', 'onclick' => "excluir_regulador_contato({$contato['ReguladorContato']['codigo']},{$contato['ReguladorContato']['codigo_regulador']})")) ?>
		      </td>
		  </tr>
		<?php endforeach; ?>
	</table>
</div>
<?php echo $javascript->codeBlock("
function excluir_regulador_contato(codigo_regulador_contato, codigo_regulador ) {
    if (confirm('Deseja realmente excluir ?'))
		jQuery.ajax({
		    type: 'POST',
			url: baseUrl + 'reguladores_contatos/excluir/' + codigo_regulador_contato + '/' + Math.random(),
			success: function(data) {
				var div = jQuery('#regulador-contatos');
				bloquearDiv(div);
				div.load(baseUrl + 'reguladores_contatos/contatos_por_regulador/' + codigo_regulador + '/' + Math.random() );
			}
		});
	}");?>