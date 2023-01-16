<div class='form-procurar'> 
    <div class='well'>
        <h5><?= $this->Html->link((!empty($cliente) ? 'Listagem Filtrada' : 'Definir Filtros'), 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show')) ?></h5>
        <?php echo $this->BForm->create('FichaScorecard', array('autocomplete' => 'off', 'url' => array('controller' => 'fichas_scorecard', 'action' => 'relatorio_sla'), 'divupdate' => '.form-procurar')) ?>
        <div id='filtros'>
            <div class="row-fluid inline">
                <?php echo $this->Buonny->input_codigo_cliente_base($this) ?>
                <?php echo $this->BForm->input("Cliente.razao_social", array('label' => false, 'class' => 'input-xxlarge', 'readonly'=>true)) ?>   
            </div>
            <div class="row-fluid inline">
                <?php echo $this->BForm->input('mes_referencia', array('class' => 'input-medium', 'label' => 'Periodo', 'options' => $meses, 'value'=>$mes_referencia)); ?>
                <?php echo $this->BForm->input('ano_referencia', array('class' => 'input-small just-number', 'label' => 'Ano', 'value'=>$ano_referencia, 'maxlength'=>4)); ?>
                <?php echo $this->BForm->input('tipo_operacao', array('label' => 'Operação', 'options' => $operacoes, 'empty' => 'Selecione um tipo de operação')); ?>
            </div>
            
            <div class="row-fluid inline">
                <span class="label label-info">Tipos de Profissional</span>
                 <span class='pull-right'>
                    <?= $html->link('Desmarcar todas', 'javascript:void(0)', array('onclick' => 'desmarcarTodos("tipo_veiculo")')) ?>
                    <?= $html->link('Marcar todas', 'javascript:void(0)', array('onclick' => 'marcarTodos("tipo_veiculo")')) ?>
                </span>
                <div id='tipo_veiculo'>
                    <?php echo $this->BForm->input('profissional', array('label'=>'', 'options'=>$tipos_profissional, 'multiple'=>'checkbox', 'class' => 'checkbox inline input-xlarge')); ?>
                </div>
            </div>
            <div class="row-fluid inline">
                <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
                <?php echo $this->BForm->end();?>
            </div>
        </div>
    </div>
</div>
<?php if (!empty($resposta) && $resposta->success): ?>
    <div class='well'>
        <strong>Código: </strong><?= $cliente['Cliente']['codigo'] ?>
        <strong>Cliente: </strong><?= $cliente['Cliente']['razao_social'] ?>
    </div>
    <?php if(count($resposta->tempos) > 0): ?>
        <?php foreach($resposta->tempos as $k=>$v): ?>
            <div class='span6 window-gadget' style="margin:5px">    
            <div class='alert alert-info'><strong><?php echo $k; ?></strong></div>
                <table class="table table-striped table-bordered tablesorter">
                    <tbody>
                        <tr>
                            <td><strong>Quantidade total</strong></td>
                            <td class="numeric"><?php echo $v['quantidade']; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Quantidade dentro do prazo</strong></td>
                            <td class="numeric"><?php echo $v['Estatistica']['no_prazo']; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Quantidade fora do prazo</strong></td>
                            <td class="numeric"><?php echo $v['Estatistica']['fora_do_prazo']; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Nível SLA</strong></td>
                            <td class="numeric"><?php echo round($v['Estatistica']['no_prazo']*100 / $v['quantidade']); ?>% </td>
                        </tr>
                        <tr>
                            <td><strong>Tempo médio</strong></td>
                            <td class="numeric"><?php echo gmdate("H:i:s", (abs($v['Estatistica']['tempo_medio'])*60) );?></td>
                        </tr>
                    </tbody>        
                </table>
            </div>
        <?php endforeach; ?>
        <?php 
            $fichas_fora_do_prazo = array();
        foreach($resposta->fichas as $ficha):            
            $tempo_restante = ( $ficha['FichaScorecard']['tempo_sla'] - $ficha[0]['tempo_pesquisa_ficha'] );
            if ( $tempo_restante <= 0 ) :
                $fichas_fora_do_prazo[] = $ficha;
            endif;
        endforeach;
        ?>
        <?php if(count($fichas_fora_do_prazo) > 0):?>
            <br style="clear: both;" />
            <h2>Fichas fora do prazo</h2>
            <table class="table table-striped">
                <thead>
                <tr>
                    <!-- <th>Ficha</th> -->
                    <th>CPF</th>
                    <th>Profissional</th>
                    <th>Status Profissional</th>
                    <th>Tempo Excedido</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach($fichas_fora_do_prazo as $ficha): ?>
                    <?php $tempo_excedido = $ficha['FichaScorecard']['tempo_sla']-$ficha[0]['tempo_pesquisa_ficha']; ?>
                    <?php $tempo_restante = ( $ficha['FichaScorecard']['tempo_sla'] - $ficha[0]['tempo_pesquisa_ficha'] );?>
                    <tr>
                        <!-- <td><?php echo $this->Buonny->codigo_ficha_scorecard($ficha['FichaScorecard']['codigo']) ?></td>                             -->
                        <td><?php echo comum::formatarDocumento($ficha['ProfissionalLog']['codigo_documento']); ?></td>
                        <td><?php echo $ficha['ProfissionalLog']['nome']; ?></td>
                        <td><?php echo $ficha['ProfissionalStatus']['descricao']; ?></td>
                        <td><?php echo gmdate("H:i:s", (abs($tempo_excedido)*60) );?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    <?php else: ?>
        <div class='alert alert-warning'><strong>Não há registros encontrados para os critérios pesquisados.</strong></div>
    <?php endif; ?>
<?php endif; ?>
<?php $this->addScript($this->Buonny->link_css('tablesorter')); ?>
<?php $this->addScript($this->Buonny->link_js('jquery.tablesorter.min')); ?>
<?php echo $this->Javascript->codeBlock('
    $(document).ready(function() {
        pesquisa_cliente( "FichaScorecardCodigoCliente" );
        jQuery("a#filtros").click(function(){
            jQuery("div#filtros").slideToggle("slow");
        });
    });', false);
?>
<?php if (!empty($cliente)): ?>
    <?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){jQuery("div#filtros").hide()})');?>
<?php endif; ?>