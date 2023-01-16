<div class="form-procurar no-print">
    <?php
    echo $this->BForm->create('Ficha', array(
        'id' => 'FormSla',
        'url' => array('controller' => 'fichas', 'action' => 'relatorio_sla')
            )
    );
    ?>
    <div class="formulario">
        <h1>Relatório SLA</h1>
        <div class='acao'>
            <?php echo $this->BForm->input('codigo_cliente', array('type' => 'text', 'label' => 'Código', 'class' => 'text-small')); ?>
            <?php echo $this->element('generico/combo_anomes'); ?>
            <?php echo $this->BForm->input('tipo_operacao', array('label' => 'Operação', 'options' => $operacoes, 'empty' => 'Selecione um tipo de operação')); ?>
            <?php echo $this->BForm->input('tipo_relacionamento', array('label' => 'Relacionamento', 'class' => 'text-small2', 'options' => $tipos_relacionamento, 'empty' => 'INDIVIDUAL')); ?>
        </div>
        <div class="fullwide" style="margin-top:10px">
            <div class="titulo-grupo">
                <label>Categorias</label>
                <?php echo $html->link('Desmarcar todas', 'javascript:void(0)', array('class' => 'desmarcar_todos')) ?>
                <?php echo $html->link('Marcar todas', 'javascript:void(0)', array('class' => 'marcar_todos')) ?>
            </div>
            <div id='categorias'>
                <?php echo $this->BForm->input('codigo_tipo_profissional', array('label' => false, 'options' => $tipos_profissional, 'class' => 'checkbox-horizontal', 'multiple' => 'checkbox')); ?>
            </div>
        </div>
    </div>
    <?php echo $this->BForm->end('Enviar'); ?>
</div>
<div class="lista"></div>
<div class="no-print">
    <button class="impressao">Impressão</button>
