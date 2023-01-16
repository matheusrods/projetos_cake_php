<?php if($conditionsSm == 'WHERE 1 = 0'): ?>
<?php else: ?>
    <div class='row-fluid inline'>
        <h4>SMs</h4>
	    <div class='row-fluid' style='overflow-x:auto'>
        <table class='table table-striped horizontal-scroll' style = 'width:500' > 
            <thead >
                <tr>
                    <th class='input-medium'>Tipo Transporte</th>
                    <th class='numeric' class='input-medium'>Quantidade</th>
                  </tr>

            </thead>
            <tbody >
                <?php foreach($dadosSm as $dado): ?>
                    <tr>
                        <td><?= $dado['TTtraTipoTransporte']['ttra_descricao'] ?></td>
                        <td class='numeric'><?= ((isset($dado[0]['qtde']) && $dado[0]['qtde'] > 0) ? $this->Html->link($dado[0]['qtde'], 'javascript:void(0)', array( 'onclick' => "consulta_sm_por_tipo_transporte('{$dado['TTtraTipoTransporte']['ttra_codigo']}','{$filtros['data_inicial_load']}','{$filtros['data_final_load']}','{$filtros['codigo_cliente']}')")) : 0) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    </div>	
    <?php echo $this->Js->writeBuffer(); ?>
    <?php echo $this->Buonny->link_js('estatisticas') ?>
<?php endif; ?>
	