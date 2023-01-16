<?php if(!empty($funcionarios)):?>
    <?php echo $paginator->options(array('update' => 'div.lista')); ?>

        <table class='table table-striped tablesorter'>
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Nome</th>
                    <th>Nascimento</th>
                    <th>RG</th>
                    <th>CPF</th>
                    <th>Sexo</th>
                    <th>Data de Admissão</th>
                    <th>Setor</th>
                    <th>Cargo</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($funcionarios as $funcionario): ?>
                <tr>
                    <td><?php echo $funcionario['Funcionario']['codigo'] ?></td>
                    <td><?php echo $funcionario['Funcionario']['nome'] ?></td>
                    <td><?php echo $funcionario['Funcionario']['data_nascimento'] ?></td>
                    <td><?php echo $funcionario['Funcionario']['rg']." - ".$funcionario['Funcionario']['rg_orgao'] ?></td>
                    <td><?php echo Comum::formatarDocumento($funcionario['Funcionario']['cpf']); ?></td>
                    <td><?php echo ($funcionario['Funcionario']['sexo'] == 'M') ? 'Masculino' : 'Feminino'; ?></td>
                    <td><?php echo $funcionario['ClienteFuncionario']['admissao'] ?></td>
                    <td><?php echo $funcionario['Setor']['descricao'] ?></td>
                    <td><?php echo $funcionario['Cargo']['descricao'] ?></td>
                    <td>
                    	<?php echo $html->link('', array('action' => 'imprimir_laudo_pcd', $funcionario['ClienteFuncionario']['codigo']), array('class' => 'icon-print', 'data-toggle' => 'tooltip', 'title' => 'Laudo Caracterizador de Deficiência')) ?>
                    </td>
                </tr>
                <?php endforeach; ?> 
            </tbody>
            <tfoot>
                <tr>
                    <td colspan = "10"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['Funcionario']['count']; ?></td>
                </tr>
            </tfoot>
        </table>
    <div class='row-fluid'>
        <div class='numbers span6'>
            <?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
            <?php echo $this->Paginator->numbers(); ?>
            <?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
        </div>
        <div class='counter span6'>
            <?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
            
        </div>
    </div>   
<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?>

<?php echo $html->link('Voltar', array('controller' => 'clientes', 'action' => 'laudo_pcd'), array('class' => 'btn')); ?>
<?php echo $this->Js->writeBuffer(); ?>

<?php 
    echo $this->Javascript->codeBlock("
        $(document).ready(function() {
            $('[data-toggle=\"tooltip\"]').tooltip();
        });
");
?>