<?php
    $url_current = $this->Html->url( str_replace('/portal', '', $this->here), false ); 
?>

<ul class="nav nav-tabs">
    <li id="geraisF" class="active"><a href="#gerais" data-toggle="tab">Dados Gerais</a></li>
    <?php if ($edit_mode): ?>
        <li id="dados_bancariosF"><a href="#dados_bancarios" data-toggle="tab">Dados Bancários</a></li>
        <li id="contatosF"><a href="#contatos" data-toggle="tab">Contatos</a></li>
        <li id="documentosF"><a href="#documentos" data-toggle="tab">Documentos</a></li>

        <?php if($saude > 0): ?>
            <li><a href="#saude" id="saude_liberado" data-toggle="tab">Saúde</a></li>
        <?php endif;  ?>

        <?php if($seguranca > 0): ?>
            <li id="segurancaF"><a href="#seguranca" data-toggle="tab">Segurança</a></li>
        <?php endif;  ?>
    <?php endif; ?>
    <li id="aba-dados_fotos" class="tab-pane <?php echo (isset($aba) && $aba == 'dados_fotos') ? 'active' : '' ?>"><a href="#dados_fotos" data-toggle="tab">Fotos do Estabelecimento</a></li>
    <?php if ($edit_mode): ?><li id="historicoF"><a href="#historico" data-toggle="tab">Histórico</a></li><?php endif; ?>
</ul>

<div class="well fornecedor_well" style="<?php if (!empty($bloquear) && $bloquear == true) { echo 'display:none;' ; } else { echo 'display: block;';}; ?>">
    <div class="row-fluid inline">
        <?php if($edit_mode): ?>
            <?php echo $this->BForm->input('Fornecedor.codigo',array('class' => 'input-mini', 'type' => 'text', 'label' => 'Código', 'readonly' => true)); ?>
        <?php else: ?>
            <?php echo $this->BForm->hidden('Fornecedor.codigo'); ?>
        <?php endif; ?>
        <?php echo $this->BForm->input('Fornecedor.razao_social', array('class' => 'input-xxlarge', 'label' => 'Razão Social (*)')); ?>
        <?php echo $this->BForm->input('Fornecedor.nome', array('class' => 'input-xlarge', 'label' => 'Nome Fantasia (*)')); ?>
    </div>
    <div class="row-fluid inline">
        <?php /*if($edit_mode == 1):?>
        <?php echo $this->BForm->input('Fornecedor.tipo_unidade_tipo', array('class' => 'input-medium', 'options' => array('F' => 'Fiscal', 'O' => 'Operacional'), 'label' => 'Tipo Filial', 'default' => 'F', 'value' => empty($this->data['Fornecedor']['tipo_unidade'])? 'F' : $this->data['Fornecedor']['tipo_unidade'], 'disabled' => true)); ?>
        <?php echo $this->BForm->input('Fornecedor.tipo_unidade', array('type' => 'hidden','value' => empty($this->data['Fornecedor']['tipo_unidade'])? 'F' : $this->data['Fornecedor']['tipo_unidade'])); ?>
        <?php else: */?>
        <?php echo $this->BForm->input('Fornecedor.tipo_unidade', array('class' => 'input-medium', 'options' => array('F' => 'Fiscal', 'O' => 'Operacional'), 'value' => empty($this->data['Fornecedor']['tipo_unidade'])? 'F' : $this->data['Fornecedor']['tipo_unidade'], 'label' => 'Tipo Filial', 'default' => 'F', 'onchange' => 'tipo_filial_fornecedor();' )); ?>
        <div id="fornecedor_matriz">
            <?php if($edit_mode): ?>
                <?php echo $this->BForm->input('Fornecedor.codigo_documento', array('class' => 'input-medium', 'label' => 'CNPJ (*)', 'readonly' => true)); ?>
            <?php else: ?>
                <?php echo $this->BForm->input('Fornecedor.codigo_documento', array('class' => 'input-medium', 'label' => 'CNPJ (*)')); ?>
            <?php endif; ?>
        </div>
        <div id="fornecedor_matriz_filial">
            <?php echo $this->Buonny->input_codigo_fornecedor($this, 'codigo_fornecedor_fiscal', 'Código', true, 'Fornecedor', '');?>
        </div>
        <?php if(!empty($this->passedArgs[0])): ?>
            <?php echo $this->BForm->input('Fornecedor.ativo', array('label' => 'Status(*)', 'class' => 'input-small', 'default' => '','empty' => 'Status', 'options' => array(1 => 'Ativo',0=> 'Inativo'))); ?>
        <?php else: ?>
            <?php echo $this->BForm->hidden('Fornecedor.ativo', array('value' => 1)); ?>
        <?php endif;  ?>
        <?php echo $this->BForm->input('Fornecedor.codigo_soc', array('label' => 'Código Externo', 'class' => 'input-small')); ?>
        <?php echo $this->BForm->input('Fornecedor.codigo_fornecedor_recebedor', array('label' => 'Cód. Cred. Recebedor', 'class' => 'input-small')); ?>
    </div>
