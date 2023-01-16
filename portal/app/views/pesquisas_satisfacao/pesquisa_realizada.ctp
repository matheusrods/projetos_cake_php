<?php
    if( !empty($observacao_complementar_success) ){
        echo $javascript->codeBlock(
            "close_dialog();
            var div = jQuery('div.lista');
            bloquearDiv(div);
            div.load(baseUrl + 'pesquisas_satisfacao/listagem_pesquisa_satisfacao/' + Math.random());");
        exit;    
    }
?>
<?php echo $bajax->form('PesquisaSatisfacao', array('url' => array('controller' => 'PesquisasSatisfacao', 'action' => 'pesquisa_realizada', $this->params['pass']['0']))); ?>
<?php echo $this->BForm->input('codigo');?>
<?php unset($status_pesquisa[4])?>
<div id="cliente">
    <div class='row-fluid'>        
        <?php echo $this->BForm->input('codigo_status_pesquisa', array('class' => 'input-xlarge', 'id'=>'status_pesquisa','options' => array($status_pesquisa), 'label' => 'Nível de Satisfação','empty' => 'Selecione'))?>
        <p>Data da Pesquisa: <?php echo $this->data['PesquisaSatisfacao']['data_pesquisa']?></p>
        <p>Cliente: <?php echo $this->data['Cliente']['razao_social']?></p>
        <p>Contato: <?=$this->data['ClienteContato']['nome']?></p>
        <p>Telefone: <?php echo '('.$this->data['ClienteContato']['ddd'].') '.$this->data['ClienteContato']['descricao']?></p>
        <p>Responsável pela pesquisa: <?php echo $this->data['Usuario']['apelido']?></p>
        <?php echo $this->BForm->input('PesquisaSatisfacao.observacao', array('label' => 'Observações:', 'type' => 'textarea','readonly'=>'readonly','class' => 'input-xxlarge')) ?>
        <? if( !empty($this->data['PesquisaSatisfacao']['codigo_status_pesquisa']) ) :?>
        <h5><?= $this->Html->link('Adicionar observação', 'javascript:void(0)', array('id' => 'observacao_complementar', 'class' => 'link-hide-show')) ?></h5>
        <div id="observacao_complementar">        
            <?php echo $this->BForm->input('PesquisaSatisfacao.observacao_complementar', array('label' => 'Observações Complementares:', 'type' => 'textarea', 'class' => 'input-xxlarge')) ?>
        </div>
        <?endif;?>
    </div> 
</div>
<?= (!empty($codigo_pai) ? $this->Html->link('Pesquisa origem',array('action' => 'pesquisa_realizada',$codigo_pai),array('escape' => false,'title' =>'Visualizar Origem da Pesquisa','onclick' => "return open_dialog(this,'Pesquisa Realizada', 760)")) : '');?>
<h5><?= $this->Html->link('Visualizar Histórico', 'javascript:void(0)', array('id' => 'historico', 'class' => 'link-hide-show')) ?></h5>
<div id="historico">
    <?= $this->element('/pesquisa_satisfacao/historico_pesquisa_satisfacao') ?>
</div>
<div class="form-actions">
    <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
</div>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        jQuery("a#historico").click(function(){
            jQuery("div#historico").slideToggle("slow");
        });        
        jQuery("div#historico").hide();
        jQuery("div#observacao_complementar").hide();        
        jQuery("a#observacao_complementar").click(function(){
            jQuery("div#observacao_complementar").slideToggle("slow");
        });        
        //$(".telefone").mask("(99)9999-9999?9").addClass("format-phone");
    });', false);?>