<?php echo $this->BForm->create('ClienteIp', array('url' => array('controller' => 'clientes_ips', 'action' => 'incluir')));?>
    <div class='row-fluid inline parent'>        
    	<?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', true, 'ClienteIp') ?>     
    	<?php echo $this->BForm->input("Cliente.razao_social", array('label' => 'Cliente', 'class' => 'input-xxlarge', 'readonly'=>true)) ?>
    </div>
        <?php echo $this->BForm->input('descricao',array('class' => 'input-xlarge', 'label' => 'Endereço IP' )) ?>
    <div class="form-actions">
          <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
          <?php echo $html->link('Voltar',array('controller' => 'clientes_ips', 'action' => 'index'), array('class' => 'btn')) ;?>
    </div>
<?php echo $this->BForm->end() ?>
<?php echo $this->Javascript->codeBlock("
    jQuery(document).ready(function(){        
        $('#ClienteIpCodigoCliente').blur(function(){
            var codigo_cliente = $('#ClienteIpCodigoCliente').val();
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