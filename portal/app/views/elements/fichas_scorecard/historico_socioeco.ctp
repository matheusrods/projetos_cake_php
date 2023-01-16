<?php
$codigo_ficha = empty($codigo_ficha) ? '' : $codigo_ficha;
//debug($consulta_serasa_socio);
//debug($consulta_serasa_socio['consulta']['Serasa']['Profissional']);
//debug($this->data['Profissional']['codigo_documento']);
//debug($ocorrencia_carreta);
//debug($ocorrencia_bitrem);
//debug($this->data['FichaScorecardVeiculo'][0]['Proprietario']['nome_razao_social']);die();
if (!empty($this->data['Profissional']['codigo_documento']) and $this->data['Profissional']['codigo_documento']!='-1') {  ?>
    <table  class="table table-condensed table-striped" >
        <tr>
            <th style="background-color:#DBEAF9;" class="input-large" colspan="12" align="center">Histórico consultas Serasa Profissional CPF: <?php echo strtoupper(COMUM::formatarDocumento($this->data['Profissional']['codigo_documento'])); ?></th>
        </tr>
        <?php
        if (!empty($consulta_serasa_socio['consulta']['Serasa']['Profissional'])) {
            ?>
            <tr><!--<th style="background-color:#DDDDDD" class="input-large">Profissional</th> -->
                <th style="background-color:#DDDDDD" class="input-large">Data Inclusão</th>
                <th style="background-color:#DDDDDD" class="input-small">Quantidade</th>
                <th style="background-color:#DDDDDD" class="input-large" >Data última Ocorrência</th>
                <th style="background-color:#DDDDDD" class="input-large" >Descrição</th>
                <th style="background-color:#DDDDDD" class="input-small">Valor</th> 
            </tr>
            <?php
            foreach ($consulta_serasa_socio['consulta']['Serasa']['Profissional'] as $log2) {
                print "<tr>";
                //print "<td>" . $this->data['Profissional']['nome'] . "</td>";
                print "<td>" . $log2['ProfissionalSerasa']['data_inclusao'] . "</td>";
                print "<td class='numeric'>" . $log2['ProfissionalSerasa']['quantidade_ocorrencias'] . "</td>";
                print "<td>" . $log2['ProfissionalSerasa']['data_ultima_ocorrencia'] . "</td>";
                print "<td>" . $log2['ProfissionalSerasa']['descricao'] . "</td>";
                print "<td class='numeric'>R$ " . number_format($log2['ProfissionalSerasa']['valor_ocorrencias'], 2, ',', '') . "</td>";
                print "</tr>";
            }
        } else {
            print "<tr>";
            print "<td colsopan='5'>Profissional sem registros de Histórico Serasa</td>";
            print "</tr>";
        } ?>
    </table><?php
    }
?>

<?php if (!empty($this->data['Profissional']['codigo_documento']) and $this->data['Profissional']['codigo_documento']!='-1') { ?>
    <table  class="table table-condensed table-striped">
        <tr><th style="background-color:#DBEAF9;" class="input-large" colspan="12" align="center" >Histórico consultas Telecheque Profissional CPF: <?php echo strtoupper(COMUM::formatarDocumento($this->data['Profissional']['codigo_documento'])); ?></th>
        </tr>
        <?php
        if (!empty($consulta_serasa_socio['consulta']['Telecheque']['Proprietario']['Veiculo'])) {
            ?>
            <tr><!--<th style="background-color:#DDDDDD" class="input-large" >Profissional</th> -->
                <th style="background-color:#DDDDDD" class="input-large">Data Inclusão</th>
                <th style="background-color:#DDDDDD" class="input-large">Nome Emitente</th>
                <th style="background-color:#DDDDDD" class="input-small" >Quantidade Ocorrência</th>
                <th style="background-color:#DDDDDD" class="input-large" >Banco</th>
                <th style="background-color:#DDDDDD" class="input-large">Agência</th> 
            </tr>
            <?php
            foreach ($consulta_serasa_socio['consulta']['Telecheque']['Profissinal'] as $log1) {
                //debug($log);
                //debug($this->data['Profissional']);
                print "<tr>";
                //print "<td>" . $this->data['Profissional']['nome'] . "</td>";
                print "<td>" . $log1['ProfissionalTelecheque']['data_inclusao'] . "</td>";
                print "<td>" . $log1['ProfissionalTelecheque']['nome_emitente'] . "</td>";
                print "<td class='numeric'>" . $log1['ProfissionalTelecheque']['quantidade_ocorrencias'] . "</td>";
                print "<td>" . $log1['ProfissionalTelecheque']['codigo_banco'] . "</td>";
                print "<td>" . $log1['ProfissionalTelecheque']['agencia_banco'] . "</td>";
                print "</tr>";
            }
        } else {

            print "<tr>";
            print "<td colsopan='5'>Profissional sem registros de histórico no telecheque</td>";
            print "</tr>";
        } ?>
    </table> <?php    
    }
    ?>

