<?php echo $this->BForm->create('Usuario', array('url' => array('controller' => 'usuarios','action' => 'incluir_minha_configuracao', $codigo_cliente))); ?>

	<?php echo $this->element('usuarios/fields_minha_configuracao'); ?>
	
    <div class="form-actions">
      <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary', 'id' => 'salvarUsuario')); ?>
      <?= $html->link('Voltar', array('action' => 'index', 'minha_configuracao'), array('class' => 'btn')); ?>
    </div>
    
<?php echo $this->BForm->end(); ?>

<?php echo $javascript->codeblock('jQuery(document).ready(function() {setup_mascaras(); });'); ?>

<script>

    $(function(){

        $("#salvarUsuario").on("click", function(e){

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
                    console.log("vou salvar")
                    $("#UsuarioIncluirMinhaConfiguracaoForm").submit();
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
                    console.log("vou salvar")
                    $("#UsuarioIncluirMinhaConfiguracaoForm").submit();
                }
            } else {
                $("#UsuarioIncluirMinhaConfiguracaoForm").submit();
            }
        })
    })
</script>
