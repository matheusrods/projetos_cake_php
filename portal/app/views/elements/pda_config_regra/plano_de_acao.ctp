
<?php 
if(isset($codigo) && !empty($codigo)) :
?>
    <hr style="margin: 0px;">
    <div class='row-fluid inline' >
        <label><b>Escolha as condições:</b></label>
        <div class="pull-right">
            <a id="cad_condicao" href="javascript:void(0);" class="btn btn-primary" style="color: #fff;" onclick="cad_condicoes(1);" >Cad. Condição</a>
        </div>
    </div>
    <div class='row-fluid inline' >
        <div class="lista_condicoes"></div>
    </div>

    <div class="modal fade" id="modal_condicoes" data-backdrop="static" style="width: 65%; left: 16%; top: 15%; margin: 0 auto;"></div>

    <hr style="margin: 0px; ">
    
    <div class='row-fluid inline email_push solicitar_analise gestor_responsavel' >
        <label><b>Execute estas ações:</b></label>
        
        <div class="email_push">
            <div class='row-fluid inline'>
                <div>
                    <?php 
                    foreach($acoes_melhoria AS $codigo_acao => $acao) :
                    ?>
                        <?php echo $this->BForm->input('.codigo_pda_tema_acoes.'.$codigo_acao, array('label' => $acao, 'class'=>'input_email_push','type' => 'checkbox', 'value'=>$codigo_acao, 'div' => true,'disabled' => 'disabled')) ?>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class='row-fluid inline '>
                <label>Para:</label>
                <div class="input_email_push_simples">
                    <?php 
                    foreach($tipos_envios AS $codigo_env => $env) :
                    ?>
                        <?php echo $this->BForm->input('.tipo_eventos.'.$codigo_env, array('label' => $env, 'class' => 'input_email_push_simples input', 'type' => 'checkbox', 'value'=>$codigo_env, 'div' => true,'disabled' => 'disabled')) ?>
                        <?php 
                        if($codigo_env == 4) {
                            echo $this->BForm->input('email', array('label' => '', 'class' => 'input_email_push_simples input-large' , 'value' => $email_checked,'disabled' => 'disabled'));
                        }
                        ?>
                    <?php endforeach; ?>
                </div>

                <div class="input_email_push_completos">
                    <?php 
                    foreach($tipos_envios_completos AS $codigo_env => $env) :
                    ?>
                        <?php echo $this->BForm->input('.tipo_eventos.'.$codigo_env, array('label' => $env, 'class' => 'input_email_push_completos input', 'type' => 'checkbox', 'value'=>$codigo_env, 'div' => true,'disabled' => 'disabled')) ?>
                        <?php 
                        if($codigo_env == 4) {
                            echo $this->BForm->input('email', array('label' => '', 'class' => 'input_email_push_completos input-large' , 'value' => $email_checked,'disabled' => 'disabled'));
                        }
                        ?>
                    <?php endforeach; ?>
                </div>

            </div>
            <div class='row-fluid inline '>
                <?php echo $this->BForm->input('assunto', array('label' => 'Assunto', 'class' => 'input_email_push input-xxlarge','disabled' => 'disabled')); ?>
            </div>
            <div class='row-fluid inline '>
                <?php echo $this->BForm->input('mensagem', array('class' => 'input_email_push input input-xxlarge', 'type'=>'textarea', 'rows'=>10, 'label' => 'Mensagem:','disabled' => 'disabled')); ?>
            </div>
            <div class='row-fluid inline '>
                <label><b>**Para gerar o link direcionando para a ação de melhoria colocar a palavra em colchetes exemplo: "[aqui]".</b></label>
            </div>
        </div>

        <div class="solicitar_analise">
            <div class='row-fluid inline'>
                <?php echo $this->BForm->input('codigo_pda_tema_acoes', array('label' => 'Ação', 'class' => 'input_solicitar_analise input', 'options' => $acoes_implantacao,'disabled' => 'disabled')); ?>
            </div>
        </div>

        <div class="gestor_responsavel">
            <div class='row-fluid inline'>
                <?php echo $this->BForm->input('codigo_pda_tema_acoes', array('label' => 'Aprovação do:', 'class' => 'input_gestor_responsavel input', 'empty' => 'Aprovação do:', 'options' => $acoes_cancelamento,'disabled' => 'disabled')); ?>
            </div>
        </div>

    </div>
