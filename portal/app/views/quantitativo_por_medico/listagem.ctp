<?php if(isset($listagem) && count($listagem)) : ?>
    <div class="row-fluid inline">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th style="width:35%;">Medico</th>
                    <th style="width:15%;">Conselho Profissional</th>
                    <th style="width:15%; text-align: right;">Número do Conselho</th>
                    <th style="width:15%;">UF Conselho</th>
                    <th style="width:20%; text-align: right;" >Quantidade de Atestados</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($listagem as $key => $linha): ?>
                    <tr>
                        <td><?= $linha['Medico']['nome']; ?></td>
                        <td><?php echo $linha['ConselhoProfissional']['descricao']; ?></td>
                        <td style="text-align: right;"><?php echo $linha['Medico']['numero_conselho']; ?></td>
                        <td><?php echo $linha['Medico']['conselho_uf']; ?></td>
                        <td style="text-align: right;">
                        	<?php echo $linha[0]['qtd']; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
	<div class="alert">Nenhum resultado encontrado.</div>
<?php endif; ?>