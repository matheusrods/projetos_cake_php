<div style="width:1000px;">
   <div style="font-family:verdana;font-size:12px;line-height:30px;">
        <span>Olá,</span><br>
        <span>Os fornecedores abaixo possuem <strong>Documentos Vencidos</strong>.</span><br>
        <span>Favor solicitar nova documentação.</span>
    </div>
    <table style="margin-bottom: 20px; width: 100%; background-color: transparent; border-collapse: collapse; border-spacing: 0;font-family:verdana; font-size:12px; line-height:30px;">
        <thead>
            <tr style="border: 1px solid #ddd; line-height: 20px; padding: 8px;text-align: left; vertical-align: middle;  background-color: #eee;">
                <th style="font-weight:bold; width: 80px;">Código</th>
                <th style="font-weight:bold;">Fornecedor</th>
                <th style="font-weight:bold;">Documento</th>
                <th style="font-weight:bold;">Data de Validade</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($dados as $key => $value): ?>
                <tr style="border: 1px solid #ddd; line-height: 20px; padding: 8px;text-align: left; vertical-align: middle;  background-color: #f9f9f9; font-size: 12px">
                    <td style="width: 80px;"><?php echo $dados[$key]['codigo_fornecedor'];?></td>
                    <td><?php echo htmlspecialchars($dados[$key]['razao_social']);?></td>
                    <td><?php echo htmlspecialchars($dados[$key]['descricao_documento']);?></td>
                    <td><?php echo $dados[$key]['data_validade'];?></td>
                    
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div style="font-family:verdana;font-size:12px;line-height:30px;">
        <span>Att,</span><br>
        <span><strong>Equipe RH Health</strong></span><br>
        <span><a href="http://www.rhhealth.com.br" target="_blank">www.rhhealth.com.br</a></span>
    </div>
</div>