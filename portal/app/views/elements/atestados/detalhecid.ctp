<h3>Detalhe CID</h3>
<div class='actionbar-right'>
    <?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', array('controller' => 'cid', 'action' => 'buscar_cid', $codigo_atestado), array('escape' => false, 'class' => 'btn btn-success dialog_atestados_cid', 'title' =>'Cadastrar Novos CID\'s'));?>
</div>

<div id="atestado-cid-lista" class="grupo"></div>

<?php echo $this->Javascript->codeBlock('
jQuery(document).ready(function(){
    $(document).on("click", ".dialog_cids", function(e) {
        e.preventDefault();
        open_dialog(this, "CID", 880);
    });

    $(document).on("click", ".dialog_atestados_cid", function(e) {
        e.preventDefault();
        open_dialog(this, "CID", 880);
    });
		
	atualizaAtestadoCid();
});
		
    function atualizaAtestadoCid(){
        var div = jQuery("#atestado-cid-lista");
        bloquearDiv(div);
        div.load(baseUrl + "atestados_cid/listagem/'.$this->data['Atestado']['codigo'].'/" + Math.random());
    }		
');
?>