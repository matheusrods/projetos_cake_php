<?php
class InscricaoEstadualBehavior extends ModelBehavior {
    
	//Acre
    private function checkIEAC($ie){
        if (strlen($ie) != 13){return 0;}
        else{
            if(substr($ie, 0, 2) != '01'){return 0;}
            else{
                $b = 4;
                $soma = 0;
                for ($i=0;$i<=10;$i++){
                    $soma += $ie[$i] * $b;
                    $b--;
                    if($b == 1){$b = 9;}
                }
                $dig = 11 - ($soma % 11);
                if($dig >= 10){$dig = 0;}
                if( !($dig == $ie[11]) ){return 0;}
                else{
                    $b = 5;
                    $soma = 0;
                    for($i=0;$i<=11;$i++){
                            $soma += $ie[$i] * $b;
                            $b--;
                            if($b == 1){$b = 9;}
                    }
                    $dig = 11 - ($soma % 11);
                    if($dig >= 10){$dig = 0;}

                    return ($dig == $ie[12]);
                }
            }
        }
    }

    // Alagoas
    private function checkIEAL($ie){
        if (strlen($ie) != 9){return 0;}
        else{
            if(substr($ie, 0, 2) != '24'){return 0;}
            else{
                $b = 9;
                $soma = 0;
                for($i=0;$i<=7;$i++){
                        $soma += $ie[$i] * $b;
                        $b--;
                }
                $soma *= 10;
                $dig = $soma - ( ( (int)($soma / 11) ) * 11 );
                if($dig == 10){$dig = 0;}

                return ($dig == $ie[8]);
            }
        }
    }

    //Amazonas
    private function checkIEAM($ie){
        if (strlen($ie) != 9){return 0;}
        else{
            $b = 9;
            $soma = 0;
            for($i=0;$i<=7;$i++){
                $soma += $ie[$i] * $b;
                $b--;
            }
            if($soma <= 11){$dig = 11 - $soma;}
            else{
                $r = $soma % 11;
                if($r <= 1){$dig = 0;}
                else{$dig = 11 - $r;}
            }

            return ($dig == $ie[8]);
        }
    }

    //Amapá
    private function checkIEAP($ie){
        if (strlen($ie) != 9){return 0;}
        else{
            if(substr($ie, 0, 2) != '03'){return 0;}
            else{
                $i = substr($ie, 0, -1);
                if( ($i >= 3000001) && ($i <= 3017000) ){$p = 5; $d = 0;}
                elseif( ($i >= 3017001) && ($i <= 3019022) ){$p = 9; $d = 1;}
                elseif ($i >= 3019023){$p = 0; $d = 0;}

                $b = 9;
                $soma = $p;
                for($i=0;$i<=7;$i++){
                    $soma += $ie[$i] * $b;
                    $b--;
                }
                $dig = 11 - ($soma % 11);
                if($dig == 10){$dig = 0;}
                elseif($dig == 11){$dig = $d;}

                return ($dig == $ie[8]);
            }
        }
    }

