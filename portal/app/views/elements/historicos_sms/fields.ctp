<?php
    if($session->read('Message.flash.params.type') == MSGT_SUCCESS){
        $session->delete('Message.flash');
        echo $javascript->codeBlock("close_dialog();atualizaListaAtendimentosSms();");
        exit;
    }
?>
<?php echo $this->BForm->create('HistoricoSm', array('url' => array('action' => 'adicionar_historico', $codigo_sm, $dados_passo_atendimento_sm['PassoAtendimentoSm']['codigo'], $codigo_atendimento))); ?>
<div class='row-fluid inline'>
    <?php echo $this->BForm->hidden('codigo_passo_atendimento_sm', array('value' => $dados_passo_atendimento_sm['PassoAtendimentoSm']['codigo'])); ?>
    <?php echo $this->BForm->input('texto', array('label' => 'Observações', 'type' => 'textarea', 'class' => 'input-xxlarge', 'rows' => 4)); ?>
</div>
<div class='row-fluid inline'>
    <?php if(isset($encaminhado)):?>
        <?php echo $this->BForm->input('tipo_acao', array('label' => false,'class' => 'checkbox inline', 'options' => array(HistoricoSm::TIPO_ACAO_FINALIZADO => 'Finalizar'), 'multiple' => 'checkbox')); ?>
    <?php elseif(isset($ultimo_passo)): ?>
        <?php echo $this->BForm->input('tipo_acao', array('label' => array('class' => 'radio inline'), 'div' => false, 'legend' => false, 'class' => 'radio inline', 'type' => 'radio','value' => HistoricoSm::TIPO_ACAO_EM_ANALISE, 'options' => array(HistoricoSm::TIPO_ACAO_EM_ANALISE => 'Em Análise', HistoricoSm::TIPO_ACAO_FINALIZADO => 'Finalizar'))); ?>
    <?php else: ?>
        <?php echo $this->BForm->input('tipo_acao', array('label' => array('class' => 'radio inline'), 'div' => false, 'legend' => false, 'class' => 'radio inline', 'type' => 'radio','value' => HistoricoSm::TIPO_ACAO_EM_ANALISE, 'options' => array(HistoricoSm::TIPO_ACAO_EM_ANALISE => 'Em Análise', HistoricoSm::TIPO_ACAO_ENCAMINHADO => 'Encaminhar', HistoricoSm::TIPO_ACAO_FINALIZADO => 'Finalizar'))); ?>
    <?php endif; ?>
</div>
<br />
<div class='row-fluid inline'>
    <?php if(!isset($encaminhado) && !isset($ultimo_passo)):?>
        <?php echo $this->BForm->input('codigo_passo_atendimento', array('label' => false, 'class' => 'input-large', 'disabled' => true,'options' => $passos_atendimentos)); ?>
    <?php endif; ?>
    <?php echo $this->BForm->input('latitude', array('label' => false, 'placeholder' => 'Latitude', 'class' => 'input-large', 'value' => $dados['TUposUltimaPosicao']['upos_latitude'])); ?>
    <?php echo $this->BForm->input('longitude', array('label' => false, 'placeholder' => 'Longitude', 'class' => 'input-large', 'value' => $dados['TUposUltimaPosicao']['upos_longitude'])); ?>
</div>
<?php 
if((isset($encaminhado) || isset($ultimo_passo)) && (isset($admin) || isset($pronta_resposta))):?>
<div class="row-fluid">     
    <?php echo $this->element('historicos_sms/prestadores') ?>
</div>
<?php endif; ?>
<div class='form-actions'>
    <?php echo $this->BForm->submit('Salvar', array('type', 'div'=> false, 'class' => 'btn')); ?>
    <?php echo $html->link('Voltar',array('controller'=>'atendimentos_sms','action'=>'atendimentos') , array('class' => 'btn')); ?>
</div>
<?php echo $form->end(); ?>
<?php 
    echo $javascript->codeBlock('
          
        jQuery(document).ready(function(){
            $("form").submit(function(event){
                $("input[type=submit]").attr("disabled", "disabled");
            });

            atualizaListaHistoricosSms('.$codigo_sm.');
                
            $("#HistoricoSmTipoAcao1").click(function(){
                $("#HistoricoSmCodigoPassoAtendimento").attr("disabled", true);
            });
            
            $("#HistoricoSmTipoAcao2").click(function(){
                $("#HistoricoSmCodigoPassoAtendimento").attr("disabled", false);
            });
            
            $("#HistoricoSmTipoAcao3").click(function(){
                $("#HistoricoSmCodigoPassoAtendimento").attr("disabled", true);
            });            
    });');
?>
