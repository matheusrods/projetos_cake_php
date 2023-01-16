<?php
class RelatorioNaveg extends AppModel {
    var $name 			= 'RelatorioNaveg';
    var $useTable 		= false;


    /**
     * [getCntPagar relatorio de contas a pagar]
     * @param  [type] $inicio [description]
     * @param  [type] $fim    [description]
     * @return [type]         [description]
     */
    public function getCntPagar($inicio,$fim)
    {
        //query contas a pagar
        $query = "
            SELECT
                movban.empresa AS movban_empresa,
                loja.descricao AS loja_descricao,
                movban.dtmov AS movban_dtmov,
                movban.numero AS movban_numero,
                movban.tipmban AS movban_tipmban,
                movban.tipodoc AS movban_tipodoc,
                movban.debcred AS movban_debcred ,
                movban.valor AS movban_valor,
                movban.historico AS movban_historico,
                movban.historico1 AS movban_historico1,
                movban.historico2 AS movban_historico2,
                movban.banco AS movban_banco,
                banco.descricao AS banco_descricao,
                movban.emitente AS movban_emitente,
                movban.razaosocia AS movban_razaosocia,
                tranpag.numero AS tranpag_numero,
                tranpag.grflux AS tranpag_grflux,
                grflux.descricao AS grflux_descricao,
                tranpag.sbflux AS tranpag_sbflux,
                sbflux.descricao AS sbflux_descricao,
                movban.ccusto AS movban_ccusto,
                ccusto.descricao AS ccusto_descricao
            FROM dbNavegarqNatec.dbo.movban,
                dbNavegarqNatec.dbo.tranpag,
                dbNavegarqNatec.dbo.ccusto,
                dbNavegarqNatec.dbo.loja,
                dbNavegarqNatec.dbo.grflux,
                dbNavegarqNatec.dbo.sbflux,
                dbNavegarqNatec.dbo.banco
            WHERE tranpag.empresa = movban.empresa
                AND (movban.dtmov >= '{$inicio} 00:00:00.000' AND movban.dtmov <= '{$fim} 00:00:00.000') 
                AND tranpag.serie = movban.serie
                AND tranpag.seq = '02'
                AND tranpag.numero = movban.numero1
                AND tranpag.tipodoc = movban.tipodoc
                AND tranpag.emitente = movban.emitente
                AND tranpag.ordem = movban.ordem
                AND tranpag.tipoemit = movban.tipoemit
                AND ccusto.codigo = movban.ccusto
                AND loja.codigo = movban.empresa
                AND tranpag.grflux = grflux.codigo
                AND tranpag.sbflux = sbflux.codigo
                AND grflux.codigo = sbflux.grflux
                AND movban.banco = banco.codigo
            ORDER BY movban.empresa,movban.dtmov
        ";

        //executa para pegar os dados
        $dados = $this->query($query);
        
        $campos = array('movban_empresa','loja_descricao','movban_dtmov','movban_numero','movban_tipmban','movban_tipodoc','movban_debcred' ,'movban_valor','movban_historico','movban_historico1','movban_historico2','movban_banco','banco_descricao','movban_emitente','movban_razaosocia','tranpag_numero','tranpag_grflux','grflux_descricao','tranpag_sbflux','sbflux_descricao','movban_ccusto','ccusto_descricao');

        return array($campos,$dados);

    }//fim getCntPagar


