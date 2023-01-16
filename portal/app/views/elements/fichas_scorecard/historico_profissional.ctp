<?php $codigo_ficha = empty($codigo_ficha) ? '' : $codigo_ficha;
if (!empty($this->data['Profissional']['codigo_documento'])){ ?>
    <table  class="table table-condensed table-striped" >
    <tr>
        <th style="background-color:#DBEAF9;" class="input-large" colspan="13" align="center">
            Histórico do Profissional: <?php echo strtoupper(COMUM::formatarDocumento($this->data['Profissional']['codigo_documento'])); ?>
        </th>
    </tr>
<?php
    if (!empty($log_faturamento)){?>
    <tr>
        <th style="background-color:#DDDDDD" class="input-large">Data Inclusão</th> 
        <th style="background-color:#DDDDDD" class="input-large">Razão Social</th>
        <th style="background-color:#DDDDDD" class="input-large">CPF </th>
        <th style="background-color:#DDDDDD" class="input-large">Profissional</th>
        <th style="background-color:#DDDDDD" class="input-large">Usuário</th> 
        <th style="background-color:#DDDDDD" class="input-large">Tipo Operacao</th>
        <th style="background-color:#DDDDDD" class="input-large">Classificação Manual</th>
        <th style="background-color:#DDDDDD" class="input-large">Classificação Score</th>
        <th style="background-color:#DDDDDD" class="input-large">Placa</th>
        <th style="background-color:#DDDDDD" class="input-large">Carreta</th>
        <th style="background-color:#DDDDDD" class="input-large">Bitrem</th>
        <th style="background-color:#DDDDDD" class="input-large">Cidade Origem</th>
        <th style="background-color:#DDDDDD" class="input-large">Cidade Destino</th>    
    </tr>
        <?php foreach ($log_faturamento as $log) {
            print "<tr>";  
            print "<td>".$log['0']['data_inclusao']."</td>";
            print "<td>".$log['0']['razao_social']."</td>";
            print "<td>".$log['0']['cpf']."</td>";
            print "<td>".$log['0']['profissional']."</td>";
            print "<td>".$log['0']['usuario']."</td>";
            print "<td>".$log['0']['tipo_operacao']."</td>";
            print "<td>".$log['0']['status_manual']."</td>";
            print "<td>".$log['0']['classificacao_motorista']."</td>";
            print "<td>".strtoupper($log['0']['placa'])."</td>";
            print "<td>".strtoupper($log['0']['carreta'])."</td>";
            print "<td>".strtoupper($log['0']['bitrem'])."</td>";
            print "<td>".$log['0']['endereco_origem']."</td>";
            print "<td>".$log['0']['endereco_destino']."</td>";
            print "</tr>";
        }
    }else{
        print "<tr>";  
        print "<td colsopan='5'>Profissional sem histórico</td>";
        print "</tr>";
    }
}?>
</table>