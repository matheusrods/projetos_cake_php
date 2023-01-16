
<?php 

// TODO - colocar aqui, valores vindos na url
$grafico = 'lyn'; 
$accessToken = "H4sIAAAAAAAEAC2Tx66EVgBD_-VtiTSUoUXKgg5D7zA7eu_1EuXf8yJlb8nysf33j5WAfkrynz9_nFfQuhMXDjIh1GukPGQboXAxhkAXNutj4CaQM9d4l3fk8i1umWrSB7JtuZhOFYiDdqePGvNuCQIOBeqVtaLMyMdQHkp_1hJHEsZAWiLmos-RaYIDSuTbp7Qrz5Jm-GDJQ3RoMBD6J2abXXFf0nIPGP6NHtzuTXJ948osFwFXG7euvIOBj3KKzAAFj8OTnV8vqXgXCEd6w1sE7-Ry2lED-niTqizrmpru4lWKvLJmqNOUorbEZSk8oLE2wmAfu2rxZvOrt6OCnCOCUjFLkumuh912E3u_FjP5gKih4AJGO5tvE77VE4HgqyykqtPFsnlbq-cg61jsNkbqEa6TLaRRGNxNGTGcB2OeARccTEFcdPi6lt9Q22ejWuaV4FOXvYdCxJjcICdHN32ElgujG_yEEjJvraBlMHOzdQ9MuROQzA0vsQIgmi7cU6d7czg_29dVGvPU0q69Pm-Tg0NyAxAVUmJpU0K3cNS-IUnVJLPdTwofnOzIS7gzErbQ61Kh23c7h3s9JynJWq2WjUjMtDQ2bM275tTZFmAOofsradljuxYdggJxOMJmHWd7VG2h-vXBvMJE3Svl9a49hWPbB_3lHOjLoxZf9tu12aTL1oHjE9fADnHGt-aewPiqlQ8XVJYk7FixSAw3crd1kUtfBJRGn3OnLU2QfdSLCFXg5ohgBkmgAe8gn3vgMFVnywW5JUhnnoN4kGib4dT-Mt3K3DHzKnROJX2Vs-BF9KE8K2hn8SKBEpl7NPTBcFV67FUagkpff7O5L-Q7gW-FVaeYzppllUZZ0QJ16XqvSuwxph8AEvUsSdZfN_QqJfkjAxRLgzLFfv744VYw75NagN87RUHe2scJe8jq3iT90dqE-bIalfKDHHcvjHp7pW5gNFnZK2EYl_K6Iooa73A3YMvsMzHKy7Anq67FGwuvRR18ShXERZ-7MGxwuW7l3kc6iCYyc-pWLlafhSNxL9FRR_pBTcLSXS7V2YzZ6hpTPvx3Pkoi2CcBglYe2wNljSwFofXnra4sbNhfcvs-K5skRszJ8G_z7QBEUtPK1sPgBzyHKistVRGr3k0Nc51RMYtAVy5XMuyMh5yg5CZdKv31hnvSC6-USEMcjSqTcqPZA8Q4ERkqTM12s6bH-ZQ0u3miL567wWfX0xFcTBpFDPPjyAQN8wXNBG0rc-FTw9PA16HmX47911__YQZzXaxK8Es5ZHsWvMNP5LFNuLLZBSzjf5XbVGOyH2vxKzvqOWWFyRS1pQZSUXuykLMCbFd5WNYu5yCR-BAVm4hx8-mgy0wNa9vDpOWMbfS23s5vNsNl76TknUF-Z9Z802PuyvgVCp82g6j6MiUiNvPQCVlNTq6YIy-2oOZicfF7EzfmFiM_wSY352D066AYzHNSnrjzc6TpehvZ2A15v_j4c-So4LjuyVM8q5U6e3TbNz2ZlmhXX40D9MgZoxgfdvwMr70yG0rqEDR-mbKdeublFGS7zITFP2VHoDoMrXdG6N8mfnYEMeGo7uNxo8mVYxWsbuOKtSmM75nupWiDFZS_7xYe5QIOCi0Fubl-ngn4yUnv6cP7A1BmwqZfSj71SVKf-fWL-Z9_AZ0moKRCBgAA.eyJjbHVzdGVyVXJsIjoiaHR0cHM6Ly9XQUJJLUJSQVpJTC1TT1VUSC1CLVBSSU1BUlktcmVkaXJlY3QuYW5hbHlzaXMud2luZG93cy5uZXQiLCJlbWJlZEZlYXR1cmVzIjp7Im1vZGVybkVtYmVkIjpmYWxzZX19";
$reportId = "22715f02-063f-4c43-b11d-278afa3e3e3f";
$groupId = "6e793889-5cb8-474d-9479-e05d650f0214";

