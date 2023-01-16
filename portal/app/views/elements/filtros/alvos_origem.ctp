<span class="label label-info">CDs</span>
<div class="lista-cds control-group <?php echo (!empty($this->validationErrors[$model]['cd_id']) ? 'error' : '')?>">
	<input type="hidden" name="data[<?php echo $model ?>][cd_id]" value="" id="<?php echo $model ?>CdId">
<?php 
//$selecteds=isset($selecteds['RelatorioSm']['cd_id']) ? $selecteds['RelatorioSm']['cd_id'] : array(); 
$selecteds = !empty($this->data[$model]['cd_id']) ? $this->data[$model]['cd_id'] : array();
?>
		<?php foreach($cds as $cd): ?>
		<?php $selected = in_array($cd['TRefeReferencia']['refe_codigo'], $selecteds) > 0 ?>
		<?php echo $this->BForm->label($model.'.cd_id.', 
			$this->BForm->checkbox($model.'.cd_id.'.$cd['TRefeReferencia']['refe_codigo'], array(
					'value' => $cd['TRefeReferencia']['refe_codigo'], 
					'checked' => $selected, 
					'hiddenField'=>false
				)
				).trim($cd['TRefeReferencia']['refe_descricao']).(!empty($cd[0]['quantidade_veiculos']) ? " -<span class='qtd-veiculos'>".$cd[0]['quantidade_veiculos']."</span>" : ""), array('class' => 'checkbox inline input-xlarge', 'escape'=>false)); ?>
	<?php endforeach; ?>
	<?php echo $this->BForm->error($model.'.cd_id.'); ?>
</div>