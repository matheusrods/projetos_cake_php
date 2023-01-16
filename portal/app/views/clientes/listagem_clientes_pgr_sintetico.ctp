<table class="table table-striped">
    <thead>
        <tr>
            <th><?=$agrupamento_selecionado?></th>
            <th class='numeric'>Sem apólice válida</th>
            <th class='numeric'>Com apólice válida</th> 
            <th class='numeric'>Sem regra de aceite</th>            
            <th class='numeric'>Com regra de aceite</th>            
        </tr>
    </thead>
    <tbody>        
        <?$qtde_com_pgr=0;?>
        <?$qtde_sem_pgr=0;?>
        <?$qtde_sem_regra=0;?>
        <?$qtde_com_regra=0;?>
        <?php
        foreach($clientes_pgr as $cliente):
            $qtde_com_pgr += $cliente[0]['com_pgr'];
            $qtde_sem_pgr += $cliente[0]['sem_pgr'];            
            $qtde_sem_regra += $cliente[0]['sem_regra'];            
            $qtde_com_regra += $cliente[0]['com_regra'];            
        ?>
        <tr>
            <td><?php echo $cliente[0]['nome'] ?></td>            
            <td class="input-medium numeric">
                <?php echo $this->Html->link( $this->Buonny->moeda($cliente[0]['sem_pgr'], array('nozero' => true, 'places' => 0)), "javascript:analitico_pgr( '{$cliente[0]['codigo']}', 2, 0 )"); ?>
            </td>
            <td class="input-medium numeric">
                <?php echo $this->Html->link( $this->Buonny->moeda($cliente[0]['com_pgr'], array('nozero' => true, 'places' => 0)) , "javascript:analitico_pgr( '{$cliente[0]['codigo']}', 1, 0 )"); ?>
            </td>
            <td class="input-medium numeric">
                <?php echo $this->Html->link( $this->Buonny->moeda($cliente[0]['sem_regra'], array('nozero' => true, 'places' => 0)) , "javascript:analitico_pgr( '{$cliente[0]['codigo']}', 0, 2 )"); ?>
            </td>
            <td class="input-medium numeric">
                <?php echo $this->Html->link( $this->Buonny->moeda($cliente[0]['com_regra'], array('nozero' => true, 'places' => 0)) , "javascript:analitico_pgr( '{$cliente[0]['codigo']}', 0, 1 )"); ?>
            </td>
        </tr>
        <?php endforeach; ?>        
    </tbody>
    <tfoot>
        <tr>
            <td><strong>Total</strong></td>
            <td class="numeric">
                <?php echo $this->Html->link( $this->Buonny->moeda($qtde_sem_pgr, array('nozero' => true, 'places' => 0)), "javascript:analitico_pgr( 0, 2, 0 )"); ?>
            </td>            
            <td class="numeric">
                <?php echo $this->Html->link( $this->Buonny->moeda($qtde_com_pgr, array('nozero' => true, 'places' => 0)), "javascript:analitico_pgr( 0, 1, 0 )"); ?>
            </td>
            <td class="numeric">
                <?php echo $this->Html->link( $this->Buonny->moeda($qtde_sem_regra, array('nozero' => true, 'places' => 0)), "javascript:analitico_pgr( 0, 0, 2 )"); ?>
            </td>
            <td class="numeric">
                <?php echo $this->Html->link( $this->Buonny->moeda($qtde_com_regra, array('nozero' => true, 'places' => 0)), "javascript:analitico_pgr( 0, 0, 1 )"); ?>
            </td>
        </tr>
    </tfoot>        
</table>
<?php echo $this->Javascript->codeBlock("
    function analitico_pgr(codigo_selecionado, vppj_validade_apolice, vppj_verificar_regra ) {   
        var agrupamento = {$this->data['ClientesPGR']['agrupamento']};
        var form = document.createElement('form');
        var form_id = ('formresult' + Math.random()).replace('.','');
        
        form.setAttribute('method', 'post');
        form.setAttribute('action', '/portal/clientes/analitico_pgr/1');
        form.setAttribute('target', form_id);
        field = document.createElement('input');
        
        field.setAttribute('name', 'data[ClientesPGR][codigo_corretora]');
        field.setAttribute('value', agrupamento == 1 ? codigo_selecionado : '{$this->data['ClientesPGR']['codigo_corretora']}');
        field.setAttribute('type', 'hidden');
        form.appendChild(field);
        
        field = document.createElement('input');
        field.setAttribute('name', 'data[ClientesPGR][codigo_seguradora]');
        field.setAttribute('value', agrupamento == 2 ? codigo_selecionado : '{$this->data['ClientesPGR']['codigo_seguradora']}');
        field.setAttribute('type', 'hidden');
        form.appendChild(field);
        
        field = document.createElement('input');
        field.setAttribute('name', 'data[ClientesPGR][codigo_gestor]');
        field.setAttribute('value', agrupamento == 3 ? codigo_selecionado : '{$this->data['ClientesPGR']['codigo_gestor']}');
        field.setAttribute('type', 'hidden');
        form.appendChild(field);
        
        field = document.createElement('input');
        field.setAttribute('name', 'data[ClientesPGR][codigo_endereco_regiao]');
        field.setAttribute('value', agrupamento == 4 ? codigo_selecionado : '{$this->data['ClientesPGR']['codigo_endereco_regiao']}');
        field.setAttribute('type', 'hidden');
        form.appendChild(field);

        field = document.createElement('input');
        field.setAttribute('name', 'data[ClientesPGR][codigo_gestor_npe]');
        field.setAttribute('value', agrupamento == 5 ? codigo_selecionado : '{$this->data['ClientesPGR']['codigo_gestor_npe']}');
        field.setAttribute('type', 'hidden');
        form.appendChild(field);
        
        field = document.createElement('input');
        field.setAttribute('name', 'data[ClientesPGR][vppj_validade_apolice]');
        field.setAttribute('value', vppj_validade_apolice);
        field.setAttribute('type', 'hidden');
        form.appendChild(field);

        field = document.createElement('input');
        field.setAttribute('name', 'data[ClientesPGR][vppj_verificar_regra]');
        field.setAttribute('value', vppj_verificar_regra);
        field.setAttribute('type', 'hidden');
        form.appendChild(field);
        
        document.body.appendChild(form);
        var janela = window_sizes();
        window.open('', form_id, 'scrollbars=yes,menubar=no,height='+(janela.height-100)+',width='+(janela.width-80).toString()+',resizable=yes,toolbar=no,status=no');
        form.submit();
    }
");?>