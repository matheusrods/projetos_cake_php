<div class='form-procurar well'>
	<?php echo $this->BForm->create('RegistroTelecom', array('type' => 'file', 'autocomplete' => 'off', 'url' => array('controller' => 'registros_telecom', 'action' => 'importar_arquivo'))); ?>
		<div class="row-fluid inline">
			<?php echo $this->BForm->input('tipo_arquivo', array('label' => 'Modelo de Importação', 'class' => 'input-medium', 
					'options' => $operadoras, 
					'empty' => 'Selecione')); ?>
			
			<!-- DOCUMENTAÇÃO -->
			<?php $css = (isset($this->data['RegistroTelecom']['tipo_arquivo']) && $this->data['RegistroTelecom']['tipo_arquivo'] == 4)?NULL:'display:none'; ?>
			<div id="doc-4" class="documentacao control-group" style='<?= $css ?>' >
				<label>&nbsp;</label>
				<?=$this->Html->link('<i class="icon-file"></i>Documentação Tarifador', $this->webroot.'../../arquivos/documentos_telecom/tarifador_v01.doc', array('escape' => false, 'target' => '_blank',  'title' => 'Visualizar documentação para montagem do modelo de importação')); ?>
			</div>					
		</div>
		<div class="row-fluid inline">
			<?php echo $this->BForm->input('arquivo', array('type'=>'file', 'label' => false,'accept'=>'.xls')); ?>
		</div>
		<?php echo $this->BForm->submit('Importar', array('div' => false, 'class' => 'btn btn-primary')); ?>
	<?php echo $this->BForm->end(); ?>
</div>

<?php if(isset($resultados) && !empty($resultados)): ?>
<div id='valores-resultados' class='well'>
	<h4>Retorno de erro</h4>
	<table class="table table-striped">
		<thead>
				<th class="input-small">Número da Linha</th>
				<th class="input-small">Contato</th>
				<th class="input-small">Tipo Contato</th>
				<th class="input-large">Informação do erro</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ($resultados as $key => $resultado):  ?>
				<tr>
					<td><?=$resultado['linha']?></td>
					<td><?=utf8_encode($resultado['telefone'])?></td>
					<td><?=(isset($resultado['tipo_retorno']) ? $resultado['tipo_retorno']:'')?></td>
					<td><?=$resultado['mensagem']?></td>
				</tr>
		<?php endforeach ?>
		</tbody>
	</table>
</div>
<?php endif ?>
<?php echo $this->Javascript->codeBlock('
	jQuery(document).ready(function(){

		$(document).on("change","#RegistroTelecomTipoArquivo",function(){
			if($(this).val()==4) {
				$(".documentacao").hide();
				$("#doc-"+$(this).val()).show();
			} else {
				$(".documentacao").hide();
			}
		});

	});', false);
?>
	