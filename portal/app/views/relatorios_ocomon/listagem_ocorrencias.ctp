<?php



?>
    <div class="row-fluid inline">
        <?= $this->element('relatorios_ocomon/grafico_evolucao') ?>
 </div>
     <div class="row-fluid inline">
        <?= $this->element('relatorios_ocomon/grafico_por_tipos_dia') ?>
 </div>
    <div class="row-fluid inline">
        <?= $this->element('relatorios_ocomon/grafico_por_tipos') ?>
 </div>


<table class="table table-striped">
    <thead>
        <tr>
            <th>Número</th>
            <th>Tipo</th>
            <th>Analista</th>
            <th>Descrição</th>
            <th>Fechado em</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($ocorrencias as $ocorrencia): ?>
        <tr>
            <td>
                <? echo "<a href='http://ocomon.buonny.com.br/ocomon/geral/mostra_consulta.php?numero=".$ocorrencia['OOcorrencias']['numero']."' target=new>".$ocorrencia['OOcorrencias']['numero']."</a>"; ?>
            </td>
            <td>
                <?= $ocorrencia['OProblemas']['problema'] ?>
            </td>
            <td>
                <?= $ocorrencia['OUsuarios']['nome'] ?>
            </td>
            <td>
                <?= $ocorrencia['OOcorrencias']['descricao'] ?>
            </td>
            <td>
                <?= $ocorrencia['OOcorrencias']['data_fechamento'] ?>
            </td>

        </tr>
        <?php endforeach; ?>        
    </tbody>

    <tfoot>
        <?php if( isset($ocorrencias) ): ?>
            <tr>
                <td colspan='5'><strong>Total: </strong><?php echo count($ocorrencias); ?></td>
            </tr>
        <?php  endif;?>
    </tfoot>



</table>
<div class='row-fluid'>

</div>
