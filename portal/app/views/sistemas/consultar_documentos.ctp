<div class='row-fluid inline'>
	<table class='table table-striped'>
		<thead>
			<th class='input-small'>Arquivos</th>
			<th  align ="center" width="5">Ações</th>
		</thead>
			<tbody>
				<?php  foreach ($lista_arquivos as $arquivo => $url):?> 
					<tr>
						<td width="400">
							<?php echo $this->Html->link($arquivo,array('controller' => 'Sistemas','action' =>'download_documento',$arquivo,rand()));?>
						</td>
						<td >
							<?php 
								if ($temPermissao) {
									echo $this->Html->link('',array('controller'=>'Sistemas', 'action' => 'excluir_documentos_rh', $arquivo), array('class' => 'icon-trash','title'=>'Excluir'));
								}
							?>
						</td>			
					</tr>
			 	<?php endforeach ?>
			</tbody>
	</table>
</div>

