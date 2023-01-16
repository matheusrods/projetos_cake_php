<?php if(!empty($listagem)):?>
<table class="table table-striped">
    <thead>
        <tr>
            <th class="input-mini">Código do Credenciado Faturamento</th>
            <th>Nome Credenciado Faturamento</th>
            <th>Código Cliente (exames)</th>
            <th>Cliente (exames)</th>
            <th>Setor</th>
            <th>Cargo</th>
            <th>Funcionário</th>
            <th>Matrícula</th>
            <th>Código Pedido Exame</th>
            <th>Data Pedido de Exame</th>
            <th>Descrição Exame</th>
            <th>Data Realização</th>
            <th>Data Baixa</th>
            <th>Imagem Anexada</th>
            <th>Status da Auditoria</th>
            <th>Motivo Bloqueio</th>
            <th>Valor</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($listagem as $linha): ?>
        <tr>
            <td><?php debug($linha); ?></td>
        </tr>
        <?php endforeach; ?>        
    </tbody>
</table>

<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?> 


