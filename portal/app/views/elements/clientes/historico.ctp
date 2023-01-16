<div class="row-fluid">
    <span class="span12 span-right">
        <?= $html->link('<i class="icon-plus icon-white"></i>', array('controller' => 'clientes_historicos', 'action' => 'incluir', $this->data['Cliente']['codigo']), array('escape' => false, 'class' => 'btn btn-success', 'title' => 'Incluir Contato', 'onclick' => "return open_dialog(this, 'Histórico', 600)")) ?>
        <?= $html->link('<i class="icon-eye-close"></i>', 'javascript:void(0)', array('escape' => false, 'class' => 'btn', 'onclick' => 'mostrar_historico(this)')) ?>
    </span>
</div>
<div id="historico-cliente" class="grupo"></div> 