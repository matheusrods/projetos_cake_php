<div class='form-procurar'>
    <?php echo $this->element('/filtros/mensagens_de_acessos'); ?>
</div>
<div class='actionbar-right'>
    <?php echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', array('controller' => 'mensagens_de_acessos', 'action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' => 'Incluir Mensagem')); ?>
</div>
<div class='lista'></div>
<?php $this->addScript( $this->Buonny->link_js('solicitacoes_monitoramento') ) ?>