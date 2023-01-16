<div class="content">
    <ul class="nav nav-tabs">
        <li class="active">
            <a href="#dados" data-toggle="tab">Dados do usuário</a>
        </li>
        <li>
            <a href="#logs" data-toggle="tab">Logs de alteração</a>
        </li>
        <?php if($this->data['Usuario']['codigo_cliente'] || $_SESSION['Auth']['Usuario']['codigo_uperfil'] == 1) : ?>
            <li id="li-multicliente">
                <a href="#multicliente" data-toggle="tab" class="aba-multiempresa">Multi Cliente</a>
            </li>
            <li id="li-usuariounidade">
                <a href="#usuariounidade" data-toggle="tab" class="aba-multiempresa">Usuário/Unidades</a>
            </li>
        <?php endif; ?>
        <li>
            <a href="#multiconselho" data-toggle="tab">Multi Conselho</a>
        </li>
    </ul>
    <?php echo $javascript->codeblock('jQuery(document).ready(function() {setup_mascaras(); });'); ?>
    <div class="tab-content">
        <div class="tab-pane active" id="dados">
            <?php echo $this->BForm->create('Usuario', array('action' => 'editar_minha_configuracao', $this->passedArgs[0] )); ?>
            <?php echo $this->element('usuarios/fields_minha_configuracao'); ?>
        </div>
        <div class="tab-pane" id="logs">&nbsp;</div>

        <div class="tab-pane" id="multicliente">
            <?php echo $this->element('usuarios_multi_cliente/clientes_por_usuario'); ?>
        </div>
        <div class="tab-pane" id="usuariounidade">
            <?php echo $this->element('usuarios/usuario_unidades'); ?>
        </div>
        <div class="tab-pane" id="multiconselho">
            <?php echo $this->element('usuarios/usuario_multi_conselho'); ?>
        </div>
    </div>

    <div class="form-actions">
        <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary', 'id' => 'editarUsuario')); ?>
        <?= $html->link('Voltar', array('action' => 'index', 'minha_configuracao'), array('class' => 'btn')); ?>
    </div>
    <?php echo $this->BForm->end(); ?>
</div>
<?php $this->addScript($this->Buonny->link_js('search')) ?>
<?php $this->addScript($this->Buonny->link_js('autocomplete')) ?>

<?php $this->addScript($this->Javascript->codeBlock("jQuery(document).ready(function() {
        atualizaListaIps('".$this->passedArgs[0]."');
        listar_listar_logs('".$this->passedArgs[0]."');
		
        function listar_listar_logs( codigo_usuario ){
            var div = $('#logs');
            $.ajax({
                type: 'post',
                url: baseUrl + 'usuarios_logs/listar/' + codigo_usuario +'/'+ Math.random(),
                cache: false,
                data: {'dados':codigo_usuario },
                beforeSend : function(){
                    bloquearDiv(div);
                },
                success: function(data){
                    div.html(data);
                },
                error: function(erro,objeto,qualquercoisa){
                    alert(erro+' - '+objeto+' - '+qualquercoisa);
                    div.unblock();
                }
            });
        }
    })"))
?>
<script>

    $(function(){

        $("#editarUsuario").on("click", function(e){

            e.preventDefault();

            var codigo_uperfil = $("#UsuarioCodigoUperfil").val();

            if (codigo_uperfil == 43) {

                if ($("#UsuarioCodigoFuncaoTipo").val() == "" && $("#UsuarioCodigoGestor").val() == "") {
                    alert("O campo Tipo de Função deve ser preenchido!\nO campo Gestor de Operações deve ser preenchido!")
                } else if ($("#UsuarioCodigoFuncaoTipo").val() == "") {
                    alert("O campo Tipo de Função deve ser preenchido!")
                } else if ($("#UsuarioCodigoGestor").val() == "") {
                    alert("O campo Gestor de Operações deve ser preenchido!")
                } else {
                    console.log("vou editar")
                    $("#UsuarioEditarMinhaConfiguracaoForm").submit();

                }
            } else if (codigo_uperfil == 50) {

                console.log($("select[name='data[Usuario][codigo_subperfil][]']").val())

                if ($("select[name='data[Usuario][codigo_subperfil][]']").val() == null && $("#interno").val() == "") {
                    alert("O campo Interno deve ser preenchido!\nO campo Permissões deve ser preenchido!")
                } else if ($("#interno").val() == "") {
                    alert("O campo Interno deve ser preenchido!")
                } else if ($("select[name='data[Usuario][codigo_subperfil][]']").val() == null) {
                    alert("O campo Permissões deve ser preenchido!")
                } else {
                    console.log("vou editar")
                    $("#UsuarioEditarMinhaConfiguracaoForm").submit();
                }
            } else {
                console.log("aquiii");
                $("#UsuarioEditarMinhaConfiguracaoForm").submit();
            }
        })
    })
</script>
