<?php
App::import('Component', 'StringView');

/**
 * Script para disparar email de validação de pré-faturamento para usuarios configurados
 * 
 * Modo de usar:
 * 
 *  cake/console/cake -app ./app validacao_faturamento validarFaturamento
 * 
 * @author Lyndon Marques 12/08/2021
 */
class ValidacaoFaturamentoShell extends Shell {
    
    //atributo que instancia as models
    var $uses = array(
		'Usuario', 
        'Configuracao',
        'PreFaturamento'
    	);   

    /**
     * Metodo para iniciar o script como o contrutor da classe
     */
    public function main()
    {
    	echo "Script de importacao\n";
        echo "cake/console/cake -app ./app validacao_faturamento getArquivo <opcional: nome_arquivo>\n";
    	

    } //fim main


    public function validarFaturamento()
    {
            
        $configuracao = $this->Configuracao->getValidacaoFaturamento();

        if (!empty($configuracao)) {

            //Dia que será disparado email
            $dia_do_disparo = $configuracao['Configuracao']['valor'];
                            
            $data_atual = date('Y-m-d H:i:s');
            $dia_atual = date('d');
            $mes_atual = date('m');
            $ano_atual = date('Y');
            $dia_final_do_mes = date('t');

            $dia_final_fevereiro = cal_days_in_month(CAL_GREGORIAN, 2, $ano_atual);

            if ($mes_atual == 2) {
                echo "Estou em fevereiro \n";

                if ($dia_do_disparo > $dia_final_fevereiro) {
                    $dia_do_disparo = $dia_final_fevereiro;
                    echo "Fevereiro erro {$dia_do_disparo} \n";
                }
                
                if ($dia_do_disparo <= $dia_final_do_mes) {

                    if ($dia_do_disparo == $dia_atual) {

                        //Retorna todos os usuários que estão configurados para receber notificação de Validação de pré-faturamento
                         $usuarios = $this->Usuario->getUsuariosValidacaoFaturamento();                   
                
                        foreach ($usuarios as $usuario) {

                            if (!empty($usuario['Usuario']['email'])) {
                                $this->PreFaturamento->scheduleMailPreFaturamento($usuario['Usuario']['email']);
    
                                echo "Disparando email para usuario: {$usuario['Usuario']['nome']} | código: {$usuario['Usuario']['codigo']}... \n";
                            }
                        }    
                    }  
                } 

            } else {

                //Se o dia do disparo for menor ou igual ao ultimo dia do mês, prossegue a função
                if ($dia_do_disparo <= $dia_final_do_mes) {
                   
                    if ($dia_do_disparo == $dia_atual) {

                        //Retorna todos os usuários que estão configurados para receber notificação de Validação de pré-faturamento
                        $usuarios = $this->Usuario->getUsuariosValidacaoFaturamento();                   
                
                        foreach ($usuarios as $usuario) {

                            if (!empty($usuario['Usuario']['email'])) {
                                $this->PreFaturamento->scheduleMailPreFaturamento($usuario['Usuario']['email']);
    
                                echo "Disparando email para usuario: {$usuario['Usuario']['nome']} | código: {$usuario['Usuario']['codigo']}... \n";
                            }
                        }    
                    }                                    
                }
            }
            exit;       
        }        
    }
       
}//fim class
?>