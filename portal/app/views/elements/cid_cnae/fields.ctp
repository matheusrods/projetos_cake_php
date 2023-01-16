 <div class='row-fluid' style="padding-top: 8px">
	 <div class='row-fluid inline'>
	 	<?php echo $this->BForm->hidden('codigo', array('value' =>  !empty($this->data['CidCnae']['codigo'])? $this->data['CidCnae']['codigo'] : '')); ?>
	 	<?php echo $this->Buonny->input_codigo_cid($this, 'codigo_cid10', 'CID10','CID10', null, (isset($this->data['Cid']['codigo_cid10']) ? $this->data['Cid']['codigo_cid10'] : ''), (isset($this->data['Cid']['descricao']) ? $this->data['Cid']['descricao'] : ''));?>
	 </div>
	 <div class='row-fluid inline'>
	 	<?php echo $this->Buonny->input_codigo_cnae($this, 'cnae', 'CNAE','CNAE', null, (isset($this->data['Cnae']['cnae']) ? $this->data['Cnae']['cnae'] : ''), (isset($this->data['Cnae']['descricao']) ? $this->data['Cnae']['descricao'] : ''));?>
	</div>
<div class='form-actions'>
	 <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
	 <?= $html->link('Voltar', array('controller' => 'cid_cnae', 'action' => 'index'), array('class' => 'btn')); ?>
</div>

<?php echo $this->Javascript->codeBlock('
	$(document).ready(function(){
	setup_mascaras(); 
	setup_datepicker();
});
'); ?>