?>
<!-- <head>
<script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
<script src="https://npmcdn.com/es6-promise@3.2.1"></script>
<script src="scripts/powerbi.js"></script>
</head> -->

<?php
echo $this->Buonny->link_js('jquery');
echo $this->Buonny->link_js('powerbi/powerbi'); 


// class BI {
    
//     private $POWERBI = array(
//         'URL_API' => "https://api.powerbi.com/v1.0/myorg",
//         'ACCESS_TOKEN' => "H4sIAAAAAAAEAC2Tx66EVgBD_-VtiTSUoUXKgg5D7zA7eu_1EuXf8yJlb8nysf33j5WAfkrynz9_nFfQuhMXDjIh1GukPGQboXAxhkAXNutj4CaQM9d4l3fk8i1umWrSB7JtuZhOFYiDdqePGvNuCQIOBeqVtaLMyMdQHkp_1hJHEsZAWiLmos-RaYIDSuTbp7Qrz5Jm-GDJQ3RoMBD6J2abXXFf0nIPGP6NHtzuTXJ948osFwFXG7euvIOBj3KKzAAFj8OTnV8vqXgXCEd6w1sE7-Ry2lED-niTqizrmpru4lWKvLJmqNOUorbEZSk8oLE2wmAfu2rxZvOrt6OCnCOCUjFLkumuh912E3u_FjP5gKih4AJGO5tvE77VE4HgqyykqtPFsnlbq-cg61jsNkbqEa6TLaRRGNxNGTGcB2OeARccTEFcdPi6lt9Q22ejWuaV4FOXvYdCxJjcICdHN32ElgujG_yEEjJvraBlMHOzdQ9MuROQzA0vsQIgmi7cU6d7czg_29dVGvPU0q69Pm-Tg0NyAxAVUmJpU0K3cNS-IUnVJLPdTwofnOzIS7gzErbQ61Kh23c7h3s9JynJWq2WjUjMtDQ2bM275tTZFmAOofsradljuxYdggJxOMJmHWd7VG2h-vXBvMJE3Svl9a49hWPbB_3lHOjLoxZf9tu12aTL1oHjE9fADnHGt-aewPiqlQ8XVJYk7FixSAw3crd1kUtfBJRGn3OnLU2QfdSLCFXg5ohgBkmgAe8gn3vgMFVnywW5JUhnnoN4kGib4dT-Mt3K3DHzKnROJX2Vs-BF9KE8K2hn8SKBEpl7NPTBcFV67FUagkpff7O5L-Q7gW-FVaeYzppllUZZ0QJ16XqvSuwxph8AEvUsSdZfN_QqJfkjAxRLgzLFfv744VYw75NagN87RUHe2scJe8jq3iT90dqE-bIalfKDHHcvjHp7pW5gNFnZK2EYl_K6Iooa73A3YMvsMzHKy7Anq67FGwuvRR18ShXERZ-7MGxwuW7l3kc6iCYyc-pWLlafhSNxL9FRR_pBTcLSXS7V2YzZ6hpTPvx3Pkoi2CcBglYe2wNljSwFofXnra4sbNhfcvs-K5skRszJ8G_z7QBEUtPK1sPgBzyHKistVRGr3k0Nc51RMYtAVy5XMuyMh5yg5CZdKv31hnvSC6-USEMcjSqTcqPZA8Q4ERkqTM12s6bH-ZQ0u3miL567wWfX0xFcTBpFDPPjyAQN8wXNBG0rc-FTw9PA16HmX47911__YQZzXaxK8Es5ZHsWvMNP5LFNuLLZBSzjf5XbVGOyH2vxKzvqOWWFyRS1pQZSUXuykLMCbFd5WNYu5yCR-BAVm4hx8-mgy0wNa9vDpOWMbfS23s5vNsNl76TknUF-Z9Z802PuyvgVCp82g6j6MiUiNvPQCVlNTq6YIy-2oOZicfF7EzfmFiM_wSY352D066AYzHNSnrjzc6TpehvZ2A15v_j4c-So4LjuyVM8q5U6e3TbNz2ZlmhXX40D9MgZoxgfdvwMr70yG0rqEDR-mbKdeublFGS7zITFP2VHoDoMrXdG6N8mfnYEMeGo7uNxo8mVYxWsbuOKtSmM75nupWiDFZS_7xYe5QIOCi0Fubl-ngn4yUnv6cP7A1BmwqZfSj71SVKf-fWL-Z9_AZ0moKRCBgAA.eyJjbHVzdGVyVXJsIjoiaHR0cHM6Ly9XQUJJLUJSQVpJTC1TT1VUSC1CLVBSSU1BUlktcmVkaXJlY3QuYW5hbHlzaXMud2luZG93cy5uZXQiLCJlbWJlZEZlYXR1cmVzIjp7Im1vZGVybkVtYmVkIjpmYWxzZX19"
//     );
    
