<style>
	.form-check.form-check-inline input {
    margin: 0px 8px 0 9px;
}
.form-check.form-check-inline {
    display: inline-block;
    margin-bottom: 15px;
}
</style>
<div class='well'>
	<?php echo $this->BForm->create('ClienteValidador', array('method'=> 'post', 'type' => 'file', 'enctype' => 'multipart/form-data', 'url' => array('controller' => 'clientes','action' => 'config_cliente_validador_incluir'))); ?>

		<div class="row-fluid inline margin-top-15">
			<?php echo $this->Buonny->input_codigo_cliente_matriz($this); ?>
			<div id="carregando" style="display: none;">
                <img src="/portal/img/ajax-loader.gif" border="0" style="padding-left: 5px; padding-top: 34px;"/>
            </div>
		</div>
		<div class="row fluid">
			<div class="span12">
				<span class="label label-success">Unidades: </span>
			</div>
		</div>
		<div class="row-fluid" style="margin-top: 7px;">
			<div class="span5">
				<?php echo $this->Form->input('from', 
					array(
						'label' => false, 
						'id' => 'multiselect', 
						'options' => '', 
						'class' => 'form-control',
						'multiple' => true, 
						'size' => '8', 
						'style' => 'width: 100%'
					)
				); ?>
			</div>

			<div class="span2">
				<button type="button" id="multiselect_rightAll" class="btn btn-block"><i class="icon-forward"></i></button>
				<button type="button" id="multiselect_rightSelected" class="btn btn-block"><i class="icon-chevron-right"></i></button>
				<button type="button" id="multiselect_leftSelected" class="btn btn-block"><i class="icon-chevron-left"></i></button>
				<button type="button" id="multiselect_leftAll" class="btn btn-block"><i class="icon-backward"></i></button>
			</div>

			<div class="span5">
				<?php echo $this->Form->input('to', 
					array(
						'label' => false, 
						'id' => 'multiselect_to', 
						'class' => 'form-control valida-campos', 
						'options' => array(), 
						'multiple' => true, 
						'size' => '8', 
						'style' => 'width: 100%'
					)
				); ?>
			</div>
		</div>

		<div class="row fluid">
			<div class="span12">
				<span class="label label-success">Usuários: </span>
			</div>
		</div>

		<div class="row-fluid" style="margin-top: 7px;">
			<div class="span5">
				<?php echo $this->Form->input('usuario', 
					array(
						'label' => false, 
						'id' => 'multiselectusuarios', 
						'options' => '', 
						'class' => 'form-control', 
						'multiple' => true, 
						'size' => '8', 
						'style' => 'width: 100%'
					)
				); ?>
			</div>

			<div class="span2">
				<button type="button" id="multiselectusuarios_rightAll" class="btn btn-block"><i class="icon-forward"></i></button>
				<button type="button" id="multiselectusuarios_rightSelected" class="btn btn-block"><i class="icon-chevron-right"></i></button>
				<button type="button" id="multiselectusuarios_leftSelected" class="btn btn-block"><i class="icon-chevron-left"></i></button>
				<button type="button" id="multiselectusuarios_leftAll" class="btn btn-block"><i class="icon-backward"></i></button>
			</div>

			<div class="span5">
				<?php echo $this->Form->input('usuario_to', 
					array(
						'label' => false, 
						'id' => 'multiselectusuarios_to', 
						'class' => 'form-control valida-campos', 
						'options' => array(), 
						'multiple' => true, 
						'size' => '8', 
						'style' => 'width: 100%'
					)
				); ?>
			</div>
		</div>
		<div class="row-fluid">
			<button class="btn btn-primary">Salvar</button>	
			<?php echo $html->link('Voltar', array('action' => 'config_cliente_validador'), array('class' => 'btn')); ?>
		</div>
	<?php echo $this->Form->end(); ?>
</div>

<?php $this->addScript($this->Buonny->link_js('config_cliente_validador.js')); ?>