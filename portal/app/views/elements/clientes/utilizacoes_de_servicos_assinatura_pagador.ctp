<div class='row-fluid'>
<?php foreach ($utilizacoes_assinatura as $produto => $assinaturas){ ?>
<div class='row-fluid'><h5><?=$produto;?></h5>
    <table class="table table-striped table-bordered">
        <thead>
            <tr> 
                <th class="input-mini">Código</th>
                <th class="input-xlarge">Razão Social</th>                
                <th class="input-medium numeric">Vr.à Pagar</th>
                <th class="input-medium numeric">Desconto</th>
                <th class="input-medium numeric">Valor</th>
            </tr>
        </thead>
        <tbody>
            <?php             
            $total_quandidade = 0;
            $total_valor = 0;
            $total = 0;
            $total_desconto = 0;
            $count = 0;
            foreach($assinaturas as $cliente => $dados){                 
                $total            +=  $dados['total'];
                $total_valor      +=  $dados['valor'];
                $total_desconto   +=  $dados['desconto'];
                $count++;
                ?>
            <tr>
              <td><?= 
$this->Html->link($cliente, "javascript:utilizacao_de_servicos_filhos_pagador('{$cliente}', '{$this->data['Cliente']['data_inicial']}', '{$this->data['Cliente']['data_final']}', '{$dados['codigo_produto']}')")
               ?></td>
              <td><?=$dados['nome'] ?></td>              
              <td class="numeric"><?= $dados['total'] > 0 ? number_format($dados['total'],2,',','.') : '' ?></td>
              <td class="numeric"><?= $dados['desconto'] > 0 ? number_format($dados['desconto'],2,',','.') : '' ?></td>
              <td class="numeric"><?= $dados['valor'] > 0 ? number_format($dados['valor'],2,',','.') : '' ?></td>
            </tr>
            <?php if(!empty($dados['detalhes']) && isset($this->data['Cliente']['detalhar_servicos'])) { ?>
            <tr>
                <td colspan="5" style="padding-left: 9%">
                    <div class="pull-left margin-bottom-10" style="width: 40%">
                        <label class="pull-left"><strong>Serviço</strong></label>
                    </div>
                    <div class="pull-left margin-bottom-10 text-center" style="width: 10%">
                        <label><strong>Quantidade</strong></label>
                    </div>
                    <div class="pull-left margin-bottom-10 text-center" style="width: 10%">
                        <label><strong>Vr. unitário</strong></label>
                    </div>
                    <div class="pull-left margin-bottom-10 text-center" style="width: 10%">
                        <label><strong>Vr. total</strong></label>
                    </div>
                    <div class="clear"></div>
                </td>
            </tr>        
                <?php } // end if ?>
            <?php } // end foreach?>
        </tbody>        
        <tfoot>
            <tr>
                <td class="numeric"><b><?=$count?></b></td>
                <td class="numeric"><b>TOTAL</b></td>                
                <td class="numeric"><b><?= $total > 0 ? number_format($total, 2,',','.') : '' ?></td>
                <td class="numeric"><b><?= $total_desconto > 0 ? number_format($total_desconto, 2,',','.') : '' ?></td>
                <td class="numeric"><b><?= $total_valor > 0 ? number_format($total_valor, 2,',','.') : '' ?></td>
            </tr>
        </tfoot>
        
    </table>
    <?php  } ?>
</div>
</div>
 <?php echo $this->Javascript->codeBlock("
    function utilizacao_de_servicos_filhos_pagador( codigo_cliente, data_inicial, data_final, codigo_produto ) {     
        var form = document.createElement('form');
        var form_id = ('formresult' + Math.random()).replace('.','');
        form.setAttribute('method', 'post');
        form.setAttribute('action', '/portal/clientes/utilizacao_de_servicos_filhos_pagador/1');
        form.setAttribute('target', form_id);
        field = document.createElement('input');
        field.setAttribute('name', 'data[Cliente][codigo_cliente]');
        field.setAttribute('value', codigo_cliente);
        field.setAttribute('type', 'hidden');
        form.appendChild(field);
        field = document.createElement('input');
        field.setAttribute('name', 'data[Cliente][codigo_produto]');
        field.setAttribute('value', codigo_produto);
        field.setAttribute('type', 'hidden');
        form.appendChild(field);
        field = document.createElement('input');
        field.setAttribute('name', 'data[Cliente][data_inicial]');
        field.setAttribute('value', data_inicial);
        field.setAttribute('type', 'hidden');
        form.appendChild(field);
        field = document.createElement('input');
        field.setAttribute('name', 'data[Cliente][data_final]');
        field.setAttribute('value', data_final);
        field.setAttribute('type', 'hidden');
        form.appendChild(field);
        document.body.appendChild(form);
        var janela = window_sizes();
        window.open('', form_id, 'scrollbars=yes,menubar=no,height='+(janela.height-100)+',width='+(janela.width-80).toString()+',resizable=yes,toolbar=no,status=no');
        form.submit();
    }
"); ?>