<?php if (!empty($this->data['FichaScorecardVeiculo'][0]['Proprietario']['codigo_documento']) and 
       $this->data['FichaScorecardVeiculo'][0]['Proprietario']['codigo_documento']!='-1') { ?>
    <table  class="table table-condensed table-striped" >
        <tr>
            <th style="background-color:#DBEAF9;" class="input-large" colspan="12" align="center" >Histórico consultas Serasa Proprietário Veículo CPF: <?php echo strtoupper(COMUM::formatarDocumento($this->data['FichaScorecardVeiculo'][0]['Proprietario']['codigo_documento'])); ?></th>
        </tr>
        <?php if (!empty($consulta_serasa_socio['consulta']['Serasa']['Proprietario']['Veiculo'])) {
            ?>
            <tr><!--<th style="background-color:#DDDDDD" class="input-large" >Profissional</th> -->
                <th style="background-color:#DDDDDD" class="input-large">Data Inclusão</th>
                <th style="background-color:#DDDDDD" class="input-small">Quantidade</th>
                <th style="background-color:#DDDDDD" class="input-large" >Data última Ocorrência</th>
                <th style="background-color:#DDDDDD" class="input-large" >Descrição</th>
                <th style="background-color:#DDDDDD" class="input-small">Valor</th> 



            </tr>
            <?php
            foreach ($consulta_serasa_socio['consulta']['Serasa']['Proprietario']['Veiculo'] as $log7) {
                //debug($log2);
                //debug($this->data['Profissional']);
                print "<tr>";
                //print "<td>" . $this->data['FichaScorecardVeiculo'][0]['Proprietario']['nome_razao_social'] . "</td>";
                print "<td>" . $log7['ProprietarioSerasa']['data_inclusao'] . "</td>";
                print "<td class='numeric'>" . $log7['ProprietarioSerasa']['quantidade_ocorrencias'] . "</td>";
                print "<td>" . $log7['ProprietarioSerasa']['data_ultima_ocorrencia'] . "</td>";
                print "<td>" . $log7['ProprietarioSerasa']['descricao'] . "</td>";
                print "<td class='numeric'>R$ " . number_format($log7['ProprietarioSerasa']['valor_ocorrencias'], 2, ',', '') . "</td>";
                print "</tr>";
            }
        } else {
            print "<tr>";
            print "<td colsopan='5'>Proprietário veículo sem registros no  Histórico Serasa</td>";
            print "</tr>";
        } ?>
    </table><?php
    }
    ?>

