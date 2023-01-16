<?php if($dados): ?>  
    <table class="table table-striped" >
        <thead>
            <tr>
                <th>Data Inclusão</th>
                <th>Usuário Inclusão</th>
                <th>Data Alteração</th>
                <th>Usuário Alteração</th>
                <th>Caminho do Arquivo</th>
                <th>Status</th>
                <th>Ação</th>
            </tr>
        </thead>
        <?php foreach($dados as $key => $value): ?>
            <tbody>
                <tr>
                    <td><?= $value['AnexoExameLog']['data_inclusao'] ?></td>
                    <td><?= $value['AnexoExameLog']['nome_usuario_inclusao'] ?></td>
                    <td><?= $value['AnexoExameLog']['data_alteracao'] ?></td>
                    <td><?= $value['AnexoExameLog']['nome_usuario_alteracao'] ?></td>
                    <td><?= $value['AnexoExameLog']['caminho_arquivo'] ?></td>
                    <td><?= $value['AnexoExameLog']['status'] ?></td>
                    <td><?= $value['AnexoExameLog']['acao_sistema'] ?></td>
                </tr>
            </tbody>    
        <?php endforeach;?>
    </table>
    
<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?>