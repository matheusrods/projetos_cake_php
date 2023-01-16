<div class="modal-dialog modal-sm" style="position: static;">
    <div class="modal-content" id="modal_data">
        <div class="modal-header" style="text-align: center;">
            <h3>Condições</h3>
        </div>

        <div class="modal-body" style="min-height: 295px;max-height: 360px;">

            <?php
            echo $this->BForm->hidden('codigo', array('value' => $codigo));
            echo $this->BForm->hidden('codigo_cliente', array('value' => $codigo_cliente));
            echo $this->BForm->hidden('codigo_tema', array('value' => $codigo_tema));
            echo $this->BForm->hidden('codigo_status', array('value' => $codigo_status));

            echo $this->BForm->hidden('codigo_pda_config_regra_condicao', array('value' => $codigo_pda_config_regra_condicao));

            ?>

            <?php if ($codigo_tema == 1 && $codigo_status <> 3) : ?>

                <div class='row-fluid inline acao_melhoria'>
                    <?php
                    foreach ($criticidade as $codigo_cr => $cr) :
                    ?>
                        <?php echo $this->BForm->input('codigo_pos_criticidade_' . $codigo_cr, array('label' => $cr, 'class' => 'input-acao-melhoria input_codigo_pos_criticidade', 'type' => 'checkbox', 'value' => $codigo_cr, 'div' => true)) ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if ($codigo_tema == 1 && $codigo_status == 3) : //tema acao de melhoria e status em andamento 
            ?>

                <div class='row-fluid inline '>
                    <?php echo $this->BForm->input('codigo_acoes_melhorias_status', array('label' => 'Tempo de vida da ação', 'class' => 'input input-acao-implantacao input-acao-eficacia input-acao-abrangencia', 'options' => $status)); ?>
                    <div id="condicoes_input">
                        <?php echo $this->BForm->input('condicao', array('label' => 'Condições', 'class' => 'input input-acao-implantacao input-acao-eficacia input-acao-abrangencia', 'empty' => 'Selecione uma Condição', 'options' => $condicoes)); ?>
                    </div>
                    <?php echo $this->BForm->input('qtd_dias', array('label' => 'Quantidade de dias', 'class' => 'input-large input-acao-implantacao input-acao-eficacia input-acao-abrangencia')); ?>
                </div>
                <div class='row-fluid inline acao_atraso acao_implantacao acao_eficacia acao_abrangencia'>
                    <label>Aplicável a:</label>
                    <?php echo $this->BForm->input('codigo_pos_criticidade', array('label' => 'Criticidade', 'class' => 'input input-acao-atraso input-acao-implantacao input-acao-eficacia input-acao-abrangencia', 'empty' => 'Selecione um Criticidade', 'options' => $criticidade)); ?>
                    <?php echo $this->BForm->input('codigo_origem_ferramentas', array('label' => 'Origem', 'class' => 'input input-acao-atraso input-acao-implantacao input-acao-eficacia input-acao-abrangencia', 'empty' => 'Selecione uma Origem', 'options' => $origem_ferramentas)); ?>
                    <?php echo $this->BForm->input('codigo_cliente_unidade', array('label' => 'Unidades', 'class' => 'input input-acao-atraso input-acao-implantacao input-acao-eficacia input-acao-abrangencia', 'empty' => 'Selecione uma Unidade', 'options' => $unidades)); ?>
                    <?php echo $this->BForm->input('codigo_cliente_bu', array('label' => 'Bussiness Unit', 'disabled' => 'disabled', 'class' => 'input input-acao-atraso input-acao-implantacao input-acao-eficacia input-acao-abrangencia', 'empty' => 'Selecione uma Bussiness Unit', 'options' => $cliente_bu)); ?>
                    <?php echo $this->BForm->input('codigo_cliente_opco', array('label' => 'Opco', 'disabled' => 'disabled', 'class' => 'input input-acao-atraso input-acao-implantacao input-acao-eficacia input-acao-abrangencia', 'empty' => 'Selecione um Opco', 'options' => $cliente_opco)); ?>
                </div>

            <?php endif; ?>

            <?php if ($codigo_tema == 2) : ?>
                <div class='row-fluid inline acao_atraso '>

                    <div class='row-fluid inline '>
                        <?php echo $this->BForm->input('qtd_dias', array('label' => 'Quantidade de dias após o vencimento', 'class' => 'input-large input-acao-atraso')); ?>
                        <?php echo $this->BForm->input(
                            'condicao',
                            array(
                                'label' => 'Condições',
                                'class' => 'input input-acao-implantacao input-acao-eficacia input-acao-abrangencia',
                                'empty' => 'Selecione uma Condição',
                                'options' => array(
                                    '>' => '>',
                                    '=' => '='
                                )
                            )
                        ); ?>
                    </div>
                </div>
                <div class='row-fluid inline acao_atraso acao_implantacao acao_eficacia acao_abrangencia'>
                    <label>Aplicável a:</label>
                    <?php echo $this->BForm->input('codigo_pos_criticidade', array('label' => 'Criticidade', 'class' => 'input input-acao-atraso input-acao-implantacao input-acao-eficacia input-acao-abrangencia', 'empty' => 'Selecione um Criticidade', 'options' => $criticidade)); ?>
                    <?php echo $this->BForm->input('codigo_origem_ferramentas', array('label' => 'Origem', 'class' => 'input input-acao-atraso input-acao-implantacao input-acao-eficacia input-acao-abrangencia', 'empty' => 'Selecione uma Origem', 'options' => $origem_ferramentas)); ?>
                    <?php echo $this->BForm->input('codigo_cliente_unidade', array('label' => 'Unidades', 'class' => 'input input-acao-atraso input-acao-implantacao input-acao-eficacia input-acao-abrangencia', 'empty' => 'Selecione uma Unidade', 'options' => $unidades)); ?>
                    <?php echo $this->BForm->input('codigo_cliente_bu', array('label' => 'Bussiness Unit', 'disabled' => 'disabled', 'class' => 'input input-acao-atraso input-acao-implantacao input-acao-eficacia input-acao-abrangencia', 'empty' => 'Selecione uma Bussiness Unit', 'options' => $cliente_bu)); ?>
                    <?php echo $this->BForm->input('codigo_cliente_opco', array('label' => 'Opco', 'disabled' => 'disabled', 'class' => 'input input-acao-atraso input-acao-implantacao input-acao-eficacia input-acao-abrangencia', 'empty' => 'Selecione um Opco', 'options' => $cliente_opco)); ?>
                </div>

            <?php endif; ?>

            <?php if (in_array($codigo_tema, array(3, 4, 5))) : ?>
                <div class='row-fluid inline '>
                    <?php echo $this->BForm->input('codigo_acoes_melhorias_status', array('label' => 'Tempo de vida da ação', 'class' => 'input input-acao-implantacao input-acao-eficacia input-acao-abrangencia', 'empty' => 'Tempo de vida da ação', 'options' => $status_imp_efi)); ?>
                    <?php echo $this->BForm->input('condicao', array('label' => 'Condições', 'class' => 'input input-acao-implantacao input-acao-eficacia input-acao-abrangencia', 'empty' => 'Selecione uma Condição', 'options' => $condicoes)); ?>
                    <?php echo $this->BForm->input('qtd_dias', array('label' => 'Quantidade de dias após o vencimento', 'class' => 'input-large input-acao-implantacao input-acao-eficacia input-acao-abrangencia')); ?>
                </div>
                <div class='row-fluid inline acao_atraso acao_implantacao acao_eficacia acao_abrangencia'>
                    <label>Aplicável a:</label>
                    <?php echo $this->BForm->input('codigo_pos_criticidade', array('label' => 'Criticidade', 'class' => 'input input-acao-atraso input-acao-implantacao input-acao-eficacia input-acao-abrangencia', 'empty' => 'Selecione um Criticidade', 'options' => $criticidade)); ?>
                    <?php echo $this->BForm->input('codigo_origem_ferramentas', array('label' => 'Origem', 'class' => 'input input-acao-atraso input-acao-implantacao input-acao-eficacia input-acao-abrangencia', 'empty' => 'Selecione uma Origem', 'options' => $origem_ferramentas)); ?>
                    <?php echo $this->BForm->input('codigo_cliente_unidade', array('label' => 'Unidades', 'class' => 'input input-acao-atraso input-acao-implantacao input-acao-eficacia input-acao-abrangencia', 'empty' => 'Selecione uma Unidade', 'options' => $unidades)); ?>
                    <?php echo $this->BForm->input('codigo_cliente_bu', array('label' => 'Bussiness Unit', 'disabled' => 'disabled', 'class' => 'input input-acao-atraso input-acao-implantacao input-acao-eficacia input-acao-abrangencia', 'empty' => 'Selecione uma Bussiness Unit', 'options' => $cliente_bu)); ?>
                    <?php echo $this->BForm->input('codigo_cliente_opco', array('label' => 'Opco', 'disabled' => 'disabled', 'class' => 'input input-acao-atraso input-acao-implantacao input-acao-eficacia input-acao-abrangencia', 'empty' => 'Selecione um Opco', 'options' => $cliente_opco)); ?>

                </div>

            <?php endif; ?>

            <?php if ($codigo_tema == 6 || $codigo_tema == 7) : ?>
                <div class='row-fluid inline acao_cancelamento acao_postergamento'>
                    <label>Aplicável a:</label>
                    <?php echo $this->BForm->input('codigo_cliente_unidade', array('label' => 'Unidades', 'class' => 'input input-acao-atraso input-acao-implantacao input-acao-eficacia input-acao-abrangencia', 'empty' => 'Selecione uma Unidade', 'options' => $unidades)); ?>
                    <?php echo $this->BForm->input('codigo_cliente_bu', array('label' => 'Bussiness Unit', 'disabled' => 'disabled', 'class' => 'input input-acao-cancelamento input-acao-postergamento', 'empty' => 'Selecione uma Bussiness Unit', 'options' => $cliente_bu)); ?>
                    <?php echo $this->BForm->input('codigo_cliente_opco', array('label' => 'Opco', 'disabled' => 'disabled', 'class' => 'input input-acao-cancelamento input-acao-postergamento', 'empty' => 'Selecione um Opco', 'options' => $cliente_opco)); ?>
                </div>

            <?php endif; ?>

        </div>

        <div class="modal-footer">
            <div class="right">
                <a href="javascript:void(0);" class="btn btn-danger" id="btn-close-modal">FECHAR</a>
                <a href="javascript:void(0);" class="btn btn-success" id="btn-submit-modal">SALVAR</a>
            </div>
        </div>
    </div>
