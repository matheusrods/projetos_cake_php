<div class="row-fluid inline">
    <?php echo $this->BForm->hidden('codigo') ?>
    <?php echo $this->BForm->hidden('codigo_usuario') ?>
    <?php echo $this->BForm->input('codigo_tipo_retorno', array('label' => 'Retorno','options' => $tipos_retorno, 'class' => 'input-medium', 'empty' => 'Selecione')) ?>
    <?php echo $this->BForm->input('descricao', array('label' => 'Contato', 'class' => 'input-xlarge')) ?>
</div>
<?php echo $javascript->codeBlock(
    "jQuery(document).ready(function(){
        tipoRetorno = jQuery('#UsuarioContatoCodigoTipoRetorno');
        tipoRetorno.change(
            function(){
                var descricao = jQuery('input#UsuarioContatoDescricao');
                descricao.unmask();
                descricao.removeClass('format-phone');                
                if ($(this).val() == 1 || $(this).val() == 3 ||$(this).val() == 5 ||$(this).val() == 7 ||$(this).val() == 8 ||$(this).val() == 9 ||$(this).val() == 11 ) {
                   descricao.addClass('telefone');
                   setup_mascaras();
                } else {
                    descricao.removeClass('telefone');
                }
            }
        );
        tipoRetorno.change();
    })"
) ?>