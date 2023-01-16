<style type="text/css">
    /*.table {
    table-layout:fixed;
    max-width:1170px;
    display: block;
}
*/
    /*.table td {
  white-space: nowrap;
  /*overflow-y: auto;*/
    /*}
.table th {
  white-space: nowrap;
}*/

    .legenda {
        padding: 5px;
        border-radius: 10px;
        display: inline;
        position: relative;
    }

    .verde {
        background-color: #32CD3266;
        cursor: pointer;
    }

    .amarelo {
        background-color: #FFD700;
    }

    .cinza {
        background-color: #d2cbcb;
    }

    .azul {
        background-color: #87ceffb3;
        cursor: pointer;
    }
</style>
<div class='well'>

    <div class="legenda cinza"><strong>Total Filtrado - Ativos:</strong> <?php echo $this->Html->tag('span', $total_filtrado['ativo']);  ?> <strong> Inativos:</strong><?php echo $this->Html->tag('span', $total_filtrado['inativo']);  ?></div>
    &nbsp;&nbsp;
    <div class="legenda azul" onclick="filtro_ativos('SI')"><strong>Saldo Inicial - Ativos:</strong> <?php echo $this->Html->tag('span', $saldo_inicial['ativo']);  ?></div>
    &nbsp;&nbsp;
    <div class="legenda azul" onclick="filtro_ativos('IP')"><strong>Inclusos no período:</strong> <?php echo $this->Html->tag('span', $inclusos_periodo['ativo']);  ?></div>
    &nbsp;&nbsp;
    <div class="legenda azul" onclick="filtro_ativos('DP')"><strong>Demitidos no período:</strong> <?php echo $this->Html->tag('span', $demitido_periodo['ativo']);  ?></div>
    &nbsp;&nbsp;
    <div class="legenda verde" onclick="filtro_ativos('SF')"><strong>Saldo Final - Ativos:</strong> <?php echo $this->Html->tag('span', $total_geral['ativo']);  ?></div>

</div>
<div class='actionbar-right margin-bottom-10'>
    <?php echo $html->link('<i class="icon-plus icon-white"></i>', array('controller' => 'funcionarios', 'action' => 'incluir', $codigo_matriz, 'percapita'), array('escape' => false, 'class' => 'btn btn-success', 'title' => 'Incluir Funcionários')) ?>
</div>
<?php if (!empty($funcionarios)) : ?>
    <?php echo $paginator->options(array('update' => 'div.lista')); ?>
    <table class='table table-striped'>
        <thead>
            <tr>
                <th>Matricula</th>
                <th>Nome</th>
                <th>CPF</th>
                <th>Unidade</th>
                <th>Setor</th>
                <th>Cargo</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($funcionarios as $funcionario) : ?>
                <tr>
                    <td><?php echo $funcionario['ClienteFuncionario']['matricula']; ?></td>
                    <td><?php echo $this->Buonny->leiamais($funcionario['Funcionario']['nome'], 25); ?></td>
                    <td><?php echo Comum::formatarDocumento($funcionario['Funcionario']['cpf']); ?></td>
                    <td><?php echo $this->Buonny->leiamais($funcionario['Cliente']['nome_fantasia'], 25); ?></td>
                    <td><?php echo $this->Buonny->leiamais($funcionario['Setor']['descricao'], 20); ?></td>
                    <td><?php echo $this->Buonny->leiamais($funcionario['Cargo']['descricao'], 20); ?></td>
                    <td>
                        <a href="javascript:void(0);" onclick="editar_data(<?php echo $funcionario['ClienteFuncionario']['codigo']; ?>,1);" class="icon-edit" title="Atualizar Data Fim da Matricula"></a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="15"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['ClienteFuncionario']['count']; ?></td>
            </tr>
        </tfoot>
    </table>
    <div class="modal fade" id="modal_funcionario" data-backdrop="static"></div>
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

    <?php if ($disparo_link['DisparoLink']['status_validacao'] != 1) { ?>
        <div class='form-actions well'>
            <?php echo $html->link('Confirmar para Faturamento', '/funcionarios/status_per_capita/' . $codigo_matriz, array('class' => 'btn btn-primary')); ?>
        </div>
    <?php } else { ?>
        <div class="alert">Per Capita validado para este mês.</div>
    <?php } ?>

<?php else : ?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif; ?>




<?php echo $this->Js->writeBuffer(); ?>

<?php
echo $this->Javascript->codeBlock("

    //funcao para os botoes com as quantidades
    function filtro_ativos(tipo)
    {        
        // console.log(tipo);
        //seta o valor e submete a pagina via ajax
        $('#FuncionarioBtFiltro').val(tipo);

        //zera os filtros
        $('FuncionarioNome').val('');
        $('FuncionarioCpf').val('');
        $('FuncionarioCodigoMatricula').val('');
        $('FuncionarioMatricula').val('');
        $('FuncionarioCodigoUnidade').val('');
        $('FuncionarioCodigoSetor').val('');
        $('FuncionarioCodigoCargo').val('');
        $('FuncionarioCodigoPagador').val('');

        $('#FuncionarioIndexPercapitaForm').submit();
        $('#FuncionarioFiltrarForm').submit();
        $('#FuncionarioLimparForm').submit();
        

    }//fim filtro_ativos

    function editar_data(codigo_matricula,mostra) {
        if(mostra) {            
            var div = jQuery('div#modal_funcionario');
            bloquearDiv(div);
            div.load(baseUrl + 'funcionarios/modal_data_fim_matricula/' + codigo_matricula + '/' + Math.random());
    
            $('#modal_funcionario').css('z-index', '1050');
            $('#modal_funcionario').modal('show');

            setup_mascaras(); 
            setup_datepicker(); 

        } else {
            $('#modal_funcionario').css('z-index', '-1');
            $('#modal_funcionario').modal('hide');
        }

    }



");

?>