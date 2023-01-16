<?php echo $this->BForm->create('Usuario'); ?>
<?php if(!empty($alertas_agrupados)):?>
	<?php foreach ($alertas_agrupados as $key => $value):?>
	    <div class="row-fluid inline">
	        <!-- <div class="alert alert-info"><?= substr($key, 12) ;?></div> -->
			<?php echo $this->BForm->input($key, array('multiple' => 'checkbox', 'options' => $value, 'label' => '', 'class' => 'checkbox inline input-large')); ?>
	    </div>
	<?php endforeach;?>   
<?php else:?>
	<div class="alert">Selecione um perfil para a exibição dos alertas</div>
<?php endif;?>	