<div class="usuarios_fields">
    <div class="row-fluid inline">
        <?php echo $this->BForm->hidden('codigo'); ?>
        <?php echo $this->BForm->hidden('codigo_cliente'); ?>
        <?php echo $this->BForm->input('apelido', array('class' => 'input-small', 'label' => 'Login', 'readonly' => true)); ?>
        <?php echo $this->BForm->input('nome', array('class' => 'input-large', 'label' => 'Nome', 'readonly' => true)); ?>
    </div>
    <div class="row-fluid inline">
        <?php echo $this->BForm->input('email', array('class' => 'input-large', 'label' => 'E-mail', 'readonly' => true)); ?>
        <?php echo $this->BForm->input('email_alternativo', array('class' => 'input-large', 'label' => 'E-mails Alternativos', 'readonly' => true)); ?>
        <?php echo $this->BForm->input('celular', array('class' => 'input-large telefone', 'label' => 'Celular', 'readonly' => true)); ?>
    </div>
</div>
<div>
    <h4>Tipos de recebimento de alertas</h4>
    <div class="row-fluid inline">
        <?php echo $this->BForm->input('alerta_portal', array('type' => 'checkbox', 'label' => 'Alertas no portal', 'class' => 'checkbox-alerta')); ?>
        <?php echo $this->BForm->input('alerta_email', array('type' => 'checkbox', 'label' => 'Alertas por email', 'class' => 'checkbox-alerta')); ?>
        <?php echo $this->BForm->input('alerta_sms', array('type' => 'checkbox', 'label' => 'Alertas por sms', 'class' => 'checkbox-alerta')); ?>
    </div>
    <div class="row-fluid inline alertas-tipos" style="display:none;">
        <div class="row-fluid inline"><?php echo $this->BForm->input('alerta_sm_usuario', array('type' => 'checkbox', 'label' => 'Alertas de RMA apenas de SMs abertas por este login')); ?></div>
        <div class="row-fluid inline"><?php echo $this->BForm->input('alerta_sm_refe_codigo_origem', array('type' => 'checkbox', 'label' => 'Alertas de RMA apenas do Alvo Origem deste login')); ?></div>
        <h4>Tipos de alertas</h4>
        <span class='pull-right'>
            <?=$this->Html->link('Desmarcar todas', 'javascript:void(0)', array('onclick' => 'desmarcarTodos("alertas")')) ?>
            <?=$this->Html->link('Marcar todas', 'javascript:void(0)', array('onclick' => 'marcarTodos("alertas")')) ?>
        </span>        
         <div class="row-fluid inline" id="alertas">
            <!-- Carregamento dos alertas via ajax-->               
        </div>
    </div>
    <?php if ($this->action != 'incluir_por_cliente'): ?>
    <div class="row-fluid inline veiculos-alertas" style="display:none;">
        <h4>Veículos</h4>
        <div class='actionbar-right'>
        <?php echo $this->Html->link('Incluir', array('controller' => 'usuarios','action' => 'incluir_veiculo_alerta', 
                    $this->data['Usuario']['codigo'], 
                    rand()), 
                    array(
                        'onclick' => 'return open_dialog(this, "Adicionar Veículo", 560)', 
                        'title' => 'Adicionar Veículo', 
                        'class' => 'btn btn-success',
                    )
                );
            ?>
        </div>
    </div>
    <?php endif; ?>
    <div class="form-actions">
      <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
      <?= $html->link('Voltar', array('action' => 'alertas_por_cliente', $this->data['Usuario']['codigo_cliente']), array('class' => 'btn')); ?>
    </div>
</div>
<?php echo $this->BForm->end(); ?>
<?php echo $this->Javascript->codeBlock('
     //Verifica alertas por Perfil
    jQuery(document).ready(function(){
        setup_mascaras();
        showAlertasTipos();
        $(".checkbox-alerta").change(function(){
            showAlertasTipos();
        });
        function showAlertasTipos(){
            var checked = false;
            $(".checkbox-alerta").each(function(){
                if($(this).is(":checked")){
                    checked = true;
                }
            })
            if(checked){
                $(".alertas-tipos").show();
            }else{
                $(".alertas-tipos").hide();
            }
        }
    });', false);?>
