<div class='well'>
    <?php echo $bajax->form('FornecedorCapacidadeAgenda', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'FornecedorCapacidadeAgenda', 'element_name' => 'fornecedores_capacidade_agenda'), 'divupdate' => '.form-procurar')) ?>
        <div class="row-fluid inline">
            <?php echo $this->BForm->input('codigo', array('class' => 'input-mini just-number', 'label' => 'Código', 'type' => 'text')) ?>
            <?php echo $this->BForm->input('razao_social', array('class' => 'input-xlarge', 'label' => 'Razão Social')) ?>
            <?php echo $this->BForm->input('nome', array('class' => 'input-xlarge', 'label' => 'Nome Fantasia')) ?>
            <?php echo $this->BForm->input('codigo_documento', array('class' => 'input-medium', 'label' => 'CNPJ')) ?>
            <?php echo $this->BForm->input('ativo', array('label' => 'Status', 'class' => 'input-small', 'default' => 1,'options' => array(0 => 'Inativo', 1 => 'Ativo'))); ?>
        </div>
        <div class="row-fluid inline" class="span12">
    <div class="span1">
        <?php echo $this->BForm->input('estado', array('class' => 'input-small', 'label' => 'Estado', 'options' => $estados, 'empty' => 'Selecione', 'default' => '', 'onchange' => 'buscaCidade(this);')) ?>
    </div>
    <div class="span3">
        <span id="cidade_combo" style="display: ;">
            <?php echo $this->BForm->input('cidade', array('label' => 'Cidade', 'class' => 'form-control input-large', 'default' => '','options' => $cidades)); ?>
        </span>
        <span id="carregando_cidade" style="display: none;">
            <label>Cidade</label>
            <img src="/portal/img/ajax-loader.gif" border="0" style="padding-top: 7px;"/>
        </span> 
    </div> 
</div>
        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
    <?php echo $this->BForm->end() ?>
</div>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        atualizaListaFornecedoresAgenda();
        setup_mascaras();
        setup_datepicker(); 

        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:FornecedorCapacidadeAgenda/element_name:fornecedores_capacidade_agenda/" + Math.random())
        });
    });
    
    function buscaCidade(element) {
        var idEstado = $(element).val();
        $.ajax({
            type: "POST",
            url: "/portal/enderecos/carrega_combo_cidade/" + idEstado,
            dataType: "html",
            beforeSend: function() { 
                $("#cidade_combo").hide();
                $("#carregando_cidade").show();
            },
            success: function(retorno) {
                $("#FornecedorCapacidadeAgendaCidade").html(retorno);
            },
            complete: function() { 
                $("#carregando_cidade").hide();
                $("#cidade_combo").show();
            }
        });
    }
', false);

?> 