</div>

<div class="tab-content">
    <div class="tab-pane active" id="gerais">
        <div class="well">
            <div class="row-fluid inline">
                <?php echo $this->BForm->input('Fornecedor.responsavel_administrativo', array('label' => 'Responsável Administrativo (*)', 'class' => 'input-xlarge'));?>

                <?php echo $this->BForm->input('Fornecedor.cnes', array('label' => 'CNES', 'class' => 'input-xlarge'));?>

                <?php echo $this->BForm->input('Fornecedor.data_contratacao', array('value' => AppModel::dbDateToDate($this->data['Fornecedor']['data_contratacao']), 'label' => 'Data de Contratação', 'type' => 'text', 'class' => 'datepickerjs date input-small form-control', 'multiple')); ?>
            </div>
            <div class="row-fluid inline">
                <div class="span3 control-group" style="margin-left: 0">
                    <label>Fornecedor Interno</label>
                    <?php echo $this->BForm->input('Fornecedor.interno', array('legend' => false, 'options' => array('1' => 'Sim', '0' => 'Não'), 'value' => empty($this->data['Fornecedor']['interno'])? 0: $this->data['Fornecedor']['interno'], 'type' => 'radio', 'hiddenField' => false)); ?>
                </div>
                <div class="span3 control-group" style="margin-left: -107px;">
                    <label>Modalidade de Atendimento</label>
                    <?php echo $this->BForm->input('Fornecedor.acesso_portal', array('value' => $this->data['Fornecedor']['acesso_portal'],'legend' => false, 'options' => array('3' => 'Atendimento Online', '2' => 'Digitação Técnica', '1' => 'Baixa de Exame'), 'type' => 'radio', 'hiddenField' => false)); ?>
                </div>
                <div class="span3 control-group" style="margin-left: -52px;" id="prestador">
                    <label>Prestador Qualificado</label>
                    <?php echo $this->BForm->input('Fornecedor.prestador_qualificado', array('value' => empty($this->data['Fornecedor']['prestador_qualificado']) ? 0 : $this->data['Fornecedor']['prestador_qualificado'], 'legend' => false, 'options' => array('1' => 'Sim', '0' => 'Não'), 'type' => 'radio', 'hiddenField' => false)); ?>
                </div>
                <div class="span3 control-group" style="margin-left: -52px;" id="prestador">
                    <label>Prestador Particular?</label>
                    <?php echo $this->BForm->input('Fornecedor.prestador_particular', array('value' => empty($this->data['Fornecedor']['prestador_particular']) ? 0 : $this->data['Fornecedor']['prestador_particular'], 'legend' => false, 'options' => array('1' => 'Sim', '0' => 'Não'), 'type' => 'radio', 'hiddenField' => false)); ?>
                </div>
            </div>
        </div>
        <?php echo $this->element('fornecedores_enderecos/fields') ?>
    </div>

    <?php if ($edit_mode): ?>
        <div class="tab-pane" id="dados_bancarios">
            <?php echo $this->element('fornecedores/dados_bancarios') ?>
        </div>

        <div class="tab-pane" id="contatos">
            <?php echo $this->element('fornecedores_contatos/contatos') ?>

            <?php echo $this->element('fornecedores_contatos/contatos_agendamento') ?>
        </div>

        <div class="tab-pane " id="documentos">
            <?php echo $this->element('fornecedores/documentos') ?>
        </div>

        <?php if($saude > 0): ?>
            <div class="tab-pane " id="saude">
                <?php echo $this->element('fornecedores/saude') ?>
            </div>
        <?php endif;?>

        <?php if($seguranca > 0): ?>
            <div class="tab-pane" id="seguranca">
                <?php echo $this->element('fornecedores/seguranca') ?>
            </div>
        <?php endif;?>

        <div class="tab-pane <?php echo (isset($aba) && $aba == 'dados_fotos') ? 'active' : '' ?>" id="dados_fotos">
            <h3>Fotos do Estabelecimento</h3>
            <div class='actionbar-right'>
                <?php echo $this->Html->link('<i class="icon-edit icon-white"></i> Gerenciar imagens', array('controller' => 'fotos_fornecedor', 
                'action' => 'listagem', $codigo_fornecedor, '?' => array('url_retorno' => $url_current)), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar Novas Imagens'));?>
            </div>
            <?php if(count($fotos)) : ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th >Descrição</th>
                            <th >Foto</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(count($fotos)) : ?>
                            <?php foreach($fotos as $key => $f): ?>
                                <tr id="arquivo_<?php echo $f['FornecedorFoto']["codigo"]; ?>">
                                    <td><?php echo $f['FornecedorFoto']['descricao'] ?></td>
                                    <td>
                                        <?php if($f['FornecedorFoto']['caminho_arquivo'] && !empty($f['FornecedorFoto']['caminho_arquivo'])) : ?>
                                            <img src="https://api.rhhealth.com.br<?php echo $f['FornecedorFoto']['caminho_arquivo']; ?>" />
                                        <?php else: ?>		
                                            <p style="margin:30px">Imagem não encontrada.</p>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>    	
                        <?php endif; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        <div class="tab-pane" id="historico">
            <?php echo $this->element('fornecedores_historicos/historico') ?>
        </div>    
    <?php endif; ?>
</div>
<div class="form-actions">
    <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
    <?= $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>
</div>    
<?php echo $this->BForm->end(); ?>
<?php $this->addScript($this->Buonny->link_js('fornecedores.js')); ?>

<?php echo $this->Javascript->codeBlock("
    jQuery(document).ready(function() {
        setup_time();
        setup_mascaras();

        tipo_filial_fornecedor();     
    });

    function tipo_filial_fornecedor(){
        var tipo_unidade = $('#FornecedorTipoUnidade');
        if($(tipo_unidade).val() == 'O'){
            $('#fornecedor_matriz_filial').show();
            $('#fornecedor_matriz').hide();   
        }
        else{
            $('#fornecedor_matriz').show();
            $('#fornecedor_matriz_filial').hide();   
        }
    }  
"); ?>

<script type="text/javascript">
    $(document).ready(function() {
        setup_mascaras();
        setup_datepicker();
        setup_time();

        <?php if (!empty($bloquear) && $bloquear == true): ?>
             // $(".fornecedor_well input, .fornecedor_well select").attr("disabled", "disabled");
              $("#gerais").removeClass("active");
             // $("#geraisF, #dados_bancariosF, #contatosF, #documentosF, #segurancaF, #aba-dados_fotos, #historicoF").remove();
            
              $("#saude_liberado").click();
               
        <?php endif; ?>

    });

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
        onClose : function() {}
    }).mask("99/99/9999");

    //se o usuario clicar em qualquer uma das 3 opcoes do modalidade atendimento, ele clica sozinho no sozinho do campo prestador qualificado
    $("input:radio[name='data[Fornecedor][acesso_portal]']").click(function() {
        // alert("Funcionou, te vira agora.");
        $("INPUT[name='data[Fornecedor][prestador_qualificado]']").val(['1']);
    });

    $("input:radio[name='data[Fornecedor][prestador_qualificado]']").click(function() {
        // alert("Funcionou, te vira agora.");
        $("INPUT[name='data[Fornecedor][acesso_portal]']").removeAttr("checked");
    });

    //****** TRECHO COMENTADO POR QUE ELA PEDIU QUE QUANDO CLICAR EM TODAS AS MODALIDADES DE ATENDIMENTO ELE SELECIONE SIM NO PRESTADOR QUALIFICADO *********

    // $("input:radio[id='FornecedorAcessoPortal1']").click(function() {
    //     // alert("Funcionou, te vira agora.");
    //     $("INPUT[name='data[Fornecedor][prestador_qualificado]']").val(['0']);
    // });


</script>  


<?php
    if (!empty($bloquear) && $bloquear == true) {   
?>
        <style>
            .nav-tabs li a {
                pointer-events: none;
            }
        </style>             
             
<?php 
    }        
?>
