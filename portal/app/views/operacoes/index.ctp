<div class='form-procurar'>
    <?php echo $this->element('/filtros/operacoes'); ?>
</div>
<div class='actionbar-right'>
    <?php echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', array('controller' => 'operacoes', 'action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' => 'Cadastrar Nova Operação')); ?>
</div>
<div class='lista'></div>