<?php if (!empty($this->data['FichaScorecardVeiculo'][0]['Proprietario']['codigo_documento']) and 
        $this->data['FichaScorecardVeiculo'][0]['Proprietario']['codigo_documento']!='-1'){ ?>
    <table  class="table table-condensed table-striped" >
        <tr>
            <th style="background-color:#DBEAF9;" class="input-large" colspan="12" align="center" >Histórico consultas Telecheque Proprietário Veículo : <?php echo strtoupper(COMUM::formatarDocumento($this->data['FichaScorecardVeiculo'][0]['Proprietario']['codigo_documento'])); ?></th>
        </tr>
        <?php if (!empty($consulta_serasa_socio['consulta']['Telecheque']['Proprietario']['Veiculo'])) { ?>
            <tr><!--<th style="background-color:#DDDDDD" class="input-large" >Proprietário</th> -->
                <th style="background-color:#DDDDDD" class="input-large">Data Inclusão</th>
                <th style="background-color:#DDDDDD" class="input-large">Nome Emitente</th>
                <th style="background-color:#DDDDDD" class="input-small" >Quantidade Ocorrência</th>
                <th style="background-color:#DDDDDD" class="input-large" >Banco</th>
                <th style="background-color:#DDDDDD" class="input-large">Agência</th> 
            </tr>
            <?php
            foreach ($consulta_serasa_socio['consulta']['Telecheque']['Proprietario']['Veiculo'] as $log) {
                print "<tr>";
                //print "<td>" . $this->data['FichaScorecardVeiculo'][0]['Proprietario']['nome_razao_social'] . "</td>";
                print "<td>" . $log['ProprietarioTelecheque']['data_inclusao'] . "</td>";
                print "<td>" . $log['ProprietarioTelecheque']['nome_emitente'] . "</td>";
                print "<td class='numeric'>" . $log['ProprietarioTelecheque']['quantidade_ocorrencias'] . "</td>";
                print "<td>" . $log['ProprietarioTelecheque']['codigo_banco'] . "</td>";
                print "<td>" . $log['ProprietarioTelecheque']['agencia_banco'] . "</td>";
                print "</tr>";
            }
        } else {
            print "<tr>";
            print "<td colsopan='5'>Proprietario Veículo sem registros no Historico de telecheque</td>";
            print "</tr>";
        } ?>
    </table><?php
    }
?>

<?php if (!empty($this->data['FichaScorecardVeiculo'][1]['Proprietario']['codigo_documento']) and $this->data['FichaScorecardVeiculo'][1]['Proprietario']['codigo_documento']!='-1') {  ?>
    <table  class="table table-condensed table-striped">
        <tr>
            <th style="background-color:#DBEAF9;" class="input-large" colspan="12" align="center" >Histórico consultas Serasa Proprietário Carreta CPF: <?php echo strtoupper(COMUM::formatarDocumento($this->data['FichaScorecardVeiculo'][1]['Proprietario']['codigo_documento'])); ?></th>
        </tr>
        <?php 
            if (!empty($consulta_serasa_socio['consulta']['Serasa']['Proprietario']['Carreta'])) { ?>
                <tr><!--<th style="background-color:#DDDDDD" class="input-large" >Profissional</th> -->
                    <th style="background-color:#DDDDDD" class="input-large">Data Inclusão</th>
                    <th style="background-color:#DDDDDD" class="input-small">Quantidade</th>
                    <th style="background-color:#DDDDDD" class="input-large" >Data última Ocorrência</th>
                    <th style="background-color:#DDDDDD" class="input-large" >Descrição</th>
                    <th style="background-color:#DDDDDD" class="input-small">Valor</th> 
                </tr>
                <?php
                    foreach ($consulta_serasa_socio['consulta']['Serasa']['Proprietario']['Carreta'] as $log7) {
                        print "<tr>";
                        //print "<td>" . $this->data['FichaScorecardVeiculo'][1]['Proprietario']['nome_razao_social'] . "</td>";
                        print "<td>" . $log7['ProprietarioSerasa']['data_inclusao'] . "</td>";
                        print "<td class='numeric'>" . $log7['ProprietarioSerasa']['quantidade_ocorrencias'] . "</td>";
                        print "<td>" . $log7['ProprietarioSerasa']['data_ultima_ocorrencia'] . "</td>";
                        print "<td>" . $log7['ProprietarioSerasa']['descricao'] . "</td>";
                        print "<td class='numeric'> R$ " . number_format($log7['ProprietarioSerasa']['valor_ocorrencias'], 2, ',', '') . "</td>";
                        print "</tr>";
                    }
            } else {
                print "<tr>";
                print "<td colsopan='5'>Proprietário carreta sem registros no  Histórico Serasa</td>";
                print "</tr>";
            }?>
    </table><?php
}  ?>

