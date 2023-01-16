<?php echo $this->BForm->hidden('codigo'); ?>
<div class="row-fluid inline">
	<?php echo $this->BForm->input('descricao', array('label' => 'Descrição')); ?>
	<?php $possui_alertas = 0; ?>
	<?php if($usuario_tipo_perfil == TipoPerfil::INTERNO): ?>
		<?php echo $this->BForm->input('codigo_tipo_perfil', array('label' => 'Tipo Perfil', 'options' => $tipos_perfis, 'empty' => 'Selecione o tipo do perfil')); ?>
		<?php echo $this->BForm->input('codigo_pai', array('label' => 'Perfil pai', 'options' => $perfis, 'empty' => 'Selecione o perfil pai')); ?>
		<?php $possui_alertas = !empty($this->data['Uperfil']['codigo_alerta_tipo']) ? 1 : 0 ;?>
	<?php endif; ?>
</div>
<div class="form-actions">
  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
  <?= $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>
</div>
<?php echo $this->BForm->end(); ?>
<?php echo $this->Javascript->codeBlock('
		if('.$possui_alertas.' === 1){
			$("#alertas").css("display", "block");	
			document.getElementById("UperfilAlertaExclusivo").checked = true;
		}   
        $("#UperfilAlertaExclusivo").click(function(){
        	if($("#UperfilAlertaExclusivo").is(":checked")){
        		$("#alertas").css("display", "block");	
        	}else{
        		$("#alertas").css("display", "none");		
        	}
    	});'
, false);
?>