    /**
     * [getCntReceber relatorio de contas a pagar]
     * @param  [type] $inicio [description]
     * @param  [type] $fim    [description]
     * @return [type]         [description]
     */
    public function getCntReceberLiquidada($inicio,$fim)
    {
        //query contas a receber liquidadas
        $query = "
            SELECT
                TR.empresa AS TR_empresa,
                TR.seqn AS TR_seqn,
                TR.serie AS TR_serie,
                TR.numero AS TR_numero,
                TR.tipodoc AS TR_tipodoc,
                TR.emitente AS TR_emitente,
                TR.tiptit AS TR_tiptit,
                TR.ordem AS TR_ordem,
                TR.tipoemit AS TR_tipoemit,
                TR.seq AS TR_seq,
                TR.razao AS TR_razao,
                TR.dtemiss AS TR_dtemiss,
                TR.dtvencto AS TR_dtvencto,
                TR.dtlan AS TR_dtlan,
                TR.dtbord AS TR_dtbord,
                TR.dtpagto AS TR_dtpagto,
                TR.dtorig AS TR_dtorig,
                TR.dtfluxo AS TR_dtfluxo,
                TR.valor AS TR_valor,
                TR.debcred AS TR_debcred,
                TR.banco AS TR_banco,
                TR.juros AS TR_juros,
                TR.desconto AS TR_desconto,
                TR.numbanc AS TR_numbanc,
                TR.ccusto AS TR_ccusto,
                TR.grflux AS TR_grflux,
                TR.sbflux AS TR_sbflux,
                TR.historico AS TR_historico,
                TR.numbord AS TR_numbord,
                TR.observ AS TR_observ,
                TR.opeban AS TR_opeban,
                AD.liquidado AS AD_liquidado,
                TR.formpag AS TR_formpag,
                TR.cartcred AS TR_cartcred,
                TR.numcart AS TR_numcart,
                TR.motpro AS TR_motpro,
                TR.multa AS TR_multa,
                TR.nossonum AS TR_nossonum,
                TR.dignosso AS TR_dignosso,
                TR.tipocob AS TR_tipocob,
                TR.cartbanco AS TR_cartbanco,
                TR.valdiatr AS TR_valdiatr,
                TR.perdiatr AS TR_perdiatr,
                TR.obs AS TR_obs,
                TR.arqcob AS TR_arqcob,
                TR.incspc AS TR_incspc,
                TR.opercobr AS TR_opercobr,
                TR.msg AS TR_msg,
                CL.ATIVO AS CL_ATIVO
            FROM dbNavegarqNatec.dbo.TRANREC TR, 
                dbNavegarqNatec.dbo.ADREC AD,
                dbNavegarqNatec.dbo.CLIENTE CL        
            WHERE (TR.dtpagto >= '{$inicio} 00:00:00.000')          
                AND (TR.dtpagto <= '{$fim} 00:00:00.000')
                AND (TR.DTEMISS >= '1900-04-15 00:00:00.000')             
                AND (TR.DTEMISS <= '{$fim} 00:00:00.000')             
                AND (TR.EMPRESA = AD.EMPRESA)      
                AND (TR.SEQN = AD.SEQN)       
                AND (TR.SERIE = AD.SERIE)        
                AND (TR.NUMERO = AD.NUMERO)       
                AND (TR.ORDEM = AD.ORDEM)              
                AND (TR.TIPODOC = AD.TIPODOC)        
                AND (AD.LIQUIDADO = 'S')        
                AND (seq = '02')              
                AND (TR.EMPRESA >= '18')        
                AND (TR.EMPRESA <= '18')        
                AND (TR.BANCO >= '04')             
                AND (TR.BANCO <= '23')             
                AND (TR.OPEBAN >= '')               
                AND (TR.OPEBAN <= 'zzzzzzzzzz')           
                AND (TR.NUMERO >= '')             
                AND (TR.NUMERO <= 'zzzzzzzzzz')         
                AND (TR.VALOR >= -9999999999999)    
                AND (TR.VALOR <= 9999999999999)      
                AND (TR.GRFLUX >= '')
                AND (TR.GRFLUX <= 'zzzz')        
                AND (TR.SBFLUX >= '') 
                AND (TR.SBFLUX <= 'zzzz')         
                AND (TR.DUPLIC >= '')  
                AND (TR.DUPLIC <= 'zzzzzzzzzz')             
                AND (TR.REPRES >= '')  
                AND (TR.REPRES <= 'zzzzzzzzz')               
                AND (TR.EMITENTE >= '')            
                AND (TR.EMITENTE <= 'zzzzzzzzz')         
                AND ( TR.TIPOEMIT = 'C' )          
                AND ( TR.EMITENTE = CL.CODIGO )       
            UNION
            SELECT
                TR.empresa AS TR_empresa,
                TR.seqn AS TR_seqn,
                TR.serie AS TR_serie,
                TR.numero AS TR_numero,
                TR.tipodoc AS TR_tipodoc,
                TR.emitente AS TR_emitente,
                TR.tiptit AS TR_tiptit,
                TR.ordem AS TR_ordem,
                TR.tipoemit AS TR_tipoemit,
                TR.seq AS TR_seq,
                TR.razao AS TR_razao,
                TR.dtemiss AS TR_dtemiss,
                TR.dtvencto AS TR_dtvencto,
                TR.dtlan AS TR_dtlan,
                TR.dtbord AS TR_dtbord,
                TR.dtpagto AS TR_dtpagto,
                TR.dtorig AS TR_dtorig,
                TR.dtfluxo AS TR_dtfluxo,
                TR.valor AS TR_valor,
                TR.debcred AS TR_debcred,
                TR.banco AS TR_banco,
                TR.juros AS TR_juros,
                TR.desconto AS TR_desconto,
                TR.numbanc AS TR_numbanc,
                TR.ccusto AS TR_ccusto,
                TR.grflux AS TR_grflux,
                TR.sbflux AS TR_sbflux,
                TR.historico AS TR_historico,
                TR.numbord AS TR_numbord,
                TR.observ AS TR_observ,
                TR.opeban AS TR_opeban,
                AD.liquidado AS AD_liquidado,
                TR.formpag AS TR_formpag,
                TR.cartcred AS TR_cartcred,
                TR.numcart AS TR_numcart,
                TR.motpro AS TR_motpro,
                TR.multa AS TR_multa,
                TR.nossonum AS TR_nossonum,
                TR.dignosso AS TR_dignosso,
                TR.tipocob AS TR_tipocob,
                TR.cartbanco AS TR_cartbanco,
                TR.valdiatr AS TR_valdiatr,
                TR.perdiatr AS TR_perdiatr,
                TR.obs AS TR_obs,
                TR.arqcob AS TR_arqcob,
                TR.incspc AS TR_incspc,
                TR.opercobr AS TR_opercobr,
                TR.msg AS TR_msg,
                CL.ATIVO AS CL_ATIVO
            FROM dbNavegarqNatec.dbo.TRANREC TR, 
                dbNavegarqNatec.dbo.ADREC AD,
                dbNavegarqNatec.dbo.FORNEC CL        
            WHERE (TR.dtpagto >= '{$inicio} 00:00:00.000')          
                AND (TR.dtpagto <= '{$fim} 00:00:00.000')
                AND (TR.DTEMISS >= '1900-10-01 00:00:00.000')             
                AND (TR.DTEMISS <= '{$fim} 00:00:00.000')             
                AND (TR.EMPRESA = AD.EMPRESA)      
                AND (TR.SEQN = AD.SEQN)       
                AND (TR.SERIE = AD.SERIE)        
                AND (TR.NUMERO = AD.NUMERO)       
                AND (TR.ORDEM = AD.ORDEM)              
                AND (TR.TIPODOC = AD.TIPODOC)        
                AND (AD.LIQUIDADO = 'S')        
                AND (seq = '02')              
                AND (TR.EMPRESA >= '18')        
                AND (TR.EMPRESA <= '18')        
                AND (TR.BANCO >= '04')             
                AND (TR.BANCO <= '23')             
                AND (TR.OPEBAN >= '')               
                AND (TR.OPEBAN <= 'zzzzzzzzz')             
                AND (TR.NUMERO >= '')             
                AND (TR.NUMERO <= 'zzzzzzzzzzzzz')   
                AND (TR.VALOR >= -9999999999999)    
                AND (TR.VALOR <= 9999999999999)      
                AND (TR.GRFLUX >= '')
                AND (TR.GRFLUX <= 'zzzzz')      
                AND (TR.SBFLUX >= '') 
                AND (TR.SBFLUX <= 'zzzzzz')     
                AND (TR.DUPLIC >= '')  
                AND (TR.DUPLIC <= 'zzzzzzzz') 
                AND (TR.REPRES >= '')  
                AND (TR.REPRES <= 'zzzzzz')     
                AND (TR.EMITENTE >= '')            
                AND (TR.EMITENTE <= 'zzzzzzzzzzz')     
                AND ( TR.TIPOEMIT = 'F' )           
                AND ( TR.EMITENTE = CL.CODIGO )       
            ORDER BY TR.EMITENTE,  TR.NUMERO
        ";

        //executa para pegar os dados
        $dados = $this->query($query);
        
        $campos = array('TR_empresa','TR_seqn','TR_serie','TR_numero','TR_tipodoc','TR_emitente','TR_tiptit','TR_ordem','TR_tipoemit','TR_seq','TR_razao','TR_dtemiss','TR_dtvencto','TR_dtlan','TR_dtbord','TR_dtpagto','TR_dtorig','TR_dtfluxo','TR_valor','TR_debcred','TR_banco','TR_juros','TR_desconto','TR_numbanc','TR_ccusto','TR_grflux','TR_sbflux','TR_historico','TR_numbord','TR_observ','TR_opeban','AD_liquidado','TR_formpag','TR_cartcred','TR_numcart','TR_motpro','TR_multa','TR_nossonum','TR_dignosso','TR_tipocob','TR_cartbanco','TR_valdiatr','TR_perdiatr','TR_obs','TR_arqcob','TR_incspc','TR_opercobr','TR_msg','CL_ATIVO');
        
        return array($campos,$dados);

    }//fim getCntReceber

