<?php
    // recebento dados da controller
    $thisData = $this->data;

    // se for necessário tratar faça na controller, mas se não tem jeito faça aqui antes de intejar nos componentes
    $disabled = (isset($readonly_mode) && $readonly_mode == true ) ? true: false;

    // configuração dos inputs para pesquisas de credenciados
    $arrElementConfig = array();

    
    $arrElementConfig['select2_group_credenciado'] = array(
        'legend' => 'Crendenciado', // deixe null se não quer legenda
        'select2CodigoCredenciado' => array(
            'strFieldName' => 'codigo_fornecedor',
            'arrOptions' => array( $thisData['NotaFiscalServico']['codigo_fornecedor'] => $thisData['NotaFiscalServico']['codigo_fornecedor']),
            'mixSelectedValue' => null,
            'arrAttributes' => array('disabled'=> $disabled),
        ),
        'select2CodigoDocumentoCredenciado' => array(
            'strFieldName' => 'Fornecedor.codigo_documento',
            'arrOptions' => array( $thisData['NotaFiscalServico']['codigo_fornecedor'] => $thisData['Fornecedor']['codigo_documento']),
            'mixSelectedValue' => null,
            'arrAttributes' => array('disabled'=> $disabled),
        ),
        'select2RazaoSocialCredenciado' => array(
            'strFieldName' => 'Fornecedor.razao_social',
            'arrOptions' => array( $thisData['NotaFiscalServico']['codigo_fornecedor'] => $thisData['Fornecedor']['razao_social']),
            'mixSelectedValue' => null,
            'arrAttributes' => array('disabled'=> $disabled),
        ),
        'inputNomeFantasiaCredenciado' => array(
            'strFieldName' => 'Fornecedor.nome',
            'mixSelectedValue' => $thisData['Fornecedor']['nome'],
            'arrAttributes' => array('disabled'=> $disabled),
        ),
    );

    // Código Rastreamento
    $arrElementConfig['input_chave_rastreamento'] = array(
        'strFieldName' => 'chave_rastreamento',
        'arrAttributes' => array(
            'style'=>'width:100%', 
            'label' => 'Código Rastreamento <abbr title="Chave para rastreamento de Nota Fiscal"><h11 style="font-size:0.95em;color: #00b1c4;font-weight:bold;">?</h11></abbr>', 
            'type' => 'text',
            'class' => 'chave_rastreamento_nfe_api',
            'disabled'=> $disabled
        )
    );
    


// Daqui pra baixo procure trabalhar apenas o que for relativo a layout

