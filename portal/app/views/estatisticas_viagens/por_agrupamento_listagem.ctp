<?php if (!empty($lista)): ?>
	<?php
		switch($filtros['agrupamento']){
			case TEviaEstaViagem::AGRP_EMBARCADOR:
				$labels = array('Embarcador','Transportadores','Seguradoras','Corretoras','Tecnologias','Operadores','SMs Abertas','SMs Monitoradas','Média');
				$colunas = array($prefixo.'_emba_pjur_razao_social',$prefixo.'_tran_total',$prefixo.'_segu_total',$prefixo.'_corr_total',$prefixo.'_tecn_total',$prefixo.'_oper_total',$prefixo.'_sm_em_aberto',$prefixo.'_sm_em_andamento',$prefixo.'_em_andamento_por_operador',$prefixo.'_emba_pjur_pess_oras_codigo');
				$colunasTotais = array('evia_emba_total','evia_tran_total','evia_segu_total','evia_corr_total','evia_tecn_total','evia_oper_total','evia_sm_em_aberto','evia_sm_em_andamento');
				$groups = array(2,3,4,5,6);
				break;
			case TEviaEstaViagem::AGRP_TRANSPORTADOR:
				$labels = array('Transportador','Embarcadores','Seguradoras','Corretoras','Tecnologias','Operadores','SMs Abertas','SMs Monitoradas','Média');
				$colunas = array($prefixo.'_tran_pjur_razao_social',$prefixo.'_emba_total',$prefixo.'_segu_total',$prefixo.'_corr_total',$prefixo.'_tecn_total',$prefixo.'_oper_total',$prefixo.'_sm_em_aberto',$prefixo.'_sm_em_andamento',$prefixo.'_em_andamento_por_operador',$prefixo.'_tran_pess_oras_codigo');
				$colunasTotais = array('evia_tran_total','evia_emba_total','evia_segu_total','evia_corr_total','evia_tecn_total','evia_oper_total','evia_sm_em_aberto','evia_sm_em_andamento');
				$groups = array(1,3,4,5,6);
				break;
			case TEviaEstaViagem::AGRP_SEGURADORA:
				$labels = array('Seguradora','Embarcadores','Transportadores','Corretoras','Tecnologias','Operadores','SMs Abertas','SMs Monitoradas','Média');
				$colunas = array(($prefixo == 'evia' ? 'evia_segu_nome' : $prefixo.'_evia_segu_nome'),$prefixo.'_emba_total',$prefixo.'_tran_total',$prefixo.'_corr_total',$prefixo.'_tecn_total',$prefixo.'_oper_total',$prefixo.'_sm_em_aberto',$prefixo.'_sm_em_andamento',$prefixo.'_em_andamento_por_operador',($prefixo == 'evia' ? 'evia_segu_codigo' : $prefixo.'_evia_segu_codigo'));
				$colunasTotais = array('evia_segu_total','evia_emba_total','evia_tran_total','evia_corr_total','evia_tecn_total','evia_oper_total','evia_sm_em_aberto','evia_sm_em_andamento');
				$groups = array(1,2,4,5,6);
				break;
			case TEviaEstaViagem::AGRP_CORRETORA:
				$labels = array('Corretora','Embarcadores','Transportadores','Seguradoras','Tecnologias','Operadores','SMs Abertas','SMs Monitoradas','Média');
				$colunas = array(($prefixo == 'evia' ? 'evia_corr_nome' : $prefixo.'_evia_corr_nome'),$prefixo.'_emba_total',$prefixo.'_tran_total',$prefixo.'_segu_total',$prefixo.'_tecn_total',$prefixo.'_oper_total',$prefixo.'_sm_em_aberto',$prefixo.'_sm_em_andamento',$prefixo.'_em_andamento_por_operador',($prefixo == 'evia' ? 'evia_corr_codigo' : $prefixo.'_evia_corr_codigo'));
				$colunasTotais = array('evia_corr_total','evia_emba_total','evia_tran_total','evia_segu_total','evia_tecn_total','evia_oper_total','evia_sm_em_aberto','evia_sm_em_andamento');
				$groups = array(1,2,3,5,6);
				break;
			case TEviaEstaViagem::AGRP_TECNOLOGIA:
				$labels = array('Tecnologia','Embarcadores','Transportadores','Seguradoras','Corretoras','Operadores','SMs Abertas','SMs Monitoradas','Média');
				$colunas = array($prefixo.'_tecn_descricao',$prefixo.'_emba_total',$prefixo.'_tran_total',$prefixo.'_segu_total',$prefixo.'_corr_total',$prefixo.'_oper_total',$prefixo.'_sm_em_aberto',$prefixo.'_sm_em_andamento',$prefixo.'_em_andamento_por_operador',$prefixo.'_tecn_codigo');
				$colunasTotais = array('evia_tecn_total','evia_emba_total','evia_tran_total','evia_segu_total','evia_corr_total','evia_oper_total','evia_sm_em_aberto','evia_sm_em_andamento');
				$groups = array(1,2,3,4,6);
				break;
			case TEviaEstaViagem::AGRP_OPERADOR:
				$labels = array('Operador','Embarcadores','Transportadores','Seguradoras','Corretoras','Tecnologias','SMs Abertas','SMs Monitoradas','Média');
				$colunas = array(($prefixo == 'evia' ? 'evia_usua_login' : $prefixo.'_oper_nome'),$prefixo.'_emba_total',$prefixo.'_tran_total',$prefixo.'_segu_total',$prefixo.'_corr_total',$prefixo.'_tecn_total',$prefixo.'_sm_em_aberto',$prefixo.'_sm_em_andamento',$prefixo.'_em_andamento_por_operador',($prefixo == 'evia' ? 'evia_usua_oras_codigo' : $prefixo.'_oper_codigo'));
				$colunasTotais = array('evia_oper_total','evia_emba_total','evia_tran_total','evia_segu_total','evia_corr_total','evia_tecn_total','evia_sm_em_aberto','evia_sm_em_andamento');
				$groups = array(1,2,3,4,5);
				break;
		}
	?>
    <div id="grafico" style="min-width: 400px; height: 400px; margin: 0 auto 50px"></div>
    <table class="table table-striped table-bordered tablesorter">
        <thead>
            <tr>
                <th class="" title="{$labels[0]}"><?= $this->Html->link($labels[0], 'javascript:void(0)') ?></th>
                <th class="numeric" title="{$labels[1]}"><?= $this->Html->link($labels[1], 'javascript:void(0)') ?></th>
                <th class="numeric" title="{$labels[2]}"><?= $this->Html->link($labels[2], 'javascript:void(0)') ?></th>
                <th class="numeric" title="{$labels[3]}"><?= $this->Html->link($labels[3], 'javascript:void(0)') ?></th>
                <th class="numeric" title="{$labels[4]}"><?= $this->Html->link($labels[4], 'javascript:void(0)') ?></th>
                <th class="numeric" title="{$labels[5]}"><?= $this->Html->link($labels[5], 'javascript:void(0)') ?></th>
                <th class="numeric" title="{$labels[6]}"><?= $this->Html->link($labels[6], 'javascript:void(0)') ?></th>
                <th class="numeric" title="{$labels[7]}"><?= $this->Html->link($labels[7], 'javascript:void(0)') ?></th>
                <?php if($filtros['agrupamento'] != TEviaEstaViagem::AGRP_OPERADOR): ?>
                	<th class="numeric" title="{$labels[8]}"><?= $this->Html->link($labels[8], 'javascript:void(0)') ?></th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($lista as $operacao): ?>
                <tr>
                    <td class=""><?= $operacao[$model][$colunas[0]] ?></td>
                    <td class="numeric"><?= $this->Html->link($operacao[$model][$colunas[1]], 'javascript:void(0)', array('onclick' => 'estatistica_por_agrupamento_e_filtros("'.$groups[0].'","'.$filtros['agrupamento'].'","'.$operacao[$model][$colunas[9]].'")')) ?></td>
                    <td class="numeric"><?= $this->Html->link($operacao[$model][$colunas[2]], 'javascript:void(0)', array('onclick' => 'estatistica_por_agrupamento_e_filtros("'.$groups[1].'","'.$filtros['agrupamento'].'","'.$operacao[$model][$colunas[9]].'")')) ?></td>
                    <td class="numeric"><?= $this->Html->link($operacao[$model][$colunas[3]], 'javascript:void(0)', array('onclick' => 'estatistica_por_agrupamento_e_filtros("'.$groups[2].'","'.$filtros['agrupamento'].'","'.$operacao[$model][$colunas[9]].'")')) ?></td>
                    <td class="numeric"><?= $this->Html->link($operacao[$model][$colunas[4]], 'javascript:void(0)', array('onclick' => 'estatistica_por_agrupamento_e_filtros("'.$groups[3].'","'.$filtros['agrupamento'].'","'.$operacao[$model][$colunas[9]].'")')) ?></td>
                    <td class="numeric"><?= $this->Html->link($operacao[$model][$colunas[5]], 'javascript:void(0)', array('onclick' => 'estatistica_por_agrupamento_e_filtros("'.$groups[4].'","'.$filtros['agrupamento'].'","'.$operacao[$model][$colunas[9]].'")')) ?></td>
                    <td class="numeric"><?= $this->Html->link($operacao[$model][$colunas[6]], 'javascript:void(0)', array('onclick' => 'listar_sms_agrupamento(0,"'.$filtros['agrupamento'].'","'.$operacao[$model][$colunas[9]].'")')) ?></td>
                    <td class="numeric"><?= $this->Html->link($operacao[$model][$colunas[7]], 'javascript:void(0)', array('onclick' => 'listar_sms_agrupamento(1,"'.$filtros['agrupamento'].'","'.$operacao[$model][$colunas[9]].'")')) ?></td>
                	<?php if($filtros['agrupamento'] != TEviaEstaViagem::AGRP_OPERADOR): ?>
                    	<td class="numeric"><?= $this->Buonny->moeda(round($operacao[$model][$colunas[8]],1), array('edit' => true,'places' => 1)) ?></td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <td class="numeric"><?= $listaTotais[0]['TEviaEstaViagem'][$colunasTotais[0]] ?></td>
            <td class="numeric"><?= $listaTotais[0]['TEviaEstaViagem'][$colunasTotais[1]] ?></td>
            <td class="numeric"><?= $listaTotais[0]['TEviaEstaViagem'][$colunasTotais[2]] ?></td>
            <td class="numeric"><?= $listaTotais[0]['TEviaEstaViagem'][$colunasTotais[3]] ?></td>
            <td class="numeric"><?= $listaTotais[0]['TEviaEstaViagem'][$colunasTotais[4]] ?></td>
            <td class="numeric"><?= $listaTotais[0]['TEviaEstaViagem'][$colunasTotais[5]] ?></td>
            <td class="numeric"><?= $listaTotais[0]['TEviaEstaViagem'][$colunasTotais[6]] ?></td>
            <td class="numeric"><?= $listaTotais[0]['TEviaEstaViagem'][$colunasTotais[7]] ?></td>
            <?php if($filtros['agrupamento'] != TEviaEstaViagem::AGRP_OPERADOR): ?>
            	<td></td>
            <?php endif; ?>
        </tfoot>
    </table>
    <?php echo $this->Javascript->codeBlock("
    	$(document).ready(function(){
		    jQuery('table.table').tablesorter({
		        sortList: [[0,0]], 
		        dateFormat: 'dd/mm/yyyy'
	       	});
    	});
    ") ?>
    <?php echo $this->Javascript->codeBlock($this->Highcharts->render($eixo_x, $series, array(
        'renderTo' => 'grafico',
        'chart' => array('type' => 'pie'),
        'plotOptions' => array('pie' => array('showInLegend' => 'true')),
    ))); ?>
<?php endif; ?>