    // Bahia
    private function checkIEBA($ie) {
        $ie = preg_replace('/\D/', '', $ie);

        if (strlen($ie) == 8) {
            return $this->_checkIEBAOitoDigitos($ie);
        } elseif (strlen($ie) == 9) {
            return $this->_checkIEBANoveDigitos($ie);
        }

        return false;
    }
    // Bahia
    private function _checkIEBANoveDigitos($ie) {
        $peso_digito1 = range(9, 2, 1);
        $peso_digito2 = range(8, 2, 1);

        $digito2_diferenciado = in_array($ie[1], array(6, 7, 9));

        // Descobre digito verificador 2 ----------------------------------------
        $soma = 0;
        foreach ($peso_digito2 as $indice_caracter => $peso) {
            $soma += $ie[$indice_caracter] * $peso;
        }

        // Segundo digito = 6, 7 ou 9
        if ($digito2_diferenciado) {
            $resto = $soma % 11;
            $digito_verificador2 = 11 - $resto;
            if ($resto == 0 || $resto == 1) {
                $digito_verificador2 = 0;
            }
        } else {
            $resto = $soma % 10;
            $digito_verificador2 = 10 - $resto;
            if ($resto == 0) {
                $digito_verificador2 = 0;
            }
        }

        // descobre digito verificador 1 ----------------------------------------
        $soma = 0;
        foreach ($peso_digito1 as $indice_caracter => $peso) {
            if ($indice_caracter == 7) {
                // debug($peso .' * '. $digito_verificador2);
                $soma += $peso * $digito_verificador2;
            } else {
                // debug($peso .' * '. $ie[$indice_caracter]);
                $soma += $peso * $ie[$indice_caracter];
            }
        }

        // digito2 = 6, 7 ou 9
        if ($digito2_diferenciado) {
            $resto = $soma % 11;
            $digito_verificador1 = 11 - $resto;
            if ($resto == 0 || $resto == 1) {
                $digito_verificador1 = 0;
            }
        } else {
            $resto = $soma % 10;
            $digito_verificador1 = 10 - $resto;
            if ($resto == 0) {
                $digito_verificador1 = 0;
            }
        }

        return substr($ie, -2) == $digito_verificador1.$digito_verificador2;
    }
    // Bahia
    private function _checkIEBAOitoDigitos($ie) {
        $peso_digito1 = range(8, 2, 1);
        $peso_digito2 = range(7, 2, 1);

        $digito2_diferenciado = in_array($ie[0], array(6, 7, 9));

        // Descobre digito verificador 2 ----------------------------------------
        $soma = 0;
        foreach ($peso_digito2 as $indice_caracter => $peso) {
            $soma += $ie[$indice_caracter] * $peso;
        }

        // Segundo digito = 6, 7 ou 9
        if ($digito2_diferenciado) {
            $resto = $soma % 11;
            $digito_verificador2 = 11 - $resto;
            if ($resto == 0 || $resto == 1) {
                $digito2 = 0;
            }
        } else {
            $digito_verificador2 = $resto = $soma % 10;
            $digito_verificador2 = 10 - $resto;
            if ($resto == 0) {
                $digito_verificador2 = 0;
            }
        }

        // descobre digito verificador 1 ----------------------------------------
        $soma = 0;
        foreach ($peso_digito1 as $indice_caracter => $peso) {
            if ($indice_caracter == 6) {
                $soma += $peso * $digito_verificador2;
            } else {
                $soma += $peso * $ie[$indice_caracter];
            }
        }

        // digito2 = 6, 7 ou 9
        if ($digito2_diferenciado) {
            $resto = $soma % 11;
            $digito_verificador1 = 11 - $resto;
            if ($resto == 0 || $resto == 1) {
                $digito_verificador1 = 0;
            }
        } else {
            $resto = $soma % 10;
            $digito_verificador1 = 10 - $resto;
            if ($resto == 0) {
                $digito_verificador1 = 0;
            }
        }

        $digito1 = $resto;
        return substr($ie, -2) == $digito_verificador1.$digito_verificador2;
    }

    //Ceará
    private function checkIECE($ie){
        if (strlen($ie) != 9){return 0;}
        else{
            $b = 9;
            $soma = 0;
            for($i=0;$i<=7;$i++){
                $soma += $ie[$i] * $b;
                $b--;
            }
            $dig = 11 - ($soma % 11);

            if ($dig >= 10){$dig = 0;}

            return ($dig == $ie[8]);
        }
    }

    // Distrito Federal
    private function checkIEDF($ie){
        if (strlen($ie) != 13){return 0;}
        else{
            if( substr($ie, 0, 2) != '07' ){return 0;}
            else{
                $b = 4;
                $soma = 0;
                for ($i=0;$i<=10;$i++){
                    $soma += $ie[$i] * $b;
                    $b--;
                    if($b == 1){$b = 9;}
                }
                $dig = 11 - ($soma % 11);
                if($dig >= 10){$dig = 0;}

                if( !($dig == $ie[11]) ){return 0;}
                else{
                    $b = 5;
                    $soma = 0;
                    for($i=0;$i<=11;$i++){
                        $soma += $ie[$i] * $b;
                        $b--;
                        if($b == 1){$b = 9;}
                    }
                    $dig = 11 - ($soma % 11);
                    if($dig >= 10){$dig = 0;}

                    return ($dig == $ie[12]);
                }
            }
        }
    }

    //Espirito Santo
    private function checkIEES($ie){
        if (strlen($ie) != 9){return 0;}
        else{
            $b = 9;
            $soma = 0;
            for($i=0;$i<=7;$i++){
                $soma += $ie[$i] * $b;
                $b--;
            }
            $i = $soma % 11;
            if ($i < 2){$dig = 0;}
            else{$dig = 11 - $i;}

            return ($dig == $ie[8]);
        }
    }

