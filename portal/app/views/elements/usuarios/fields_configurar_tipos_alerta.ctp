<?php
//Elemento contendo os campos e JS necessários para configurar os alertas do usuário
//2016-08-24
?>

<h4>Tipos de recebimento de alertas</h4>

<div class="row-fluid inline">
    <?php echo $this->BForm->input('alerta_portal', array('type' => 'checkbox', 'label' => 'Alertas no portal', 'class' => 'checkbox-alerta')); ?>
    <?php echo $this->BForm->input('alerta_email',  array('type' => 'checkbox', 'label' => 'Alertas por email', 'class' => 'checkbox-alerta')); ?>
    <?php echo $this->BForm->input('alerta_sms',    array('type' => 'checkbox', 'label' => 'Alertas por sms',   'class' => 'checkbox-alerta')); ?>
</div>

<div class="row-fluid inline">
    <?php echo $this->BForm->input('alerta_sm_usuario', array('type' => 'checkbox', 'label' => 'Somente pedidos emitidos por este login', 'class' => 'checkbox-alerta')); ?>
</div>

<div class="row-fluid inline alertas-tipos" style="display: none;">  
    
    <h4>Tipos de alertas</h4>
    
    <span class='pull-right'>
        <?php echo $this->Html->link('Desmarcar todas', 'javascript:void(0)', array('onclick' => 'desmarcarTodos("alertas")')) ?>
        <?php echo $this->Html->link('Marcar todas',    'javascript:void(0)', array('onclick' => 'marcarTodos("alertas")'   )) ?>
    </span>
    
    <div class="row-fluid inline" id="alertas">
        <!-- Carregamento dos alertas via ajax-->
    </div>

</div>
<?php echo $this->Javascript->codeBlock('
    $(document).ready(function(){
        
        function showAlertasTipos(){
            var checked = false;
            
            $(".checkbox-alerta").each(function(){
                if($(this).is(":checked")){
                    console.log($(this));
                    checked = true;
                }
            })

            if(checked){                
                $(".alertas-tipos").show();
                $(".veiculos-alertas").show();
            }
            else{                
                $(".alertas-tipos").hide();
                $(".veiculos-alertas").hide();
            }
        }
        
        function verifica_alertas_perfis(perfil, codigo_usuario){            
            if (perfil > "" && codigo_usuario > "") {
                var div = $("#alertas");                 
                $.ajax({
                    type: "post",
                    url: baseUrl + "alertas/alertas_por_perfil/"+perfil+"/"+codigo_usuario+"/"+Math.random(),
                    cache: false,                    
                    beforeSend : function(){
                        bloquearDiv(div);
                    },
                    success: function(data){
                        $("#alertas").html(data);
                        div.unblock();
                    },
                    error: function(erro,objeto,qualquercoisa){                        
                        div.unblock();
                    }
                });
            }
        }

        $(".checkbox-alerta").change(function(){
            showAlertasTipos();
        });

        verifica_alertas_perfis('.$codigo_perfil.','.$codigo_usuario.');
        showAlertasTipos(); 

    });',
    false);
?>