?>
<div class="well">

    <?= $this->element('ithealth/select2_group_credenciado', array('ithealth_element_config' => $arrElementConfig['select2_group_credenciado'] )); ?>

    <div class="row-fluid inline">
        <div class="span12">
            <p class="legend">Dados da nota fiscal<p>
        </div>
    </div>

    <div class="row-fluid inline">
        <div class="span3">
            <?= $ithealth->input('numero_nota_fiscal', array( 'style'=>'width:100%', 'label' => 'Número Nota Fiscal', 'required' => true, 'type' => 'text', 'readonly' => $edit_mode )); ?>
        </div>

        <div class="span3">
            <?= $ithealth->input('valor', array('value' => empty($this->data['NotaFiscalServico']['valor']) ? $this->data['NotaFiscalServico']['valor'] : $this->Ithealth->moeda($this->data['NotaFiscalServico']['valor'], array('nozero' => true)), 'label' => 'Valor NFS', 'required' => true, 'style'=> 'width:100%;', 'class' => 'numeric moeda input-medium')); ?>
        </div>

        <div class="span3">
            <?= $ithealth->selectTipoServicosNfs(); ?>
        </div>
        <div class="span3">
            <?= $ithealth->selectStatusNotaFiscal('codigo_nota_fiscal_status'); ?> 
        </div>
    </div>

    <div class="row-fluid inline">
        <div class="span4">
            <label class="legend">Incluir Anexo NF</label>
            <div class="row-fluid inline">
                <div class="span4">
                    <div class="control-group">   
                        <?php
                        if(isset($this->data['AnexoNotaFiscalServico']) 
                            && isset($this->data['AnexoNotaFiscalServico']['caminho_arquivo']) 
                            && !empty($this->data['AnexoNotaFiscalServico']['caminho_arquivo'])){

                                $arquivo_app = '';
                                if(!is_array($this->data['AnexoNotaFiscalServico']['caminho_arquivo'])) {
                                    if(!strstr($this->data['AnexoNotaFiscalServico']['caminho_arquivo'],'https://api.rhhealth.com.br')) {
                                        $arquivo_app = 'https://api.rhhealth.com.br'.$this->data['AnexoNotaFiscalServico']['caminho_arquivo'];
                                    }
                                }
                                
                                if(!empty($arquivo_app))
                                {
                                    echo '<div>'.$this->data['AnexoNotaFiscalServico']['descricao'].'  '.$this->Html->link($this->Html->tag('i','',array('class' => 'icon-file btn-anexos visualiza_anexo')), $arquivo_app, array('escape' => false, 'target' => '_blank', 'title' => 'Visualizar anexo do item')) .'</div>';
                                }
                            }
                        ?>
                        <?= $this->BForm->input('AnexoNotaFiscalServico.caminho_arquivo_binario', array('type'=>'file', 'label' => false, 'class' => 'input-file', 'data-param'=>'anexo_nota_fiscal_servico')); ?>
                    </div>  
                </div>
                <div class="span2">
                    <?= $this->BForm->button('&times;', array('type'=>'button', 'id' => 'LimparNotaFiscal', 'class' => 'close')); ?>
                </div>
            </div>    
        </div>
        <div class="span4">
            <label class="legend">Incluir Anexo Boleto</label>
            <div class="row-fluid inline">
                <div class="span4">
                    <div class="control-group">   
                        <?php
                        if(isset($this->data['AnexoNFsBoleto']) 
                            && isset($this->data['AnexoNFsBoleto']['caminho_arquivo']) 
                            && !empty($this->data['AnexoNFsBoleto']['caminho_arquivo'])){

                                $arquivo_app = '';
                                if(!is_array($this->data['AnexoNFsBoleto']['caminho_arquivo'])) {
                                    if(!strstr($this->data['AnexoNFsBoleto']['caminho_arquivo'],'https://api.rhhealth.com.br')) {
                                        $arquivo_app = 'https://api.rhhealth.com.br'.$this->data['AnexoNFsBoleto']['caminho_arquivo'];
                                    }
                                }
                                
                                if(!empty($arquivo_app))
                                {
                                    echo '<div>'.$this->data['AnexoNFsBoleto']['descricao'].'  '.$this->Html->link($this->Html->tag('i','',array('class' => 'icon-file btn-anexos visualiza_anexo')), $arquivo_app, array('escape' => false, 'target' => '_blank', 'title' => 'Visualizar anexo do item')) .'</div>';
                                }
                            }
                        ?>
                        <?= $this->BForm->input('AnexoNFsBoleto.caminho_arquivo_binario', array('type'=>'file', 'label' => false, 'class' => 'input-file', 'data-param'=>'anexo_nota_fiscal_servico_boleto')); ?>
                    </div>  
                </div>
                <div class="span2">
                    <?= $this->BForm->button('&times;', array('type'=>'button', 'id' => 'LimparBoleto', 'class' => 'close')); ?>
                </div>
            </div>    
        </div>
        <div class="span4">
            <label class="legend">Incluir Anexo Espelho de faturamento</label>
            <div class="row-fluid inline">
                <div class="span4">
                    <div class="control-group">   
                        <?php
                        if(isset($this->data['AnexoNFSEspelhoFaturamento']) 
                            && isset($this->data['AnexoNFSEspelhoFaturamento']['caminho_arquivo']) 
                            && !empty($this->data['AnexoNFSEspelhoFaturamento']['caminho_arquivo'])){

                                $arquivo_app = '';
                                if(!is_array($this->data['AnexoNFSEspelhoFaturamento']['caminho_arquivo'])) {
                                    if(!strstr($this->data['AnexoNFSEspelhoFaturamento']['caminho_arquivo'],'https://api.rhhealth.com.br')) {
                                        $arquivo_app = 'https://api.rhhealth.com.br'.$this->data['AnexoNFSEspelhoFaturamento']['caminho_arquivo'];
                                    }
                                }
                                
                                if(!empty($arquivo_app))
                                {
                                    echo '<div>'.$this->data['AnexoNFSEspelhoFaturamento']['descricao'].'  '.$this->Html->link($this->Html->tag('i','',array('class' => 'icon-file btn-anexos visualiza_anexo')), $arquivo_app, array('escape' => false, 'target' => '_blank', 'title' => 'Visualizar anexo do item')) .'</div>';
                                }
                            }
                        ?>
                        <?= $this->BForm->input('AnexoNFSEspelhoFaturamento.caminho_arquivo_binario', array('type'=>'file', 'label' => false, 'class' => 'input-file', 'data-param'=>'anexo_nota_fiscal_servico_espelho_faturamento')); ?>
                    </div>  
                </div>
                <div class="span2">
                    <?= $this->BForm->button('&times;', array('type'=>'button', 'id' => 'LimparEspelhoFaturamento', 'class' => 'close')); ?>
                </div>
            </div>    
        </div>
    </div>
    
    
    <div class="row-fluid inline">

        <div class="span9">
            
            <p class="legend">Recebimento e vencimento<p>
            
            <div class="row-fluid">
                <div class="span3">
                    <?= $ithealth->input('data_emissao', array('label' => 'Data Emissão', 'style' => 'margin-right:-24px; width:80%;', 'required' => true, 'disabled'=> $disabled,'type' => 'text', 'class' => 'datepickerjs date', 'multiple')); ?>
                </div>

                <div class="span3">
                    <?= $ithealth->input('data_recebimento', array('label' => 'Data de Recebimento', 'required' => true, 'style' => 'margin-right:-24px; width:80%;', 'type' => 'text', 'class' => 'datepickerjs date', 'multiple')); ?>
                </div>

                <div class="span3">
                    <?= $ithealth->selectTiposRecebimento('codigo_tipo_recebimento'); ?>
                </div>

                <div class="span3">
                    <?= $this->element('ithealth/chave_rastreamento_nfe_api', array('ithealth_element_config' => $arrElementConfig['input_chave_rastreamento'] )); ?>
                </div>               
            </div>
            <div class="row-fluid">
                <div class="span3">
                    <?php 
                    if ($liberar_edicao_vencimento) { 
                        echo $ithealth->input('data_vencimento', array('label' => 'Vencimento', 'type' => 'text', 'class' => 'datepickerjs date', 'multiple'));
                    } else { 
                        echo $ithealth->input('data_vencimento', array('label' => 'Vencimento', 'readonly' => true, 'type' => 'text', 'class' => 'date', 'multiple'));
                    } ?>
                    <img src="/portal/img/loading.gif" style="display: none;" id="data_vencimento_img_loading" />
                </div>
                <div class="span3">
                    <?= $ithealth->input('data_vencimento_prorrogado', array('label' => 'Vencimento Prorrogado', 'type' => 'text','style' => 'margin-right:-24px; width:80%;', 'class' => 'datepickerjs date', 'multiple')); ?>
                </div>

                <div class="span5">

                    <?= $ithealth->selectFormasPagamento('codigo_formas_pagto', array(), array('disabled'=> $disabled)); ?> 

                    <div class="row-fluid hide-boleto-solicitacao hidden">
                        <div class="span4">
                            
                            <?= $ithealth->input('baixa_boleto_data', array('label' => 'Data Boleto',
                                                                                    'type' => 'text',
                                                                                    'disabled'=> $disabled,
                                                                                    'style' => 'margin-right:-24px; width:80%;', 
                                                                                    'class' => 'datepickerjs date', 'multiple')); ?>

                        </div>
                        <div class="span8">

                            <?= $ithealth->input('baixa_boleto_descricao', array('label' => 'Solicitação de Baixa de Boleto', 'style'=> 'width:100%;')); ?>
                            
                        </div>
                    </div>

                </div>
            </div>  
        </div>
    </div>

    <div class="row-fluid inline">
        <div class="span12">

            <label class="legend">Observações</label>

            <div class="row-fluid inline">

                <div class="span6">
                    <?= $ithealth->input('observacao', array(
                        'label' => false, 
                        'type' => 'textarea',
                        'style'=> 'width:100%; height:60px'
                        )); 
                    ?>
                </div>
            </div>
        </div>
    </div>
    

    <div class="row-fluid inline">

        <div class="span8">

            <label class="legend">Liberação</label>

            <div class="row-fluid inline">

                <div class="span2">
                    <?= $ithealth->input('data_conclusao', array('label' => 'Data de Liberação', 'readonly' => true, 'style' => 'width:75%;','type' => 'text', 'class' => 'date conclusao', 'multiple')); ?>

                </div>

                <div class="span9">
                    <?= $ithealth->input('nome_usuario_conclusao', array(
                        'label' => 'Responsável pela Auditoria', 
                        'readonly' => true, 
                        'value' => $this->data['UsuarioConclusao']['apelido'],
                        'type' => 'text', 'style' => 'width:100%;', 'multiple')); ?>
                </div>

                    <?= $ithealth->input('codigo_usuario_conclusao', array(
                        'value' => $this->data['UsuarioConclusao']['codigo'],
                        'type' => 'hidden')); ?>

            </div>    

        </div>

        <div class="span4">

            <label class="legend">Pagamento</label>

            <div class="row-fluid">
                
                <div class="span4">
                    <?= $ithealth->input('data_pagamento', array('label' => 'Data de Pagamento', 'type' => 'text','style' => 'margin-right:-24px; width:80%;', 'class' => 'datepickerjs date', 'multiple')); ?>
                </div>

            </div>
        </div>
    </div>



