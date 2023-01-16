<style type="text/css">
.badge {
    display: inline-block;
    min-width: 0px;
    padding: 3px 7px;
    font-size: 12px;
    font-weight: 700;
    line-height: 1;
    color: #fff;
    text-align: center;
    white-space: nowrap;
    vertical-align: middle;
    background-color: #777;
    border-radius: 10px;
}

.badge-success {
    color: #fff;
    background-color: #28a745;
}

.badge-danger {
    color: #fff;
    background-color: #dc3545;
}

.badge-info {
    color: #fff;
    background-color: #17a2b8;
}


</style>


<?php if(!empty($dados)):?>
    <?php 
        echo $paginator->options(array('update' => 'div.lista')); 
        $total_paginas = $this->Paginator->numbers();
    ?>
    <div class='well'>
        <?php echo $this->Html->link('<i class="cus-page-white-excel"></i>', array( 'controller' => $this->name, 'action' => $this->action, 'export'), array('escape' => false, 'title' =>'Exportar para Excel', 'style' => 'float:right'));?>
    </div>
    <table class="table table-striped" style='width:2500px;max-width:none;' >
        <thead>
            <tr>
                <th>Código Credenciado</th>
                <th>Nome Fantasia</th>
                <th>Código Cliente</th>
                <th>Cliente</th>
                <th>Setor</th>
                <th>Cargo</th>
                <th>Funcionário</th>
                <th>Matrícula</th>
                <th>Pedido de Exame</th>
                <th>Data Pedido Exame</th>
                <th>Exame</th>
                <th>Data Realização</th>
                <th>Data Baixa</th>
                <th>Anexo Exame</th>
                <th>Anexo Ficha Clinica</th>
                <th>Status</th>
                <th>Motivo</th>                
                <th>Valor</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $total = 0;
            ?>
            <?php foreach ($dados as $dado): ?>
                <tr>
                    <td><?php echo $dado[0]['codigo_fornecedor'] ?></td>
                    <td><?php echo $dado[0]['fornecedor_nome'] ?></td>
                    <td><?php echo $dado[0]['codigo_cliente'] ?></td>
                    <td><?php echo $dado[0]['nome_cliente'] ?></td>
                    <td><?php echo $dado[0]['setor_descricao'] ?></td>
                    <td><?php echo $dado[0]['cargo_descricao'] ?></td>
                    <td><?php echo $dado[0]['nome_funcionario'] ?></td>
                    <td><?php echo $dado[0]['matricula'] ?></td>
                    <td><?php echo $dado[0]['codigo_pedido_exame'] ?></td>
                    <td><?php echo $dado[0]['data_pedido_exame'] ?></td>
                    <td><?php echo $dado[0]['exame'] ?></td>
                    <td><?php echo AppModel::dbDateToDate($dado[0]['data_realizacao']) ?></td>
                    <td><?php echo $dado[0]['data_baixa'] ?></td>
                    <td>
                        <?php if($dado[0]['codigo_anexo_exame'] != ''):  ?>
                            <?php 
                            $caminho_arquivo = '/files/anexos_exames/'.$dado[0]['caminho_arquivo_exame'];
                            //quando tiver no fileserver
                            if(strstr($dado[0]['caminho_arquivo_exame'],'https://api.rhhealth.com.br')) {
                                $caminho_arquivo = $dado[0]['caminho_arquivo_exame'];
                            }
                            ?>
                            <a href="<?php echo $caminho_arquivo; ?>" target="_blank" title="Visualizar anexo do item">Visualizar</a>
                        <?php endif;  ?>
                    </td>
                    <td>
                        <?php if($dado[0]['codigo_anexo_ficha_clinica'] != ''):  ?>
                            <a href="/files/anexos_exames/<?php echo $dado[0]['caminho_arquivo_ficha_clinica']; ?>" target="_blank" title="Visualizar anexo da Ficha Clinica">Visualizar</a>
                        <?php endif;  ?>
                    </td>
                    <td >
                        <?php
                        $badge_color = "badge-info";
                        if($dado[0]['codigo_status_auditoria'] == 2) {
                            $badge_color = "badge-danger";
                        }
                        elseif($dado[0]['codigo_status_auditoria'] == 3) {
                            $badge_color = "badge-success";
                        }
                        ?>
                        <div class="badge <?php echo $badge_color; ?>">&nbsp;</div>
                        <span style="font-size: 11px;" >
                            <?php echo $dado[0]['status'] ?>
                        </span>
                                
                    </td>
                    <td>
                        <?php 
                        if($dado[0]['codigo_status_auditoria'] == 2){
                            echo $dado[0]['motivo']; 
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        if($dado[0]['codigo_status_auditoria'] == 2) {                            
                            echo "<span style='color:#848282'>".$dado[0]['valor']."</span>";
                        }
                        elseif($dado[0]['codigo_status_auditoria'] == 3) {
                            $total += $dado[0]['valor'];
                            echo "<span style='color:#28a745'>".$dado[0]['valor']."</span>";
                        }
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="17">Total:</td>
                <td >
                    <?php echo "R$ " . $total; ?>
                </td>
            </tr>
        </tbody>
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

    <?php echo $this->Js->writeBuffer(); ?>
    <?php echo $this->Javascript->codeBlock('
        jQuery(document).ready(function() {
            setup_mascaras(); setup_time(); setup_datepicker();
        });
    '); ?>  

<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?> 

