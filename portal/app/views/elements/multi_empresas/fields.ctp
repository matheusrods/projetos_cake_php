<ul class="nav nav-tabs">
    <li class="active"><a href="#gerais" data-toggle="tab">Dados Gerais</a></li>
</ul>
<div class="well">
    <div class="row-fluid inline">
        <?php echo $this->BForm->hidden('MultiEmpresa.codigo'); ?>
        <?php echo $this->BForm->input('MultiEmpresa.razao_social', array('class' => 'input-xxlarge', 'label' => 'Razão Social (*)')); ?>
        <?php echo $this->BForm->input('MultiEmpresa.nome_fantasia', array('class' => 'input-xxlarge', 'label' => 'Nome Fantasia (*)')); ?>
    </div>
    <div class="row-fluid inline">
        <?php echo $this->BForm->input('MultiEmpresa.codigo_documento', array('class' => 'input-medium', 'label' => 'CNPJ')); ?>

        <?php echo $this->BForm->checkbox('MultiEmpresa.integrar_com_naveg', array('label' => '', 'div' => array('class' => 'control-group input checkbox checkbox-parent-with-label')))?>Gerar Integração com o Naveg<br />

        <?php if(is_null($authUsuario['Usuario']['codigo_empresa'])) : ?>
            <?php if(!empty($this->passedArgs[0])): ?>
                <?php echo $this->BForm->input('MultiEmpresa.codigo_status_multi_empresa', array('label' => 'Status(*)', 'class' => 'input', 'default' => '','empty' => 'Status', 'options' => array('1' => 'Período Experimental', '2' => 'Ativo', '3' => 'Inativo'))); ?>
            <?php else: ?>
                <?php echo $this->BForm->hidden('MultiEmpresa.codigo_status_multi_empresa', array('value' => 1)); ?>
            <?php endif;  ?>
        <?php endif; ?>
    </div>
</div>



<div class="well">
    <h3 >Endereço da Empresa:</h3>
    <?php echo $this->BForm->input('MultiEmpresaEndereco.cep', array('class' => 'input-medium formata-cep', 'label' => false, 'multiple', 'onchange' => '$("#pesquisa_cep").show();', 'label' => 'Cep ( * )', 'style' => 'margin-bottom: 0;', 'div' => false)); ?>
    <img src="/portal/img/default.gif" id="carregando" style="display: none;" />
    <span style="font-size: 10px;" id="pesquisa_cep">
        <a href="javascript:void(0);" onclick="multiempresa.buscaCEP('MultiEmpresaEndereco', 'MultiEmpresa');">COMPLETAR ENDEREÇO</a>
    </span>         
    <div style="clear: both; margin-bottom: 15px;"></div>
    <?php echo $this->BForm->input('MultiEmpresaEndereco.logradouro', array('class' => 'input-xxlarge', 'label' => false, 'multiple', 'label' => 'Logradouro ( * )')); ?>
    <?php echo $this->BForm->input('MultiEmpresaEndereco.numero', array('class' => 'input-medium', 'label' => false, 'multiple', 'label' => 'Número ( * )')); ?>
    <?php echo $this->BForm->input('MultiEmpresaEndereco.complemento', array('class' => 'input-xlarge', 'label' => false, 'multiple', 'label' => 'Complemento')); ?>
    <?php echo $this->BForm->input('MultiEmpresaEndereco.bairro', array('class' => 'input-xlarge', 'label' => false, 'multiple', 'label' => 'Bairro ( * )')); ?>
    <?php echo $this->BForm->input('MultiEmpresaEndereco.codigo_estado_endereco', array('label' => false, 'class' => 'input-xxlarge uf', 'style' => 'text-transform: uppercase;', 'empty' => false, 'options' => $estados, 'onchange' => 'multiempresa.buscaCidade(this, null, "MultiEmpresaEnderecoCodigoCidadeEndereco", null)', 'label' => 'Estado ( * )')) ?>
    <label>Cidade ( * )</label>
    <span id="cidade_combo">
        <?php echo $this->BForm->input('MultiEmpresaEndereco.codigo_cidade_endereco', array('label' => false, 'class' => 'input-xxlarge', 'style' => 'text-transform: uppercase;', 'empty' => false, 'options' => $cidades)) ?>
    </span>
    <div id="carregando_cidade" style="display: none; text-align: left; border: 1px solid #CCCCCC; padding: 8px;">
        <img src="/portal/img/ajax-loader.gif" border="0"/>
    </div>
</div>


<div class="form-actions">
    <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
    <?= $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>
</div>    

<?php echo $this->BForm->end(); ?>

<?php echo $this->Buonny->link_js('multi_empresa'); ?>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
    	setup_time();
    	setup_mascaras();
        //atualizaListaMultiEmpresa();
    });

    // function atualizaListaMultiEmpresa() {
    //     var div = jQuery("div#logomarca");
    //     bloquearDiv(div);
    //     div.load(baseUrl + "multi_empresas/logomarca/" + Math.random());
    // }

', false);
?>