//     function obterAccessToken(){
//         return "H4sIAAAAAAAEAC2Tx66EVgBD_-VtiTSUoUXKgg5D7zA7eu_1EuXf8yJlb8nysf33j5WAfkrynz9_nFfQuhMXDjIh1GukPGQboXAxhkAXNutj4CaQM9d4l3fk8i1umWrSB7JtuZhOFYiDdqePGvNuCQIOBeqVtaLMyMdQHkp_1hJHEsZAWiLmos-RaYIDSuTbp7Qrz5Jm-GDJQ3RoMBD6J2abXXFf0nIPGP6NHtzuTXJ948osFwFXG7euvIOBj3KKzAAFj8OTnV8vqXgXCEd6w1sE7-Ry2lED-niTqizrmpru4lWKvLJmqNOUorbEZSk8oLE2wmAfu2rxZvOrt6OCnCOCUjFLkumuh912E3u_FjP5gKih4AJGO5tvE77VE4HgqyykqtPFsnlbq-cg61jsNkbqEa6TLaRRGNxNGTGcB2OeARccTEFcdPi6lt9Q22ejWuaV4FOXvYdCxJjcICdHN32ElgujG_yEEjJvraBlMHOzdQ9MuROQzA0vsQIgmi7cU6d7czg_29dVGvPU0q69Pm-Tg0NyAxAVUmJpU0K3cNS-IUnVJLPdTwofnOzIS7gzErbQ61Kh23c7h3s9JynJWq2WjUjMtDQ2bM275tTZFmAOofsradljuxYdggJxOMJmHWd7VG2h-vXBvMJE3Svl9a49hWPbB_3lHOjLoxZf9tu12aTL1oHjE9fADnHGt-aewPiqlQ8XVJYk7FixSAw3crd1kUtfBJRGn3OnLU2QfdSLCFXg5ohgBkmgAe8gn3vgMFVnywW5JUhnnoN4kGib4dT-Mt3K3DHzKnROJX2Vs-BF9KE8K2hn8SKBEpl7NPTBcFV67FUagkpff7O5L-Q7gW-FVaeYzppllUZZ0QJ16XqvSuwxph8AEvUsSdZfN_QqJfkjAxRLgzLFfv744VYw75NagN87RUHe2scJe8jq3iT90dqE-bIalfKDHHcvjHp7pW5gNFnZK2EYl_K6Iooa73A3YMvsMzHKy7Anq67FGwuvRR18ShXERZ-7MGxwuW7l3kc6iCYyc-pWLlafhSNxL9FRR_pBTcLSXS7V2YzZ6hpTPvx3Pkoi2CcBglYe2wNljSwFofXnra4sbNhfcvs-K5skRszJ8G_z7QBEUtPK1sPgBzyHKistVRGr3k0Nc51RMYtAVy5XMuyMh5yg5CZdKv31hnvSC6-USEMcjSqTcqPZA8Q4ERkqTM12s6bH-ZQ0u3miL567wWfX0xFcTBpFDPPjyAQN8wXNBG0rc-FTw9PA16HmX47911__YQZzXaxK8Es5ZHsWvMNP5LFNuLLZBSzjf5XbVGOyH2vxKzvqOWWFyRS1pQZSUXuykLMCbFd5WNYu5yCR-BAVm4hx8-mgy0wNa9vDpOWMbfS23s5vNsNl76TknUF-Z9Z802PuyvgVCp82g6j6MiUiNvPQCVlNTq6YIy-2oOZicfF7EzfmFiM_wSY352D066AYzHNSnrjzc6TpehvZ2A15v_j4c-So4LjuyVM8q5U6e3TbNz2ZlmhXX40D9MgZoxgfdvwMr70yG0rqEDR-mbKdeublFGS7zITFP2VHoDoMrXdG6N8mfnYEMeGo7uNxo8mVYxWsbuOKtSmM75nupWiDFZS_7xYe5QIOCi0Fubl-ngn4yUnv6cP7A1BmwqZfSj71SVKf-fWL-Z9_AZ0moKRCBgAA.eyJjbHVzdGVyVXJsIjoiaHR0cHM6Ly9XQUJJLUJSQVpJTC1TT1VUSC1CLVBSSU1BUlktcmVkaXJlY3QuYW5hbHlzaXMud2luZG93cy5uZXQiLCJlbWJlZEZlYXR1cmVzIjp7Im1vZGVybkVtYmVkIjpmYWxzZX19";
//     }

