<div class='well'>
    <?php echo $this->BForm->create('Motorista', array('type' => 'file', 'autocomplete' => 'off', 'url' => array('controller' => 'motoristas', 'action' => 'importar_motorista'))); ?>
    <?php if (!empty($authUsuario['Usuario']['codigo_cliente'])) { ?>
    <strong>Código: </strong><?= $authUsuario['Usuario']['codigo_cliente'] ?>
    <strong style="margin:0 0 0 20px">Cliente: </strong><?=$authUsuario['Usuario']['nome'] ?>
    <div>
    <?php }else{ ?>
    <div class='row-fluid inline'>      
        <?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', true, 'Motorista') ?>     
        <?php echo $this->BForm->input("Cliente.razao_social", array('label' => 'Cliente', 'class' => 'input-xxlarge', 'readonly'=>true)) ?>
        <?php }?>
        <span class="pull-right">
            <?php echo $this->Html->Link(
            '<i class="cus-page-white-excel"></i>&nbsp;Arquivo Exemplo', 
            '/files/Modelo_de_Importacao_Motorista.xls',
            array(
                'class'  => 'button', 
                'target' => '_blank',
                'escape' => false,
                )
            );?>
        </span>
    </div>
</div>
<div class='row-fluid inline'>      
    <?php echo $this->BForm->input('arquivo', array('type'=>'file', 'label'=>'Selecione o arquivo CSV - <font color="red">Preferencialmente codificado em UTF-8</font>', 'class' => 'input-xlarge' )); ?>
</div>
<div class="form-actions">
    <?php echo $this->BForm->submit('Processar', array('div' => false, 'class' => 'btn btn-primary')); ?>
</div>
<?php echo $this->BForm->end(); ?>
<?php echo $this->Javascript->codeBlock("
    jQuery(document).ready(function(){        
        $('#MotoristaCodigoCliente').blur(function(){
            var codigo_cliente = $('#MotoristaCodigoCliente').val();
            if (codigo_cliente != '') {
                $.ajax({
                    url:baseUrl + 'embarcadores_transportadores/listar_por_cliente/' + codigo_cliente + '/' + Math.random(),
                    dataType: 'json',
                    success: function(data) {
                        if (data) {
                            $('#ClienteRazaoSocial').val(data.razao_social);
                        }
                    }
                })
            }
        });
    });", false);?>