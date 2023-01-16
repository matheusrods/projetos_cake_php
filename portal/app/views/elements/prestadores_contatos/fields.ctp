<div class="row-fluid inline">
    <?php echo $this->BForm->hidden('codigo') ?>
    <?php echo $this->BForm->hidden('codigo_prestador') ?>
    <?php echo $this->BForm->input('codigo_tipo_retorno', array('label' => 'Retorno','options' => $tipos_retorno, 'class' => 'input-medium', 'empty' => 'Selecione')) ?>
    <?php echo $this->BForm->input('descricao', array('label' => 'Contato (Fone / Email)', 'class' => 'input-xlarge')) ?>
    <?php echo $this->BForm->input('nome', array('label' => 'Representante', 'class' => 'text-medium')) ?>
    <?php echo $this->BForm->input('codigo_tipo_contato', array('label' => 'Tipo','options' => $tipos_contato, 'class' => 'input-medium', 'empty' => 'Selecione')) ?>
</div>
<?php echo $javascript->codeBlock(
    "jQuery(document).ready(function(){
        tipoRetorno = jQuery('#PrestadorContatoCodigoTipoRetorno');
        tipoRetorno.change(
            function(){
                var descricao = jQuery('input#PrestadorContatoDescricao');
                if ($(this).val() == 1 || $(this).val() == 3 ||$(this).val() == 5) {
                   descricao.addClass('telefone');
                   setup_mascaras();
                } else {
                    descricao.unmask();
                    descricao.removeClass('telefone');
                }
            }
        );
        tipoRetorno.change();
    })"
) ?>