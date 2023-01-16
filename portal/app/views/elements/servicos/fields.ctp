  <div class='row-fluid inline'>
    <?php echo $this->BForm->hidden('codigo'); ?>
    <?php echo $this->BForm->input('descricao', array('label' => 'Descrição (*)', 'class' => 'input-xxlarge')); ?>
    <?php echo $this->BForm->input('codigo_externo', array('label' => 'Código Personalizado', 'class' => 'input-medium')); ?>
  </div>  
  <div class='row-fluid inline'> 		
	<?php echo $this->BForm->input('tipo_servico', array('label' => 'Tipo de Serviço (*)', 'class' => 'input', 'default' => '','empty' => 'Tipo de Serviço', 'options' => array('E' => 'Exames Complementares', 'G' => 'Engenharia', 'C' => 'Consultorias e Palestras','S'=> 'Saúde','M'=>'Mensalidade','D'=>'Desenvolvimento'),  'disabled' => ($edit_mode)? 'disabled': false)); ?>
<?php if($edit_mode): ?>
  <?php echo $this->BForm->hidden('tipo_servico'); ?>
<?php endif; ?>

<?php if($this->Session->read('Auth.Usuario.codigo_empresa') == 2): ?>
  <?php echo $this->BForm->input('codigo_classificacao_servico', array('options' => $classificacao)); ?>
<?php endif; ?>

    <?php if(empty($this->passedArgs)): ?>
      <?php echo $this->BForm->hidden('ativo', array('value' => 1)); ?>
    <?php else: ?>
      <?php echo $this->BForm->input('ativo', array('label' => 'Status (*)', 'class' => 'input-small', 'default' => '', 'empty' => 'Status', 'options' => array(1 => 'Ativo', 0 => 'Inativo'))); ?>
  <?php endif;  ?>
  </div>  
  
  <div class='form-actions'>
  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
  <?= $html->link('Voltar', array('controller' => 'servicos', 'action' => 'index'), array('class' => 'btn')); ?>
</div>
<?php echo $this->BForm->end(); ?>
<?php echo $this->Javascript->codeBlock('$(document).ready(function() {setup_mascaras();});');?>