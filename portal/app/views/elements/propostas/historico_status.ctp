<? if (!empty($historico_status)): ?>
<table class="table table-striped">
    <thead>
        <tr>
            <!--<th width="13">&nbsp;</th>-->
            <th class="input-large">Dt. Alteração</th>
            <th class="input-medium">Status</th>
            <th class="input-large">Responsável</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($historico_status as $seq=> $alteracao_status): ?>
        <tr>
            <!--<td class="numeric"><?=($seq+1)?></td>-->
            <td><?= $alteracao_status['PropostaLogStatus']['data_inclusao'] ?></td>
            <td><?= $alteracao_status['StatusProposta']['descricao'] ?></td>
            <td>
                <?php if (!empty($alteracao_status['PropostaLogStatus']['codigo_usuario_inclusao'])): ?>
                <?=$alteracao_status['Usuario']['nome'] ?>
                <?php else: ?>
                <?=$alteracao_status['PropostaLogStatus']['email_usuario_inclusao'] ?>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>        
    </tbody>
</table>

<?php echo $this->Js->writeBuffer(); ?>
<?php echo $this->Javascript->codeBlock('

    jQuery(document).ready(function(){
        //
    });', false); 
?>    
<? endif; ?>