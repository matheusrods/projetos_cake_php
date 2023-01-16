<div class="row-fluid inline">
    
    <?php echo $this->BForm->hidden('codigo') ?>
    <?php echo $this->BForm->hidden('codigo_fornecedor') ?>
    <?php echo $this->BForm->input('codigo_tipo_retorno', array('label' => 'Retorno','options' => $tipos_retorno, 'class' => 'input-medium', 'empty' => 'Selecione')) ?>
    <?php echo $this->BForm->input('descricao', array('label' => 'Contato (Fone / Email)', 'class' => 'input-xlarge')) ?>
    <?php echo $this->BForm->input('nome', array('label' => 'Representante', 'class' => 'text-medium')) ?>
    <?php echo $this->BForm->input('codigo_tipo_contato', array('label' => 'Tipo','options' => $tipos_contato, 'class' => 'input-medium', 'empty' => 'Selecione')) ?>
</div>
<script type="text/javascript">
    jQuery(document).ready(function(){

        tipoRetorno = jQuery('#FornecedorContatoCodigoTipoRetorno');

        tipoRetorno.change(
            function(){
                var descricao = jQuery('input#FornecedorContatoDescricao');
                if ($(this).val() == 7 || $(this).val() == 12) {
                   descricao.val('')
                   descricao.addClass('celular');
        
                   descricao.mask('(99) 99999-9999');

                } else if($(this).val() == 1 ){
                    descricao.val('')
                    descricao.addClass('telefone');

                    descricao.mask('(99) 9999-9999');
                } else {
                    
                    descricao.val('')
                    descricao.unmask();
                    descricao.removeClass('celular');
                    descricao.removeClass('telefone');
                    descricao.removeClass('format-phone');
              
                }
            }
        );
    });
</script>

