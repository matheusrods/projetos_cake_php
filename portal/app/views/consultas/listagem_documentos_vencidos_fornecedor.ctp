<?php if(isset($dados_lista) && count($dados_lista)) : ?>

    <div class='well'>
        <?php echo $this->Html->link('<i class="cus-page-white-excel"></i>', array( 'controller' => 'consultas', 'action' => $this->action, 'export'), array('escape' => false, 'title' =>'Exportar para Excel', 'style' => 'float:right'));?>
    </div>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Razão Social</th>            
                <th>Telefone</th>            
                <th>E-mail</th>            
                <th>Estado</th>
                <th>Cidade</th>
                <th>Situação</th>
                <th>Documento</th>            
                <th>Data de Validade</th>
            </tr>
        </thead>
        <tbody>
            <?php $total = 0 ?>
            <?php foreach($dados_lista as $key => $value) : ?>
                <?php $total += 1 ?>
            <tr>
                <td><?php echo $value['Fornecedor']['razao_social'];?></td>
                <td><?php echo Comum::formatarTelefone($value[0]['telefone']);?></td>
                <td><?php echo $value[0]['email'];?></td>
                <td><?php echo $value['FornecedorEndereco']['estado_descricao'];?></td>
                <td><?php echo $value['FornecedorEndereco']['cidade'];?></td>               
                <td><?php echo $value[0]['status'];?></td>
                <td><?php echo mb_convert_encoding($value[0]['Fornecedor__documento'], "HTML-ENTITIES", "UTF-8");?></td>
                <td><?php echo Comum::formataData($value[0]['Fornecedor__data_validade'],'ymd', 'dmy');?></td>
            </tr>
        <?php endforeach ?>
    </tbody>
        <tfoot>
            <tr>
                <td><?= $total ?></td>
                <td colspan = "15"></td>
            </tr>
        </tfoot>    
    </table>
<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?>