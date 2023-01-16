<div class="well">
	<?= $cliente['TPjurPessoaJuridica']['pjur_razao_social'] ;?>
</div>
<?php echo $this->BForm->create('TIcheItemChecklist', array('autocomplete' => 'off', 'url' => array('controller' => 'itens_checklist', 'action' => 'incluir'))) ?>
<div class="row-fluid inline">
	<?php echo $this->BForm->hidden('iche_pjur_pess_oras_codigo',array('value' => $this->params['pass'][0]));?>
	<?php echo $this->BForm->input('iche_descricao', array('class' => 'input-xlarge', 'label' => 'Descrição',)); ?>         
</div>
<div class="form-actions">
      <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
      <?php echo $html->link('Voltar',array('controller' => 'itens_checklist', 'action' => 'index'), array('class' => 'btn')) ;?>
</div>