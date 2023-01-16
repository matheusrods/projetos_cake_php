<div class='well'>
    <strong>Código: </strong><?php echo $this->Html->tag('span', $corretora['Corretora']['codigo']); ?>
    <strong>Corretora: </strong><?php echo $this->Html->tag('span', $corretora['Corretora']['nome']); ?>
</div>
<div class='actionbar-right'>
    <?= $html->link('<i class="icon-plus icon-white"></i> Incluir', array('action' => 'incluir_por_corretora', $this->passedArgs[0]), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Incluir Perfil', 'onclick' => 'return open_dialog(this, \'Incluir Usuário\')')) ?>
</div>
<div class='lista'>
</div>
<?php $this->addScript($this->Javascript->codeBlock("atualizaListaUsuariosPorCorretora({$corretora['Corretora']['codigo']})")) ?>