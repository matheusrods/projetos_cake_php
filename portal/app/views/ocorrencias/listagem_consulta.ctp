<?php 
    echo $paginator->options(array('update' => 'div.lista')); 
    $total_paginas = $this->Paginator->numbers();
?>
<table id="listaOcorrencias" class="table table-striped">
    <thead>
        <tr>
            <th style='width:41px'><?= $this->Paginator->sort('SM', 'codigo_sm') ?></th>
            <th style='width:120px'><?= $this->Paginator->sort('Data', 'data_ocorrencia') ?></th>
            <th style='width:95px'><?= $this->Paginator->sort('Operação', 'operacao') ?></th>
            <th style='text-align:center'><?= $this->Paginator->sort('Tipo Ocorrência', 'tipos_ocorrencia') ?></th>
            <th style='width:170px'><?= $this->Paginator->sort('Empresa', 'empresa') ?></th> 
            <th style='width:170px'><?= $this->Paginator->sort('Status', 'codigo_status_ocorrencia') ?></th>
            <th style="width:14px"></th>
        </tr>
    </thead>
    <tbody>
        <?php
         $classe = 'cor-sim';
         foreach ($ocorrencias as $lista_ocorrencia) : ;?>
            <tr>
                <td><?php echo $lista_ocorrencia['Ocorrencia']['codigo_sm']; ?></td>
                <td><?php echo $lista_ocorrencia['Ocorrencia']['data_ocorrencia']; ?></td>
                <td><?php echo (isset($operacoes[$lista_ocorrencia['ClientEmpresa']['tipo_operacao']]) ? $operacoes[$lista_ocorrencia['ClientEmpresa']['tipo_operacao']] : ''); ?></td>
                <td><?php echo $lista_ocorrencia['Ocorrencia']['tipos_ocorrencia']; ?></td>
                <td><?php echo substr($lista_ocorrencia['Ocorrencia']['empresa'],0,20); ?></td> 
                <td><?php echo substr($lista_ocorrencia['StatusOcorrencia']['descricao'],0,20).'...'; ?></td>
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
<?= $javascript->codeBlock('jQuery(window).ready(function($) { $("tr" , $(\'#listaOcorrencias\')).tooltip() });') ?>
<?= $this->Js->writeBuffer(); ?>