<?php if (!empty($this->data['FichaScorecardVeiculo'][1]['Proprietario']['codigo_documento']) and 
          $this->data['FichaScorecardVeiculo'][1]['Proprietario']['codigo_documento']!='-1'){ ?>
    <table  class="table table-condensed table-striped" >
        <tr><th style="background-color:#DBEAF9;" class="input-large" colspan="12" align="center" >Histórico consultas Telecheque Proprietário Carreta CPF: <?php echo strtoupper(COMUM::formatarDocumento($this->data['FichaScorecardVeiculo'][1]['Proprietario']['codigo_documento'])); ?></th>
        </tr>
        <?php 
        if (!empty($consulta_serasa_socio['consulta']['Telecheque']['Proprietario']['Carreta'])) {   ?>
                <tr><!--<th style="background-color:#DDDDDD" class="input-large">Proprietário</th>  -->
                    <th style="background-color:#DDDDDD" class="input-large">Data Inclusão</th>
                    <th style="background-color:#DDDDDD" class="input-large">Nome Emitente</th>
                    <th style="background-color:#DDDDDD" class="input-small">Quantidade Ocorrência</th>
                    <th style="background-color:#DDDDDD" class="input-large">Banco</th>
                    <th style="background-color:#DDDDDD" class="input-large">Agência</th> 
                </tr>
                <?php
                foreach ($consulta_serasa_socio['consulta']['Telecheque']['Proprietario']['Carreta'] as $log) {
                    print "<tr>";
                    //print "<td>" . $this->data['FichaScorecardVeiculo'][1]['Proprietario']['nome_razao_social'] . "</td>";
                    print "<td>" . $log['ProprietarioTelecheque']['data_inclusao'] . "</td>";
                    print "<td>" . $log['ProprietarioTelecheque']['nome_emitente'] . "</td>";
                    print "<td class='numeric'>" . $log['ProprietarioTelecheque']['quantidade_ocorrencias'] . "</td>";
                    print "<td>" . $log['ProprietarioTelecheque']['codigo_banco'] . "</td>";
                    print "<td>" . $log['ProprietarioTelecheque']['agencia_banco'] . "</td>";
                    print "</tr>";
                }
            } else {

                print "<tr>";
                print "<td colsopan='5'>Proprietario Carreta sem registros no Historico telecheque</td>";
                print "</tr>";
        }?>
    </table><?php
}  ?>


