<div class='form-procurar well'>
	<?php echo $this->BForm->create('MWebsm', array('type' => 'file', 'autocomplete' => 'off', 'url' => array('controller' => 'solicitacoes_monitoramento', 'action' => 'importar_txt'))); ?>
		<div class="row-fluid inline">
			<?php echo $this->BForm->input('tipo_arquivo', array('label' => 'Modelo de Importação', 'class' => 'input-medium', 'options' => array(/*'1' => 'ESL',*/ 
					'2'  => 'TransSat', 
					'3'  => 'PortServer', 
					'4'  => 'GPA',
					'12' => 'GPA2',
					'5'  => 'LOTE', 
					'6'  => 'CLA', 
					'7'  => 'RODO',
					'11'  => 'Romaneio(csv)',
					'8'  => 'PIRA', 
					'9'  => 'AUR',
					 ), 'empty' => 'Selecione')); ?>
			
			<!-- DOCUMENTAÇÃO -->
			<?php $css = (isset($this->data['MWebsm']['tipo_arquivo']) && $this->data['MWebsm']['tipo_arquivo'] == 1)?NULL:'display:none'; ?>
			<div id="doc-1" class="documentacao control-group" style='<?= $css ?>' >
				<label>&nbsp;</label>
				<?php //echo $this->Html->link('<i class="icon-file"></i>Documentação ESL', '../../arquivos/documentos_integracoes/lote_v01.doc', array('escape' => false, 'target' => '_blank',  'title' => 'Visualizar documentação da integração')); ?>
			</div>

			<?php $css = (isset($this->data['MWebsm']['tipo_arquivo']) && $this->data['MWebsm']['tipo_arquivo'] == 2)?NULL:'display:none'; ?>
			<div id="doc-2" class="documentacao control-group" style='<?= $css ?>' >
				<label>&nbsp;</label>
				<?php //echo $this->Html->link('<i class="icon-file"></i>Documentação TranSat', '../../arquivos/documentos_integracoes/lote_v01.doc', array('escape' => false, 'target' => '_blank',  'title' => 'Visualizar documentação da integração')); ?>
			</div>

			<?php $css = (isset($this->data['MWebsm']['tipo_arquivo']) && $this->data['MWebsm']['tipo_arquivo'] == 3)?NULL:'display:none'; ?>
			<div id="doc-3" class="documentacao control-group" style='<?= $css ?>' >
				<label>&nbsp;</label>
				<?php echo $this->Html->link('<i class="icon-file"></i>Documentação PortServer', $this->webroot.'../../arquivos/documentos_integracoes/PortServer_01.doc', array('escape' => false, 'target' => '_blank',  'title' => 'Visualizar documentação da integração')); ?>
			</div>

			<?php $css = (isset($this->data['MWebsm']['tipo_arquivo']) && $this->data['MWebsm']['tipo_arquivo'] == 4)?NULL:'display:none'; ?>
			<div id="doc-4" class="documentacao control-group" style='<?= $css ?>' >
				<label>&nbsp;</label>
				<?php echo $this->Html->link('<i class="icon-file"></i>Documentação GPA', $this->webroot.'../../arquivos/documentos_integracoes/gpa_v01.doc', array('escape' => false, 'target' => '_blank',  'title' => 'Visualizar documentação da integração')); ?>
			</div>

			<?php $css = (isset($this->data['MWebsm']['tipo_arquivo']) && $this->data['MWebsm']['tipo_arquivo'] == 5)?NULL:'display:none'; ?>
			<div id="doc-5" class="documentacao control-group" style='<?= $css ?>' >
				<label>&nbsp;</label>
				<?php echo $this->Html->link('<i class="icon-file"></i>Documentação LOTE', $this->webroot.'../../arquivos/documentos_integracoes/lote_v01.doc', array('escape' => false, 'target' => '_blank',  'title' => 'Visualizar documentação da integração')); ?>
			</div>

			<?php $css = (isset($this->data['MWebsm']['tipo_arquivo']) && $this->data['MWebsm']['tipo_arquivo'] == 6)?NULL:'display:none'; ?>
			<div id="doc-6" class="documentacao control-group" style='<?= $css ?>' >
				<label>&nbsp;</label>
				<?php echo $this->Html->link('<i class="icon-file"></i>Documentação', $this->webroot.'../../arquivos/documentos_integracoes/claro_v01.doc', array('escape' => false, 'target' => '_blank',  'title' => 'Visualizar documentação da integração')); ?>
			</div>

			<?php $css = (isset($this->data['MWebsm']['tipo_arquivo']) && $this->data['MWebsm']['tipo_arquivo'] == 12)?NULL:'display:none'; ?>
			<div id="doc-12" class="documentacao control-group" style='<?= $css ?>' >
				<label>&nbsp;</label>
				<?php echo $this->Html->link('<i class="icon-file"></i>Documentação GPA2', $this->webroot.'../../arquivos/documentos_integracoes/gpa2_v01.doc', array('escape' => false, 'target' => '_blank',  'title' => 'Visualizar documentação da integração')); ?>
			</div>

			<!-- DOCUMENTAÇÃO FIM -->	

		</div>
		<div class="row-fluid inline">
			<?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', false,'MWebsm') ?>
			<?php echo $this->BForm->input('arquivo', array('type'=>'file', 'label' => false)); ?>
		</div>
		<div id="hidden" class="row-fluid inline" style="display:none">
			<?php if ($readonly_alvo): ?>
				<?php echo $this->BForm->hidden('refe_codigo'); ?>
				<?php echo $this->BForm->input('refe_codigo_visual',Array('class'=>'input-large','label'=>false,'readonly'=>true)); ?>

			<?php else: ?>
				<?php echo $this->Buonny->input_referencia($this, '#MWebsmCodigoCliente', 'MWebsm','refe_codigo',false,'Alvo Origem') ?>
			<?php endif; ?>
			<div id="divChkRetorno" class="row-fluid inline" style="display:none">
				<?php echo $this->BForm->input('retorno', array('type'=>'checkbox','value'=>'S', 'label' => 'Monitora Retorno')); ?>
			</div>
		</div>
		<?php echo $this->BForm->submit('Importar', array('div' => false, 'class' => 'btn btn-primary')); ?>
	<?php echo $this->BForm->end(); ?>
