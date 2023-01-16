<?php if(!is_null($inclusao_exclusao) && !is_null($faturamento)){ ?>
    <fieldset>
        <legend>Importação Inclusão / Exclusão</legend>
        <?php
            echo $this->BForm->create(
                'AutotracInclusaoExclusao', 
                array('type' => 'file', 'autocomplete' => 'off', 
                    'url' => array(
                        'controller' => 'autotrac_faturamentos', 
                        'action' => 'importar_excel_inclusao'
                      )
                )
            ); 

        ?>
        <div class="row-fluid inline">    
            <span class="label label-success importado"><i class="icon-ok icon-white"></i> Importado</span>
            <?php                 
            echo $this->BForm->input('arquivo', array('type'=>'file', 'label' => false)); 
            echo $this->BForm->submit('Importar', array('div' => false, 'class' => 'btn btn-primary enviar')); ?>
            <span class="btn btn-danger desfazer" style="margin-left: 5px" data-tipo="inclusao">Desfazer</span>
        </div>
        <?php echo $this->BForm->end(); ?>
    </fieldset>
    
    <fieldset>
        <legend>Importação Faturamento</legend>
        <?php
            echo $this->BForm->create(
                'AutotracFaturamento', 
                array(
                    'type' => 'file', 'autocomplete' => 'off', 
                    'url' => array(
                        'controller' => 'autotrac_faturamentos', 
                        'action' => 'importar_excel_faturamento'
                      )
                )
            ); 
        ?>
        <div class="row-fluid inline">  
        <span class="label label-success importado"><i class="icon-ok icon-white"></i> Importado</span>      
        <span class="label label-warning aguardando" style="display:none;"><i class="icon-warning-sign icon-white"></i> Aguardando Importação Inclusão/Exclusão</span>
            <?php 
            echo $this->BForm->input('arquivo', array('type'=>'file', 'label' => false)); 

            echo $this->BForm->submit('Importar', array('div' => false, 'class' => 'btn btn-primary enviar')); 
            ?>            
            <span class="btn btn-danger desfazer " style="margin-left: 5px" data-tipo="faturamento">Desfazer</span>
        </div>
        <?php echo $this->BForm->end();  ?>
    </fieldset>

    <?php 

        echo $this->Html->link('Visualizar Faturamento', 
                            array('action'=>'listagem_analitico', 'popup' ), 
                            array('onclick'=>'return open_popup(this);', 'class' => 'btn btn-primary',  
                                'id' => 'visualizar_faturamento')
                            ); 
                            ?>
    <?php
        echo $this->Html->link('Gerar Pedidos', 
                            array('action'=>'gerar_pedido', 'popup' ), 
                            array('class' => 'btn btn-primary', 'id' => 'gerar_pedidos')
                            ); 
    ?>
    <span class="btn btn-danger desfazer" style="margin-left: 5px" data-tipo="pedido" id="desfazer_pedido">Desfazer Pedidos</span>        

<?php 
if($inclusao_exclusao == 0){      
    echo $this->Javascript->codeBlock('jQuery(document).ready(function(){            
            jQuery("#AutotracInclusaoExclusaoListagemForm").find(".importado").hide();            
            jQuery("#AutotracInclusaoExclusaoListagemForm").find(".desfazer").hide(); 
            
            jQuery("#AutotracFaturamentoListagemForm").find(".file").hide();
            jQuery("#AutotracFaturamentoListagemForm").find(".enviar").hide(); 
            
            jQuery("#AutotracFaturamentoListagemForm").find(".importado").hide(); 
            jQuery("#AutotracFaturamentoListagemForm").find(".desfazer").hide(); 

            jQuery("#visualizar_faturamento").hide();      
            jQuery("#desfazer_pedido").hide();
            jQuery("#gerar_pedidos").hide();           
            
            jQuery(".aguardando").show();
        })');
}else if($faturamento == 0){ 
    echo $this->Javascript->codeBlock('jQuery(document).ready(function(){
            
            jQuery("#AutotracInclusaoExclusaoListagemForm").find(".file").hide();
            jQuery("#AutotracInclusaoExclusaoListagemForm").find(".enviar").hide(); 

            jQuery("#AutotracFaturamentoListagemForm").find(".importado").hide();
            jQuery("#AutotracFaturamentoListagemForm").find(".desfazer").hide(); 

            jQuery("#visualizar_faturamento").hide();
            jQuery("#desfazer_pedido").hide();
            jQuery("#gerar_pedidos").hide();    
        })');

}else{ 
    echo $this->Javascript->codeBlock('jQuery(document).ready(function(){
            jQuery("#AutotracInclusaoExclusaoListagemForm").find(".file").hide();
            jQuery("#AutotracInclusaoExclusaoListagemForm").find(".enviar").hide(); 
            jQuery("#AutotracInclusaoExclusaoListagemForm").find(".desfazer").hide();             
            
            jQuery("#AutotracFaturamentoListagemForm").find(".file").hide();            
            jQuery("#AutotracFaturamentoListagemForm").find(".enviar").hide();
            
        })');
    
    if(count($pedido) > 0){
        echo $this->Javascript->codeBlock('jQuery(document).ready(function(){
            jQuery("#gerar_pedidos").hide();
            jQuery("#AutotracFaturamentoListagemForm").find(".desfazer").hide();            
        })');
        if($pedido_faturado > 0)
            echo $this->Javascript->codeBlock('jQuery(document).ready(function(){
               jQuery("#desfazer_pedido").hide();
            })');
    }else{
        echo $this->Javascript->codeBlock('jQuery("#desfazer_pedido").hide();');
    }
} 
echo $this->Javascript->codeBlock('
    jQuery("#AutotracInclusaoExclusaoListagemForm, #AutotracFaturamentoListagemForm").submit(function(){ 
        if(jQuery(this).find("input:file").val()==""){       
            alert("Favor selecionar um arquivo");
            return false;
        }
        bloquearDiv(jQuery(".form-procurar"));
        bloquearDiv(jQuery(".lista"));         

    });
    jQuery(".desfazer, #gerar_pedidos").click(function(){           
        if($(this).prop("id") == "gerar_pedidos"){
            bloquearDiv(jQuery(".form-procurar"));
            bloquearDiv(jQuery(".lista"));         
            window.location.href="/portal/autotrac_faturamentos/desfazer/"+$(this).data("tipo");
        }else{
            if(confirm("Tem certeza que deseja desfazer?")){
                bloquearDiv(jQuery(".form-procurar"));
                bloquearDiv(jQuery(".lista"));         
                window.location.href="/portal/autotrac_faturamentos/desfazer/"+$(this).data("tipo");
            }
        }
    });

    ');
?>
<?php } ?>