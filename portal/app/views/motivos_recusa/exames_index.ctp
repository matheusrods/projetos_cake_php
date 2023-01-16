<div class = 'form-procurar'>
    <?= $this->element('/filtros/motivos_recusa_exame') ?>
</div>

<div class='actionbar-right'>
    <?php echo $this->Html->link('Incluir', array('controller' => 'motivos_recusa', 'action' => 'exames_incluir'), array('title' => 'Incluir', 'class' => 'btn btn-success'));?>
</div>

<div class='lista'></div>

