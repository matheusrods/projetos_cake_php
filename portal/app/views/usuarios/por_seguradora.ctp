<div class='well'>
    <strong>Código: </strong><?php echo $this->Html->tag('span', $seguradora['Seguradora']['codigo']); ?>
    <strong>Seguradora: </strong><?php echo $this->Html->tag('span', $seguradora['Seguradora']['nome']); ?>
</div>
<div class='actionbar-right'>
    <?= $html->link('<i class="icon-plus icon-white"></i> Incluir', array('action' => 'incluir_por_seguradora', $this->passedArgs[0]), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Incluir Perfil', 'onclick' => 'return open_dialog(this, \'Incluir Usuário\')')) ?>
</div>
<div class='lista'>
</div>
<?php $this->addScript($this->Javascript->codeBlock("atualizaListaUsuariosPorSeguradora({$seguradora['Seguradora']['codigo']})")) ?>