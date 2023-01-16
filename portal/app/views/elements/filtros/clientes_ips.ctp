<div class='well'>
	<?php echo $bajax->form('ClienteIp', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'ClienteIp', 'element_name' => 'clientes_ips'), 'divupdate' => '.form-procurar')) ?>
    	<div class="row-fluid inline">
            <?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', true, 'ClienteIp') ?>     
            <?php echo $this->BForm->input("Cliente.razao_social", array('label' => 'Cliente', 'class' => 'input-xxlarge', 'readonly'=>true)) ?>
            <?php echo $this->BForm->input('descricao', array('class' => 'input-medium', 'placeholder' => false, 'label'=>'Endereço IP', 'type' => 'text')) ?>
        </div>
        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
    <?php echo $this->BForm->end() ?>
</div>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        var div = jQuery("div.lista");
        bloquearDiv(div);
        div.load(baseUrl + "clientes_ips/listar/" + Math.random());        
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:ClienteIp/element_name:clientes_ips/" + Math.random())
        });
    });', false);

?>
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
