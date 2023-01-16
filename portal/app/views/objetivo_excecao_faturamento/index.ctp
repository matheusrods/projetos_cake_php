<div class = 'form-procurar'>
    <?= $this->element('/filtros/objetivo_comercial_exc_faturamento') ?>
</div>
<div class='actionbar-right'>
    <?php echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', array( 'controller' => 'objetivo_excecao_faturamento', 'action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Incluir'));?>
</div>
<br/>
<div class='lista'></div>