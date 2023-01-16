import Itau.Itaucripto;

public class Itau {
    public static void main(String args[]) {
        
        String codEmp; // Código de identificação da empresa
        String chave; // Chave de criptografia da empresa
        String codSacado; // Código do Sacado - CNPJ/CPF
        String dados; //Armazena os dados criptografados
        
        //Inicializa as variáveis
        // <- Coloque aqui seu Código de Empresa (26 posições)
        codEmp = "J0201837260001140000001756";
        // <- Coloque aqui sua chave de criptografia (16 posições)
        chave = "6A41G47W92X06HKP";
        // <- Coloque aqui o CNPJ/CPF do sacado
        //codSacado = "01586624000103";
        codSacado = args[0];
        //Inicializa a classe de criptografia
        Itaucripto cripto = new Itaucripto();

        //Criptografa os dados chamando o método geraCripto da classe Itaucripto
        dados = cripto.geraCripto(codEmp,codSacado,chave);

        System.out.println(dados);
        
    }
}