</div>
<?php if (isset($log) && empty($log['erro'])): ?>
	<table class="table table-striped">
		<thead>
				<th class="input-large">Pedido</th>
				<th>Status</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ($log as $placa => $status):  ?>
			<?php if (!is_array($status)):  ?>
				<tr>
					<td><?php echo $placa; ?></td>
					<td><?php echo $status; ?></td>
				</tr>
			<?php endif ?>
		<?php endforeach ?>
		</tbody>
	</table>
<?php endif ?>
<?php if (isset($log['erro'])): ?>
	<table class="table table-striped">
		<thead>
				<th class="input-large">Pedido</th>
				<th>Status</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ($log['erro'] as $erro):  ?>
				<tr>
					<td><?php echo $erro[0]; ?></td>
					<td><?php echo "<b>Endereço não localizado ou inexistente, favor verificar: </b>".$erro[1]; ?></td>
				</tr>
		<?php endforeach ?>
				<th colspan="2">
					Tente separar endereço e o número, com uma vírgula. <br> Exemplo: Alameda dos Guatas <font color = 'red'>,</font> 102   
				</th>
		</tbody>
	</table>
<?php endif ?>
<?php echo $this->Javascript->codeBlock('
	jQuery(document).ready(function(){
		$("form").submit(function(){
			$(".btn").addClass("disabled");
		});
		$(document).on("change","#MWebsmTipoArquivo",function(){
			if($(this).val()) {
				$(".documentacao").hide();
				$("#doc-"+$(this).val()).show();

			} else {
				$(".documentacao").hide();
			}
		});

		hidden("'.$this->data['MWebsm']['tipo_arquivo'].'");

		$("#MWebsmTipoArquivo").change(function(){
			hidden($(this).val());
		});

		$("#MWebsmCodigoCliente").change(function(){
			hidden($("#MWebsmTipoArquivo").val());
		});

		function hidden(valor){
			var codigo_cliente = $("#MWebsmCodigoCliente").val();
			var hidden_area = $("#hidden");
			if(valor == 4 || valor == 6 || valor == 7 || valor == 8 || valor == 9 || valor == 10 || valor == 11 || valor == 12) {
				hidden_area.css({"display":"block"});
				var display = ((valor==8 || valor==12) && codigo_cliente!="" && codigo_cliente!="23096" ?true:false);
				if (display) {
					$("#divChkRetorno").show();
				} else {
					$("#divChkRetorno").hide();
				}
			}
			else
				hidden_area.css({"display":"none"});
		}
	});', false);
?>
	

