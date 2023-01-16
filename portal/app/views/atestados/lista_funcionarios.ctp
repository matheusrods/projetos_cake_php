<?php if(isset($listagem) && count($listagem)) : ?>
    <div class="row-fluid inline">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th style="width:25%">Unidade</th>
                    <th style="width:20%">Setor</th>
                    <th style="width:20%">Cargo</th>
                    <th style="width:30%">Funcionário</th>
                    <th style="width:30%">Status</th>
                    <th style="width:25%">Ação</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($listagem as $key => $linha): ?>
                    <tr>
                        <td class="input-mini"><?= $linha['Cliente']['nome_fantasia']; ?></td>
                        <td><?php echo $linha['Setor']['descricao']; ?></td>
                        <td><?php echo $linha['Cargo']['descricao']; ?></td>
                        <td><?php echo $linha['Funcionario']['nome']; ?></td>
                        <td class="right">
                        	<?php if($linha['ClienteFuncionario']['ativo'] == '1') : ?>
                        		<span class="badge-empty badge badge-success" title="Ativo"></span>
                        	<?php else : ?>
	                        	<span class="badge-empty badge badge-error" title="Inativo"></span>
                        	<?php endif; ?>
                        </td>
                        <td>
                    		<?php echo $html->link('', array('controller' => 'atestados', 'action' => 'lista_atestados', $linha['ClienteFuncionario']['codigo'], $linha['FuncionarioSetorCargo']['codigo']), array('class' => 'icon-plus-sign', 'title' => 'Atestados/Afastamentos do Funcionário')); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>        
            </tbody>
        </table>
    </div>
<?php else: ?>
	<div class="alert">Nenhum resultado encontrado.</div>
<?php endif; ?>