</div>

<div class="form-actions row-fluid">
    <div class="span6">
        <?= $ithealth->buttonSalvar(array('disabled'=> $disabled)); ?>
        <?= $ithealth->buttonLinkVoltar(array('action' => 'index')); ?>
    </div>
    <div class="span3">
        <span class="muted">Criado Por: </span><?= $carimbo['criacao']['nome'] ?>  <br />
        <span class="muted">Data : </span><?= $carimbo['criacao']['data'] ?>
    </div>
    <div class="span3">
        <?php if($edit_mode):?>
        <span class="muted">Alterado Por: </span><?= $carimbo['alteracao']['nome'] ?>  <br />
        <span class="muted">Data : </span><?= $carimbo['alteracao']['data'] ?>
        <?php endif;?>
    </div>

</div>

<div id="mdlApiNFsObterCodigo" class="modal hide fade">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3>Chave Encontrada no Cadastro</h3>
  </div>
  <div class="modal-body">
    <p id="mdlApiNFsObterCodigoMessage"></p>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Fechar</button>
    <a href="#" class="btn btn-primary" id="mdlApiNFsObterCodigoOk">Editar</a>
  </div>
</div>
<!-- 
    POC SERPRO
<div id="mdlApiSerpro" class="modal hide fade">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/8/82/Serpro.svg/1280px-Serpro.svg.png" width="200"></h3>
  </div>
  <div class="modal-body">
      <h3>Dados desta chave foram encontrados</h3>
      <div id="mdlApiSerproMessage"> </div>

      <table class="table table-striped table-condensed">
              <tbody>
                <tr>
                  <td class="b">CNPJ</td>
                  <td>99348255000107</td>
                </tr>
                <tr>
                  <td class="b">Razão Social</td>
                  <td>EMPRESA DE SEGURANCA FICTICIA E SERVICOS LTDA - EPP</td>
                </tr>
                <tr>
                  <td class="b">Nome Fantasia</td>
                  <td>EMPRESA FICTICIA</td>
                </tr>
                <tr>
                  <td class="b">Emissão</td>
                  <td>2013-12-19</td>
                </tr>
                <tr>
                  <td class="b">Número NF</td>
                  <td>54341016</td>
                </tr>
                <tr>
                  <td class="b">Info</td>
                  <td>DOCUMENTO EMITIDO POR ME OU EPP OPTANTE PELO SIMPLES NACIONAL</td>
                </tr>
                <tr>
                  <td class="b">xProd</td>
                  <td>REF. A PRESTACAO DE SERVICO</td>
                </tr>
                <tr>
                  <td class="b">vProd</td>
                  <td>181,74</td>
                </tr>
              </tbody>
        </table>
      
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Fechar</button>
    <a href="#" class="btn btn-primary" id="mdlApiSerproOk">Deseja Utilizar?</a>
  </div>
