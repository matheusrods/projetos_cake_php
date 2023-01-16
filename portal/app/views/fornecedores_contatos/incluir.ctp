<?php
    if($session->read('Message.flash.params.type') == MSGT_SUCCESS){
        $session->delete('Message.flash');

        echo $this->Javascript->codeBlock("
            $(document).ready(function(){
                close_dialog();
                atualizaFornecedorContato();
                atualizaFornecedorContatoAgendamento();
            });

            function atualizaFornecedorContato(){
                var div = jQuery('#fornecedor-contato-lista');
                bloquearDiv(div);
                div.load(baseUrl + 'fornecedores_contatos/contatos_por_fornecedores/".$codigo_fornecedor."/' + Math.random());
            }

            function atualizaFornecedorContatoAgendamento(){
                var div = jQuery('#fornecedor_contato_agendamento_lista');
                bloquearDiv(div);
                div.load(baseUrl + 'fornecedores_contatos/contatos_por_fornecedores_agendamento/".$codigo_fornecedor."/' + Math.random());
            }
        ");
        exit;
    }
?>

<?php echo $this->Bajax->form('FornecedorContato',array('url' => array('controller'=>'fornecedores_contatos','action' => 'incluir', $this->passedArgs[0]))) ?>
 

<div class="row-fluid inline">
    <?php echo $this->BForm->hidden('codigo_fornecedor',array('value'=>$this->data['FornecedorContato']['codigo_fornecedor'])) ?>
    <?php echo $this->BForm->input('nome', array('label' => 'Representante', 'class' => 'text-medium')) ?>
    <?php echo $this->BForm->input('codigo_tipo_contato', array('label' => 'Tipos de Contato','options' => $tipos_contato, 'multiple' => 'checkbox', 'class' => 'checkbox inline')) ?>
</div>
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
<?php echo $this->Buonny->link_js('fornecedores'); ?>
<?php echo $javascript->codeBlock(
    "jQuery(document).ready(function(){

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


