
<div class="well">
        <?php echo $this->BForm->create('SituacaoMonitoramento', array( 'url' => array('controller' => 'solicitacoes_monitoramento', 'action' => 'situacao_monitoramento'))) ?>
        <div class="row-fluid inline">
                <?php echo $this->BForm->input('quantidade_eventos', array('class' => 'input-small numeric', 'label' => 'Qtd.Eventos')); ?>
                <?php echo $this->BForm->input('intervalo', array('class' => 'input-medium numeric', 'label' => 'Segundos para Atualização')); ?>
            </div>          
            <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
        <?php echo $this->BForm->end();?>
</div>


<?php $this->addScript($this->Buonny->link_js('highcharts/highcharts')) ?>
<?php $this->addScript($this->Buonny->link_js('highcharts/modules/exporting')) ?>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){

        atualizaListaSituacaoMonitoramento();        
        var intervaloFixo = 0000;
        var carregaDados = setInterval( atualizaListaSituacaoMonitoramento, intervaloFixo );            

        
        jQuery("a#filtros").click(function(){
            jQuery("div#filtros").slideToggle("slow");
        });*/
        
        
    });', false);
?>