</div>
 -->


<?php // echo $this->BForm->end(); ?>

<?php $this->addScript($this->Buonny->link_css('sweetalert')) ?>
<?php $this->addScript($this->Buonny->link_js('sweetalert.min')) ?>

<?php echo $this->Javascript->codeBlock("



    jQuery(document).ready(function() {
        setup_time();
        setup_mascaras();

        $('.datepickerjs').datepicker({
            dateFormat: 'dd/mm/yy',
            showOn : 'button',
            buttonImage : baseUrl + 'img/calendar.gif',
            buttonImageOnly : true,
            buttonText : 'Escolha uma data',
            dayNames : ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sabado'],
            dayNamesShort : ['Dom','Seg','Ter','Qua','Qui','Sex','Sab'],
            dayNamesMin : ['D','S','T','Q','Q','S','S'],
            monthNames : ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
            monthNamesShort : ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
            onClose : function() {
            }
        }).mask('99/99/9999');

        $('.conclusao').mask('99/99/9999');
    

        $('#LimparNotaFiscal').click(function(){
            $('#AnexoNotaFiscalServicoCaminhoArquivoBinario').val('');     
            $('#LimparNotaFiscal').hide();           
        });
    
        $('#LimparNotaFiscal').hide();
    
        $(document).on('change','#AnexoNotaFiscalServicoCaminhoArquivoBinario',function(){
            if( $(this).val() ){
                $('#LimparNotaFiscal').show();
            }
        });

        $('#LimparBoleto').click(function(){
            $('#AnexoNFsBoletoCaminhoArquivoBinario').val('');     
            $('#LimparBoleto').hide();           
        });
    
        $('#LimparBoleto').hide();
    
        $(document).on('change','#AnexoNFsBoletoCaminhoArquivoBinario',function(){
            if( $(this).val() ){
                $('#LimparBoleto').show();
            }
        });

        $('#LimparEspelhoFaturamento').click(function(){
            $('#AnexoNFSEspelhoFaturamentoCaminhoArquivoBinario').val('');     
            $('#LimparEspelhoFaturamento').hide();           
        });
    
        $('#LimparEspelhoFaturamento').hide();
    
        $(document).on('change','#AnexoNFSEspelhoFaturamentoCaminhoArquivoBinario',function(){
            if( $(this).val() ){
                $('#LimparEspelhoFaturamento').show();
            }
        });
        
        $('#NotaFiscalServicoFlagAcrescimo').change(function(){
                    
            if($(this).is(':checked')){
                $('.hide-motivo-acrescimo').removeClass('hidden');
            } else {
                $('.hide-motivo-acrescimo').addClass('hidden');
            }
    
        });

        $('#NotaFiscalServicoFlagDesconto').change(function(){
                    
            if($(this).is(':checked')){
                $('.hide-motivo-desconto').removeClass('hidden');
            } else {
                $('.hide-motivo-desconto').addClass('hidden');
            }
    
        });

        $('#NotaFiscalServicoCodigoFormasPagto').change(function(){
            
            var _this = $(this);     

            if(_this.val() == 1){
                
                $('.hide-boleto-solicitacao').removeClass('hidden');
            } else {
                $('.hide-boleto-solicitacao').addClass('hidden');
            }
    
        });

        $('#NotaFiscalServicoCodigoFormasPagto').trigger('change');


        function abrirDetalhes(_this){
            var dataId = $(_this).attr('data-id');
            console.log('The data-id of clicked item is: ' + dataId);
        }

        $('.btn').on('click', function(){
           var dataId = $(this).attr('data-id');
           console.log('data-id: ' + dataId);
            
            var dataCmd = $(this).attr('data-cmd');
            console.log('data-cmd: ', dataCmd);
        });


        var data_recebimento = $('#NotaFiscalServicoDataRecebimento');
        var data_emissao = $('#NotaFiscalServicoDataEmissao');
        var data_pagamento = $('#NotaFiscalServicoDataPagamento');
        var data_baixa_boleto = $('#NotaFiscalServicoBaixaBoletoData');

        data_recebimento.on('change', function () 
        {

            //verifica se tem o codigo do fornecedor
            var codigo_fornecedor = $('#select2-NotaFiscalServicoCodigoFornecedor-container').text();
            if(codigo_fornecedor == '') {
                swal({type: 'info', title: 'Atenção!', text: 'Não foi selecionado o fornecedor/credenciado para calcular a data de pagamento.'});
                return false;
            }

            if(moment($(this).val(), ['DD/MM/YYYY'], true).isValid()){
                obterFaturamentoDiasCredenciado(codigo_fornecedor);
            } else {
                $(this).val('');
                console.log('data_recebimento invalida', $(this).val());
            }
        });

        // data_emissao.on('change', function () 
        // {
        //     if(!moment($(this).val(), ['DD/MM/YYYY'], true).isValid()){
        //         $(this).val('');
        //     }

        //     // var dataHoje = moment();
        //     // var dataInicio = dataHoje.subtract(1, 'years').format('DD/MM/YYYY');
        //     // var dataFim = moment().add(1,'days').format('DD/MM/YYYY');
            
        //     // if(!moment($(this).val()).isBetween(dataInicio, dataFim)){
        //     //     $(this).val('');
        //     // }
          
        // });


        // data_pagamento.on('change', function () 
        // {
        //     // if(!moment($(this).val(), ['DD/MM/YYYY'], true).isValid() || !moment(data_emissao.val(), ['DD/MM/YYYY'], true).isValid() ){
        //     //     $(this).val('');
        //     // }                         
        // });

        data_baixa_boleto.on('change', function () 
        {
            if(!moment($(this).val(), ['DD/MM/YYYY'], true).isValid()){
                $(this).val('');
            }
        });

    }); // Jquery        



    function select2CallbackOnChange(responseData){
        obterFaturamentoDiasCredenciado(responseData.codigo_credenciado);
    }


    function obterFaturamentoDiasCredenciado(codigo_credenciado)
    {
        var service_url = baseUrl +'notas_fiscais_servico/obter_faturamento_credenciado/'+codigo_credenciado;
        var quantos_dias = $('#NotaFiscalServicoQuantosDias');
        var data_vencimento = $('#NotaFiscalServicoDataVencimento');
        var data_recebimento = $('#NotaFiscalServicoDataRecebimento');
        var quantos_dias_loading = $('#quantos_dias_img_loading');
        var data_vencimento_loading = $('#data_vencimento_img_loading');
        
        quantos_dias.val('');

        $.ajax({
            url: service_url,
            type: 'get',
            beforeSend: function(xhr) {
                quantos_dias_loading.show();
                data_vencimento_loading.show();
            },
            success: function(data) {

                if (typeof(data.faturamento_dias) != 'undefined' && data.faturamento_dias !== null && data.faturamento_dias !== ''){
                    
                    quantos_dias.val(data.faturamento_dias);
                    
                    validaCalculaQuantosDias();
                } else {
                    swal({type: 'info', title: 'Atenção!', text: 'Credenciado não possui data de faturamento cadastrado.'});
                    quantos_dias.val('');
                }
            },
            error: function(erro) {
                swal({type: 'info', title: 'Atenção!', text: 'Erro ao tentar obter a data de faturamento deste credenciado.'});
            },
            complete: function(){
                quantos_dias_loading.hide();
                data_vencimento_loading.hide();
            }
        });


    }

    
    function validaCalculaQuantosDias(){

        var data_recebimento = $('#NotaFiscalServicoDataRecebimento');
        var quantos_dias = $('#NotaFiscalServicoQuantosDias');
        var data_vencimento = $('#NotaFiscalServicoDataVencimento');

        if (typeof(quantos_dias.val()) != 'undefined' && quantos_dias.val() !== null && quantos_dias.val() !== ''){
            
            if(moment(data_recebimento.val(), ['DD/MM/YYYY'], true).isValid())
            {
    
                var data_recebimento_moment = moment(data_recebimento.val(), 'DD/MM/YYYY');
                
                var novaData = moment(data_recebimento_moment).add(parseInt(quantos_dias.val()-1), 'days');
                
                //ajuste para -1 para pegar o dia escolhido, ele estava incluindo mais 1, e alterando o data de vencimento. Ajuste feito para solucionar o problema apresentado no chamado CDCT-159
                
                var nova_data_vencimento = novaData.format('YYYY-MM-DD');
                
                //busca o dia do pagamento de acordo com o configurado
                $.ajax({
                    url: baseUrl +'notas_fiscais_servico/obter_nova_data/'+nova_data_vencimento,
                    type: 'get',
                    success: function(data) {

                        if (typeof(data.data_vencimento) != 'undefined' && data.data_vencimento !== null && data.data_vencimento !== ''){                            
                            data_vencimento.val(data.data_vencimento);
                        } else {
                            swal({type: 'info', title: 'Atenção!', text: 'Não foi possivel calcular a data de vencimento da nota.'});
                        }
                    }
                });

            } else {
                data_vencimento.val('');

            }
        }
        
    }//fim validaCalculaQuantosDias


    function callbackBeforeSend(xhr){
        var div = jQuery('div.well');
        bloquearDiv(div);
        bloquearSubmit();
    }

    function callbackSuccess(responseData){
        console.info('responseData',responseData);
        // document.location.reload();
    }

    function callbackError(error){
        console.error('error',error);
        // document.location.reload();
    }

    function callbackComplete(response){
        console.info('complete',response);
        var div = jQuery('div.well');
        desbloquearDiv(div);
        desbloquearSubmit();

    }
    

    function callbackBeforeSendApiNFsObterCodigo(xhr){
        var div = jQuery('div.well');
        bloquearDiv(div);
        bloquearSubmit();
    }

    function callbackSuccessApiNFsObterCodigo(responseData){
        console.info('responseData',responseData);
        // document.location.reload();
        
    }

    function callbackErrorApiNFsObterCodigo(error){
        console.error('error',error);
        // document.location.reload();
    }

    function callbackCompleteApiNFsObterCodigo(response){
        console.info('complete',response);
        var div = jQuery('div.well');
        desbloquearDiv(div);
        desbloquearSubmit();
        
        // strHtml = '';

        // $('#mdlApiSerproMessage').html(strHtml);

        // $('#mdlApiSerpro').modal('show');

        if(response.error){

            return;
        }

        $('#mdlApiNFsObterCodigoMessage').text('O código digitado foi encontrado, deseja alterar a nota com este código?');
        $('#mdlApiNFsObterCodigo').modal('show');
        
    }

    //mdlApiNFsObterCodigoOk

    // $('#myModal').on('hidden', function () {
    //     // do something…
    // })

    // function callbackBeforeSendApiSerpro(xhr){
    //     var div = jQuery('div.well');
    //     bloquearDiv(div);
    //     bloquearSubmit();
    // }

    // function callbackSuccessApiSerpro(responseData){
    //     console.info('responseData',responseData);
    //     // document.location.reload();
        
    // }

    // function callbackErrorApiSerpro(error){
    //     console.error('error',error);
    //     // document.location.reload();
    // }

    // function callbackCompleteApiSerpro(response){
    //     console.info('complete',response);
    //     var div = jQuery('div.well');
    //     desbloquearDiv(div);
    //     desbloquearSubmit();

    // }


    $('#mdlApiNFsObterCodigo').on('click', function(){
        var dataId = $(this).attr('data-id');
        console.log('data-id: ' + dataId);
         
         var dataCmd = $(this).attr('data-cmd');
         console.log('data-cmd: ', dataCmd);
     });



"); 
