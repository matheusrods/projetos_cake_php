<div class = 'form-procurar'>
    <?php echo $this->element('/filtros/gestao_cronograma_pcmso') ?>
</div>
<div class='lista'></div>
<script type="text/javascript">
    function atualizaListaGestaoCronogramaPcmso() {
        var div = jQuery("div.lista");
        bloquearDiv(div);
        div.load(baseUrl + "clientes_implantacao/gestao_cronograma_pcmso_listagem/" + Math.random());
    }

    jQuery(document).ready(function(){
        atualizaListaGestaoCronogramaPcmso();
    });
</script>