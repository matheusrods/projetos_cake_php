<table class='table table-striped ocorrencias tablesorter'>
	<thead>
		<th class='input-medium'><?= $this->Html->link('Data', 'javascript:void(0)') ?></th>
		<th><?= $this->Html->link('Descrição', 'javascript:void(0)') ?></th>
		<th class='input-large'><?= $this->Html->link('Usuario Adicionou', 'javascript:void(0)') ?></th>
		<th class='input-mini'>&nbsp;</th>
	</thead>
	<tbody>
	<?php foreach($dados as $voco): ?>
		<tr> 
			<td><?php echo $voco['TVocoViagemOcorrencia']['voco_data_cadastro'] ?></td>
			<td title="<?php echo $voco['TVocoViagemOcorrencia']['voco_descricao'] ?>" >
				<?php echo $this->Buonny->truncate($voco['TVocoViagemOcorrencia']['voco_descricao'], 100); ?>
			</td>
			<td><?php echo $voco['TVocoViagemOcorrencia']['voco_usuario_adicionou'] ?></td>
			<td class="numeric">
				<?php 
					if(date('Y-m-d',strtotime(str_replace('/', '-', $voco['TVocoViagemOcorrencia']['voco_data_cadastro']))) == date('Y-m-d'))
						echo $this->BMenu->linkOnClick('', array('controller' => 'Ocorrencias', 'action' => 'excluir_viagem_ocorrencia', $voco['TVocoViagemOcorrencia']['voco_codigo']), array('class' => 'icon-trash ex-ocorrencia', 'title' => 'Excluir ocorrencia')); 
				?>
			</td>
		</tr>
			
	<?php endforeach ?>	
	</tbody>
</table>
<?php echo $this->Buonny->link_css('tablesorter') ?>
<?php echo $this->Buonny->link_js('jquery.tablesorter.min') ?>
<?php echo $this->Javascript->codeBlock("
	jQuery('table.ocorrencias').tablesorter()
	$(function(){
		$('.ex-ocorrencia').click(function(){
			if(confirm('Excluir a ocorrencia?')){
				var ex = $( this );
				$.ajax({
					url: ex.attr('href'),
					success:function(data){
						if(data){
							alert(data);
						} else {
							listaOcorrencias({$viag_codigo});
						}
					}
				});
			}

			return false;
		});
	});
") ?>