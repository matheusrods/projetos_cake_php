<?php
    if($session->read('Message.flash.params.type') == MSGT_SUCCESS){
        $session->delete('Message.flash');
        echo $javascript->codeBlock("close_dialog();carrega_contatos_cliente(".$this->passedArgs[0].")");
        exit;
    }
?>
<?php echo $this->Bajax->form('ClienteContato',array('url' => array('action' => 'incluir', $this->passedArgs[0]))) ?>
<div class="row-fluid inline">
    <?php echo $this->BForm->hidden('0.codigo') ?>
    <?php echo $this->BForm->hidden('0.codigo_cliente') ?>
    <?php echo $this->BForm->input('0.nome', array('label' => 'Representante', 'class' => 'text-medium')) ?>
    <?php echo $this->BForm->input('0.codigo_tipo_contato', array('label' => 'Tipos de Contato','options' => $tipos_contato, 'multiple' => 'checkbox', 'class' => 'checkbox inline')) ?>
</div>
<?= $this->Html->link('<i class="icon-plus"></i>', 'javascript:void(0)', array('escape' => false, 'class' => 'btn', 'title' => 'Incluir Contato', 'id' => 'incluir-contato', 'style' => 'margin-right: 10px;')) ?>
<?= $this->Html->link('<i class="icon-minus"></i>', 'javascript:void(0)', array('escape' => false, 'class' => 'btn', 'title' => 'Incluir Contato', 'id' => 'excluir-contato')) ?>

<div id="retornos" style="margin-top: 10px;">
    <div class="row-fluid inline">
        <?php echo $this->BForm->input('0.codigo_tipo_retorno', array('label' => false,'options' => $tipos_retorno, 'class' => 'input-medium evt-tipo-contato-codigo', 'empty' => 'Selecione')) ?>
        <?php echo $this->BForm->input('0.descricao', array('label' => false, 'class' => 'input-xlarge evt-tipo-contato-descricao')) ?>
    </div>
    <?php if (isset($this->data['ClienteContato'])): ?>
        <?php for ($indice = 1; $indice < count($this->data['ClienteContato']); $indice++): ?>
            <div class="row-fluid inline">
                <?php echo $this->BForm->input("{$indice}.codigo_tipo_retorno", array('label' => false,'options' => $tipos_retorno, 'class' => 'input-medium', 'empty' => 'Selecione')) ?>
                <?php echo $this->BForm->input("{$indice}.descricao", array('label' => false, 'class' => 'input-xlarge')) ?>
            </div>
        <?php endfor; ?>
    <?php endif; ?>
</div>
<div class="form-actions">
  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
  <?= $html->link('Voltar', 'javascript:close_dialog()', array('class' => 'btn')); ?>
</div> 
<?php echo $this->BForm->end(); ?>
<?php echo $javascript->codeBlock(
    "jQuery(document).ready(function(){
        jQuery(document).on('change', '.evt-tipo-contato-codigo',
            function(){
                var descricao = jQuery(this).parent().parent().find('.evt-tipo-contato-descricao');
                descricao.unmask();
                if ($(this).val() == 1 || $(this).val() == 7 || $(this).val() == 12) {
                    descricao.val('')
                    descricao.addClass('celular');
         
                    descricao.mask('(99) 99999-9999');
 
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