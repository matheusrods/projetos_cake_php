<?$edit_mode=isset($edit_mode)?$edit_mode:NULL;?>
<div id="cliente-endereco">
<div class="row-fluid inline">
    <?php
        $exibir_repetir_endereco = ($this->params['controller'] == strtolower('clientes') && $this->params['action'] == strtolower('incluir'));
        $options = array('class' => 'input-medium', 'label' => 'Contato', 'options' => $tipos_contato);
    ?>

    <?php if ($exibir_repetir_endereco == true): ?>
        <?php $options = array_merge($options, array('disabled' => true)) ?>
    <?php endif; ?>

    <?php if ($this->name == 'ClientesEnderecos'): ?>
        <?php echo $this->BForm->input('ClienteEndereco.codigo_tipo_contato', $options); ?>
    <?php endif; ?>

    <?php if ($exibir_repetir_endereco == true): ?>
        <?php echo $this->BForm->input('Outros.repetir_para', array('class' => 'checkbox inline', 'options' => $tipos_contato, 'multiple' => 'checkbox', 'label' => 'Repetir endereço para')); ?>
    <?php endif; ?>

</div>
<div class="span6" style="margin-left: 0px;">
    <div class="row-fluid inline">
        <?php echo $this->BForm->hidden('ClienteEndereco.codigo'); ?>
        <?php echo $this->BForm->input('ClienteEndereco.cep', array('class' => 'endereco-cep input-small', 'label' => 'CEP', 'readonly' => $edit_mode)); ?>
    </div>
    <div class="row-fluid inline">
        <?php echo $this->BForm->input('ClienteEndereco.logradouro', array('label' => 'Logradouro', 'class' => 'input-large endereco-logradouro')); ?>
        <?php echo $this->BForm->input('ClienteEndereco.bairro', array('label' => 'Bairro', 'class' => 'input-medium endereco-bairro')); ?>
        <?php echo $this->BForm->input('ClienteEndereco.cidade', array('label' => 'Cidade', 'class' => 'input-medium endereco-cidade')); ?>
        <?php echo $this->BForm->input('ClienteEndereco.estado_descricao', array('label' => 'Estado', 'class' => 'input-small endereco-estado')); ?>
        <?php echo $this->BForm->input('ClienteEndereco.estado_abreviacao', array('label' => 'Estado Abreviação', 'class' => 'input-small endereco-estado-abreviacao')); ?>
    </div>
    <div class="row-fluid inline">
        <div class="clear">
            <?php echo $this->BForm->input('ClienteEndereco.numero', array('class' => 'input-mini evt-endereco-numero', 'size' => 6, 'maxlength' => 6, 'label' => 'Número', 'readonly' => $edit_mode)); ?>
            <?php echo $this->BForm->input('ClienteEndereco.complemento', array('class' => 'input-medium complemento', 'label' => 'Complemento', 'readonly' => $edit_mode)); ?>
        </div>
    </div>
    <div class="row-fluid inline">
        <span id="itens_necessarios_para_busca" class="control-group input text error"></span>
        <?php echo $this->BForm->input('ClienteEndereco.latitude', array('label' => 'Latitude <a title="Utilize o formato decimal. (Ex: -9.000000 )" href="javascript:void(0)" class="icon-question-sign"></a>', 'type' => 'text','class' => 'input-small')) ?>
        <?php echo $this->BForm->input('ClienteEndereco.longitude', array('label' => 'Longitude <a title="Utilize o formato decimal. (Ex: -54.000000 )" href="javascript:void(0)" class="icon-question-sign"></a>', 'type' => 'text','class' => 'input-small')) ?>  
           
        <? $raio = 150; ?>
		<?php echo $this->BForm->hidden('ClienteEndereco.raio', array('value' => $raio));?>
        <?php echo $this->BForm->hidden('ClienteEndereco.poligono');?>

        <div class="control-group input text">
            <div id="btn">
                <label for="ClienteEnderecoLongitude">&nbsp</label>  
                <?php echo $html->link('Buscar Lat/Long', 'javascript:void(0)', array('class' => 'btn btn-success bt-send-xy')); ?>
            </div>
        </div>
        <div id="carregando" style="display: none;">
                <img src="/portal/img/ajax-loader.gif" border="0" style="padding-left: 5px; padding-top: 34px;"/>
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
        
            
    if(empty($this->data['ClienteEndereco']['latitude'])){
        $latitude = 0;
    }
    else{
        $latitude = $this->data['ClienteEndereco']['latitude'];
    }

    if(empty($this->data['ClienteEndereco']['longitude'])){
        $longitude = 0;
    }
    else{
        $longitude = $this->data['ClienteEndereco']['longitude'];
    }

    $latitude_min    = $latitude - ($raio / 111.18);
    $latitude_max    = $latitude + ($raio / 111.18);
    $longitude_min   = $longitude - ($raio / 111.18);
    $longitude_max   = $longitude + ($raio / 111.18);
    
    $mapOptions = array(
            'title' => $this->data['Cliente']['razao_social'],
            'polygon_string' => null, 
            'latitude_center' => $latitude,
            'longitude_center' => $longitude,
            'rectangle' => array(
                'lat_min' => $latitude_min, 
                'lat_max' => $latitude_max, 
                'lng_min' => $longitude_min, 
                'lng_max' => $longitude_max
            ),
            'polygon_input' => 'ClienteEnderecoPoligono',
            'latitude_input' => 'ClienteEnderecoLatitude',
            'longitude_input' => 'ClienteEnderecoLongitude',
            'range_input' => 'ClienteEnderecoRaio'
        ); 
    
    echo $this->Mapa->mapaClientes($mapOptions);

    ?>

</div>  
</div>  

<?php echo $this->Javascript->codeBlock("
    $(document).ready(function(){
        $('.bt-send-xy').click(function(e){
            if($(e.target).is('a')){
                var validacao = valida_campos_para_busca_lat_long();
                if(validacao) {
                     cliente_busca_xy(validacao);
                     return true;
                }else {
                    return false;
                }
            }
        });
    });     

    function valida_campos_para_busca_lat_long() {

        var logradouro = $('#ClienteEnderecoLogradouro').val();
        var cidade = $('#ClienteEnderecoCidade').val();
        var estado_descricao = $('#ClienteEnderecoEstadoDescricao').val();
        var estado_abreviacao = $('#ClienteEnderecoEstadoAbreviacao').val();
        var numero = $('#ClienteEnderecoNumero').val();

        if(logradouro == '' || cidade == '' || estado_descricao == '' || estado_abreviacao == ''){
            return 0;
        } else {
            return (logradouro + ', ' + numero + ' - ' + cidade + ' - ' + estado_descricao);
        }

    }

    function cliente_busca_xy(logradouro){
        var bt_send = $('.bt-send-xy');
        var btn = $('#btn');
        var carregando = $('#carregando');
        var title = bt_send.html();
        var lat = $('#ClienteEnderecoLatitude');
        var long = $('#ClienteEnderecoLongitude');

        $.ajax({
            url: baseUrl + 'clientes_enderecos/busca_x_y_endereco/' + Math.random(),
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
                if(data == 0){
                    lat.val(0);
                    long.val(0);
                }
                else{
                    lat.val(data.latitude);
                    long.val(data.longitude);
        
                    lat.blur();
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