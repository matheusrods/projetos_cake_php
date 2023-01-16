<div class='well'>
    <?php echo $bajax->form('Fornecedor', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Fornecedor', 'element_name' => 'fornecedores'), 'divupdate' => '.form-procurar')) ?>
    <div class="row-fluid inline">
        <?php echo $this->BForm->input('codigo', array('class' => 'input-mini just-number', 'label' => 'Código', 'type' => 'text')) ?>
        <?php echo $this->BForm->input('razao_social', array('class' => 'input-xlarge', 'label' => 'Razão Social')) ?>
        <?php echo $this->BForm->input('nome', array('class' => 'input-xlarge', 'label' => 'Nome Fantasia')) ?>
        <?php echo $this->BForm->input('codigo_documento', array('class' => 'input-medium', 'label' => 'CNPJ')) ?>
        <?php echo $this->BForm->input('ativo', array('label' => 'Status', 'class' => 'input-small', 'default' => 1,'options' => array(0 => 'Inativo', 1 => 'Ativo'))); ?>
    </div>
    <div class="row-fluid inline" class="span12">
        <div class="span1">
            <?php echo $this->BForm->input('estado', array('class' => 'input-small', 'label' => 'Estado', 'options' => $estados, 'empty' => 'Selecione', 'default' => '')) ?>
        </div>
        <div class="span3">
            <span id="cidade_combo" style="display: ;">
                <?php echo $this->BForm->input('cidade', array('class' => 'input-xlarge', 'label' => 'Cidade')) ?>
            </span>    
        </div>
        <div class="span3" style="margin-left: 22px">
            <?php echo $this->BForm->input('bairro', array('class' => 'input-medium', 'label' => 'Bairro')) ?>
        </div>
    </div>
        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
    <?php echo $this->BForm->end() ?>
</div>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        atualizaListaFornecedores();
        setup_mascaras();
        setup_datepicker(); 
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Fornecedor/element_name:fornecedores/" + Math.random())
        });
    });
    
    function buscaCidade(element) {
        var idEstado = $(element).val();
        $.ajax({
            type: "POST",
            url: "/portal/enderecos/carrega_combo_cidade_abreviacao/" + idEstado,
            dataType: "html",
            beforeSend: function() { 
                $("#cidade_combo").hide();
                $("#carregando_cidade").show();
            },
            success: function(retorno) {
                $("#FornecedorCidade").html(retorno);
            },
            complete: function() { 
                $("#carregando_cidade").hide();
                $("#cidade_combo").show();
            }
        });
    }
', false);
?> 
