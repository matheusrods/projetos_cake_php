<div class='well'>
    <?php echo $bajax->form('ClienteImplantacao', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'ClienteImplantacao', 'element_name' => 'clientes_estrutura'), 'divupdate' => '.form-procurar')) ?>   
        <div class="row-fluid inline">
            <?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente Principal do Grupo Econômico', '&nbsp;', 'ClienteImplantacao'); ?>
            <?//Cliente Principal do Grupo Econômico?>
            <?php echo $this->BForm->hidden('referencia', array('value' => 'sistema')); ?>
        </div>        
        <?php //echo $html->link('Buscar', 'javascript:void(0)', array('class' => 'btn btn-primary', 'style'=> 'display: inline-block;', 'onclick' => 'buscar_estrutura();')); ?>
        <?php echo $this->BForm->submit('Buscar', array('id' => 'buscar', 'div' => false, 'class' => 'btn btn-primary')); ?>
        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
    <?php echo $this->BForm->end() ?>
</div> 

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        setup_mascaras(); 
        setup_datepicker(); 
        setup_time(); 


        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:ClienteImplantacao/element_name:clientes_estrutura/" + Math.random())
            
            var div = jQuery("div.lista");
            bloquearDiv(div);
            div.html("");
        });

        jQuery("#buscar").click(function(){
            var codigo_cliente = $("#ClienteImplantacaoCodigoCliente").val();
            if(codigo_cliente != ""){
                buscar_estrutura(codigo_cliente);
            }
        });
    });

function buscar_estrutura(codigo_cliente) {
    
    $.ajax({
            dataType: "html",
            type: "POST",
            url: baseUrl + "clientes_implantacao/estrutura/" + codigo_cliente + "/sistema/" + Math.random(),
            success: function(data){
                $(".lista").html(data);
            },
            error: function(erro){
                console.log(erro);
            }
        });
    
}
', false);
?>