    //Goias
    private function checkIEGO($ie){
        if (strlen($ie) != 9){return 0;}
        else{
            $s = substr($ie, 0, 2);

            if( !( ($s == 10) || ($s == 11) || ($s == 15) ) ){return 0;}
            else{
                $n = substr($ie, 0, 7);

                if($n == 11094402){
                    if($ie[8] != 0){
                        if($ie[8] != 1){
                            return 0;
                        }else{return 1;}
                    }else{return 1;}
                }else{
                    $b = 9;
                    $soma = 0;
                    for($i=0;$i<=7;$i++){
                        $soma += $ie[$i] * $b;
                        $b--;
                    }
                    $i = $soma % 11;
                    if ($i == 0){$dig = 0;}
                    else{
                        if($i == 1){
                            if(($n >= 10103105) && ($n <= 10119997)){$dig = 1;}
                            else{$dig = 0;}
                        }else{$dig = 11 - $i;}
                    }

                    return ($dig == $ie[8]);
                }
            }
        }
    }

    // Maranhão
    private function checkIEMA($ie){
        if (strlen($ie) != 9){return 0;}
        else{
            if(substr($ie, 0, 2) != 12){return 0;}
            else{
                $b = 9;
                $soma = 0;
                for($i=0;$i<=7;$i++){
                    $soma += $ie[$i] * $b;
                    $b--;
                }
                $i = $soma % 11;
                if ($i <= 1){$dig = 0;}
                else{$dig = 11 - $i;}

                return ($dig == $ie[8]);
            }
        }
    }

    // Mato Grosso
    private function checkIEMT($ie){
        if (strlen($ie) != 11){return 0;}
        else{
            $b = 3;
            $soma = 0;
            for($i=0;$i<=9;$i++){
                $soma += $ie[$i] * $b;
                $b--;
                if($b == 1){$b = 9;}
            }
            $i = $soma % 11;
            if ($i <= 1){$dig = 0;}
            else{$dig = 11 - $i;}

            return ($dig == $ie[10]);
        }
    }

    // Mato Grosso do Sul
    private function checkIEMS($ie){
        if (strlen($ie) != 9){return 0;}
        else{
            if(substr($ie, 0, 2) != 28){return 0;}
            else{
                $b = 9;
                $soma = 0;
                for($i=0;$i<=7;$i++){
                    $soma += $ie[$i] * $b;
                    $b--;
                }
                $i = $soma % 11;
                if ($i == 0){$dig = 0;}
                else{$dig = 11 - $i;}

                if($dig > 9){$dig = 0;}

                return ($dig == $ie[8]);
            }
        }
    }

    //Minas Gerais
    private function checkIEMG($ie){
        if (strlen($ie) != 13){return 0;}
        else{
            $ie2 = substr($ie, 0, 3) . '0' . substr($ie, 3,8);
            $multiplicador = 2;
            $total = 0;
            for ($i = 0 ; $i < strlen($ie2) ; $i++) {
                $multiplicador = $multiplicador == 2 ? 1 : 2;
                $numero = (substr($ie2, $i, 1) * $multiplicador);
                for ($i2 = 0 ; $i2 < strlen($numero) ; $i2++) {
                    $total += substr($numero, $i2, 1);
                }
            }
            $proxima_dezena = substr($total, 1, 1) != 0 ? (substr($total, 0, 1) + 1) * 10 : $total;
            $digito1 = $proxima_dezena - $total;
            if ($ie[11] != $digito1) return false;
            $ie2 = substr($ie, 0, 11) . $digito1;
            $multiplicador = 4;
            $total = 0;
            for ($i = 0 ; $i < strlen($ie2) ; $i++) {
                $multiplicador--;
                if ($multiplicador < 2)
                    $multiplicador = 11;
                $total += substr($ie2, $i, 1) * $multiplicador;
            }
            $resto = ($total % 11);
            if($resto == 0 || $resto == 1) {
                $digito2 = 0;
            } else {
                $digito2 = (11 - $resto);
            }
            return ($ie[12] == $digito2);
        }
    }

    //Pará
    private function checkIEPA($ie){
        if (strlen($ie) != 9){return 0;}
        else{
            if(substr($ie, 0, 2) != 15){return 0;}
            else{
                $b = 9;
                $soma = 0;
                for($i=0;$i<=7;$i++){
                    $soma += $ie[$i] * $b;
                    $b--;
                }
                $i = $soma % 11;
                if ($i <= 1){$dig = 0;}
                else{$dig = 11 - $i;}

                return ($dig == $ie[8]);
            }
        }
    }

    //Paraíba
    private function checkIEPB($ie){
        if (strlen($ie) != 9){return 0;}
        else{
            $b = 9;
            $soma = 0;
            for($i=0;$i<=7;$i++){
                $soma += $ie[$i] * $b;
                $b--;
            }
            $i = $soma % 11;
            if ($i <= 1){$dig = 0;}
            else{$dig = 11 - $i;}

            if($dig > 9){$dig = 0;}

            return ($dig == $ie[8]);
        }
    }