<?php 
endif;
?>

<?php echo $this->Javascript->codeBlock('        
jQuery(document).ready(function(){
    
    libera_tema = function(codigo_tema){

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

        //console.log(codigo_tema);

        switch(codigo_tema) {
            case "1": //acao_melhoria
                $(".acao_melhoria").show();
                $(".email_push").show();
                $(".input-acao-melhoria").removeAttr("disabled");
                $(".input_email_push").removeAttr("disabled");

                if($("#PdaConfigRegraCodigoAcoesMelhoriasStatus").val() == 3) {
                    $(".input_email_push_completos").show();
                    $(".input_email_push_completos").removeAttr("disabled");
                }
                else {
                    $(".input_email_push_simples").show();
                    $(".input_email_push_simples").removeAttr("disabled");
                }

                break;
            case "2": //acao em atraso

                $(".email_push").show();
                $(".input_email_push").removeAttr("disabled");
                $(".input_email_push_completos").show();
                $(".input_email_push_completos").removeAttr("disabled");
                break;
            case "3": //Em implementação da ação
                
                $(".solicitar_analise").show();
                $(".input_solicitar_analise").removeAttr("disabled");
                break;
            case "4": //Em eficácia da ação

                $(".solicitar_analise").show();
                $(".input_solicitar_analise").removeAttr("disabled");
                break;
            case "5": //Em abrangência da ação

                $(".email_push").show();
                $(".input_email_push").removeAttr("disabled");
                $(".input_email_push_simples").show();
                $(".input_email_push_simples").removeAttr("disabled");

                break;
            case "6": //Aprovação cancelamento

                $(".gestor_responsavel").show();
                $(".input_gestor_responsavel").removeAttr("disabled");
                break;
            case "7": //Aprovação postergamento

                $(".gestor_responsavel").show();
                $(".input_gestor_responsavel").removeAttr("disabled");
                break;
            default:
                break;
        }//fim switch

    } //fim libera tema

    $("#PdaConfigRegraCodigoPdaTema").on("change",function(){
        libera_tema(this.value);

        if(this.value == 1) {
            $("#PdaConfigRegraCodigoAcoesMelhoriasStatus").val("");
        }

        atualizaListaCondicoes();
    });

    $("#PdaConfigRegraCodigoAcoesMelhoriasStatus").on("change",function(){
        libera_tema($("#PdaConfigRegraCodigoPdaTema").val());
        atualizaListaCondicoes();
    });

    libera_tema("'.$this->data['PdaConfigRegra']['codigo_pda_tema'].'");


    cad_condicoes = function (mostra) {
        if(mostra) {
            
            //pega o codigo da configuracao, tema e status caso haja
            var codigo = $("#PdaConfigRegraCodigo").val();
            var codigo_cliente = $("#PdaConfigRegraCodigoCliente").val();
            var codigo_tema = $("#PdaConfigRegraCodigoPdaTema").val();
            var codigo_status = $("#PdaConfigRegraCodigoAcoesMelhoriasStatus").val();

            var div = jQuery("div#modal_condicoes");
            bloquearDiv(div);
            div.load(baseUrl + "pda_config_regra/modal_condicoes/" + codigo + "/"  + codigo_cliente + "/" + codigo_tema + "/" + codigo_status + "/" + Math.random());
    
            $("#modal_condicoes").css("z-index", "1050");
            $("#modal_condicoes").modal("show");

        } else {
            $(".modal").css("z-index", "-1");
            $("#modal_condicoes").modal("hide");
        }

    }

    atualizaListaCondicoes = function(){

        if($("#PosConfigRegraCodigo").val() != "") {

            var codigo = $("#PdaConfigRegraCodigo").val();
            var codigo_pda_tema = $("#PdaConfigRegraCodigoPdaTema").val();
            var codigo_status = $("#PdaConfigRegraCodigoAcoesMelhoriasStatus").val();

            var div = jQuery(".lista_condicoes");
            bloquearDiv(div);
            div.load(baseUrl + "pda_config_regra/listagem_condicoes/" + codigo + "/" + codigo_pda_tema + "/" + codigo_status + "/" + Math.random());

        }

    }

    atualizaListaCondicoes();

});

'); //fim function codeblock
?>
<script type="text/javascript"></script>