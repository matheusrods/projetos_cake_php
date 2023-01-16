<div class = 'form-procurar'>
    <?php echo $this->element('/filtros/pmps') ?>
</div>
<!--
<div class='actionbar-right'>
    <?php //echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', array( 'controller' => 'tipos_acoes', 'action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar Novos Tipos de Acoes'));?>
</div>
-->
<div class='lista'></div>
<script type="text/javascript">
    function atualizaListaPmps() {
        var div = jQuery("div.lista");
        bloquearDiv(div);
        div.load(baseUrl + "pmps/listagem/" + Math.random());
    }

    jQuery(document).ready(function(){
        atualizaListaPmps();
    });
</script>