<?php if(isset($info_empresa['MultiEmpresa']['logomarca']) && !empty($info_empresa['MultiEmpresa']['logomarca'])) : ?>
	<img id="logomarca-thumb" style="height: 117px;border: 2px solid #99F;" src="https://api.rhhealth.com.br<?php echo $info_empresa['MultiEmpresa']['logomarca']; ?>" />
<?php else: ?>
	<div class="alert">Nenhuma imagem encontrada.</div>
<?php endif; ?>
<br /><br /><br />

<?php echo $this->BForm->create('MultiEmpresa', array('type' => 'file', 'url' => array('controller' => 'multi_empresas', 'action' => 'logomarca'), 'type' => 'post', 'onSubmit' => 'return false;')); ?>
	<?php echo $this->BForm->input('MultiEmpresa.logomarca', array('type' => 'file', 'class' => 'input-xlarge', 'label' => 'Logomarca')); ?>
	<p><b>PS:</b> É orientado que envie uma logomarca com 30px de Altura, ou o sistema vai redimensionar-la para melhor visualização no sistema.</p>
	<a href="javascript:void(0);" onclick="enviar_logomarca();" class="btn btn-success">Enviar Logomarca</a>
	<?php // echo $this->BForm->submit('Enviar Logomarca', array('div' => false, 'class' => 'btn btn-success', 'onclick' => 'enviar_logomarca()')); ?>
<?php echo $this->BForm->end(); ?>
