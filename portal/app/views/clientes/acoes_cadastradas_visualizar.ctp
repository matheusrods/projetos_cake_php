<div class="form-procurar">
    <?= $this->element('/filtros/acoes_cadastradas_visualizar') ?>
</div>

<div class="actionbar-right" style="margin-bottom: 10px;">
    <div style="display: inline-flex;">
        <div class="btn btn-success dialog_responsavel">Transferir ações em lote</div>
        <a title="Exportar ações de melhorias" class="dialog_responsavel_export" style="margin-left: 10px;cursor: pointer;">
            <i class="cus-page-white-excel"></i>
        </a>
    </div>
</div>

<div class='lista'></div>

<div class="well">
    <?php echo $html->link('Voltar', array('action' => 'acoes_cadastradas'), array('class' => 'btn')); ?>
</div>

<!-- Dialog para vincular usuário responsável a cliente-->
<div id="dialogResponsavel" title="Usuário responsável"></div>

<style>
    h3 {
        text-decoration: none;
    }
</style>

<script>
    $( function() {
        $( "#dialogResponsavel" ).dialog({
            autoOpen: false,
            width: 1270,
            resizable: false,
            height: "auto",
            modal: true,
        });

        $( ".dialog_responsavel" ).on( "click", function() {

            $( "#dialogResponsavel" ).dialog( "open" );

            setup_time();
            setup_mascaras();
            setup_datepicker();

            var codigo_cliente = <?= $codigo_cliente?>;

            atualizaListaFiltro(codigo_cliente);
            return;
        });

        $( ".dialog_responsavel_export" ).on( "click", function() {

            var codigo_cliente = <?= $codigo_cliente?>;
            var is_admin = <?= $is_admin?>;

            location.href = baseUrl + "clientes/listagem_acoes_cadastradas_visualizar/" + codigo_cliente + "/"+is_admin+"/export";
            return;
        });
    });

    function atualizaListaFiltro(codigo_cliente) {
        var div = jQuery("#dialogResponsavel");
        console.log('atualiza lista filtro 1')
        bloquearDiv(div);
        div.load(baseUrl + "usuarios/buscar_usuario_cliente_acao/" + codigo_cliente + "/" + Math.random());
    }

    $("#limpar-filtro-usuario").click(function(){
        bloquearDiv($(".form-procurar-user"));
        $(".form-procurar-user").load(baseUrl + "filtros/limpar/model:Usuario/element_name:buscar_usuario_cliente/"+codigo_cliente+" /")
    });
</script>
