<div class = 'form-procurar'>
  <?= $this->element('/filtros/clientes_ips') ?>
</div>
<div class='actionbar-right'>
  <?php echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', array( 'controller' => 'clientes_ips', 'action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Incluir Endereço IP'));?>
</div>
<br />
<div class='lista'>&nbsp;</div>