    //Paraná
    private function checkIEPR($ie){
        if (strlen($ie) != 10){return 0;}
        else{
            $b = 3;
            $soma = 0;
            for($i=0;$i<=7;$i++){
                $soma += $ie[$i] * $b;
                $b--;
                if($b == 1){$b = 7;}
            }
            $i = $soma % 11;
            if ($i <= 1){$dig = 0;}
            else{$dig = 11 - $i;}

            if ( !($dig == $ie[8]) ){return 0;}
            else{
                $b = 4;
                $soma = 0;
                for($i=0;$i<=8;$i++){
                    $soma += $ie[$i] * $b;
                    $b--;
                    if($b == 1){$b = 7;}
                }
                $i = $soma % 11;
                if($i <= 1){$dig = 0;}
                else{$dig = 11 - $i;}

                return ($dig == $ie[9]);
            }
        }
    }

    //Pernambuco
    private function checkIEPE($ie){
        if (strlen($ie) == 9){
            $b = 8;
            $soma = 0;
            for($i=0;$i<=6;$i++){
                $soma += $ie[$i] * $b;
                $b--;
            }
            $i = $soma % 11;
            if ($i <= 1){$dig = 0;}
            else{$dig = 11 - $i;}

            if ( !($dig == $ie[7]) ){return 0;}
            else{
                $b = 9;
                $soma = 0;
                for($i=0;$i<=7;$i++){
                    $soma += $ie[$i] * $b;
                    $b--;
                }
                $i = $soma % 11;
                if ($i <= 1){$dig = 0;}
                else{$dig = 11 - $i;}

                return ($dig == $ie[8]);
            }
        }
        elseif(strlen($ie) == 14){
            $b = 5;
            $soma = 0;
            for($i=0;$i<=12;$i++){
                $soma += $ie[$i] * $b;
                $b--;
                if($b == 0){$b = 9;}
            }
            $dig = 11 - ($soma % 11);
            if($dig > 9){$dig = $dig - 10;}

            return ($dig == $ie[13]);
        }
        else{return 0;}
    }

    //Piauí
    private function checkIEPI($ie){
        if (strlen($ie) != 9){return 0;}
        else{
            $b = 9;
            $soma = 0;
            for($i=0;$i<=7;$i++){
                $soma += $ie[$i] * $b;
                $b--;
            }
            $i = $soma % 11;
            if($i <= 1){$dig = 0;}
            else{$dig = 11 - $i;}
            if($dig >= 10){$dig = 0;}

            return ($dig == $ie[8]);
        }
    }

    // Rio de Janeiro
    private function checkIERJ($ie){
        if (strlen($ie) != 8){return 0;}
        else{
            $b = 2;
            $soma = 0;
            for($i=0;$i<=6;$i++){
                $soma += $ie[$i] * $b;
                $b--;
                if($b == 1){$b = 7;}
            }
            $i = $soma % 11;
            if ($i <= 1){$dig = 0;}
            else{$dig = 11 - $i;}

            return ($dig == $ie[7]);
        }
    }

    //Rio Grande do Norte
    private function checkIERN($ie){
        if( !( (strlen($ie) == 9) || (strlen($ie) == 10) ) ){return 0;}
        else{
            $b = strlen($ie);
            if($b == 9){$s = 7;}
            else{$s = 8;}
            $soma = 0;
            for($i=0;$i<=$s;$i++){
                $soma += $ie[$i] * $b;
                $b--;
            }
            $soma *= 10;
            $dig = $soma % 11;
            if($dig == 10){$dig = 0;}

            $s += 1;
            return ($dig == $ie[$s]);
        }
    }

    // Rio Grande do Sul
    private function checkIERS($ie){
        if (strlen($ie) != 10){return 0;}
        else{
            $b = 2;
            $soma = 0;
            for($i=0;$i<=8;$i++){
                $soma += $ie[$i] * $b;
                $b--;
                if ($b == 1){$b = 9;}
            }
            $dig = 11 - ($soma % 11);
            if($dig >= 10){$dig = 0;}

            return ($dig == $ie[9]);
        }
    }

