
<?php if(!empty($dados)):?>
    <?php 
        echo $paginator->options(array('update' => 'div.lista')); 
        $total_paginas = $this->Paginator->numbers();
    ?>
    <table class="table table-striped" style="width: 1500px;" >
        <thead>
            <tr>
                <th>Código Credenciado</th>
                <th>Nome Fantasia</th>
                <th>CNPJ</th>
                <th>Número Nfs</th>
                <th>Data Emissão</th>
                <th>Data Vencimento</th>
                <th>Data Recebimento</th>
                <th>Data Pagamento</th>
                <th>Valor</th>
                <th>Status</th>
                <th>Data Pagto Real.</th>
                <th>Valor Pago</th>
                <th>Ação</th>
            </tr>
        </thead>
        <tbody>

            <?php foreach ($dados as $dado): ?>
                <tr>
                    <td><?php echo $dado[0]['codigo_credenciado'] ?></td>
                    <td><?php echo $dado[0]['nome_credenciado'] ?></td>
                    <td><?php echo $buonny->documento($dado[0]['cnpj_credenciado']) ?></td>
                    <td><?php echo $dado[0]['numero_nfs'] ?></td>
                    <td><?php echo AppModel::dbDateToDate($dado[0]['data_emissao_nfs']) ?></td>
                    <td><?php echo AppModel::dbDateToDate($dado[0]['data_vencimento_nfs']) ?></td>
                    <td><?php echo AppModel::dbDateToDate($dado[0]['data_recebimento_nfs']) ?></td>
                    <td><?php echo AppModel::dbDateToDate($dado[0]['data_pagamento_nfs']) ?></td>
                    <td><?php echo $dado[0]['valor_nfs'] ?></td>
                    <td><?php echo (!empty($dado[0]['data_pago_tranpag']) ? "Pago" : "Não Pago"); ?></td>
                    <td><?php echo AppModel::dbDateToDate($dado[0]['data_pago_tranpag']) ?></td>
                    <td><?php echo $dado[0]['valor_tranpag'] ?></td>
                    <td>
                        <a href="javascript:void(0);" onclick="detalhes_exames('<?php echo $dado[0]['codigo_nfs']; ?>');"><i class="icon-eye-open" title="Detalhes Exames"></i></a>
                    </td>

                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <br />

    <?php echo $this->Js->writeBuffer(); ?>
    <?php echo $this->Javascript->codeBlock('
        jQuery(document).ready(function() {
            setup_mascaras(); setup_time(); setup_datepicker();

            detalhes_exames = function(codigo_nfs){

                var janela = window_sizes();
                window.open(baseUrl + "notas_fiscais_servico/listagem_demonstrativo_contas_medicas/" + codigo_nfs + "/" + Math.random(), janela, "scrollbars=yes,menubar=no,height="+(janela.height-200)+",width="+(janela.width-80)+",resizable=yes,toolbar=no,status=no");

            }//fim detalhes_exames

        });
    '); ?>  
<?php else:?>    
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?> 