    /**
     * [getCntReceberAberto relatorio de contas a receber em aberto]
     * @param  [type] $inicio [description]
     * @param  [type] $fim    [description]
     * @return [type]         [description]
     */
    public function getCntReceberAberto($inicio,$fim)
    {
        //query contas a receber liquidadas
        $query = "
            SELECT
                TR.empresa AS TR_empresa,
                TR.seqn AS TR_seqn,
                TR.serie AS TR_serie,
                TR.numero AS TR_numero,
                TR.tipodoc AS TR_tipodoc,
                TR.emitente AS TR_emitente,
                TR.tiptit AS TR_tiptit,
                TR.ordem AS TR_ordem,
                TR.tipoemit AS TR_tipoemit,
                TR.seq AS TR_seq,
                TR.razao AS TR_razao,
                TR.dtemiss AS TR_dtemiss,
                TR.dtvencto AS TR_dtvencto,
                TR.dtlan AS TR_dtlan,
                TR.dtbord AS TR_dtbord,
                TR.dtpagto AS TR_dtpagto,
                TR.dtorig AS TR_dtorig,
                TR.dtfluxo AS TR_dtfluxo,
                TR.valor AS TR_valor,
                TR.debcred AS TR_debcred,
                TR.banco AS TR_banco,
                TR.juros AS TR_juros,
                TR.desconto AS TR_desconto,
                TR.numbanc AS TR_numbanc,
                TR.ccusto AS TR_ccusto,
                TR.grflux AS TR_grflux,
                TR.sbflux AS TR_sbflux,
                TR.historico AS TR_historico,
                TR.numbord AS TR_numbord,
                TR.observ AS TR_observ,
                TR.opeban AS TR_opeban,
                AD.liquidado AS AD_liquidado,
                TR.formpag AS TR_formpag,
                TR.cartcred AS TR_cartcred,
                TR.numcart AS TR_numcart,
                TR.motpro AS TR_motpro,
                TR.multa AS TR_multa,
                TR.nossonum AS TR_nossonum,
                TR.dignosso AS TR_dignosso,
                TR.tipocob AS TR_tipocob,
                TR.cartbanco AS TR_cartbanco,
                TR.valdiatr AS TR_valdiatr,
                TR.perdiatr AS TR_perdiatr,
                TR.obs AS TR_obs,
                TR.arqcob AS TR_arqcob,
                TR.incspc AS TR_incspc,
                TR.opercobr AS TR_opercobr,
                TR.msg AS TR_msg,
                CL.ATIVO AS CL_ATIVO
            FROM dbNavegarqNatec.dbo.TRANREC TR, 
                dbNavegarqNatec.dbo.ADREC AD,
                dbNavegarqNatec.dbo.FORNEC CL 
            -- WHERE (TR.dtvencto >= '1900-04-15 00:00:00.000')
            --     AND (TR.dtvencto <= '2018-12-17 00:00:00.000')
            --     AND (TR.DTEMISS >= '1900-04-15 00:00:00.000')
            --     AND (TR.DTEMISS <= '2018-12-17 00:00:00.000')
            WHERE (TR.dtvencto >= '{$inicio} 00:00:00.000')
                AND (TR.dtvencto <= '{$fim} 00:00:00.000')
                AND (TR.DTEMISS >= '{$inicio} 00:00:00.000')
                AND (TR.DTEMISS <= '{$fim} 00:00:00.000')
                AND (TR.EMPRESA = AD.EMPRESA)
                AND (TR.SEQN = AD.SEQN)
                AND (TR.SERIE = AD.SERIE)
                AND (TR.NUMERO = AD.NUMERO)
                AND (TR.ORDEM = AD.ORDEM)
                AND (TR.TIPODOC = AD.TIPODOC)
                AND (AD.LIQUIDADO = 'N')
                AND (seq = '01')
                AND (TR.EMPRESA >= '18')
                AND (TR.EMPRESA <= '18')
                AND (TR.BANCO >= '04')
                AND (TR.BANCO <= '04')
                AND (TR.OPEBAN >= '')
                AND (TR.OPEBAN <= 'zzzzzzzzzz')
                AND (TR.NUMERO >= '')
                AND (TR.NUMERO <= 'zzzzzzzzzz')
                AND (TR.VALOR >= -9999999999999)
                AND (TR.VALOR <= 9999999999999)
                AND (TR.GRFLUX >= '')
                AND (TR.GRFLUX <= 'zzzz')
                AND (TR.SBFLUX >= '')
                AND (TR.SBFLUX <= 'zzzz')
                AND (TR.DUPLIC >= '')
                AND (TR.DUPLIC <= 'zzzzzzzzzz')
                AND (TR.REPRES >= '')
                AND (TR.REPRES <= 'zzzzzzzzz')
                AND (TR.EMITENTE >= '')
                AND (TR.EMITENTE <= 'zzzzzzzzz')
                AND ( TR.TIPOEMIT = 'C' )
                AND ( TR.EMITENTE = CL.CODIGO )
            UNION
            SELECT
                TR.empresa AS TR_empresa,
                TR.seqn AS TR_seqn,
                TR.serie AS TR_serie,
                TR.numero AS TR_numero,
                TR.tipodoc AS TR_tipodoc,
                TR.emitente AS TR_emitente,
                TR.tiptit AS TR_tiptit,
                TR.ordem AS TR_ordem,
                TR.tipoemit AS TR_tipoemit,
                TR.seq AS TR_seq,
                TR.razao AS TR_razao,
                TR.dtemiss AS TR_dtemiss,
                TR.dtvencto AS TR_dtvencto,
                TR.dtlan AS TR_dtlan,
                TR.dtbord AS TR_dtbord,
                TR.dtpagto AS TR_dtpagto,
                TR.dtorig AS TR_dtorig,
                TR.dtfluxo AS TR_dtfluxo,
                TR.valor AS TR_valor,
                TR.debcred AS TR_debcred,
                TR.banco AS TR_banco,
                TR.juros AS TR_juros,
                TR.desconto AS TR_desconto,
                TR.numbanc AS TR_numbanc,
                TR.ccusto AS TR_ccusto,
                TR.grflux AS TR_grflux,
                TR.sbflux AS TR_sbflux,
                TR.historico AS TR_historico,
                TR.numbord AS TR_numbord,
                TR.observ AS TR_observ,
                TR.opeban AS TR_opeban,
                AD.liquidado AS AD_liquidado,
                TR.formpag AS TR_formpag,
                TR.cartcred AS TR_cartcred,
                TR.numcart AS TR_numcart,
                TR.motpro AS TR_motpro,
                TR.multa AS TR_multa,
                TR.nossonum AS TR_nossonum,
                TR.dignosso AS TR_dignosso,
                TR.tipocob AS TR_tipocob,
                TR.cartbanco AS TR_cartbanco,
                TR.valdiatr AS TR_valdiatr,
                TR.perdiatr AS TR_perdiatr,
                TR.obs AS TR_obs,
                TR.arqcob AS TR_arqcob,
                TR.incspc AS TR_incspc,
                TR.opercobr AS TR_opercobr,
                TR.msg AS TR_msg,
                CL.ATIVO AS CL_ATIVO
            FROM dbNavegarqNatec.dbo.TRANREC TR, 
                dbNavegarqNatec.dbo.ADREC AD,
                dbNavegarqNatec.dbo.FORNEC CL 
            WHERE (TR.dtvencto >= '{$inicio} 00:00:00.000')
                AND (TR.dtvencto <= '{$fim} 00:00:00.000')
                AND (TR.DTEMISS >= '{$inicio} 00:00:00.000')
                AND (TR.DTEMISS <= '{$fim} 00:00:00.000')
                AND (TR.EMPRESA = AD.EMPRESA)
                AND (TR.SEQN = AD.SEQN)
                AND (TR.SERIE = AD.SERIE)
                AND (TR.NUMERO = AD.NUMERO)
                AND (TR.ORDEM = AD.ORDEM)
                AND (TR.TIPODOC = AD.TIPODOC)
                AND (AD.LIQUIDADO = 'N')
                AND (seq = '01')
                AND (TR.EMPRESA >= '18')
                AND (TR.EMPRESA <= '18')
                AND (TR.BANCO >= '04')
                AND (TR.BANCO <= '04')
                AND (TR.OPEBAN >= '')
                AND (TR.OPEBAN <= 'zzzzzzzzz')
                AND (TR.NUMERO >= '')
                AND (TR.NUMERO <= 'zzzzzzzzzzzzz')
                AND (TR.VALOR >= -9999999999999)
                AND (TR.VALOR <= 9999999999999)
                AND (TR.GRFLUX >= '')
                AND (TR.GRFLUX <= 'zzzzz')
                AND (TR.SBFLUX >= '')
                AND (TR.SBFLUX <= 'zzzzzz')
                AND (TR.DUPLIC >= '')
                AND (TR.DUPLIC <= 'zzzzzzzz')
                AND (TR.REPRES >= '')
                AND (TR.REPRES <= 'zzzzzz')
                AND (TR.EMITENTE >= '')
                AND (TR.EMITENTE <= 'zzzzzzzzzzz')
                AND ( TR.TIPOEMIT = 'F' )
                AND ( TR.EMITENTE = CL.CODIGO )
            ORDER BY TR.EMITENTE,  TR.NUMERO
        ";

        //executa para pegar os dados
        $dados = $this->query($query);
        
        $campos = array('TR_empresa','TR_seqn','TR_serie','TR_numero','TR_tipodoc','TR_emitente','TR_tiptit','TR_ordem','TR_tipoemit','TR_seq','TR_razao','TR_dtemiss','TR_dtvencto','TR_dtlan','TR_dtbord','TR_dtpagto','TR_dtorig','TR_dtfluxo','TR_valor','TR_debcred','TR_banco','TR_juros','TR_desconto','TR_numbanc','TR_ccusto','TR_grflux','TR_sbflux','TR_historico','TR_numbord','TR_observ','TR_opeban','AD_liquidado','TR_formpag','TR_cartcred','TR_numcart','TR_motpro','TR_multa','TR_nossonum','TR_dignosso','TR_tipocob','TR_cartbanco','TR_valdiatr','TR_perdiatr','TR_obs','TR_arqcob','TR_incspc','TR_opercobr','TR_msg','CL_ATIVO');
        
        return array($campos,$dados);

    }//fim getCntReceberAberto



}