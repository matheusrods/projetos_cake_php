
    <?php echo $this->BForm->hidden('cdis_codigo',array('value' => isset($this->passedArgs[0])?$this->passedArgs[0]:null)) ?>
    <div class='row-fluid inline parent'>
        <?php echo $this->BForm->input('cdis_cdfv_codigo', array('class' => 'input-large', 'label'=> 'Faixa de Valor', 'options'=>$faixa_valor,'empty'=>'Selecione um')); ?>
        <?php echo $this->BForm->input('cdis_aatu_codigo', array('class' => 'input-medium', 'label'=> 'Área Atuação', 'options'=>$area_atuacao,'empty'=>'Selecione uma')); ?>
        <?php echo $this->BForm->input('cdis_aatu_codigo_sec', array('class' => 'input-medium', 'label'=> 'Área Atuação Secundária', 'options'=>$area_atuacao,'empty'=>'Selecione uma')); ?>
        <?php echo $this->BForm->input('cdis_aatu_codigo_ter', array('class' => 'input-medium', 'label'=> 'Área Atuação Terciária', 'options'=>$area_atuacao,'empty'=>'Selecione uma')); ?>
        <?php echo $this->BForm->input('cdis_max_sm', array('class' => 'input-small numeric just-number', 'label' => 'Qtd. máxima SM','maxlength'=>3)); ?> 
    </div>    

    <div class='row-fluid inline parent'>        
        <?php echo $this->Buonny->input_codigo_cliente($this, 'cdis_emba_pjur_pess_oras_codigo', 'Embarcador',true,'TCdisCriterioDistribuicao') ?>
        <?php echo $this->BForm->input('nome_embarcador',array('class' => 'input-xlarge name', 'readonly' => true )) ?>
    </div>
    <div class='row-fluid inline parent'>       
        <?php echo $this->Buonny->input_codigo_cliente($this, 'cdis_tran_pess_oras_codigo', 'Transportador',true,'TCdisCriterioDistribuicao') ?>        
        <?php echo $this->BForm->input('nome_transportador',array('class' => 'input-xlarge name', 'readonly' => true )) ?>
    </div>    
    <div class='row-fluid inline parent alvo'>    
        <?php 
        echo $this->Buonny->input_referencia($this, '#TCdisCriterioDistribuicaoCdisEmbaPjurPessOrasCodigo', 'TCdisCriterioDistribuicao', 'cdis_refe_codigo', false, 'Alvo de Origem', true, false, '#TCdisCriterioDistribuicaoCdisTranPessOrasCodigo', $this->data['TCdisCriterioDistribuicao']['cdis_refe_codigo']) ?>
    </div>
    
    <div class='row-fluid inline parent'>

        <?php echo $this->BForm->input('cdis_prod_codigo', array('class' => 'input-large', 'label'=> 'Produto', 'options'=>$produtos,'empty'=>'Selecione um')); ?>
        <?php echo $this->BForm->input('cdis_tecn_codigo', array('class' => 'input-medium', 'label'=> 'Tecnologia', 'options'=>$tecnologias,'empty'=>'Selecione uma')); ?>
        <?php echo $this->BForm->input('cdis_ttra_codigo', array('class' => 'input-medium', 'label'=> 'Tipo Transporte', 'options'=>$tipo_transporte,'empty'=>'Selecione um')); ?>

    </div>
    
    <div class="form-actions">
          <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
          <?php echo $html->link('Voltar',array('controller' => 'criterios_distribuicao', 'action' => 'index'), array('class' => 'btn')) ;?>
    </div>

<?php $this->addScript($this->Buonny->link_js('search')) ?>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){

        setup_mascaras();

        $("input.input-mini").blur(function(){
            carregar($(this));
        });
        
        if( $("#TCdisCriterioDistribuicaoCdisEmbaPjurPessOrasCodigo").val() != "" )
            carregar($("#TCdisCriterioDistribuicaoCdisEmbaPjurPessOrasCodigo"));

        if( $("#TCdisCriterioDistribuicaoCdisTranPessOrasCodigo").val() != "" )
            carregar($("#TCdisCriterioDistribuicaoCdisTranPessOrasCodigo"));

        function carregar(obj){
            var field_name = obj.parents("div.parent:eq(0)").find("input.name");

            if(obj.val()){
                $.ajax({
                    url: baseUrl + "Clientes/buscar/" + obj.val() + "/"+  Math.random(),
                    dataType: "json",
                    success: function(data){
                        field_name.val(data.dados.razao_social);
                        alvo();
                    }
                });
            } else {
                field_name.val("");
                alvo();
            }
        }
        function alvo(){            
            $emb = $("#TCdisCriterioDistribuicaoCdisEmbaPjurPessOrasCodigo").val();
            $emb_nome = $("#TCdisCriterioDistribuicaoNomeEmbarcador").val();
            $tra = $("#TCdisCriterioDistribuicaoCdisTranPessOrasCodigo").val();
            $tra_nome = $("#TCdisCriterioDistribuicaoNomeTransportador").val();

            if($emb_nome == "" && $tra_nome == ""){
                $("#TCdisCriterioDistribuicaoCdisRefeCodigo").val("");
                $(".alvo").hide();
            }else{
                $(".alvo").show();
            }
        }
        if($("#TCdisCriterioDistribuicaoCdisRefeCodigo").val() == "")
            alvo();        

    });', false);