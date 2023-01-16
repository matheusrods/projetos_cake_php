<div class="row-fluid inline">
    <?php echo $this->BForm->input("Proprietario.codigo_documento", array('label' => 'CPF/CNPJ', 'class' => 'input-medium cpf_cnpj', 'maxlength' => 18, 'after' => $html->link('...', "javascript:setup_proprietario(this);", array('id' =>'avancar','class' => 'btn btn-search-ellipsis', 'title' => 'Buscar dados')) )) ?>

    <?php echo $this->BForm->input('Proprietario.nome_razao_social', array('class' => 'enderecoProprietario input-xlarge','type'=>'text', 'label' => 'Nome/Razão Social')); ?>
    <?php echo $this->BForm->input('Proprietario.inscricao_estadual',array('class' => 'enderecoProprietario input-medium','type'=>'text', 'label' => 'RG/Inscrição Estadual')); ?>
    <?php echo $this->BForm->input('Proprietario.rntrc',array('class' => 'input-small just-number enderecoProprietario input-medium','type'=>'text', 'label' => 'RNTRC', 'maxlength' => 8)); ?>
</div>
<h5>Endereço do proprietário</h5>
<div class="row-fluid inline">
    <?php echo $this->Buonny->input_cep_endereco($this,array('cep_field' => 'endereco_cep','endereco_field' => 'codigo_endereco'),$enderecos,true,false,'ProprietarioEndereco'); ?>
</div>
<?php 
echo $this->Javascript->codeBlock("
        function setup_proprietario(documento){
            var propr = documento.codigo_documento;
            if ( parseInt(propr) > 0){
                $.ajax({
                    url: baseUrl + 'proprietarios/buscar/' + propr + '/' + Math.random(),
                    type: 'post',
                    dataType: 'json',
                    beforeSend: function(){
                        $('#ProprietarioCodigoDocumento').addClass('ui-autocomplete-loading');
                    },
                    success: function(data){
                        if (data){
                            preenche_campos_prop(data);                          
                        }
                    },
                    complete: function(){
                        $('#ProprietarioCodigoDocumento').removeClass('ui-autocomplete-loading');
                    }
                }); 
            } else {
                limpar_campos_proprietario();
            }
        }
        function preenche_campos_prop(data){ 
            if (data){                
                $('#ProprietarioNomeRazaoSocial').val(data.Proprietario.nome_razao_social);
                $('#ProprietarioInscricaoEstadual').val(data.Proprietario.rg);
                $('#ProprietarioRntrc').val(data.Proprietario.rntrc);
                $('#ProprietarioEnderecoEnderecoCep').val(data.EnderecoCep.cep);
                $('#ProprietarioEnderecoNumero').val(data.ProprietarioEndereco.numero);
                $('#ProprietarioEnderecoComplemento').val(data.ProprietarioEndereco.complemento);
                buscar_cep($('#ProprietarioEnderecoEnderecoCep'), 
                    '#ProprietarioEnderecoCodigoEndereco', 
                    data.ProprietarioEndereco.codigo_endereco 
                );      
            }else{
                $('#ProprietarioEnderecoEnderecoCep').trigger('blur');
            }
        }
        function limpar_campos_proprietario(){
            $('#ProprietarioCodigoDocumento').val('');
            $('#ProprietarioNomeRazaoSocial').val('');
            $('#ProprietarioRg').val('');
            $('#ProprietarioEnderecoEnderecoCep').val('');
            $('#ProprietarioEnderecoNumero').val('');
            $('#ProprietarioEnderecoComplemento').val('');
            $('#ProprietarioEnderecoCodigoEndereco').val('');             
            $('#ProprietarioEnderecoEnderecoCep').trigger('blur');
        }
    jQuery(document).ready(function(){
        setup_mascaras();
        jQuery('input[id^=TVeicVeiculoProprietario]').click(function() {
            selecionado = jQuery('input[id^=TVeicVeiculoProprietario]:checked').val();
            if (selecionado == 1) {
                $('#ProprietarioEnderecoEnderecoCep').val('{$cliente['VEndereco']['endereco_cep']}');         
                $('#ProprietarioEnderecoNumero').val('{$cliente['ClienteEndereco']['numero']}');         
                $('#ProprietarioEnderecoComplemento').val('{$cliente['ClienteEndereco']['complemento']}');
                $('#ProprietarioCodigoDocumento').val('{$cliente['Cliente']['codigo_documento']}');
                $('#ProprietarioNomeRazaoSocial').val('{$cliente['Cliente']['razao_social']}');
                $('#ProprietarioInscricaoEstadual').val('{$cliente['Cliente']['inscricao_estadual']}');
                $('#ProprietarioRntrc').val('');
            } else {
                $('div.proprietario_veiculo').find('input').val('');
                $('#ProprietarioEnderecoEnderecoCep').val('')
            }
            buscar_cep($('#ProprietarioEnderecoEnderecoCep'), 
                    '#ProprietarioEnderecoCodigoEndereco', 
                   {$cliente['ClienteEndereco']['codigo_endereco']}
                ); 
            //$('#ProprietarioEnderecoEnderecoCep').blur();
        });

    });", false);
?>