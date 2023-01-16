<div class = 'form-procurar'>
    <?= $this->element('/filtros/profissionalnegativado') ?>
</div>
<div class='actionbar-right'><?php echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', array( 'controller' => $this->name, 'action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar Novas Usuarios'));?></div>
<div class='lista'></div> 