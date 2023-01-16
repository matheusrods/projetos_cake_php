<?php if(!$somente_cd): ?>
	<?php echo $this->BForm->input($model.'.cd_id', array('class' => 'input-medium', 'label'=>false, 'title'=>'CD', 'empty'=>'CD', 'options'=>$cds));?>
	<?php echo $this->BForm->input($model.'.bandeira_id', array('class' => 'input-medium', 'label'=>false, 'title'=>'Bandeira', 'empty'=>'Bandeira', 'options'=>$bandeiras));?>
	<?php echo $this->BForm->input($model.'.regiao_id', array('class' => 'input-medium', 'label'=>false, 'title'=>'Região', 'empty'=>'Região', 'options'=>$regioes));?>
	<?php echo $this->BForm->input($model.'.loja_id', array('class' => 'input-medium', 'label'=>false, 'title'=>'Loja', 'empty'=>'Loja', 'options'=>$lojas));?>
	<?php echo $this->BForm->input($model.'.transportador_id', array('label' => false, 'empty' => 'Transportadores','class' => 'input-medium', 'options' => $transportadores)) ?>
<?php else: ?>
	<?php echo $this->BForm->input($model.'.cd_id', array('label' => 'Alvo Origem', 'empty' => 'Alvo Origem','class' => 'input-medium', 'options' => $cds)) ?>
<?php endif; ?>