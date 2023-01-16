<?php echo $this->BForm->create('MensagemDeAcesso', array('enctype'=>'multipart/form-data', 'url'=>array('controller'=>'mensagens_de_acessos', 'action'=>'upload_imagem')));?>
<div class="row-fluid inline">
	<?php echo $this->BForm->input('titulo', array('type'=>'text', 'label' => 'Titulo')); ?>
</div>
<div class="row-fluid inline">
	<?php echo $this->BForm->input('arquivo', array('name' => 'data[arquivo]','type'=>'file', 'label' => false)); ?>
</div>
<div class="form-actions">
   <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-success','onclick' => 'alert("Todos os caracteres especiais serão removidos")')); ?>
  <?php echo $html->link('Voltar',array('action'=>'visualizar_imagem') , array('class' => 'btn')); ?>
</div>