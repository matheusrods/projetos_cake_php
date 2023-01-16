<?php if(!empty($questoes_respondidas)):?>
    <table class="table table-striped">
        <thead>
            <tr>
               <th class="input-medium">Pergunta</th>
               <th class="input-medium">Resposta</th>
           </tr>
       </thead>
       <tbody>
        <?php foreach ($questoes_respondidas as $dados) { ?>
            <tr>
                <td class="input-mini"><?php echo $dados['Resposta']['label_questao'] ?></td>
                <td class="input-mini"><?php echo $dados['Resposta']['label'] ?></td>
            </tr>
        <?php } ?>
    </tbody>
</table>
<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?>    