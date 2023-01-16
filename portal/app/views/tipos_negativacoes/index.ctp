<div class = 'form-procurar'>
  <?= $this->element('/filtros/tipos_negativacoes') ?>
</div>
<div class='actionbar-right'>
  <?php echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', array( 'controller' => 'tipos_negativacoes', 'action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar Tipo de Negativação'));?>
</div>
<div class='lista'></div>