<div class='form-procurar'>
    <?php echo $this->element('/filtros/sinistros'); ?>
</div>
<div class='actionbar-right'>
    <?php echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', array('controller' => 'sinistros', 'action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' => 'Incluir Sinistro')); ?>
</div>
<div class='lista_sinistro' style='min-heigth:30px'></div>
<?php $this->addScript( $this->Buonny->link_js('solicitacoes_monitoramento') ) ?>
<?php $this->addScript( $this->Buonny->link_js('autocomplete') ) ?>