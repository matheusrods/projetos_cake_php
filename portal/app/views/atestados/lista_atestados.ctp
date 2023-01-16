<div class='inline well'>
	<?php echo $this->BForm->input('Cliente.razao_social', array('value' => $funcionario['Cliente']['razao_social'], 'class' => 'input-xlarge', 'label' => 'Unidade' , 'readonly' => true, 'type' => 'text')); ?>
	<?php echo $this->BForm->input('Setor.descricao', array('value' => $funcionario['Setor']['descricao'], 'class' => 'input-xlarge', 'label' => 'Setor', 'readonly' => true, 'type' => 'text')); ?>
	<?php echo $this->BForm->input('Cargo.descricao', array('value' => $funcionario['Cargo']['descricao'], 'class' => 'input-xlarge', 'label' => 'Cargo' , 'readonly' => true, 'type' => 'text')); ?>
	<?php echo $this->BForm->input('Funcionario.nome', array('value' => $funcionario['Funcionario']['nome'], 'class' => 'input-xlarge', 'label' => 'Funcionario' , 'readonly' => true, 'type' => 'text')); ?>
	<div style="clear: both;"></div>
</div>

<div class="row-fluid inline text-right control-group">
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', array('action' => 'incluir', $this->passedArgs[0], $this->passedArgs[1]), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Incluir')); ?>
</div>
<?php if(isset($lista_atestados) && count($lista_atestados)) : ?>
	<div id="listagem">
		<table class="table table-striped">
		    <thead>
		        <tr>
					<td>Código</td>
					<td>Tipo</td>
					<td>Data Afastamento</td>
					<td>Data Retorno</td>
					<td>Arquivo</td>
					<td>Ações</td>
				</tr>
			</thead>
			<tbody>
				<?php foreach($lista_atestados as $key => $atestado) : ?>					
					<tr>
						<td><?php echo $atestado['Atestado']['codigo']; ?></td>
						<td><?php echo $atestado['Atestado']['tipo_atestado'] == 2 ? 'Afastamento Temporário' : 'Atestado Saúde'; ?></td>
						<td><?php echo $atestado['Atestado']['data_afastamento_periodo']; ?></td>
						<td><?php echo $atestado['Atestado']['data_retorno_periodo']; ?></td>
						<td>
						<?php 
							if(!empty($atestado['Atestado']['anexo'])) {

								if(strstr($atestado['Atestado']['anexo'],'https://api.rhhealth.com.br')) {

				                    echo $this->Html->link($this->Html->tag('i','',array('class' => 'icon-file btn-anexos visualiza_anexo')).' Ver Arquivo', $atestado['Atestado']['anexo'], array('escape' => false, 'target' => '_blank', 'title' => 'Visualizar anexo do item'));

				                }
				                else {

									echo $this->Html->link($this->Html->tag('i','',array('class' => 'icon-file btn-anexos visualiza_anexo')).' Ver Arquivo', '/files/anexos_atestados/'.$atestado['Atestado']['codigo'].'/'.basename($atestado['Atestado']['anexo']), array('escape' => false, 'target' => '_blank', 'title' => 'Visualizar anexo do item'));
									
				                }

								echo " | ";

								echo $this->Html->link($this->Html->tag('i','',array('class' => 'icon-remove')).' Remover Arquivo', array('controller' => 'atestados', 'action' => 'excluir_anexo', $atestado['Atestado']['codigo']), array('escape' => false, 'title' => 'Excluir Anexo'),'Confirma exclusão do anexo?');
							} 
						?>
						</td>

						<td>
							<?php echo $html->link('', array('controller' => 'atestados', 'action' => 'editar', $this->passedArgs[0], $this->passedArgs[1], $atestado['Atestado']['codigo']), array('class' => 'icon-edit', 'title' => 'Editar')) ?>
							<?php echo $html->link('', array('controller' => 'atestados', 'action' => 'excluir', $atestado['Atestado']['codigo'], $this->passedArgs[0], $this->passedArgs[1]), array('class' => 'icon-trash', 'title' => 'Excluir Atestado'), 'Confirma exclusão?'); ?>
							<?php echo $html->link('','javascript:void(0);', array('class' => 'icon-upload', 'title' => 
								'Anexo', 'onclick' => 'anexo_atestado('.$atestado['Atestado']['codigo'].',1)')); ?>

							<?php echo $html->link('','javascript:void(0);', array('class' => 'icon-eye-open', 'title' => 
								'Log Anexo', 'onclick' => 'visualiza_log_atestado('.$atestado['Atestado']['codigo'].')')); ?>	

						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<div class="modal fade" id="modal_anexo" data-backdrop="static"></div>
<?php else: ?>
	<div class="alert">Nenhum resultado encontrado.</div>
<?php endif; ?>

<div class='form-actions well'>
	<?php echo $html->link('Voltar', array('controller' => 'atestados', 'action' => 'index'), array('class' => 'btn btn-default')); ?>
</div>
	
<?php echo $this->Javascript->codeBlock('
	jQuery(document).ready(function() {
		setup_mascaras();
	});

	function anexo_atestado(codigo_atestado,exibe) {
		if(exibe) {
			var div = jQuery("div#modal_anexo");
			bloquearDiv(div);
			div.load(baseUrl + "atestados/upload_anexo_atestado/" + codigo_atestado + "/" + Math.random());
	
			$("#modal_anexo").css("z-index", "1050");
			$("#modal_anexo").modal("show");

		} else {
			$("#modal_anexo").css("z-index", "-1");
			$("#modal_anexo").modal("hide");
		}
	}

   	function visualiza_log_atestado(codigo_atestado){
        var janela = window_sizes();
        window.open(baseUrl + "atestados/listagem_log_anexo/" + codigo_atestado + "/" + Math.random(), janela, "scrollbars=yes,menubar=no,height="+(janela.height-400)+",width="+(janela.width-80)+",resizable=yes,toolbar=no,status=no");
    }

		
'); ?>