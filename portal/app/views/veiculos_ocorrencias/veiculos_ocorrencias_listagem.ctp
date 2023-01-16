<?php echo $this->BForm->hidden('campo_ordem', array('class' => 'campo_ordem')) ?>
<?php echo $this->BForm->hidden('direcao_ordem', array('class' => 'direcao_ordem')) ?>
<?php 
    echo $paginator->options(array('update' => 'div.veiculos-ocorrencias')); 
?>
<table class="table table-striped">
    <thead>
        <tr>
            <th class='input-mini'><?php echo $this->Paginator->sort('Placa', 'TVeicVeiculo.veic_placa') ?></th>
            <th><?php echo $this->Paginator->sort('Transportador', 'Transportador.pjur_razao_social') ?></th>
            <th><?php echo $this->Paginator->sort('Embarcador', 'Embarcador.pjur_razao_social') ?></th>
            <th><?php echo $this->Paginator->sort('Tecnologia', 'TTecnTecnologia.tecn_descricao') ?></th>
            <th><?php echo $this->Paginator->sort('Versão da Tecnologia', 'TVtecVersaoTecnologia.vtec_descricao') ?></th>
            <th><?php echo $this->Paginator->sort('Número do Terminal', 'TTermTerminal.term_numero_terminal') ?></th>
            <th class='input-small numeric'><?php echo $this->Paginator->sort('Tempo (minutos)', 'TOveiOcorrenciaVeiculo.ovei_data_cadastro') ?></th>
            <th class='input-small'><?php echo $this->Paginator->sort('Tipo', 'TTvocTipoVeiculoOco.tvoc_descricao') ?></th>
            <th class='input-small'><?php echo $this->Paginator->sort('Status', 'TSvocStatusVeiculoOco.svoc_descricao') ?></th>
            <th class='action-icon'></th>
            <th class='action-icon'></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($veiculos as $veiculo): ?>
            <tr class='<?php echo (($veiculo['TOveiOcorrenciaVeiculo']['ovei_usuario_tratamento'] == substr($_SESSION['Auth']['Usuario']['apelido'], 0,20) && $veiculo['TOveiOcorrenciaVeiculo']['ovei_svoc_codigo'] == TSvocStatusVeiculoOco::EM_ANALISE) ? 'atribuido' : 'nao-atribuido') ?>'>
                <td><?php echo $this->Buonny->placa($veiculo['TVeicVeiculo']['veic_placa'],substr($veiculo['TOveiOcorrenciaVeiculo']['ovei_data_cadastro'],0,10),date('d/m/Y')) ?></td>
                <td><?php echo $veiculo['Transportador']['pjur_razao_social'] ?></td>
                <td><?php echo $veiculo['Embarcador']['pjur_razao_social'] ?></td>
                <td><?php echo $veiculo['TTecnTecnologia']['tecn_descricao'] ?></td>
                <td><?php echo $veiculo['TVtecVersaoTecnologia']['vtec_versao'] ?></td>
                <td><?php echo $veiculo['TTermTerminal']['term_numero_terminal'] ?></td>
                <td class="numeric"><?php echo ($veiculo['TOveiOcorrenciaVeiculo']['tempo_ocorrencia'] ? $veiculo['TOveiOcorrenciaVeiculo']['tempo_ocorrencia'] : '0'); ?></td>
                <?php if($veiculo['TTvocTipoVeiculoOco']['tvoc_codigo'] == TTvocTipoVeiculoOco::VEICULO_SEM_CHECKLIST): ?>
                    <td><?php echo $veiculo['TTvocTipoVeiculoOco']['tvoc_descricao'].' - '.($veiculo['TOveiOcorrenciaVeiculo']['ovei_checklist_solicitado']?'SOLICITADO':'SM') ?></td>
                <?php else: ?>
                    <td><?php echo $veiculo['TTvocTipoVeiculoOco']['tvoc_descricao'] ?></td>
                <?php endif; ?>
                <td><?php echo $veiculo['TSvocStatusVeiculoOco']['svoc_descricao'] ?></td>
                <td>
                    <?php if($veiculo['TTvocTipoVeiculoOco']['tvoc_codigo'] == TTvocTipoVeiculoOco::VEICULO_SEM_CHECKLIST && empty($veiculo['TOveiOcorrenciaVeiculo']['ovei_data_recusa'])):?>
                        <?php echo $this->BMenu->linkOnClick('',array('controller' => 'veiculos_ocorrencias','action' => 'recusar_checklist', $veiculo['TOveiOcorrenciaVeiculo']['ovei_codigo']), array('onclick' => 'return open_dialog(this, "Recusar Checklist", 560, undefined, atualizaListaVeiculosOcorrencias2)','class' => 'icon-ban-circle', 'title' => 'Resusar Checklist')); ?>
                    <?php endif;?>
                </td>
                <td>
                    <?php if(empty($veiculo['TOveiOcorrenciaVeiculo']['ovei_data_recusa'])):?>
                        <?php 
                        if($veiculo['TTvocTipoVeiculoOco']['tvoc_codigo'] == TTvocTipoVeiculoOco::VEICULO_SEM_SINAL){
                            echo $this->Html->link('', array('controller' => 'veiculos_ocorrencias', 'action' => 'tratar_veiculo_ocorrencia', $veiculo['TOveiOcorrenciaVeiculo']['ovei_codigo']), array('onclick' => 'return abrir_dialog(this,'.$veiculo['TOveiOcorrenciaVeiculo']['ovei_codigo'].');', 'title' => 'Tratar ocorrência', 'class' => 'icon-wrench'));
                        }elseif($veiculo['TTvocTipoVeiculoOco']['tvoc_codigo'] == TTvocTipoVeiculoOco::VEICULO_SEM_CHECKLIST){
                            echo $this->Html->link('', 'javascript:void(0)', array('title' => 'Tratar ocorrência', 'class' => 'icon-wrench','onclick' => "ocorrencia_veiculo_checklist({$veiculo['TOveiOcorrenciaVeiculo']['ovei_codigo']})"));
                        }
                        ?>
                    <?php endif;?>
                </td>
            </tr>
        <?php endforeach; ?>        
    </tbody>
    <tfoot>
        <tr>
            <td><strong>Total: </strong><?php echo $this->params['paging']['TOveiOcorrenciaVeiculo']['count']; ?></td>
            <td colspan='10'></td>
        </tr>
    </tfoot>
</table>
<?php if($this->params['paging']['TOveiOcorrenciaVeiculo']['pageCount'] > 1): ?>
    <div class='row-fluid'>
        <div class='numbers span6'>
            <?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
            <?php echo $this->Paginator->numbers(); ?>
            <?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
        </div>
        <div class='counter span6'>
            <?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
        </div>
    </div>
<?php endif; ?>
<?php echo $this->Js->writeBuffer(); ?>
<?php echo $this->Javascript->codeBlock("
    jQuery(document).ready(function(){
        $('.atribuido>td').css({'background-color':'#fcf8e3'});
        $('.atribuido').hover(function(){
            $(this).find('td').css({'background-color':'#f5f5f5'});
        },function(){
            $(this).find('td').css({'background-color':'#fcf8e3'});
        });
        $('.numbers a[id^=\"link\"]').bind('click', function (event) { 
            bloquearDiv($('.veiculos-ocorrencias')); 
        });
    });", false);
?>