<style type="text/css">
    .input-max{
        width: 100%;
    } 
</style>
<?php if ($this->name == 'fornecedoresEnderecos'): ?>
<div class="well">
    <div class="row-fluid inline">
        
            <?php $options = array('class' => 'input-medium', 'label' => 'Contato', 'options' => $tipos_contato) ?>
            <?php if ($this->action != 'incluir'): ?>
                <?php $options = array_merge($options, array('disabled' => true)) ?>
            <?php endif; ?>
            <?php echo $this->BForm->input('FornecedorEndereco.codigo_tipo_contato', $options); ?>
    </div>
</div>
<?php endif; ?>
<div class="well" style="display: block; min-height: 510px;">
    <div class="span6" style="margin-left: 0px;">
    <div class="row-fluid inline">
        <?php echo $this->BForm->hidden('FornecedorEndereco.codigo'); ?>
        
        <?php echo $this->BForm->input('FornecedorEndereco.cep', array('class' => 'evt-endereco-cep input-small', 'label' => 'CEP (*)')); ?>
    </div>
    <div class="row-fluid inline">
        <div >
        <?php   echo $this->BForm->input(   'FornecedorEndereco.estado_descricao', 
                                        array(  'class' => 'input-mini evt-endereco-estado', 
                                                'label' => 'UF',
                                                'type' => 'select',
                                                'options' => $estados)); ?>
        </div>
        <div >
        <?php                                                                     
            echo $this->BForm->input(   'FornecedorEndereco.cidade',
                                        array(  'class' => 'input evt-endereco-cidade', 
                                                'size' => 60,
                                                'label' => 'Cidade')); 

        ?>
        </div>
        <div >
        <?php                                                                     
            echo $this->BForm->input(   'FornecedorEndereco.bairro',
                                        array(  'class' => 'input-meduim evt-endereco-bairro', 
                                                'size' => 60,
                                                'label' => 'Bairro')); 

        ?>
        </div>
    </div>
    <div class="row-fluid inline">
        <div >
        <?php                                                                     
            echo $this->BForm->input(   'FornecedorEndereco.logradouro',
                                        array(  'class' => 'input-max evt-endereco-lagradouro', 
                                                'size' => 60,
                                                'label' => 'Logradouro')); 

        ?>
        </div>
    </div>
    <div class="row-fluid inline">
            <?php echo $this->BForm->input('FornecedorEndereco.numero', array('class' => 'input-mini evt-endereco-numero', 'size' => 6, 'label' => 'Número')); ?>
            <?php echo $this->BForm->input('FornecedorEndereco.complemento', array('class' => 'input-medium complemento', 'label' => 'Complemento')); ?>
    </div>
    <div class="row-fluid inline">
        <span id="itens_necessarios_para_busca" class="control-group input text error"></span>
        <?php echo $this->BForm->input('FornecedorEndereco.latitude', array('label' => 'Latitude <a title="Utilize o formato decimal. (Ex: -9.000000 )" href="javascript:void(0)" class="icon-question-sign"></a>', 'type' => 'text','class' => 'input-small')) ?>
        <?php echo $this->BForm->input('FornecedorEndereco.longitude', array('label' => 'Longitude <a title="Utilize o formato decimal. (Ex: -54.000000 )" href="javascript:void(0)" class="icon-question-sign"></a>', 'type' => 'text','class' => 'input-small')) ?>  
        <? $raio = 150; ?>   
            <?php echo $this->BForm->hidden('FornecedorEndereco.raio', array('value' => $raio));?>
            <?php echo $this->BForm->hidden('FornecedorEndereco.poligono');?>

        <div class="control-group input text">
            <div id="btn">
                <label for="FornecedorEnderecoLongitude">&nbsp</label>  
                <?php echo $html->link('Buscar Lat/Long', 'javascript:void(0)', array('class' => 'btn btn-success bt-send-xy')) ;?>
            </div>
            <div id="carregando" style="display: none;">
                <img src="/portal/img/ajax-loader.gif" border="0" style="padding-left: 5px; padding-top: 34px;"/>
            </div>
        </div>
        <span id="error_coordenadas" class="control-group input text error" style="float: left; display: relative; clear:both"></span> 
    </div>