</div>

<?php
echo $this->Javascript->codeBlock(' ', false);
?>

<script type="text/javascript">
    var ControleTelaCadastroCondicao = (function() {
        return {
            init: function() {

                this.inputCodigo = $('input[id="codigo"][type="hidden"]');
                this.inputCodigoCliente = $('input[id="codigo_cliente"][type="hidden"]');
                this.inputCodigoTema = $('input[id="codigo_tema"][type="hidden"]');
                this.inputCodigoStatus = $('input[id="codigo_status"][type="hidden"]');
                this.inputCodigoPdaConfigRegraCondicao = $('input[id="codigo_pda_config_regra_condicao"][type="hidden"]');
                this.inputCodigoOrigemFerramentas = $('input[id="codigo_origem_ferramentas"][type="hidden"]');
                this.inputQtdDias = $('input[id="qtd_dias"][type="text"]');

                this.inputsCheckboxCodigoPosCriticidade = $('.codigo_pos_criticidade');

                this.selectCodigoAcoesMelhoriasStatus = $('select[id="codigo_acoes_melhorias_status"]');
                this.selectCondicao = $('select[id="condicao"]');
                this.selectCodigoPosCriticidade = $('select[id="codigo_pos_criticidade"]');
                this.selectCodigoOrigemFerramentas = $('select[id="codigo_origem_ferramentas"]');
                this.selectCodigoClienteUnidade = $('select[id="codigo_cliente_unidade"]');
                this.selectCodigoClienteBu = $('select[id="codigo_cliente_bu"]');
                this.selectCodigoClienteOpco = $('select[id="codigo_cliente_opco"]');

                this.divModalData = $('div[id="modal_data"]');
                this.divCondicoesInput = $('div[id="condicoes_input"]');

                this.buttonCloseModal = $('a[id="btn-close-modal"]');
                this.buttonSubmitModal = $('a[id="btn-submit-modal"]');

                setup_mascaras();
                setup_datepicker();
                setup_time();

                this.sweepSelectCodigoClienteUnidadeOptions();
                this.sweepSelectCodigoClienteBuOptions();
                this.sweepSelectCodigoClienteOpcoOptions();

                this.events();
            },
            events: function() {

                this.selectCodigoAcoesMelhoriasStatus.on('change', this.handleSelectCodigoAcoesMelhoriasStatusChange.bind(this));
                this.selectCodigoClienteUnidade.on('change', this.handleSelectCodigoClienteUnidadeChange.bind(this));
                this.selectCodigoClienteBu.on('change', this.handleSelectCodigoClienteBuChange.bind(this));

                this.buttonCloseModal.on('click', this.handleButtonCloseModalClick.bind(this));
                this.buttonSubmitModal.on('click', this.handleButtonSubmitModalClick.bind(this));
            },
            handleButtonCloseModalClick: function() {
                cad_condicoes(0);
            },
            handleButtonSubmitModalClick: function() {
                this.saveCondicoes();
            },
            handleSelectCodigoAcoesMelhoriasStatusChange: function(e) {

                var codigoAcoesMelhoriasStatus = $(e.currentTarget).val();

                if (codigoAcoesMelhoriasStatus == 13)
                    this.divCondicoesInput.hide();
                else
                    this.divCondicoesInput.show();
            },
            handleSelectCodigoClienteUnidadeChange: function(e) {

                var codigoClienteUnidade = $(e.currentTarget).val();

                this.ajaxCodigoClienteBuOptions(codigoClienteUnidade);
            },
            ajaxCodigoClienteBuOptions: function(codigoClienteUnidade) {

                $.ajax({
                    'url': baseUrl + 'pda_config_regra/combo_bu_ajax/' + codigoClienteUnidade + '/' + Math.random(),
                    'dataType': 'json',
                    'beforeSend': function() {
                        this.selectCodigoClienteBu.html('');
                        bloquearDiv(this.selectCodigoClienteBu.parent());
                    }.bind(this),
                    'success': function(result) {

                        this.selectCodigoClienteBu.append($('<option />').val('').text('Selecione uma Business Unit'));

                        if (result != null) {
                            $.each(result, function(i, r) {
                                this.selectCodigoClienteBu.append($('<option />').val(i).text(r));
                            }.bind(this));
                        }

                        this.selectCodigoClienteOpco.find('option').remove();
                        this.selectCodigoClienteOpco.append($('<option />').val('').text('Selecione um Opco'));
                        this.selectCodigoClienteOpco.attr('disabled', 'disabled');
                    }.bind(this),
                    'complete': function() {
                        desbloquearDiv(this.selectCodigoClienteBu.parent());
                        this.selectCodigoClienteBu.removeAttr('disabled');
                    }.bind(this)
                });
            },
            handleSelectCodigoClienteBuChange: function(e) {

                var codigoClienteUnidade = this.selectCodigoClienteUnidade.val();
                var codigoClienteBu = this.selectCodigoClienteBu.val();

                this.ajaxCodigoClienteOpcoOptions({
                    codigoClienteUnidade,
                    codigoClienteBu
                });
            },
            ajaxCodigoClienteOpcoOptions: function({
                codigoClienteUnidade,
                codigoClienteBu
            }) {

                $.ajax({
                    'url': baseUrl + 'pda_config_regra/combo_opco_ajax/' + codigoClienteUnidade + '/' + codigoClienteBu + '/' + Math.random(),
                    'dataType': 'json',
                    'beforeSend': function() {
                        this.selectCodigoClienteOpco.html('');
                        bloquearDiv(this.selectCodigoClienteOpco.parent());
                    }.bind(this),
                    'success': function(result) {

                        this.selectCodigoClienteOpco.append($('<option />').val('').text('Selecione um Opco'));

                        if (result != null) {
                            $.each(result, function(i, r) {
                                this.selectCodigoClienteOpco.append($('<option />').val(i).text(r));
                            }.bind(this));
                        }
                    }.bind(this),
                    'complete': function() {

                        desbloquearDiv(this.selectCodigoClienteOpco.parent());
                        this.selectCodigoClienteOpco.removeAttr('disabled');
                    }.bind(this)
                });
            },
            sweepSelectCodigoClienteUnidadeOptions: function() {

                this.selectCodigoClienteUnidade.find('option').each(function() {
                    if ($(this).is(':selected') && $(this).val() != '')
                        $(this).removeAttr('disabled');
                });
            },
            sweepSelectCodigoClienteBuOptions: function() {

                this.selectCodigoClienteBu.find('option').each(function() {
                    if ($(this).is(':selected') && $(this).val() != '')
                        $(this).removeAttr('disabled');
                });
            },
            sweepSelectCodigoClienteOpcoOptions: function() {
                this.selectCodigoClienteOpco.find('option').each(function() {
                    if ($(this).is(':selected') && $(this).val() != '')
                        $(this).removeAttr('disabled');
                });
            },
            saveCondicoes: function() {

                var codigo = this.inputCodigo.val();
                var codigoCliente = this.inputCodigoCliente.val();
                var codigoPdaConfigRegraCondicao = this.inputCodigoPdaConfigRegraCondicao.val();

                var arrObjPosCriticidade = [];

                this.inputsCheckboxCodigoPosCriticidade.each(function() {
                    if ($(this).prop("checked")) {
                        arrObjPosCriticidade.push({
                            id: $(this).val()
                        });
                    }
                });

                var qtdDias = this.inputQtdDias.val();
                var codigoPosCriticidade = this.selectCodigoPosCriticidade.val();
                var codigoOrigemFerramentas = this.selectCodigoOrigemFerramentas.val();
                var codigoClienteUnidade = this.selectCodigoClienteUnidade.val();
                var codigoClienteBu = this.selectCodigoClienteBu.val();
                var codigoClienteOpco = this.selectCodigoClienteOpco.val();
                var codigoAcoesMelhoriasStatus = this.selectCodigoAcoesMelhoriasStatus.val();
                var condicao = this.selectCondicao.val();

                this.ajaxSaveCondicoes({
                    codigo,
                    codigoCliente,
                    codigoPdaConfigRegraCondicao,
                    arrObjPosCriticidade,
                    qtdDias,
                    codigoPosCriticidade,
                    codigoOrigemFerramentas,
                    codigoClienteUnidade,
                    codigoClienteBu,
                    codigoClienteOpco,
                    codigoAcoesMelhoriasStatus,
                    condicao
                });
            },
            ajaxSaveCondicoes: function({
                codigo,
                codigoCliente,
                codigoPdaConfigRegraCondicao,
                arrObjPosCriticidade,
                qtdDias,
                codigoPosCriticidade,
                codigoOrigemFerramentas,
                codigoClienteUnidade,
                codigoClienteBu,
                codigoClienteOpco,
                codigoAcoesMelhoriasStatus,
                condicao
            }) {

                $.ajax({
                    url: baseUrl + 'pda_config_regra/salvar_condicoes',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        "codigo": codigo,
                        "codigo_cliente": codigoCliente,
                        "codigo_pda_config_regra_condicao": codigoPdaConfigRegraCondicao,
                        "arr_obj_pos_criticidade": arrObjPosCriticidade,
                        "qtd_dias": qtdDias,
                        "codigo_pos_criticidade": codigoPosCriticidade,
                        "codigo_origem_ferramentas": codigoOrigemFerramentas,
                        "codigo_cliente_unidade": codigoClienteUnidade,
                        "codigo_cliente_opco": codigoClienteOpco,
                        "codigo_cliente_bu": codigoClienteBu,
                        "codigo_acoes_melhorias_status": codigoAcoesMelhoriasStatus,
                        "condicao": condicao,
                    },
                    beforeSend: function() {

                        bloquearDiv(this.divModalData);
                    }.bind(this),
                    complete: function(data) {

                        if (data.retorno == 'false') {
                            swal({
                                type: 'warning',
                                title: 'Atenção',
                                text: data.mensagem,
                            });

                        } else {
                            cad_condicoes(0);
                            atualizaListaCondicoes();
                        }

                        desbloquearDiv(this.divModalData);
                    }.bind(this),
                });
            }
        };
    })();

    var mensagem = function(mensagem, tipo, titulo) {

        this.tipo = tipo || 'warning'
        this.titulo = titulo || 'Atenção'

        return swal({
            type: this.tipo,
            title: this.titulo,
            text: mensagem
        });
    }

    function isValidDate(d) {
        return d instanceof Date && !isNaN(d);
    }

    $(function() {
        ControleTelaCadastroCondicao.init();
    });
</script>