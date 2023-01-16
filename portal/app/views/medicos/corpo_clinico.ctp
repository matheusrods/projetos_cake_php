<div class = 'form-procurar'>
    <?php echo $this->element('/filtros/corpo_clinico') ?>
</div>
<div class='lista'></div>
<script type="text/javascript">
    function atualizaListaCorpoClinico() {
        var div = jQuery("div.lista");
        bloquearDiv(div);
        div.load(baseUrl + "medicos/corpo_clinico_listagem/" + Math.random());
    }

    jQuery(document).ready(function(){
        //atualizaListaCorpoClinico();
    });
</script>