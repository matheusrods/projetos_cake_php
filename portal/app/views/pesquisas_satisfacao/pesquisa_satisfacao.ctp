<?php  
    if($session->read('Message.flash.params.type') == MSGT_SUCCESS){
        $session->delete('Message.flash');
        echo $javascript->codeBlock(
            "close_dialog();
            var div = jQuery('div.lista');
            bloquearDiv(div);
            div.load(baseUrl + 'pesquisas_satisfacao/listagem_pesquisa_satisfacao/' + Math.random());");
        exit;
    } else if($session->read('Message.flash.params.type') == MSGT_ERROR){
        $session->delete('Message.flash');
    }
    $codigo_cliente_contato = !empty($this->data['PesquisaSatisfacao']['codigo_cliente_contato']) ? $this->data['PesquisaSatisfacao']['codigo_cliente_contato'] : NULL;
?>
<?php echo $bajax->form('PesquisaSatisfacao', array('url' => array('controller' => 'PesquisasSatisfacao', 'action' => 'pesquisa_satisfacao', $this->params['pass']['0']))); ?>
<?php echo $this->BForm->input('codigo');?>
<?php echo $this->BForm->hidden('codigo_cliente');?>
<?php echo $this->BForm->hidden('codigo_pai');?>
<div class="row-fluid inline">
    <?php echo $this->BForm->input('codigo_status_pesquisa', array('class' => 'input-xlarge', 'id'=>'status_pesquisa','options' => array($status_pesquisa), 'label' => 'Nível de Satisfação','empty' => 'Selecione'))?>
    <div id="reagendamento">
    <?php echo $this->BForm->input('data_reagendamento', array('class' => 'input-small data'));?>
    <?php echo $this->BForm->input('hora_reagendamento', array('class' => 'hora input-mini'));?>
    </div>
</div>
<?php echo $this->BForm->input('observacao', array('label' => 'Observações', 'type' => 'textarea', 'class' => 'input-xxlarge')) ?>
<div class="row-fluid inline">
    <?php echo $this->BForm->input('codigo_tipo_retorno', array('label' => 'Retorno','options' => $tipos_retorno, 'class' => 'input-medium', 'empty' => 'Selecione')) ?>
    <?php echo $this->BForm->input('descricao', array('label' => 'Contato', 'type' => 'text', 'class' => 'input-xlarge')) ?>
    <?php echo $this->BForm->input('codigo_tipo_contato', array('label' => 'Tipo','options' => $tipos_contato, 'class' => 'input-medium', 'empty' => 'Selecione')) ?>
    <?php echo $this->BForm->input('nome', array('label' => 'Representante', 'type' => 'text', 'class' => 'text-medium')) ?>
</div>
<div id="cliente_contato">&nbsp;</div>
<div class="form-actions">
    <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
    <?= $html->link('Voltar', 'javascript:close_dialog()', array('class' => 'btn')); ?>
</div> 
<?php echo $this->BForm->end(); ?>
<?php echo $this->Javascript->codeBlock("    
    function setup_radio() {
        $('input[type=radio]').click(function() {
            var tr = $(this).parent().parent();
            $('#PesquisaSatisfacaoCodigoTipoRetorno').val( tr.find('td#tipo_retorno_descricao').attr('codigo') );
            $('#PesquisaSatisfacaoDescricao').val( tr.find('td#contato_descricao').html() );
            $('#PesquisaSatisfacaoCodigoTipoContato').val( tr.find('td#tipo_contato_descricao').attr('codigo') );
            $('#PesquisaSatisfacaoNome').val( tr.find('td#contato_nome').html() );
            $('#PesquisaSatisfacaoCodigoTipoRetorno').attr('readonly', true);
            $('#PesquisaSatisfacaoDescricao').attr('readonly', true);
            $('#PesquisaSatisfacaoCodigoTipoContato').attr('readonly', true);
            $('#PesquisaSatisfacaoNome').attr('readonly', true);
        });
    }

    jQuery(document).ready(function(){
        setup_time();
        setup_datepicker();
        if( $('#status_pesquisa').val() == 4){
            $('#reagendamento').show();                
        }else{
            $('#reagendamento').hide();
        }
        $('#status_pesquisa').change(function() {
            if( $('#status_pesquisa').val() == 4){
                $('#reagendamento').show();
            }else{
                $('#reagendamento').hide();                
            }
        });

        tipoRetorno = jQuery('#PesquisaSatisfacaoCodigoTipoRetorno');
        tipoRetorno.change(
          function(){
            var descricao = jQuery('input#PesquisaSatisfacaoDescricao');
            if ($(this).val() == 1 || $(this).val() == 3 ||$(this).val() == 5) {
               descricao.addClass('telefone');
               setup_mascaras();
            } else {
                descricao.unmask();
                descricao.removeClass('telefone');
            }
        });
        tipoRetorno.change();
    });
    var div = $('#cliente_contato');
    $.ajax({
        type: 'post',
        url: baseUrl + 'clientes_contatos/lista_contatos_cliente/' + Math.random(),
        cache: false,
        data: {
            'data[codigo_cliente]': '{$this->data['PesquisaSatisfacao']['codigo_cliente']}', 
            'data[codigo_cliente_contato]': '{$codigo_cliente_contato}', 
            'data[codigo_tipo_retorno]': [1],
            'data[tipo_exibicao]': 1,
            'data[disabled_contato]': '{$disabled_contato}'
        },
        beforeSend : function(){
            bloquearDiv(div);
        },
        success: function(data){
            div.html(data);
            setup_radio();
        }
    });", false );?>