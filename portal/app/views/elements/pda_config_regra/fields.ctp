<div class='well'>  
    <div class='row-fluid inline'>
        <?php echo $this->BForm->input('descricao', array('label' => 'Nome da Regra', 'class' => 'input-xxlarge')); ?>
    </div>
    <div class='row-fluid inline'>
        <?php echo $this->BForm->input('codigo_pos_ferramenta', array('label' => 'Ferramenta (*)', 'class' => 'input required', 'empty' => 'Selecione uma Ferramenta', 'options' => $pos_ferramenta)); ?>
        
        <?php echo $this->BForm->input('codigo_pda_tema', array('label' => 'Tema (*)', 'class' => 'input required', 'empty' => 'Selecione um tema', 'options' => $temas)); ?>
        <img src="/portal/img/loading.gif" title="carregando..." id="loading_tema" style="position: relative; margin-top: 30px; display: none;" />
        
        <div class="acao_melhoria"  >
            <?php echo $this->BForm->input('codigo_acoes_melhorias_status', array('label' => 'Status', 'class' => 'input input-acao-melhoria', 'empty' => 'Selecione um Status', 'options' => $status,'disabled' => 'disabled')); ?>
        </div>

    </div>

    <?php 
    if(isset($codigo) && !empty($codigo)) {
        if($this->data['PdaConfigRegra']['codigo_pos_ferramenta'] == 1) { //plano de acao
            echo $this->element('pda_config_regra/plano_de_acao');
        }
        else if($this->data['PdaConfigRegra']['codigo_pos_ferramenta'] == 2) { //swt
            echo '<script type="text/javascript">jQuery(document).ready(function(){ $(".acao_melhoria").hide(); });</script>';

            echo $this->element('pda_config_regra/swt');
        }
        else if($this->data['PdaConfigRegra']['codigo_pos_ferramenta'] == 3) { //obs
            echo '<script type="text/javascript">jQuery(document).ready(function(){ $(".acao_melhoria").hide(); });</script>';

            echo $this->element('pda_config_regra/obs');
        }
    }
    ?>
</div>

<div class='form-actions'>
    <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
    <?= $html->link('Voltar', array('controller' => 'pda_config_regra', 'action' => 'index_pda_regra'), array('class' => 'btn')); ?>
</div>

<?php echo $this->Javascript->codeBlock('        
jQuery(document).ready(function(){
    
    get_tema = function() {

        $(".acao_melhoria").hide();
        $(".input-acao-melhoria").attr("disabled");
        $(".email_push").hide();
        
        $(".input_email_push").attr("disabled");

        $(".input_email_push_simples").hide();
        $(".input_email_push_simples").attr("disabled");
        $(".input_email_push_completos").hide();
        $(".input_email_push_completos").attr("disabled");

        $(".solicitar_analise").hide();
        $(".input_solicitar_analise").attr("disabled");
        $(".gestor_responsavel").hide();
        $(".input_gestor_responsavel").attr("disabled");

        //verifica se está selecinado
        var ferramenta = $("#PdaConfigRegraCodigoPosFerramenta").val();
        if(ferramenta != "" ) {
            $("#loading_tema").show();
            $.ajax({
                "url": "/portal/pda_config_regra/combo_tema/" + ferramenta + "/" + Math.random(),
                "success": function(data) {
                    $("#PdaConfigRegraCodigoPdaTema").html(data).val();
                    $("#loading_tema").hide();
                }
            });
        }
    }

    $("#PdaConfigRegraCodigoPosFerramenta").on("change",function(){
        get_tema();
    });    
});

'); //fim function codeblock
?>
<script type="text/javascript"></script>