</div>
<div id="mapa" class="span5" style="margin-left: 20px;">
    <div style="width: 100%; background: none repeat scroll 0% 0% rgb(229, 227, 223); position: relative;" id="canvas_mapa"> </div>
        <?php         
        $latitude_min = null;
        $latitude_max = null;
        $longitude_min = null;
        $longitude_max = null;
        if(empty($this->data['FornecedorEndereco']['latitude'])){
            $latitude = 0;
        }
        else{
            $latitude = $this->data['FornecedorEndereco']['latitude'];
        }

        if(empty($this->data['FornecedorEndereco']['longitude'])){
            $longitude = 0;
        }
        else{
            $longitude = $this->data['FornecedorEndereco']['longitude'];
        }
            
        $latitude_min    = $latitude - ($raio / 111.18);
        $latitude_max    = $latitude + ($raio / 111.18);
        $longitude_min   = $longitude - ($raio / 111.18);
        $longitude_max   = $longitude + ($raio / 111.18);
            
        $mapOptions = array(
                'title' => $this->data['Fornecedor']['razao_social'],
                'polygon_string' => null, 
                'latitude_center' => $latitude,
                'longitude_center' => $longitude,
                'rectangle' => array(
                    'lat_min' => $latitude_min, 
                    'lat_max' => $latitude_max, 
                    'lng_min' => $longitude_min, 
                    'lng_max' => $longitude_max
                ),
                 'polygon_input' => 'FornecedorEnderecoPoligono',
                'latitude_input' => 'FornecedorEnderecoLatitude',
                'longitude_input' => 'FornecedorEnderecoLongitude',
                'range_input' => 'FornecedorEnderecoRaio'
            ); 
           echo $this->Mapa->mapaFornecedores($mapOptions);  
        ?>
    </div>  
</div>

<?php echo $this->Javascript->codeBlock("
   $(function(){
        $('#FornecedorEnderecoCep').attr('callback','RetornoCep');   
    })

    $(document).ready(function(){

        $('.bt-send-xy').click(function(){
            var validacao = valida_campos_para_busca_lat_long();
            if(validacao) {
                 fornecedor_busca_xy(validacao);
                 return true;
            }else {
                return false;
            }
            
        });
    });    

    function Disabled(  ){
        $('#FornecedorEnderecoEstadoDescricao').removeAttr('readonly')
        $('#FornecedorEnderecoCidade').removeAttr('readonly')
        $('#FornecedorEnderecoBairro').removeAttr('readonly')
        $('#FornecedorEnderecoLogradouro').removeAttr('readonly')
    } 

function RetornoCep( data ){

    $('#FornecedorEnderecoCep').parent().find('.alert').remove()
    Disabled( )

    if( data ){
       Fill( data )
    } else {

        $('#FornecedorEnderecoCep').after('<div class=\'alert\'>CEP Não encontrado</div>');
        setTimeout(function(){
            $('#FornecedorEnderecoCep').parent().find('.alert').remove()
        }, 3000)
    }    
}

function Fill( data ){
    
    $('#FornecedorEnderecoEstadoDescricao').val( data.VEndereco.endereco_estado_abreviacao );
    $('#FornecedorEnderecoCidade').val( data.VEndereco.endereco_cidade );        

    $('#FornecedorEnderecoBairro').val( data.VEndereco.endereco_bairro );
    
    $('#FornecedorEnderecoLogradouro').val( data.VEndereco.endereco_tipo + ' '+data.VEndereco.endereco_logradouro );
       
}

     function valida_campos_para_busca_lat_long() {

        var logradouro = $('#FornecedorEnderecoLogradouro').val();
        var cidade = $('#FornecedorEnderecoCidade').val();
        var estado_descricao = $('#FornecedorEnderecoEstadoDescricao').val();
        var estado_abreviacao = $('#FornecedorEnderecoEstadoAbreviacao').val();
        var numero = $('#FornecedorEnderecoNumero').val();

        if(logradouro == '' || cidade == '' || estado_descricao == '' || estado_abreviacao == ''){
            return 0;
        } else {
            return(logradouro + ', ' + numero + ' - ' + cidade + ' - ' + estado_descricao);
        }

    }

    function remove_classe_error(elemento) {
       elemento.removeClass('form-error').parent().removeClass('error').find('#lbl-error').remove();
    }
    function incluir_classe_error(elemento) {
        elemento.addClass('form-error').parent().addClass('error').append('<div id=\'lbl-error\' class=\'help-block\'></div>');
    }

    function fornecedor_busca_xy(logradouro){
        var bt_send = $('.bt-send-xy');
        var btn = $('#btn');
        var carregando = $('#carregando');
        var title = bt_send.html();
        var lat_y = $('#FornecedorEnderecoLatitude');
        var long_x = $('#FornecedorEnderecoLongitude');
        
        $.ajax({
            url: baseUrl + 'fornecedores_enderecos/buscaXY/' + Math.random(),
            type: 'post',
            dataType: 'json',
            data: {
                'logradouro': logradouro,
            },
            beforeSend: function(){
                btn.hide();
                carregando.show();
            },
            success: function(data){
                $('#error_coordenadas').removeClass('form-error').removeClass('error');

                if(data == 0){
                    $('#error_coordenadas').addClass('form-error').addClass('error').append('<div id=\'lbl-error\' class=\'help-block error-message error\'>Coordenadas Não Encontradas!</div>');
                    lat_y.val(0);
                    long_x.val(0);
                }
                else{
                    lat_y.val(data.latitude);
                    long_x.val(data.longitude);
        
                    lat_y.blur();
                }
            },
            complete: function(data){
                btn.show();
                bt_send.html(title);
                carregando.hide();
            }
        });
    }
");
?>