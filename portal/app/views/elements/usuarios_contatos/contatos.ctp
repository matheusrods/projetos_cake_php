<div class="row-fluid">
    <span class="span12 span-right">
    <?= $this->BMenu->linkOnClick('<i class="icon-plus icon-white"></i>', 
        array('controller' => 'usuarios_contatos', 'action' => 'incluir', $this->data['Usuario']['codigo'] ), 
        array('escape' => false, 'class' => 'btn btn-success', 'title' => 'Incluir Contato', 'onclick' => "return open_dialog(this, 'Contato', 960)")
    )?>
    </span>
</div>
<div id="contato-usuario" class="grupo"></div> 