<?php if (!empty($this->data['FichaScorecardVeiculo'][2]['Proprietario']['codigo_documento']) and $this->data['FichaScorecardVeiculo'][2]['Proprietario']['codigo_documento']!='-1') {  ?>
    <table  class="table table-condensed table-striped">
        <tr>
            <th style="background-color:#DBEAF9;" class="input-large" colspan="12" align="center" >Histórico consultas Serasa Proprietário Bitrem CPF: <?php echo strtoupper(COMUM::formatarDocumento($this->data['FichaScorecardVeiculo'][2]['Proprietario']['codigo_documento'])); ?></th>
        </tr>
        <?php
        if (!empty($consulta_serasa_socio['consulta']['Serasa']['Proprietario']['Bitrem'])) {
            ?>
            <tr><!--<th style="background-color:#DDDDDD" class="input-large" >Profissional</th> -->
                    <th style="background-color:#DDDDDD" class="input-large">Data Inclusão</th>
                    <th style="background-color:#DDDDDD" class="input-small">Quantidade</th>
                    <th style="background-color:#DDDDDD" class="input-large" >Data última Ocorrência</th>
                    <th style="background-color:#DDDDDD" class="input-large" >Descrição</th>
                    <th style="background-color:#DDDDDD" class="input-small">Valor</th> 
            </tr>
        <?php 
            foreach ($consulta_serasa_socio['consulta']['Serasa']['Proprietario']['Bitrem'] as $log7) {
                print "<tr>";
                //print "<td>" . $this->data['FichaScorecardVeiculo'][2]['Proprietario']['nome_razao_social'] . "</td>";
                print "<td>" . $log7['ProprietarioSerasa']['data_inclusao'] . "</td>";
                print "<td class='numeric'>" . $log7['ProprietarioSerasa']['quantidade_ocorrencias'] . "</td>";
                print "<td>" . $log7['ProprietarioSerasa']['data_ultima_ocorrencia'] . "</td>";
                print "<td>" . $log7['ProprietarioSerasa']['descricao'] . "</td>";
                print "<td class='numeric'> R$" . number_format($log7['ProprietarioSerasa']['valor_ocorrencias'], 2, ',', '') . "</td>";
                print "</tr>";
            }
        } else {
            print "<tr>";
            print "<td colsopan='5'>Proprietário Bitrem sem registros no  Histórico Serasa</td>";
            print "</tr>";
        }?>
    </table><?php
} ?>


<?php if (!empty($this->data['FichaScorecardVeiculo'][2]['Proprietario']['codigo_documento']) and 
          $this->data['FichaScorecardVeiculo'][2]['Proprietario']['codigo_documento']!='-1'){ ?>
    <table  class="table table-condensed table-striped" >
        <tr><th style="background-color:#DBEAF9;" class="input-large" colspan="12" align="center" >Histórico consultas Telecheque Proprietário Bitrem CPF: <?php echo strtoupper(COMUM::formatarDocumento($this->data['FichaScorecardVeiculo'][2]['Proprietario']['codigo_documento'])); ?></th>
        </tr>
        <?php
        if (!empty($consulta_serasa_socio['consulta']['Telecheque']['Proprietario']['Bitrem'])) {
            ?>
            <tr><!--<th style="background-color:#DDDDDD" class="input-large" >Proprietário</th> -->
                <th style="background-color:#DDDDDD" class="input-large">Data Inclusão</th>
                <th style="background-color:#DDDDDD" class="input-large">Nome Emitente</th>
                <th style="background-color:#DDDDDD" class="input-small" >Quantidade Ocorrência</th>
                <th style="background-color:#DDDDDD" class="input-large" >Banco</th>
                <th style="background-color:#DDDDDD" class="input-large">Agência</th> 
            </tr>
            <?php 
            foreach ($consulta_serasa_socio['consulta']['Telecheque']['Proprietario']['Bitrem'] as $log5) {
                print "<tr>";
                //print "<td>" . $this->data['FichaScorecardVeiculo'][2]['Proprietario']['nome_razao_social'] . "</td>";
                print "<td>" . $log5['ProprietarioTelecheque']['data_inclusao'] . "</td>";
                print "<td>" . $log5['ProprietarioTelecheque']['nome_emitente'] . "</td>";
                print "<td class='numeric'>" . $log5['ProprietarioTelecheque']['quantidade_ocorrencias'] . "</td>";
                print "<td>" . $log5['ProprietarioTelecheque']['codigo_banco'] . "</td>";
                print "<td>" . $log5['ProprietarioTelecheque']['agencia_banco'] . "</td>";
                print "</tr>";
            }
    } else {
        print "<tr>";
        print "<td colsopan='5'>Proprietário Bitrem sem registros no Histórico de telecheque</td>";
        print "</tr>";
    }?>
    </table><?php
} ?>