//     function obterToken(){

//         $data = array(
//             'accessLevel'=> 'Edit'
//         );

//         $cURL = curl_init();

//         curl_setopt( $cURL, CURLOPT_URL, $POWERBI['URL_API'] .'/groups/22715f02-063f-4c43-b11d-278afa3e3e3f/reports/6e793889-5cb8-474d-9479-e05d650f0214/GenerateToken' );
//         curl_setopt( $cURL, CURLOPT_POST, true );
//         curl_setopt( $cURL, CURLOPT_POSTFIELDS, $data);
//         curl_setopt( $cURL, CURLOPT_RETURNTRANSFER, true );
        
//         $result = curl_exec( $cURL );
//         $result = json_decode($result);

//         curl_close ($cURL);

//         return $result;
//     }

//     function request($absFileName){

//         $data = array(
//         );

//         $cURL = curl_init();
//         curl_setopt( $cURL, CURLOPT_URL, $POWERBI['URL_API'] .'/groups/22715f02-063f-4c43-b11d-278afa3e3e3f/reports/6e793889-5cb8-474d-9479-e05d650f0214/GenerateToken' );
//         curl_setopt( $cURL, CURLOPT_POST, true );
//         curl_setopt( $cURL, CURLOPT_POSTFIELDS, $data);
//         curl_setopt( $cURL, CURLOPT_RETURNTRANSFER, true );
        
//         $result = curl_exec( $cURL );

//         $result = json_decode($result);
//         curl_close ($cURL);

//         return $result;
//     }
// }

// $bi = new BI();

