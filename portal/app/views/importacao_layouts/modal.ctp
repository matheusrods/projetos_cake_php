<div class="modal fade" id="upload-modal" data-backdrop="static" style="width: 25%; left: 50%; top: 15%;display:none">
	<?php echo $this->BForm->create('Layouts', array('type' => 'file', 'enctype' => 'multipart/form-data', 'url' => array('controller' => $this->name, 'action' => 'incluir'))); ?>
	<div class="modal-dialog modal-sm" style="position: static;">
		<div class="modal-content">
			<div class="modal-header" style="text-align: center;">
				<h3>Novo arquivo</h3>
			</div>

			<div class="modal-body" style="min-height: 95px; display: flex; align-items: center; justify-content: center;">
				<div class="span5" style="width: 100%;padding: 0;margin: 0;">
					<input type="file" id="upload-real" name="arquivo" style="display:none" />
					<div class="content-inputs" style="display: flex;align-items: center;justify-content: center;flex-direction: column;">
						<?php
							$inputValue = "";
							if(isset($_SESSION['FiltrosIntUploadCliente']['codigo_cliente']) == true) {
								$inputValue = $_SESSION['FiltrosIntUploadCliente']['codigo_cliente'];
							}
						?>
						<!-- <label for="codigo_cliente">Código do cliente</label> -->
						<input required style="text-align: center;" type="hidden" name="codigo_cliente" value="<?= $inputValue ?>" />
						<label for="upload-trigger" style="font-size: 14px">Nenhum arquivo selecionado</label>
						<button id="upload-trigger" class="btn btn-success">Upload</button>
					</div>
				</div>
			</div>

		</div>
		<div class="modal-footer">
			<div class="right">
				<button class="btn btn-danger close-modal">CANCELAR</button>
				<button class="btn btn-success save" type="submit">CONFIRMAR</button>
			</div>
		</div>
	</div>
	<?php echo $this->BForm->end(); ?>
</div>