</div>
<script>
    $(document).ready(function($) {        
        $('.marcar_todos').click(function() {
            marcarTodos("categorias");
        });
        $('.desmarcar_todos').click(function() {
            desmarcarTodos("categorias");
        });
        
        var inputEsquemaoCpf = $('<input>');
        $('.checkbox-horizontal').css('width', '255px');
        $('input[type="checkbox"]').each(function() {
            $(this).next().css({padding: '0 3px', 'float': 'left'})    
        }).css({padding: '0 3px', 'float': 'left'});

        var esquemaCpf = function() {
            inputEsquemaoCpf.val(this.codigo_documento)
            mascaraCPF(inputEsquemaoCpf);
            var valor = inputEsquemaoCpf.val();
            return valor
        }
        $('.impressao').hide().click(function() {
            window.print();
        });
        var inputCodigoCliente = $('#FichaCodigoCliente');
        var comboTipoOperacao = $('#FichaTipoOperacao');
        var comboCategoria = $('#FichaCategoria');
        var comboTipoRelacionamento = $('#FichaTipoRelacionamento');
        
        var form = $("#FormSla");
        form.submit(function(e) {
            e.preventDefault();
            bloquearDiv(form);
            $('.impressao').hide();
    
            var divLista = $('.lista').addClass('lista_sla').empty();
            var colunas = ['CPF', 'Profissional', 'Status do Profissional', 'Tempo Excedido'];
            var action = $(this).attr('action');
            $.post(action, $(this).serialize(), function(data) {  
                form.unblock();
                if (!data.success) {
                    return false;
                }
                var total_fichas = data.fichas.length;
                
                $('.impressao').show();
                window.data = data;
                
                var clienteRazaoSocial = data.dados_cliente.razao_social;
                
                var camposTipoProfissional = $('[name^="data[Ficha][codigo_tipo_profissional]["]');
                var arrTiposProfSelecionadas = camposTipoProfissional.map(function() {
                    if ($(this).is(':checked')) {
                        return $(this).next().text();
                    }
                    return null
                }).get();

                var arrCategoria = ['<strong>Categorias:</strong>'];
                var textCategoriasSelecionadas;
                if (arrTiposProfSelecionadas.length == 0 
                    || arrTiposProfSelecionadas.length == camposTipoProfissional.length) {
                    textCategoriasSelecionadas = 'Todas';
                } else {
                    textCategoriasSelecionadas = arrTiposProfSelecionadas.join(', ');
                }
                arrCategoria.push(textCategoriasSelecionadas);
                var arrTipoOperacao = ['<strong>Operação</strong>']
                arrTipoOperacao.push(comboTipoOperacao.val() ? comboTipoOperacao.find('option:selected').text() : 'TODAS')
                
                var arrTipoRelacionamento = ['<strong>Relacionamento</strong>']
                arrTipoRelacionamento.push(comboTipoRelacionamento.find('option:selected').text());
                
                var arrCliente = ['<strong>Cliente</strong>', inputCodigoCliente.val()]
                var arrRazaoSocial = ['<strong>Razão Social</strong>', clienteRazaoSocial];
  
                var periodoInicial = new Date(data.periodo[0]);
                var periodoFinal = new Date(data.periodo[1]);
                var dataGeracao = new Date(data.geracao);
                var textoTotalFichas = ['<strong>Total de fichas:</strong> ', total_fichas].join('');
                var textoGeracao = 'Relatório do mês ' + formataMes(periodoInicial.getMonth()+1) + '/' + periodoInicial.getFullYear() + ' gerado em ' + formataData(dataGeracao);
                var fieldsetPeriodo = $('<fieldset>');
                var divTotalFichas = $('<span>').addClass('div-total-fichas').css({padding: '0 .5em'}).html(textoTotalFichas).appendTo(fieldsetPeriodo);
                var divPeriodo = $('<span>').addClass('div-periodo-sla').text(textoGeracao).appendTo(fieldsetPeriodo);
                
                
                var divInformacoesFiltro = $('<fieldset>').css('text-align', 'left').addClass('only-print');
                //$('<span>').html(textoTotalFichas).appendTo(divInformacoesFiltro);
                //$('<br />').appendTo(divInformacoesFiltro);
                $('<span>').css({padding: '0 .5em'}).html(arrCliente.join(': ')).appendTo(divInformacoesFiltro);
                $('<span>').css({padding: '0 .5em'}).html(arrRazaoSocial.join(': ')).appendTo(divInformacoesFiltro);
                

                var divInformacoesTiposProfissionais = $('<fieldset>').css('text-align', 'left').addClass('only-print');                
                $('<span>').css({padding: '0 .5em'}).html(arrCategoria.join(': ')).appendTo(divInformacoesTiposProfissionais);
                  
                divInformacoesFiltro.appendTo(divLista);
                
                
                fieldsetOperacaoRelacionamento = $('<fieldset>').addClass('only-print').appendTo(divLista);
                $('<span>').css({padding: '0 .5em'}).html(arrTipoOperacao.join(': ')).appendTo(fieldsetOperacaoRelacionamento);
                $('<span>').css({padding: '0 .5em'}).html(arrTipoRelacionamento.join(': ')).appendTo(fieldsetOperacaoRelacionamento);   
                divInformacoesTiposProfissionais.appendTo(divLista);
                
          
                fieldsetPeriodo.appendTo(divLista);               
                var quantidade_total;
                var quantidade_no_prazo;
                var quantidade_fora_do_prazo;
                var porcentagem_fora_do_prazo;
                var tempo_medio_por_extenso;
                var tempo_medio;
                var panel_estatistica;
                var fieldsetLista;
                var fichas_filtradas;
                $.each(data.tempos, function (i, item) {
                    quantidade_total = ["Quantidade total", item.quantidade].join(': ');
                    quantidade_no_prazo = ["Quantidade dentro do prazo",item.Estatistica.no_prazo].join(': ');
                    quantidade_fora_do_prazo = ['Quantidade fora do prazo', item.Estatistica.fora_do_prazo].join(': ');
                    porcentagem_fora_do_prazo = ['Nível SLA: ', 100-Math.round(item.Estatistica.porcentagem_fora_do_prazo), '%'].join('');
                    tempo_medio_por_extenso = ConversorTempo(item.Estatistica.tempo_medio * 60);
                    tempo_medio = ['Tempo médio', tempo_medio_por_extenso].join(': ');

                    fieldsetLista = $('<fieldset>').appendTo(divLista);
                    panel_estatistica = $('<div>').addClass('panel_estatistica').appendTo(fieldsetLista);
                    $('<h1>').text(i).appendTo(panel_estatistica);
                    $('<p>').text(quantidade_total).appendTo(panel_estatistica);
                    $('<p>').text(quantidade_no_prazo).appendTo(panel_estatistica);
                    $('<p>').text(quantidade_fora_do_prazo).appendTo(panel_estatistica);
                    $('<p>').text(porcentagem_fora_do_prazo).appendTo(panel_estatistica);
                    $('<p>').text(tempo_medio).appendTo(panel_estatistica);
                    });

                fieldsetLista = $('<fieldset>').addClass('no-print').appendTo(divLista);
                $('<h1>').text('FICHAS FORA DO PRAZO').appendTo(fieldsetLista);
                var tabela = $("<table>").appendTo(fieldsetLista);
                var tHeader = $('<tr>').appendTo(tabela);

                $(colunas).each(function(idx, val) {
                    $('<th>').text(val).appendTo(tHeader);
                });    
                
                fichas_filtradas = data.fichas.filter(function(item, idx) {
                    return item[0]['tempo_pesquisa_ficha'] > item['FichaPesquisa']['tempo_restante'];
                });
                
                $.each(fichas_filtradas, function(i, item) {
                    var linha = $('<tr>').appendTo(tabela);
                    var profissional = item['ProfissionalLog'];
                    var profissional_status = item['ProfissionalStatus'];
                    var documento = esquemaCpf.apply(profissional);
                    var nomeProfissional = profissional.nome;
                    var statusProfissional = profissional_status.descricao;
                    var tempoExcedido = item[0]['tempo_pesquisa_ficha'] - item['FichaPesquisa']['tempo_restante'];
                    tempoExcedido = ConversorTempo(tempoExcedido * 60).toString();

                    $('<td>').text(documento).appendTo(linha);
                    $('<td>').text(nomeProfissional).appendTo(linha);
                    $('<td>').text(statusProfissional).appendTo(linha);
                    $('<td>').text(tempoExcedido).appendTo(linha);
                    linha.appendTo(tabela);
                });
            }, 'json');
        });
    });
</script>