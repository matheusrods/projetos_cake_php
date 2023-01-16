<?php if(!empty($prestadores)):?>
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th class="input-xxlarge">Descrição</th>
                <th class="input-mini numeric">Quantidade</th>
            </tr>
        </thead>
        <tbody>
            <?php $total = 0;?>
            <?php foreach ($prestadores as $prestador): ?>
            <tr>
                <?php $total += $prestador[0]['qtd']; ?>
                <?php $codigo_selecionado = !empty($prestador[0]['codigo']) ? $prestador[0]['codigo'] : '-1';?>
                <td><?php echo $prestador[0]['descricao']; ?></td>
                <td class='numeric input-small'><?= $this->Html->link($this->Buonny->moeda($prestador[0]['qtd'], array('nozero' => true, 'places' => 0)), "javascript:analitico('{$codigo_selecionado}')") ?>
            </tr>
        <?php endforeach ?>
    </tbody>
        <tfoot>
            <tr>
                <td colspan = "1"><strong>Total</strong></td>
                <td class='numeric' colspan = "1"><?= $this->Html->link($total, "javascript:analitico('')") ?></td>
            </tr>
        </tfoot>    
    </table>
    <?php echo $this->Javascript->codeBlock("
        function analitico(codigo_selecionado) {
            var agrupamento = {$agrupamento}; 
        
            var form = document.createElement('form');
            var form_id = ('formresult' + Math.random()).replace('.','');
            form.setAttribute('method', 'post');
            form.setAttribute('target', form_id);
            form.setAttribute('action', '/portal/prestadores/analitico/1/' + Math.random());
           
            if(agrupamento == 1){
                field = document.createElement('input');
                field.setAttribute('name', 'data[PrestadoresPostgres][codigo_transportador]');     
                field.setAttribute('value', (codigo_selecionado != '') ? codigo_selecionado  : '{$this->data['PrestadoresPostgres']['codigo_transportador']}');
                field.setAttribute('type', 'hidden');
                form.appendChild(field);
            }
            if(agrupamento == 2){
                field = document.createElement('input');
                field.setAttribute('name', 'data[PrestadoresPostgres][codigo_embarcador]');     
                field.setAttribute('value', (codigo_selecionado != '') ? codigo_selecionado  : '{$this->data['PrestadoresPostgres']['codigo_embarcador']}');
                field.setAttribute('type', 'hidden');
                form.appendChild(field);
            }
            if(agrupamento == 3){
                field = document.createElement('input');
                field.setAttribute('name', 'data[PrestadoresPostgres][codigo_tecnologia]');     
                field.setAttribute('value', (codigo_selecionado != '') ? codigo_selecionado  : '{$this->data['PrestadoresPostgres']['codigo_tecnologia']}');
                field.setAttribute('type', 'hidden');
                form.appendChild(field);
            }
            if(agrupamento == 4){
                field = document.createElement('input');
                field.setAttribute('name', 'data[PrestadoresPostgres][codigo_prestador]');     
                field.setAttribute('value', (codigo_selecionado != '') ? codigo_selecionado : '{$this->data['PrestadoresPostgres']['codigo_prestador']}');
                field.setAttribute('type', 'hidden');
                form.appendChild(field);
            }
                   
            var janela = window_sizes();
            window.open('', form_id, 'scrollbars=yes,menubar=no,height='+(janela.height-200)+',width='+(janela.width-80)+',resizable=yes,toolbar=no,status=no');
            document.body.appendChild(form);
            form.submit();

        }"
    );?>
<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?>