    // Rondônia
    private function checkIERO($ie){
        if (strlen($ie) == 9){
            $b=6;
            $soma =0;
            for($i=3;$i<=7;$i++){
                $soma += $ie[$i] * $b;
                $b--;
            }
            $dig = 11 - ($soma % 11);
            if($dig >= 10){$dig = $dig - 10;}

            return ($dig == $ie[8]);
        }
        elseif(strlen($ie) == 14){
            $b=6;
            $soma=0;
            for($i=0;$i<=12;$i++) {
                $soma += $ie[$i] * $b;
                $b--;
                if($b == 1){$b = 9;}
            }
            $dig = 11 - ( $soma % 11);
            if ($dig > 9){$dig = $dig - 10;}

            return ($dig == $ie[13]);
        }
        else{return 0;}
    }

    //Roraima
    private function checkIERR($ie){
        if (strlen($ie) != 9){return 0;}
        else{
            if(substr($ie, 0, 2) != 24){return 0;}
            else{
                $b = 1;
                $soma = 0;
                for($i=0;$i<=7;$i++){
                    $soma += $ie[$i] * $b;
                    $b++;
                }
                $dig = $soma % 9;

                return ($dig == $ie[8]);
            }
        }
    }

    //Santa Catarina
    private function checkIESC($ie){
        if (strlen($ie) != 9){return 0;}
        else{
            $b = 9;
            $soma = 0;
            for($i=0;$i<=7;$i++){
                $soma += $ie[$i] * $b;
                $b--;
            }
            $resto = ($soma % 11);
            if ($resto <= 1)
                $dig = 0;
            else
                $dig = 11 - $resto;
            return ($dig == $ie[8]);
        }
    }

    //São Paulo
    private function checkIESP($ie){
        if( strtoupper( substr($ie, 0, 1) )  == 'P' ){
            if (strlen($ie) != 13){return 0;}
            else{
                $b = 1;
                $soma = 0;
                for($i=1;$i<=8;$i++){
                    $soma += $ie[$i] * $b;
                    $b++;
                    if($b == 2){$b = 3;}
                    if($b == 9){$b = 10;}
                }
                $dig = $soma % 11;
                return ($dig == $ie[9]);
            }
        }else{
            if (strlen($ie) != 12){return 0;}
            else{
                $b = 1;
                $soma = 0;
                for($i=0;$i<=7;$i++){
                    $soma += $ie[$i] * $b;
                    $b++;
                    if($b == 2){$b = 3;}
                    if($b == 9){$b = 10;}
                }
                $dig = $soma % 11;
                if($dig > 9){$dig = 0;}

                if($dig != $ie[8]){return 0;}
                else{
                    $b = 3;
                    $soma = 0;
                    for($i=0;$i<=10;$i++){
                        $soma += $ie[$i] * $b;
                        $b--;
                        if($b == 1){$b = 10;}
                    }
                    $dig = $soma % 11;
                    if($dig > 9){$dig = 0;}
                    return ($dig == $ie[11]);
                }
            }
        }
    }

    //Sergipe
    private function checkIESE($ie){
        if (strlen($ie) != 9){return 0;}
        else{
            $b = 9;
            $soma = 0;
            for($i=0;$i<=7;$i++){
                $soma += $ie[$i] * $b;
                $b--;
            }
            $dig = 11 - ($soma % 11);
            if ($dig > 9){$dig = 0;}

            return ($dig == $ie[8]);
        }
    }

    //Tocantins
    private function checkIETO($ie){
        if (strlen($ie) == 11) {
            $s = substr($ie, 2, 2);
            if( !( ($s=='01') || ($s=='02') || ($s=='03') || ($s=='99') ) ){return 0;}
            else{
                $b=9;
                $soma=0;
                for($i=0;$i<=9;$i++){
                    if( !(($i == 2) || ($i == 3)) ){
                        $soma += $ie[$i] * $b;
                        $b--;
                    }
                }
                $i = $soma % 11;
                if($i < 2){$dig = 0;}
                else{$dig = 11 - $i;}

                return ($dig == $ie[10]);
             }
        }
        elseif(strlen($ie) == 9) {
            $digito = substr($ie, 8, 1);
            $soma = 0;
            for($i = 0, $n = 9; $i < strlen($ie) - 1; $i++, $n--) {
                $soma += (substr($ie, $i, 1) * $n);
            }
            $resto = $soma % 11;
            return $resto < 2 ? ($digito == 0): ($digito == 11 - $resto);
        }
        else {
            return 0;
        }
    }

    public function checkIE(&$model, $ie, $uf){
        if( trim(strtoupper($ie)) == 'ISENTO' ){
            return true;
        } else {
            $uf = strtoupper($uf);
            $ie = ereg_replace("[()-./,:]", "", $ie);
            $comando = '$valida = $this->CheckIE'.$uf.'("'.$ie.'");';
            eval($comando);
            return $valida;
        }
    }
}
?>