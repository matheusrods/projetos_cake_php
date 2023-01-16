<?php



?>


    <div class="row-fluid inline">
        <?= $this->element('relatorios_redmine/grafico') ?>
        <?= $this->element('relatorios_redmine/pizzaclientes') ?>
 </div>
    <div class="row-fluid inline">
        <?= $this->element('relatorios_redmine/graficohorasestimadasrealizadas') ?>
    </div>
    <div class="row-fluid inline">
        <?= $this->element('relatorios_redmine/graficototalanalistastatus') ?>
    </div>


<table class="table table-striped">
    <thead>
        <tr>
            <th>Código</th>
            <th>Analista</th>
            <th>Issue</th>
            <th>Entregue em</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($issues as $issue): ?>
        <tr>
            <td>
                <? echo "<a href='http://projetos.buonny.com.br/issues/".$issue['RIssues']['id']."' target=new>".$issue['RIssues']['id']."</a>"; ?>
            </td>
            <td>
                <?= $issue['RUsers']['firstname'] ?>
            </td>
            <td>
                <?= $issue['RIssues']['subject'] ?>
            </td>
            <td>
                <?= $issue['RIssues']['closed_on'] ?>
            </td>

        </tr>
        <?php endforeach; ?>        
    </tbody>

    <tfoot>
        <?php if( isset($issues) ): ?>
            <tr>
                <td colspan='4'><strong>Total: </strong><?php echo count($issues); ?></td>
            </tr>
        <?php  endif;?>
    </tfoot>



</table>
<div class='row-fluid'>

</div>