// $bi->obterAccessToken() // "H4sIAAAAAAAEAC2Tx66EVgBD_-VtiTSUoUXKgg5D7zA7eu_1EuXf8yJlb8nysf33j5WAfkrynz9_nFfQuhMXDjIh1GukPGQboXAxhkAXNutj4CaQM9d4l3fk8i1umWrSB7JtuZhOFYiDdqePGvNuCQIOBeqVtaLMyMdQHkp_1hJHEsZAWiLmos-RaYIDSuTbp7Qrz5Jm-GDJQ3RoMBD6J2abXXFf0nIPGP6NHtzuTXJ948osFwFXG7euvIOBj3KKzAAFj8OTnV8vqXgXCEd6w1sE7-Ry2lED-niTqizrmpru4lWKvLJmqNOUorbEZSk8oLE2wmAfu2rxZvOrt6OCnCOCUjFLkumuh912E3u_FjP5gKih4AJGO5tvE77VE4HgqyykqtPFsnlbq-cg61jsNkbqEa6TLaRRGNxNGTGcB2OeARccTEFcdPi6lt9Q22ejWuaV4FOXvYdCxJjcICdHN32ElgujG_yEEjJvraBlMHOzdQ9MuROQzA0vsQIgmi7cU6d7czg_29dVGvPU0q69Pm-Tg0NyAxAVUmJpU0K3cNS-IUnVJLPdTwofnOzIS7gzErbQ61Kh23c7h3s9JynJWq2WjUjMtDQ2bM275tTZFmAOofsradljuxYdggJxOMJmHWd7VG2h-vXBvMJE3Svl9a49hWPbB_3lHOjLoxZf9tu12aTL1oHjE9fADnHGt-aewPiqlQ8XVJYk7FixSAw3crd1kUtfBJRGn3OnLU2QfdSLCFXg5ohgBkmgAe8gn3vgMFVnywW5JUhnnoN4kGib4dT-Mt3K3DHzKnROJX2Vs-BF9KE8K2hn8SKBEpl7NPTBcFV67FUagkpff7O5L-Q7gW-FVaeYzppllUZZ0QJ16XqvSuwxph8AEvUsSdZfN_QqJfkjAxRLgzLFfv744VYw75NagN87RUHe2scJe8jq3iT90dqE-bIalfKDHHcvjHp7pW5gNFnZK2EYl_K6Iooa73A3YMvsMzHKy7Anq67FGwuvRR18ShXERZ-7MGxwuW7l3kc6iCYyc-pWLlafhSNxL9FRR_pBTcLSXS7V2YzZ6hpTPvx3Pkoi2CcBglYe2wNljSwFofXnra4sbNhfcvs-K5skRszJ8G_z7QBEUtPK1sPgBzyHKistVRGr3k0Nc51RMYtAVy5XMuyMh5yg5CZdKv31hnvSC6-USEMcjSqTcqPZA8Q4ERkqTM12s6bH-ZQ0u3miL567wWfX0xFcTBpFDPPjyAQN8wXNBG0rc-FTw9PA16HmX47911__YQZzXaxK8Es5ZHsWvMNP5LFNuLLZBSzjf5XbVGOyH2vxKzvqOWWFyRS1pQZSUXuykLMCbFd5WNYu5yCR-BAVm4hx8-mgy0wNa9vDpOWMbfS23s5vNsNl76TknUF-Z9Z802PuyvgVCp82g6j6MiUiNvPQCVlNTq6YIy-2oOZicfF7EzfmFiM_wSY352D066AYzHNSnrjzc6TpehvZ2A15v_j4c-So4LjuyVM8q5U6e3TbNz2ZlmhXX40D9MgZoxgfdvwMr70yG0rqEDR-mbKdeublFGS7zITFP2VHoDoMrXdG6N8mfnYEMeGo7uNxo8mVYxWsbuOKtSmM75nupWiDFZS_7xYe5QIOCi0Fubl-ngn4yUnv6cP7A1BmwqZfSj71SVKf-fWL-Z9_AZ0moKRCBgAA.eyJjbHVzdGVyVXJsIjoiaHR0cHM6Ly9XQUJJLUJSQVpJTC1TT1VUSC1CLVBSSU1BUlktcmVkaXJlY3QuYW5hbHlzaXMud2luZG93cy5uZXQiLCJlbWJlZEZlYXR1cmVzIjp7Im1vZGVybkVtYmVkIjpmYWxzZX19"
// $bi->obterEmbedUrl()
// $bi->obterEmbedReportId()
?>

<div>


<div style="width:100%;min-height:100%; height:100%" id="embedContainer"></div>


<?php echo $this->Javascript->codeBlock('

jQuery(document).ready(function() {
    
    var accessToken = "'.$accessToken.'";

    var embedUrl = "https://app.powerbi.com/reportEmbed?reportId='.$reportId.'&groupId='.$groupId.'&w=2&config=eyJjbHVzdGVyVXJsIjoiaHR0cHM6Ly9XQUJJLUJSQVpJTC1TT1VUSC1CLVBSSU1BUlktcmVkaXJlY3QuYW5hbHlzaXMud2luZG93cy5uZXQiLCJlbWJlZEZlYXR1cmVzIjp7Im1vZGVybkVtYmVkIjp0cnVlfX0%3d";

    var embedReportId = "'.$reportId.'";

    var models = window["powerbi-client"].models;
    
    var config = {
        type: "report",
        tokenType: models.TokenType.Embed,
        accessToken: accessToken,
        embedUrl: embedUrl,
        id: embedReportId,
        permissions: models.Permissions.All,
        settings: {
            filterPaneEnabled: true,
            navContentPaneEnabled: true
        }
    };

    // Get a reference to the embedded report HTML element
    // var reportContainer = $(\'#embedContainer\')[0];
    var reportContainer = document.getElementById("embedContainer");

    // Embed the report and display it within the div container.
    var report = powerbi.embed(reportContainer, config);
        
});
'); ?>

</script>

</div>