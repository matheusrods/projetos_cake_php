<?php
    if($session->read('Message.flash.params.type') == MSGT_SUCCESS){
        $session->delete('Message.flash');
        echo $javascript->codeBlock("carrega_contatos_seguradora('{$this->passedArgs[0]}','div#contatos-seguradoras');close_dialog();");
        exit;
    }
?>
<?php echo $this->Bajax->form('SeguradoraContato',array('url' => array('controller'=>'seguradoras_contatos','action' => 'incluir', $this->passedArgs[0]))) ?>
<div class="row-fluid inline">
    <?php echo $this->BForm->hidden('codigo') ?>
    <?php echo $this->BForm->hidden('codigo_seguradora',array('value'=>$this->data['SeguradoraContato']['codigo_seguradora'])) ?>
    <?php echo $this->BForm->input('nome', array('label' => 'Representante', 'class' => 'text-medium')) ?>
    <?php echo $this->BForm->input('codigo_tipo_contato', array('label' => 'Tipos de Contato','options' => $tipos_contato, 'multiple' => 'checkbox', 'class' => 'checkbox inline')) ?>
</div>

<?//= $this->Html->link('<i class="icon-plus"></i>', 'javascript:void(0)', array('escape' => false, 'class' => 'btn', 'title' => 'Incluir Contato', 'id' => 'incluir-contato')) ?>
<?//= $this->Html->link('<i class="icon-minus"></i>', 'javascript:void(0)', array('escape' => false, 'class' => 'btn', 'title' => 'Excluir Contato', 'id' => 'excluir-contato')) ?>

<div id="retornos">
    <div class="row-fluid inline">
        <?php echo $this->BForm->input('codigo_tipo_retorno', array('label' => false,'options' => $tipos_retorno, 'class' => 'input-medium evt-tipo-contato-codigo', 'empty' => 'Selecione')) ?>
        <?php echo $this->BForm->input('descricao', array('label' => false, 'class' => 'input-xlarge evt-tipo-contato-descricao')) ?>
    </div>
    
</div>
<div class="form-actions">
  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
  <?= $html->link('Voltar', 'javascript:close_dialog()', array('class' => 'btn')); ?>
</div> 
<?php echo $this->BForm->end(); ?>
<?php echo $javascript->codeBlock(
    "jQuery(document).ready(function(){
         tipoRetorno = jQuery('#SeguradoraContatoCodigoTipoRetorno');
        tipoRetorno.change(
            function(){
                var descricao = jQuery('input#SeguradoraContatoDescricao');
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
        
        jQuery('#incluir-contato').click(function() {
            var text_input = jQuery('#retornos').find('.row-fluid:first').html();
            var qtd_grupos = jQuery('#retornos').find('.row-fluid').size();
            text_input = text_input.replace('[0]', '[' + qtd_grupos + ']');
            text_input = text_input.replace('[0]', '[' + qtd_grupos + ']');
            jQuery('#retornos').append('<div class=\"row-fluid inline\">' + text_input + '</div>');
            mostra_minus();
        });
        
        





        jQuery('#excluir-contato').click(function() {
            jQuery('#retornos').find('.row-fluid:last').remove();
            mostra_minus();
        });
        
        function mostra_minus() {
            var qtd_grupos = jQuery('#retornos').find('.row-fluid').size();
            if (qtd_grupos>1)
                jQuery('#excluir-contato').show();
            else
                jQuery('#excluir-contato').hide();
        }        
        mostra_minus();
        setup_mascaras();
    })"
) ?>




