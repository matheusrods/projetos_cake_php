<?php
if($session->read('Message.flash.params.type') == MSGT_SUCCESS){
	$session->delete('Message.flash');
	echo $javascript->codeBlock("close_dialog();atualizaListaUsuarioVeiculoAlerta(".$codigo_usuario.");");
	exit;
}else if($session->read('Message.flash.params.type') == MSGT_ERROR){
	$session->delete('Message.flash');
}
?>
<?php echo $bajax->form('UsuarioVeiculoAlerta',array('url' => array('controller' => 'usuarios', 'action' => 'incluir_veiculo_alerta', $codigo_usuario),'type' => 'post') ) ?>
<div class="placas">
	<?php echo $this->BForm->hidden('codigo_usuario'); ?>
	<?php if(isset($this->data['UsuarioVeiculoAlerta']['placa']) && count($this->data['UsuarioVeiculoAlerta']['placa']) > 0): ?>
		<?php $contador = count($this->data['UsuarioVeiculoAlerta']['placa']); ?>
		<?php $i = 0; ?>
		<?php foreach($this->data['UsuarioVeiculoAlerta']['placa'] AS $key => $placa): ?>
		<div class="row-fluid inline placa<?php echo $i; ?>">
			<?php if($i == 0): ?>
				<?php echo $this->BForm->input('UsuarioVeiculoAlerta.placa.'.$i, array('label' => 'Placa','class' => 'input-mini placa-veiculo','value' => $placa)); ?>
				<label>&nbsp;</label><?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', 'javascript:void(0)',array('class' => 'btn btn-success', 'escape' => false, 'onclick' => "adiciona_placa()")); ?>
			<?php else: ?>
				<?php echo $this->BForm->input('UsuarioVeiculoAlerta.placa.'.$i, array('label' => false,'class' => 'input-mini placa-veiculo','value' => $placa)); ?>
				<?php echo $this->Html->link('<i class="icon-minus"></i>', 'javascript:void(0)',array('class' => 'btn', 'escape' => false, 'onclick' => "remove_placa(".$i.")")); ?>
			<?php endif; ?>
			<?php echo $this->BForm->input('UsuarioVeiculoAlerta.tipo.'.$i, array('label' => false, 'class' => 'input-medium tipo','placeholder' => 'Tipo do Veículo', 'readonly' => true, 'value'=>$this->data['UsuarioVeiculoAlerta']['tipo'][$key])) ?>
			<?php echo $this->BForm->input('UsuarioVeiculoAlerta.tecnologia.'.$i, array('label' => false, 'class' => 'input-medium tecnologia','placeholder' => 'Tecnologia', 'readonly' => true, 'value'=>$this->data['UsuarioVeiculoAlerta']['tecnologia'][$key])) ?>
			<?php $i++; ?>
		</div>
		<?php endforeach; ?>
	<?php else: ?>
		<?php $contador = 2; ?>
		<div class="row-fluid inline placa0">
			<?php echo $this->BForm->input('UsuarioVeiculoAlerta.placa.0', array('label' => 'Placa','class' => 'input-mini placa-veiculo')); ?>
			<label>&nbsp;</label><?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', 'javascript:void(0)',array('class' => 'btn btn-success', 'escape' => false, 'onclick' => "adiciona_placa()")); ?>
			<?php echo $this->BForm->input('UsuarioVeiculoAlerta.tipo.0', array('label' => false, 'class' => 'input-medium tipo','placeholder' => 'Tipo do Veículo', 'readonly' => true)) ?>
			<?php echo $this->BForm->input('UsuarioVeiculoAlerta.tecnologia.0', array('label' => false, 'class' => 'input-medium tecnologia','placeholder' => 'Tecnologia', 'readonly' => true)) ?>
		</div>
	<?php endif; ?>
</div>

<div class="form-actions">
	<?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-success')); ?>
	<?= $html->link('Cancelar', 'javascript:close_dialog()', array('class' => 'btn')); ?>
</div>
<?php echo $this->BForm->end(); ?>
<?php echo $this->Javascript->codeBlock('
	$(document).ready(function(){
		setup_mascaras();

		$(".placa-veiculo").on("change",function(){
			consulta_tipo_placa($(this).parent().parent(),".placa-veiculo",".tipo",".tecnologia");
		});

		$(".placa-veiculo").each(function(){
			$(this).change();
		});
	});
	key = '.$contador.';
	function adiciona_placa(){
		key++;
		$(".placas").append("<div class=\"row-fluid inline placa"+key+"\"><div class=\"control-group input text\"><input name=\"data[UsuarioVeiculoAlerta][placa]["+key+"]\" type=\"text\" class=\"input-mini placa-veiculo\" id=\"UsuarioVeiculoAlertaPlaca"+key+"\"></div><a href=\"javascript:void(0)\" class=\"btn\" onclick=\"remove_placa("+key+")\"><i class=\"icon-minus\"></i></a><div class=\"control-group input text\"><input name=\"data[UsuarioVeiculoAlerta][tipo]["+key+"]\" type=\"text\" class=\"input-medium tipo\" placeholder=\"Tipo do Veículo\" readonly=\"readonly\" id=\"UsuarioVeiculoAlertaTipo"+key+"\"></div><div class=\"control-group input text\"><input name=\"data[UsuarioVeiculoAlerta][tecnologia]["+key+"]\" type=\"text\" class=\"input-medium tecnologia\" placeholder=\"Tecnologia\" readonly=\"readonly\" id=\"UsuarioVeiculoAlertaTecnologia"+key+"\"></div></div>");
		$(".placa"+key).find(".placa-veiculo").on("change",function(){
			consulta_tipo_placa($(this).parent().parent(),".placa-veiculo",".tipo",".tecnologia");
		});
		setup_mascaras();
	}

	function remove_placa(key){
		$(".placa"+key).remove();
	}	
'); ?>