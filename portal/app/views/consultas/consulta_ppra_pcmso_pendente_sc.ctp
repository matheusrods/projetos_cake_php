<div class="well">
	Código: <b><?= $CodigoCliente ?></b> | Cliente: <b><?= $NomeCliente ?></b>
</div>

<?php echo $bajax->form('Consulta', 
							array(	'autocomplete' => 'off', 
									'url' => array(	'controller' => 'filtros', 
													'action' => 'filtrar', 
													'model' => 'Consulta', 
													'element_name' => 'pcmso_ppra_pendente_sc'), 
									'divupdate' => '.form-procurar')) ?>
<div class='form-procurar'> 
	<?= $this->element('filtros/pcmso_ppra_pendente_sc') ?>
</div>
<?php echo $this->BForm->end() ?>
<div class='lista'></div>


