<div class="row-fluid inline" >
    <h4> Dados Principais</h4>
    <?php echo $this->BForm->hidden('Proprietario.codigo'); ?>
    <?php echo $this->BForm->input('Proprietario.codigo_documento', array('class' => 'input-medium formata-cpf-cnpj','label' => 'CPF/CNPJ')); ?>
    <?php echo $this->BForm->input('Proprietario.nome_razao_social', array('class' => 'input-xlarge','type'=>'text', 'label' => 'Nome/Razão Social')); ?>
</div>      
<div class="row-fluid inline">
    <?php echo $this->BForm->input('Proprietario.rg',array('class' => 'input-medium','type'=>'text', 'label' => 'RG')); ?>  
    <?php echo $this->BForm->input('Proprietario.ssp',array('class' => 'input-mini','type'=>'text', 'label' => 'SSP')); ?>
</div> 
<div class="row-fluid inline">
    <?php echo $this->BForm->input('Proprietario.inscricao_estadual',array('class' => 'input-medium','type'=>'text', 'label' => 'Inscrição Estadual')); ?>
    <?php echo $this->BForm->input('Proprietario.rntrc',array('class' => 'input-medium','type'=>'text', 'label' => 'RNTRC')); ?>
</div>
<div class="row-fluid inline parent">
    <h4> Endereço</h4> 
    <?php echo $this->element('proprietarios_enderecos/fields') ?>
</div>
<div class="row-fluid inline parent">
    <h4> Contatos </h4>
     <div id="pre-modelo">
        <?php echo $this->element('proprietarios_contatos/consultar_para_incluir') ?> 
    </div>
</div>
<div class="form-actions">       
    <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
    <?= $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>
</div>
<?php echo $this->BForm->end(); ?>
<?php $this->addScript($this->Javascript->codeBlock("
    setup_mascaras();

    $('.formata-cpf-cnpj').mask('99.999.999/999?9-99');

    $('.formata-cpf-cnpj').on('blur', function() {   
        var last = $(this).val().substr( $(this).val().indexOf('/') + 1 );

        if( last.length == 3 ) {
            var str1 = $(this).val().replace('.','');
            var str2 = str1.replace('/','');
            var str3 = str2.replace('.','');            

            comeco = str3.substr(0,3);
            meio = str3.substr(3,3);
            fim = str3.substr(6,3);
            digito = str3.substr(9,2);
       
            tudo = comeco+'.'+meio+'.'+fim+'-'+digito;
            $(this).val(tudo);
        }
    });
    function setup_remove_linha(linha) {
        $(linha).find('a').attr('onclick', 'remove_placa(jQuery(this).parent().parent())').removeClass('btn-success');
        $(linha).find('i').removeClass('icon-plus').removeClass('icon-white').addClass('icon-minus');
    }     
    function adiciona_contato(linha) {
        if($(linha).find('#tipo_retorno').val() != '' && $(linha).find('#contato').val() != '' ) {
            var insert_tr = $(linha).clone();
            setup_remove_linha(linha);
            insert_tr.find('div.error').removeClass('error').find('div.help-inline').remove();
            insert_tr.find('#nome').val('');
            insert_tr.find('#tipo').val('');
            insert_tr.find('#tipo_retorno').val('');
            insert_tr.find('#contato').val('');
            $(linha).after(insert_tr);
        } else {
            alert('Prencha os dados de contato');
        }
    }    
")) ?>