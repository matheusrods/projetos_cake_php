<?php //debug($clientes)?>

<?php if (!empty($clientes)):?>

    <table class="table table-striped">
        <thead>
        <tr>
            <th class="input-mini">Código</th>
            <th>Razão Social</th>
            <th>CNPJ</th>
            <th>Nome Fantasia</th>
            <th>Business Unit</th>
            <th>Unidade Organizacional</th>
            <th style="text-align: center">Responsável</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach($clientes as $cliente) :?>
            <tr>
                <td class="input-mini"><?php echo $cliente['Matriz']['codigo'] ?></td>
                <td><?php echo $cliente['Matriz']['razao_social'] ?></td>
                <td><?php echo $buonny->documento($cliente['Matriz']['codigo_documento']) ?></td>
                <td><?php echo $cliente['Matriz']['nome_fantasia'] ?></td>
                <td><?php echo $cliente['ClienteBu']['descricao'] ?></td>
                <td><?php echo $cliente['ClienteOpco']['descricao'] ?></td>
                <td style="text-align: center">
                    <a href="javascript:void(0);" data-codigo="<?php echo $cliente['Matriz']['codigo'] ?>" class="icon-eye-open dialog_responsavel_visualizar" title="Visualizar responsável"></a>
                    <a href="javascript:void(0);" data-codigo="<?php echo $cliente['Matriz']['codigo'] ?>" class="icon-cog dialog_responsavel" title="Vincular responsável"></a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

<input id="codigo_cliente_input" type="hidden" />

<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?>

<div class="well">
    <?php echo $html->link('Voltar', array('action' => 'matriz_responsabilidade'), array('class' => 'btn')); ?>
</div>

<!-- Dialog para vincular usuário responsável a cliente-->
<div id="dialogResponsavel" title="Usuário responsável"></div>

<!-- Dialog para visualizar usuário responsável a cliente-->
<div id="dialogResponsavelVisualizar" title="Visualizar usuário responsável"></div>

<?php

echo $this->Javascript->codeBlock(" ");
?>

<script>
    $( function() {
        $( "#dialogResponsavel" ).dialog({
            autoOpen: false,
            width: 1270,
            resizable: false,
            height: "auto",
            modal: true,
        });
        $( "#dialogResponsavelVisualizar" ).dialog({
            autoOpen: false,
            width: 1270,
            resizable: false,
            height: "auto",
            modal: true,
        });

        $( ".dialog_responsavel" ).on( "click", function() {

            $("#codigo_cliente_input").val($(this).attr("data-codigo"))

            $( "#dialogResponsavel" ).dialog( "open" );

            setup_time();
            setup_mascaras();
            setup_datepicker();

            var codigo_cliente = $("#codigo_cliente_input").val();

            atualizaListaFiltro(codigo_cliente);
            return;
        });

        $( ".dialog_responsavel_visualizar" ).on( "click", function() {

            $("#codigo_cliente_input").val($(this).attr("data-codigo"))

            $( "#dialogResponsavelVisualizar" ).dialog( "open" );

            setup_time();
            setup_mascaras();
            setup_datepicker();

            var codigo_cliente = $("#codigo_cliente_input").val();

            atualizaListaVisualizarFiltro(codigo_cliente);
            return;
        });
    } );

    function atualizaListaFiltro(codigo_cliente) {
        var div = jQuery("#dialogResponsavel");
        console.log('atualiza lista filtro 1')
        bloquearDiv(div);
        div.load(baseUrl + "usuarios/buscar_usuario_cliente/" + codigo_cliente + "/" + Math.random());
    }

    function atualizaListaVisualizarFiltro(codigo_cliente) {
        var div = jQuery("#dialogResponsavelVisualizar");
        console.log('atualiza lista filtro 1')
        bloquearDiv(div);
        div.load(baseUrl + "usuarios/buscar_usuario_cliente_visualizar/" + codigo_cliente + "/" + Math.random());
    }

    $("#limpar-filtro-usuario").click(function(){
        bloquearDiv($(".form-procurar-user"));
        $(".form-procurar-user").load(baseUrl + "filtros/limpar/model:Usuario/element_name:buscar_usuario_cliente/"+codigo_cliente+" /")
    });
</script>
