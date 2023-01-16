<div class='well'>
  <?php echo $bajax->form('EmbarcadorTransportador', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'EmbarcadorTransportador', 'element_name' => 'embarcador_transportador'), 'divupdate' => '.form-procurar')) ?>
    <div class="row-fluid inline">
		<?if( empty($usuario['Usuario']['codigo_cliente']) ):?>
        <?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', false, 'EmbarcadorTransportador') ?>
        <?endif;?>
        <?php echo $this->BForm->input('codigo_documento', array('class' => 'input-medium cnpj', 'label' => FALSE, 'placeholder' => 'CNPJ', 'maxlength' => 18)); ?>
        <?php echo $this->BForm->input('razao_social', array('class' => 'input-xxlarge', 'label' => FALSE, 'placeholder' =>'Razão Social')); ?>
    </div>    
    <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
    <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro-clientes', 'class' => 'btn')) ;?>
	<?php echo $this->BForm->end() ?>
</div>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        var div = jQuery("div.lista");
        bloquearDiv(div);
        div.load(baseUrl + "embarcadores_transportadores/listagem_embarcador_transportador/" + Math.random());
        jQuery("#limpar-filtro-clientes").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:EmbarcadorTransportador/element_name:embarcador_transportador/" + Math.random())
        });        
        jQuery(".codigo-cliente-tipo").bind("change",
            function() {
                jQuery.ajax({
                    "url": baseUrl + "/clientes_sub_tipos/combo/" + jQuery(this).val() + "/" + Math.random(),
                    "success": function(data) {                        
                        jQuery(".codigo-cliente-sub-tipo").html(data).val();                        
                    }
                });
            }
        );
    });', false);
?>