<?php 
    echo $paginator->options(array('update' => 'div.lista')); 
    $total_paginas = $this->Paginator->numbers();
?>
<table id="listaOcorrencias" class="table table-striped">
    <thead>
        <tr>
            <th style='width:41px; text-align:center;' title="Prioridade"><?= $this->Paginator->sort('Prior', 'codigo_prioridade') ?></th>
            <th style='width:150px'><?= $this->Paginator->sort('Nome cliente', 'codigo_sm') ?></th>
            <th style='width:250px'><?= $this->Paginator->sort('Data', 'data_ocorrencia') ?></th>
            <th style='width:54px'><?= $this->Paginator->sort('Placa', 'placa') ?></th>
            <th style='text-align:center'><?= $this->Paginator->sort('Tipo Ocorrência', 'tipos_ocorrencia') ?></th>
            <th style='width:88px'><?= $this->Paginator->sort('Rodovia', 'rodovia') ?></th> 
            <th style='width:157px'><?= $this->Paginator->sort('Status', 'codigo_status_ocorrencia') ?></th>
            <th style="width:14px"></th>
            <th style="width:14px"></th>
        </tr>
    </thead>
    <tbody>
        <?php
         $classe = 'cor-sim';
         foreach ($ocorrencias as $lista_ocorrencia) : ;?>
            <? $classe = ($classe == 'cor-sim' ? 'cor-nao' : 'cor-sim'); ?>
            <tr>
                <td><?php echo ($lista_ocorrencia['Ocorrencia']['codigo_prioridade'] == 1 ? '<span class="badge-empty badge badge-success" title="Prioridade Baixa"></span>' : ($lista_ocorrencia['Ocorrencia']['codigo_prioridade'] == 2 ? '<span class="badge-empty badge badge-warning" title="Prioridade Média"></span>' : ($lista_ocorrencia['Ocorrencia']['codigo_prioridade'] == 3 ? '<span class="badge-empty badge badge-important" title="Prioridade Alta"></span>' : ''))) ; ?></td>
                <td title="<?php echo preg_replace('/^.*-\s+/', '', $lista_ocorrencia['Ocorrencia']['empresa']); ?>"><?php echo preg_replace('/^.*-\s+/', '', substr($lista_ocorrencia['Ocorrencia']['empresa'],0,20).'...'); ?></td>
                
                <td><?php echo substr($lista_ocorrencia['Ocorrencia']['data_ocorrencia'],0,16); ?></td>
                <td style="width: 62px"><?php echo $lista_ocorrencia['Ocorrencia']['placa']; ?></td>
                <td style='text-align:center'><?php echo $lista_ocorrencia['Ocorrencia']['tipos_ocorrencia']; ?></td>
                <td><?php echo $lista_ocorrencia['Ocorrencia']['rodovia']; ?></td> 
                <td><?php echo substr($lista_ocorrencia['StatusOcorrencia']['descricao'],0,10).'...'; ?></td>
                <td><?php echo $this->Html->link('', array('action' => 'adicionar_acao', $lista_ocorrencia['Ocorrencia']['codigo'], rand()), array('onclick' => 'return open_dialog(this, "Adicionar histórico", 960)', 'class' => 'icon-plus-sign', 'title' => 'Adicionar histórico')) ?></td>
                <td><?php echo $this->Html->link('', array('action' => 'visualiza_ocorrencia', $lista_ocorrencia['Ocorrencia']['codigo'], rand()), array('onclick' => 'return open_dialog(this, "Detalhe Ocorrência", 960)', 'class' => 'icon-eye-open', 'title' => 'Detalhar ocorrência')) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php
echo $this->Paginator->prev('« Anterior ', null, null, array('class' => 'disabled'));
echo $this->Paginator->numbers();
echo $this->Paginator->next(' Proximo » ', null, null, array('class' => 'disabled'));
echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%,mostrando %current% registros de um total de %count%'));
?>
<?= $javascript->codeBlock('
    jQuery(window).ready(function($) {
        $("tr", $(\'#listaOcorrencias\')).tooltip();
    });
') ?>
<?= $this->Js->writeBuffer(); ?>