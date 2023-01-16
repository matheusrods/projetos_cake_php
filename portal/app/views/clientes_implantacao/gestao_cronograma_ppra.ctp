<div class = 'form-procurar'>
    <?php echo $this->element('/filtros/gestao_cronograma_ppra') ?>
</div>
<div class='lista'></div>
<script type="text/javascript">
    function atualizaListaGestaoCronogramaPpra() {
        var div = jQuery("div.lista");
        bloquearDiv(div);
        div.load(baseUrl + "clientes_implantacao/gestao_cronograma_ppra_listagem/" + Math.random());
    }

    jQuery(document).ready(function(){
        atualizaListaGestaoCronogramaPpra();
    });
</script>