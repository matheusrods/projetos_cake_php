<div class='form-procurar'>
    <?php echo $this->element('/filtros/rotas'); ?>
</div>
<div class='actionbar-right'>
    <?php echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', array('controller' => 'rotas', 'action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' => 'Incluir Rota')); ?>
</div>
<div class='lista'></div>
