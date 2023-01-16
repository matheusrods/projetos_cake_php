
<table id="listaOcorrencias">
    <thead>
        <tr>
            <th>SM</th>
            <th>Placa</th>
            <th>Tipo Ocorrencia</th>
            <th>Local</th>
            <th>Rodovia</th>
            <th>Origem</th>
            <th>Destino</th>
            <th>Status</th>
            <th>Ação</th>
        </tr>
    </thead>
    <tbody>
        <?php
         foreach ($lista_ocorrencias as $lista_ocorrencia) : ;?>

            <?php $tooltip  = '<b>Empresa:</b> ' . $lista_ocorrencia['Ocorrencia']['empresa'] . '<br/>';
                  $tooltip .= '<b>Telefone:</b> ' . $lista_ocorrencia['Ocorrencia']['telefone_empresa'] . '<br/>';
                  $tooltip .= '<b>Motorista:</b> ' . $lista_ocorrencia['Ocorrencia']['motorista'] . '<br/>';
                  $tooltip .= '<b>Telefone:</b> ' . $lista_ocorrencia['Ocorrencia']['telefone_motorista'] . '<br/>';
                  $tooltip .= '<b>Celular:</b> ' . $lista_ocorrencia['Ocorrencia']['celular_motorista'] . '<br/>';
                  $tooltip .= '<b>Tecnologia:</b> ' . $lista_ocorrencia['Equipamento']['Descricao'] . '<br/>';
                  $tooltip .= '<b>Observação:</b> ' . $lista_ocorrencia['Ocorrencia']['observacao'] . '<br/>';
                  $tooltip .= '<b>Data Ocorrência:</b> ' . $lista_ocorrencia['Ocorrencia']['data_ocorrencia'] . '<br/>';
                  $tooltip .= '<b>Incluído por:</b> ' . $lista_ocorrencia['Funcionario']['Apelido'] . '<br/>';
                  $tooltip .= '<b>Incluído em:</b> ' . $lista_ocorrencia['Ocorrencia']['data_inclusao'] . '<br/>';
                  $tooltip .= '<b>Alterado por:</b> ' . $lista_ocorrencia['FuncionarioAlteracao']['Apelido'] . '<br/>';
                  $tooltip .= '<b>Data Alteração:</b> ' . $lista_ocorrencia['Ocorrencia']['data_alteracao'] . '<br/>';
                  $tooltip .= '<b>Descrição outros:</b> ' . $lista_ocorrencia['Ocorrencia']['descricao_tipo_ocorrencia'] . '<br/>';
                  $tooltip = nl2br($tooltip);

            ?>
            <tr title="<?= $tooltip ?>" class='coluna-com-borda'>
                <td><?php echo $this->Buonny->codigo_sm($lista_ocorrencia['Ocorrencia']['codigo_sm']); ?></td>
                <td style="width: 62px"><?php echo $lista_ocorrencia['Ocorrencia']['placa']; ?></td>
                <td style='text-align:center'><?php echo $lista_ocorrencia['Ocorrencia']['tipos_ocorrencia']; ?></td>
                <td><?php echo $lista_ocorrencia['Ocorrencia']['local']; ?></td>
                <td><?php echo $lista_ocorrencia['Ocorrencia']['rodovia']; ?></td>
                <td><?php echo $lista_ocorrencia['Ocorrencia']['origem']; ?></td>
                <td><?php echo $lista_ocorrencia['Ocorrencia']['destino']; ?></td>
                <td><?php echo $tipoStatusSVizualizacao[$lista_ocorrencia['Ocorrencia']['codigo_status_ocorrencia']]; ?></td>
                <td>
                    <?php
                    $status = $lista_ocorrencia['Ocorrencia']['codigo_status_ocorrencia'];
                    if(($status == 2) || ($status == 5)){
                        echo "<strong>Finalizado</strong>";
                    }else{
                        echo $html->link('Atualizar Status', array('action' => 'atualizar', $lista_ocorrencia['Ocorrencia']['codigo']), array('onclick' => "return open_dialog(this)"));
                    }
                    ?>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php
echo $this->Paginator->prev('« Anterior ', null, null, array('class' => 'disabled'));
echo $this->Paginator->numbers();
echo $this->Paginator->next(' Proximo » ', null, null, array('class' => 'disabled'));
echo $this->Paginator->counter(array(
    'format' => 'Página %page% de %pages%,
mostrando %current% registros de um total de %count%'
));
?>
<?= $javascript->link('jquery-tooltip/jquery.tooltip.js') ?>
<?= $html->css('jquery.tooltip.css') ?>
<?= $javascript->codeBlock('jQuery(window).ready(function($) { $("tr" , $(\'#listaOcorrencias\')).tooltip() });') ?>