<?php
header('Content-Type: application/json');

function logMe($arquivo, $msg){
	$fp = fopen($arquivo, "a");
	$escreve = fwrite($fp, $msg);
	fclose($fp);
}

function InsertFor($titulo, $cor, $formato, $data_start, $start_time, $data_end,$end_time, $post, $usuario){

    $sql = "INSERT into events(
                title, color, formato, start, start_time, end,end_time, convidados, usuario_id
            ) values (
                '$titulo', '$cor', '$formato' ,
                '$data_start', '$start_time', '$data_end', '$end_time', 
                '$post', '$usuario'
            )";

    return $sql;
                    
};

function InsertFor2($titulo, $cor, $formato, $data_start, $start_time, $data_end,$end_time, $usuario){

    $sql = "INSERT into events(title, color, formato, start, start_time, end,end_time, usuario_id) 
        values ('$titulo', '$cor', '$formato',
                '$data_start', '$start_time', '$data_end', '$end_time', 
                '$usuario'
            )";

    return $sql;
                    
};

function validaRastreadores($type){

    require '../../conexao.php';
    $sql = "SELECT tolerancia,disponivel, qt from estoque where type = '$type'";
    
    $query = mysqli_query($conexao, $sql);
    $result = mysqli_fetch_assoc($query);
    
    $quantidade = $result['qt'];
    $tolerancia = $result['tolerancia'];

    $disponivel = ($tolerancia > $quantidade) ? false : true;
    
    if($disponivel == false && $quantidade != 0){
        return 0;
    }else if($disponivel == false && $quantidade == 0){
        return 1;
    }else{
        return 2;
    }

}

function curlEmail($produto, $data){

    require '../../conexao.php';
    
    $sql = "SELECT id from usuario where nivel IN (4, 1)";
    $query = mysqli_query($conexao, $sql);
    $configuradores = array();
    while($array = mysqli_fetch_array($query)) {
        array_push($configuradores, $array['id']);                   
    }


    $post = array(
        'produto' => $produto, 
        'data' => $data,
        'user' => $configuradores
    );
    
    $url =  "127.0.0.1/engedoc_rapha/funcoes/calendario/email_instalacao.php";

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); 
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json'
    ));

    $response = curl_exec($ch);
    // echo $response;
    curl_close($ch);

}

function rotinaRastreador($produto){

    require '../../conexao.php';
    $sql = "SELECT disponivel, qt, tolerancia from estoque where type = '$produto'"; 
    $query = mysqli_query($conexao, $sql);
    $result = mysqli_fetch_assoc($query);
    
    $disponivel = $result['disponivel'];
    $quantidade = $result['qt'];
    $tolerancia = $result['tolerancia'];

    $sql = "SELECT id from usuario where nivel IN (1, 3)";
    $query = mysqli_query($conexao, $sql);
    $compradores = array();
    
    while($array = mysqli_fetch_array($query)) {
        array_push($compradores, $array['id']);                   
    }

    if($quantidade < $tolerancia){
        $disponivel = 0;
    }else{
        $disponivel = 1;
    }
    

    if($disponivel != 1){

        if($produto == 'ST340UR'){  
            $post = array(
                        'valor' => $quantidade, 
                        'tolerancia' => 3, 
                        'rastreador' => $produto,
                        'user' => $compradores
                    );
        }else if($produto == 'ST310UC2'){
            $post = array(
                        'valor' => $quantidade, 
                        'tolerancia' => 3, 
                        'rastreador' => $produto,
                        'user' => $compradores
                    );
        }

        $url = "127.0.0.1/engedoc_rapha/funcoes/calendario/email_falta_rastreador.php";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json'
        ));


        $response = curl_exec($ch);
        curl_close($ch);
        if($response == 'Mensagem enviada.'){
            return true;
        }else{
            return false;
        }
    }
}


include_once '../../get_dados.php';
include_once '../../conexao.php';

$dados = filter_input_array(INPUT_POST, FILTER_DEFAULT); // substitui o $_POST[''] :))
$json = json_encode($dados);
file_put_contents('json.txt', $json);
$usuario = $userSession;

if(isset($_POST['current_select']) && $_POST['title'] != null){
        
    $titulo = $dados['title'];
    $cor = $dados['formato_cad'];
    $select = $_POST['current_select'];
    $post = implode(",", $select);
    $count = count($select);
    $destinatario = explode(",", $post);

    // formato
    if($dados['formato_cad'] === '#A020F0'){
        $formato = 'Presencial';
    }else if($dados['formato_cad'] === '#FF4500'){
        $formato = 'Remoto';
    }else if($dados['formato_cad'] === '#808080'){
        $formato = 'Compromisso Pessoal';
    }else if($dados['formato_cad'] === '#FFCCCC'){
        $formato = 'Presencial em Campo';
    }else if($dados['formato_cad'] === '#1A3B5C'){
        $formato = 'Instalação';
    }
    // 

    // datas
    $data_start = str_replace('/', '-', $dados['start']);
    $data_end = str_replace('/', '-', $dados['end']);

    $end_time = $dados['end-time'];
    $start_time = $dados['start-time'];

    $end_time = date("H:i", strtotime($dados['end-time']));
    $start_time = date("H:i", strtotime($dados['start-time']));

    $data_start_conv = date("Y-m-d", strtotime($data_start));
    $data_end_conv = date("Y-m-d", strtotime($data_end));

    $start_time_conv = date('H:i:s', strtotime($start_time));
    $end_time_conv = date('H:i:s', strtotime($end_time));
    //

    
    if($data_start == $data_end && $start_time == $end_time){
        $retorna = ['sit' => false, 'msg' => '<div class="alert alert-danger" role="alert">Selecione horas diferentes!</div>'];
        echo json_encode($retorna);
        exit;
    }else if($data_start_conv > $data_end_conv || ($data_start_conv == $data_end_conv && $start_time > $end_time)){
        $retorna = ['sit' => false, 'msg' => '<div class="alert alert-danger" role="alert">Data de inicio deve ser menor que data de término!</div>'];
        echo json_encode($retorna);
        exit;
    }else{
        
        $sql = "SELECT * from events where convidados LIKE '%$post%' 
        and start = '$data_start_conv' and (start_time between time('$start_time_conv') and time('$end_time_conv') 
        or end_time between time('$start_time_conv') and time('$end_time_conv'))";
        $query = mysqli_query($conexao, $sql);
        $i = mysqli_num_rows($query);

        if($i > 0){    
            $retorna = ['sit' => false, 'msg' => '<div class="alert alert-danger" role="alert">Erro: Convidado(s) em reunião neste horário!</div>'];
            echo json_encode($retorna);
            exit;
        }else{

            if(isset($_POST['recorrente'])){
                $recorrente = true;
            }else{
                $recorrente = false;
            }

            if($recorrente == true && ($_POST['recorrenteSemanas'] == null && $_POST['recorrenteMeses'] == null)){
                
                $retorna = [
                    'sit' => false,
                    'msg' => '<div class="alert alert-danger" role="alert">Diga o quanto de semanas ou mêses o compromisso será recorrente!</div>'
                ];

                echo json_encode($retorna);
                exit;
            }
            else{
                
                if($_POST['recorrenteTipo'] == 'semanalmente'){
                    $x_semanas = $_POST['recorrenteSemanas'];
                }else if($_POST['recorrenteTipo'] == 'mensalmente'){
                    $x_meses = $_POST['recorrenteMeses'];           
                }

                if(isset($x_meses)){
                    for($i = 1; $i <= $x_meses; $i++ ){
                        if($i == 1){
                            $datestart7 = date('Y-m-d', strtotime($data_start_conv . ' +1 month'));
                            $dateend7 = date('Y-m-d', strtotime($data_end_conv . ' +1 month'));
                            
                            $sql = InsertFor($titulo,$cor,$formato,$datestart7,$start_time,$dateend7,$end_time,$post,$usuario);
                            $query = mysqli_query($conexao, $sql);
                        }else if($i == 2){
                            $datestart7 = date('Y-m-d', strtotime($data_start_conv . ' +2 month'));
                            $dateend7 = date('Y-m-d', strtotime($data_end_conv . ' +2 month'));
                            
                            $sql = InsertFor($titulo,$cor,$formato,$datestart7,$start_time,$dateend7,$end_time,$post,$usuario);
                            $query = mysqli_query($conexao, $sql);
                        }else if($i == 3){
                            $datestart7 = date('Y-m-d', strtotime($data_start_conv . ' +3 month'));
                            $dateend7 = date('Y-m-d', strtotime($data_end_conv . ' +3 month'));
                            
                            $sql = InsertFor($titulo,$cor,$formato,$datestart7,$start_time,$dateend7,$end_time,$post,$usuario);
                            $query = mysqli_query($conexao, $sql);
                        }else if($i == 4){
                            $datestart7 = date('Y-m-d', strtotime($data_start_conv . ' +4 month'));
                            $dateend7 = date('Y-m-d', strtotime($data_end_conv . ' +4 month'));
                            
                            $sql = InsertFor($titulo,$cor,$formato,$datestart7,$start_time,$dateend7,$end_time,$post,$usuario);
                            $query = mysqli_query($conexao, $sql);
                        }else if($i == 5){
                            $datestart7 = date('Y-m-d', strtotime($data_start_conv . ' +5 month'));
                            $dateend7 = date('Y-m-d', strtotime($data_end_conv . ' +5 month'));
                            
                            $sql = InsertFor($titulo,$cor,$formato,$datestart7,$start_time,$dateend7,$end_time,$post,$usuario);
                            $query = mysqli_query($conexao, $sql);
                        }else if($i == 6){
                            $datestart7 = date('Y-m-d', strtotime($data_start_conv . ' +6 month'));
                            $dateend7 = date('Y-m-d', strtotime($data_end_conv . ' +6 month'));
                            
                            $sql = InsertFor($titulo,$cor,$formato,$datestart7,$start_time,$dateend7,$end_time,$post,$usuario);
                            $query = mysqli_query($conexao, $sql);
                        }else if($i == 7){
                            $datestart7 = date('Y-m-d', strtotime($data_start_conv . ' +7 month'));
                            $dateend7 = date('Y-m-d', strtotime($data_end_conv . ' +7 month'));
                            
                            $sql = InsertFor($titulo,$cor,$formato,$datestart7,$start_time,$dateend7,$end_time,$post,$usuario);
                            $query = mysqli_query($conexao, $sql);
                        }else if($i == 8){
                            $datestart7 = date('Y-m-d', strtotime($data_start_conv . ' +8 month'));
                            $dateend7 = date('Y-m-d', strtotime($data_end_conv . ' +8 month'));
                            
                            $sql = InsertFor($titulo,$cor,$formato,$datestart7,$start_time,$dateend7,$end_time,$post,$usuario);
                            $query = mysqli_query($conexao, $sql);
                        }else if($i == 9){
                            $datestart7 = date('Y-m-d', strtotime($data_start_conv . ' +9 month'));
                            $dateend7 = date('Y-m-d', strtotime($data_end_conv . ' +9 month'));
                            
                            $sql = InsertFor($titulo,$cor,$formato,$datestart7,$start_time,$dateend7,$end_time,$post,$usuario);
                            $query = mysqli_query($conexao, $sql);
                        }else if($i == 10){
                            $datestart7 = date('Y-m-d', strtotime($data_start_conv . ' +10 month'));
                            $dateend7 = date('Y-m-d', strtotime($data_end_conv . ' +10 month'));
                            
                            $sql = InsertFor($titulo,$cor,$formato,$datestart7,$start_time,$dateend7,$end_time,$post,$usuario);
                            $query = mysqli_query($conexao, $sql);
                        }else if($i == 11){
                            $datestart7 = date('Y-m-d', strtotime($data_start_conv . ' +11 month'));
                            $dateend7 = date('Y-m-d', strtotime($data_end_conv . ' +11 month'));
                            
                            $sql = InsertFor($titulo,$cor,$formato,$datestart7,$start_time,$dateend7,$end_time,$post,$usuario);
                            $query = mysqli_query($conexao, $sql);
                        }else if($i == 12){
                            $datestart7 = date('Y-m-d', strtotime($data_start_conv . ' +12 month'));
                            $dateend7 = date('Y-m-d', strtotime($data_end_conv . ' +12 month'));
                            
                            $sql = InsertFor($titulo,$cor,$formato,$datestart7,$start_time,$dateend7,$end_time,$post,$usuario);
                            $query = mysqli_query($conexao, $sql);
                        }
                    }
                }
                
                if(isset($x_semanas)){
                    for($i = 1; $i <= $x_semanas; $i++ ){
                        if($i == 1){
                            $datestart7 = date('Y-m-d', strtotime($data_start_conv . ' +7 days'));
                            $dateend7 = date('Y-m-d', strtotime($data_end_conv . ' +7 days'));
                            
                            $sql = InsertFor($titulo,$cor,$formato,$datestart7,$start_time,$dateend7,$end_time,$post,$usuario);
                            $query = mysqli_query($conexao, $sql);
                        }else if($i == 2){
                            $datestart14 = date('Y-m-d', strtotime($data_start_conv . ' +14 days'));
                            $dateend14 = date('Y-m-d', strtotime($data_end_conv . ' +14 days'));
                            
                            $sql = InsertFor($titulo,$cor,$formato,$datestart14,$start_time,$dateend14,$end_time,$post,$usuario);
                            $query = mysqli_query($conexao, $sql);
                        }else if($i == 3){
                            $datestart21 = date('Y-m-d', strtotime($data_start_conv . ' +21 days'));
                            $dateend721 = date('Y-m-d', strtotime($data_end_conv . ' +21 days'));
                            
                            $sql = InsertFor($titulo,$cor,$formato,$datestart21,$start_time,$dateend721,$end_time,$post,$usuario);
                            $query = mysqli_query($conexao, $sql);
                        }else if($i == 4){
                            $datestart28 = date('Y-m-d', strtotime($data_start_conv . ' +28 days'));
                            $dateend28 = date('Y-m-d', strtotime($data_end_conv . ' +28 days'));
                            
                            $sql = InsertFor($titulo,$cor,$formato,$datestart28,$start_time,$dateend28,$end_time,$post,$usuario);
                            $query = mysqli_query($conexao, $sql);
                        }else if($i == 5){
                            $datestart28 = date('Y-m-d', strtotime($data_start_conv . ' +35 days'));
                            $dateend28 = date('Y-m-d', strtotime($data_end_conv . ' +35 days'));
                            
                            $sql = InsertFor($titulo,$cor,$formato,$datestart28,$start_time,$dateend28,$end_time,$post,$usuario);
                            $query = mysqli_query($conexao, $sql);
                        }else if($i == 6){
                            $datestart28 = date('Y-m-d', strtotime($data_start_conv . ' +42 days'));
                            $dateend28 = date('Y-m-d', strtotime($data_end_conv . ' +42 days'));
                            
                            $sql = InsertFor($titulo,$cor,$formato,$datestart28,$start_time,$dateend28,$end_time,$post,$usuario);
                            $query = mysqli_query($conexao, $sql);
                        }else if($i == 7){
                            $datestart28 = date('Y-m-d', strtotime($data_start_conv . ' +49 days'));
                            $dateend28 = date('Y-m-d', strtotime($data_end_conv . ' +49 days'));
                            
                            $sql = InsertFor($titulo,$cor,$formato,$datestart28,$start_time,$dateend28,$end_time,$post,$usuario);
                            $query = mysqli_query($conexao, $sql);
                        }else if($i == 8){
                            $datestart28 = date('Y-m-d', strtotime($data_start_conv . ' +56 days'));
                            $dateend28 = date('Y-m-d', strtotime($data_end_conv . ' +56 days'));
                            
                            $sql = InsertFor($titulo,$cor,$formato,$datestart28,$start_time,$dateend28,$end_time,$post,$usuario);
                            $query = mysqli_query($conexao, $sql);
                        }else if($i == 9){
                            $datestart28 = date('Y-m-d', strtotime($data_start_conv . ' +63 days'));
                            $dateend28 = date('Y-m-d', strtotime($data_end_conv . ' +63 days'));
                            
                            $sql = InsertFor($titulo,$cor,$formato,$datestart28,$start_time,$dateend28,$end_time,$post,$usuario);
                            $query = mysqli_query($conexao, $sql);
                        }else if($i == 10){
                            $datestart28 = date('Y-m-d', strtotime($data_start_conv . ' +70 days'));
                            $dateend28 = date('Y-m-d', strtotime($data_end_conv . ' +70 days'));
                            
                            $sql = InsertFor($titulo,$cor,$formato,$datestart28,$start_time,$dateend28,$end_time,$post,$usuario);
                            $query = mysqli_query($conexao, $sql);
                        }else if($i == 11){
                            $datestart28 = date('Y-m-d', strtotime($data_start_conv . ' +77 days'));
                            $dateend28 = date('Y-m-d', strtotime($data_end_conv . ' +77 days'));
                            
                            $sql = InsertFor($titulo,$cor,$formato,$datestart28,$start_time,$dateend28,$end_time,$post,$usuario);
                            $query = mysqli_query($conexao, $sql);
                        }else if($i == 12){
                            $datestart28 = date('Y-m-d', strtotime($data_start_conv . ' +84 days'));
                            $dateend28 = date('Y-m-d', strtotime($data_end_conv . ' +84 days'));
                            
                            $sql = InsertFor($titulo,$cor,$formato,$datestart28,$start_time,$dateend28,$end_time,$post,$usuario);
                            $query = mysqli_query($conexao, $sql);
                        }else if($i == 13){
                            $datestart28 = date('Y-m-d', strtotime($data_start_conv . ' +91 days'));
                            $dateend28 = date('Y-m-d', strtotime($data_end_conv . ' +91 days'));
                            
                            $sql = InsertFor($titulo,$cor,$formato,$datestart28,$start_time,$dateend28,$end_time,$post,$usuario);
                            $query = mysqli_query($conexao, $sql);
                        }else if($i == 14){
                            $datestart28 = date('Y-m-d', strtotime($data_start_conv . ' +98 days'));
                            $dateend28 = date('Y-m-d', strtotime($data_end_conv . ' +98 days'));
                            
                            $sql = InsertFor($titulo,$cor,$formato,$datestart28,$start_time,$dateend28,$end_time,$post,$usuario);
                            $query = mysqli_query($conexao, $sql);
                        }else if($i == 15){
                            $datestart28 = date('Y-m-d', strtotime($data_start_conv . ' +105 days'));
                            $dateend28 = date('Y-m-d', strtotime($data_end_conv . ' +105 days'));
                            
                            $sql = InsertFor($titulo,$cor,$formato,$datestart28,$start_time,$dateend28,$end_time,$post,$usuario);
                            $query = mysqli_query($conexao, $sql);
                        }else if($i == 16){
                            $datestart28 = date('Y-m-d', strtotime($data_start_conv . ' +112 days'));
                            $dateend28 = date('Y-m-d', strtotime($data_end_conv . ' +112 days'));
                            
                            $sql = InsertFor($titulo,$cor,$formato,$datestart28,$start_time,$dateend28,$end_time,$post,$usuario);
                            $query = mysqli_query($conexao, $sql);
                        }else if($i == 17){
                            $datestart28 = date('Y-m-d', strtotime($data_start_conv . ' +119 days'));
                            $dateend28 = date('Y-m-d', strtotime($data_end_conv . ' +119 days'));
                            
                            $sql = InsertFor($titulo,$cor,$formato,$datestart28,$start_time,$dateend28,$end_time,$post,$usuario);
                            $query = mysqli_query($conexao, $sql);
                        }else if($i == 18){
                            $datestart28 = date('Y-m-d', strtotime($data_start_conv . ' +126 days'));
                            $dateend28 = date('Y-m-d', strtotime($data_end_conv . ' +126 days'));
                            
                            $sql = InsertFor($titulo,$cor,$formato,$datestart28,$start_time,$dateend28,$end_time,$post,$usuario);
                            $query = mysqli_query($conexao, $sql);
                        }else if($i == 19){
                            $datestart28 = date('Y-m-d', strtotime($data_start_conv . ' +133 days'));
                            $dateend28 = date('Y-m-d', strtotime($data_end_conv . ' +133 days'));
                            
                            $sql = InsertFor($titulo,$cor,$formato,$datestart28,$start_time,$dateend28,$end_time,$post,$usuario);
                            $query = mysqli_query($conexao, $sql);
                        }else if($i == 20){
                            $datestart28 = date('Y-m-d', strtotime($data_start_conv . ' +140 days'));
                            $dateend28 = date('Y-m-d', strtotime($data_end_conv . ' +140 days'));
                            
                            $sql = InsertFor($titulo,$cor,$formato,$datestart28,$start_time,$dateend28,$end_time,$post,$usuario);
                            $query = mysqli_query($conexao, $sql);
                        }else if($i == 21){
                            $datestart28 = date('Y-m-d', strtotime($data_start_conv . ' +147 days'));
                            $dateend28 = date('Y-m-d', strtotime($data_end_conv . ' +147 days'));
                            
                            $sql = InsertFor($titulo,$cor,$formato,$datestart28,$start_time,$dateend28,$end_time,$post,$usuario);
                            $query = mysqli_query($conexao, $sql);
                        }else if($i == 22){
                            $datestart28 = date('Y-m-d', strtotime($data_start_conv . ' +154 days'));
                            $dateend28 = date('Y-m-d', strtotime($data_end_conv . ' +154 days'));
                            
                            $sql = InsertFor($titulo,$cor,$formato,$datestart28,$start_time,$dateend28,$end_time,$post,$usuario);
                            $query = mysqli_query($conexao, $sql);
                        }else if($i == 23){
                            $datestart28 = date('Y-m-d', strtotime($data_start_conv . ' +161 days'));
                            $dateend28 = date('Y-m-d', strtotime($data_end_conv . ' +161 days'));
                            
                            $sql = InsertFor($titulo,$cor,$formato,$datestart28,$start_time,$dateend28,$end_time,$post,$usuario);
                            $query = mysqli_query($conexao, $sql);
                        }else if($i == 24){
                            $datestart28 = date('Y-m-d', strtotime($data_start_conv . ' +168 days'));
                            $dateend28 = date('Y-m-d', strtotime($data_end_conv . ' +168 days'));
                            
                            $sql = InsertFor($titulo,$cor,$formato,$datestart28,$start_time,$dateend28,$end_time,$post,$usuario);
                            $query = mysqli_query($conexao, $sql);
                        } 
                    }
                }
            }



            if($formato == 'Remoto'){
                if(isset($_POST['link'])){
                    $link = $_POST['link'];
                    $sql = "INSERT INTO events (title, color, formato, start, start_time, end, end_time, convidados, count, link, usuario_id) 
                    values ('$titulo', '$cor', '$formato' , '$data_start_conv', '$start_time', '$data_end_conv', '$end_time',  '$post', '$count', '$link' , '$usuario')"; 
                    $query = mysqli_query($conexao, $sql);
                }
            }else if($formato == 'Presencial'){
                if(isset($_POST['sala-engeline'])){
                    $sql = "INSERT INTO events 
                    (title, color, formato, start, start_time, end, end_time, convidados, count, sala_engeline,  usuario_id) 
                    values ('$titulo', '$cor', '$formato' , '$data_start_conv', '$start_time', 
                    '$data_end_conv', '$end_time',  '$post', '$count', 1 , '$usuario')"; 
                    $query = mysqli_query($conexao, $sql);
                }else{
                    $sql = "INSERT INTO events 
                    (title, color, formato, start, start_time, end, end_time, convidados, count, usuario_id) 
                        values ('$titulo', '$cor', '$formato' , '$data_start_conv', '$start_time', 
                        '$data_end_conv', '$end_time',  '$post', '$count', '$usuario')"; 
                    $query = mysqli_query($conexao, $sql);
                }
            }else if($formato == 'Instalação' && isset($_POST['produto'])){

                    $produto = $_POST['produto'];  
                    $validacao = validaRastreadores($produto);

                    if($validacao === 1){
                        $retorna = ['sit' => false, 'msg' => '<div class="alert alert-danger" role="alert">Erro: Faltam rastreadores no estoque!</div>'];
                        echo json_encode($retorna);
                        rotinaRastreador($produto);
                        exit;
                    }else if($validacao === 0){

                        $sql = "INSERT INTO events (title, color, formato, start,start_time, end, end_time, produto, convidados, usuario_id)
                            values ('$titulo', '$cor', '$formato' , '$data_start_conv',
                            '$start_time', '$data_end_conv', '$end_time','$produto', '$post',  '$usuario')"; 
                        
                        $query = mysqli_query($conexao, $sql);
                        
                        if($query){
                            $retorna = ['warning' => true, 'msg' => '<div class="alert alert-success" role="alert">Evento cadastrado com sucesso!</div>'];
                            $issetRetorna = true;
                        }

                        curlEmail($produto, $data_start_conv);
                        rotinaRastreador($produto);
                    }else if($validacao == 2){
                        $sql = "INSERT INTO events (title, color, formato, start,start_time, end, end_time, produto, convidados, usuario_id)
                            values ('$titulo', '$cor', '$formato' , '$data_start_conv','$start_time', '$data_end_conv', '$end_time','$produto','$post',  '$usuario')"; 
                        $query = mysqli_query($conexao, $sql);

                        curlEmail($produto, $data_start_conv);
                    }
            }
            else{
                $sql = "INSERT INTO events (title, color, formato, start, start_time, end, end_time, convidados, `count`, usuario_id) 
                    values ('$titulo', '$cor', '$formato' , '$data_start_conv','$start_time', '$data_end_conv','$end_time', '$post', '$count', '$usuario')"; 
                $query = mysqli_query($conexao, $sql);
            }

            if($query){
                $sqllog = "INSERT INTO logs(mensagem, usuario, target) values (
                    'Cadastro de evento',
                    $usuario, 
                    '$titulo')";
                $querylog = mysqli_query($conexao, $sqllog);
            }else{
                $retorna = ['sit' => false, 'msg' => '<div class="alert alert-danger" role="alert">Erro: Não foi possivel completar query</div>'];
                echo json_encode($retorna);
                exit;
            }
            if($querylog){
                if(isset($issetRetorna) && $issetRetorna === true){
                    echo json_encode($retorna);    
                }else{
                    $retorna = ['sucessful' => true, 'msg' => '<div class="alert alert-success" role="alert">Evento cadastrado com sucesso!</div>'];
                    echo json_encode($retorna);    
                }
            }  
                   
            foreach($destinatario as $user){ 

                $sql = "SELECT * from usuario where nome  = '$user' limit 1";
                $query2 = mysqli_query($conexao, $sql);
                
                while($array = mysqli_fetch_assoc($query2)){
                    $email = $array['email'];
                }

                $url =  "192.168.0.124/engedoc_rapha/funcoes/calendario/email.php";
                $ch = curl_init($url);

                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); // needed to disable SSL checks for this site
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // needed to disable SSL checks for this site
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                
                $headers = array(
                    "Content-Type: application/x-www-form-urlencoded",
                );

                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                $data = "titulo=$titulo&email=$email&formato=$formato&nome=$user";
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                $output = curl_exec($ch);
                // echo $output;
                curl_close($ch);
            }
        }
    }
}else if($_POST['title'] != null){

    if($dados['formato_cad'] === '#A020F0'){
        $formato = 'Presencial';
    }else if($dados['formato_cad'] === '#FF4500'){
        $formato = 'Remoto';
    }else if($dados['formato_cad'] === '#808080'){
        $formato = 'Compromisso Pessoal';
    }else if($dados['formato_cad'] === '#FFCCCC'){
        $formato = 'Presencial em Campo';
    }else if($dados['formato_cad'] === '#1A3B5C'){
        $formato = 'Instalação';
    }

    $titulo = $dados['title'];
    $cor = $dados['formato_cad'];

    // data
    $data_start = str_replace('/', '-', $dados['start']);
    $data_end = str_replace('/', '-', $dados['end']);
    $end_time = $dados['end-time'];
    $start_time = $dados['start-time'];
    $end_time = date("H:i", strtotime($dados['end-time']));
    $start_time = date("H:i", strtotime($dados['start-time']));
    $data_start_conv = date("Y-m-d", strtotime($data_start));
    $data_end_conv = date("Y-m-d", strtotime($data_end));
    // 

    if($data_start == $data_end && $start_time == $end_time){
        $retorna = ['sit' => false, 'msg' => '<div class="alert alert-danger" role="alert">Selecione horas diferentes!</div>'];
        echo json_encode($retorna);
        exit;
    }else if($data_start_conv > $data_end_conv || ($data_start_conv == $data_end_conv && $start_time > $end_time)){
        $retorna = ['sit' => false, 'msg' => '<div class="alert alert-danger" role="alert">Data de inicio deve ser menor que data de término!</div>'];
        echo json_encode($retorna);
        exit;
    }else{
        
        if(isset($_POST['recorrente'])){
            $recorrente = true;
        }else{
            $recorrente = false;
        }
        
        if($recorrente == true && ($_POST['recorrenteSemanas'] == null && $_POST['recorrenteMeses'] == null)){
            
            $retorna = [
                'sit' => false,
                'msg' => '<div class="alert alert-danger" role="alert">Diga o quanto de semanas ou mêses o compromisso será recorrente!</div>'
            ];

            echo json_encode($retorna);
            exit;
        }
        else
        {
            if($_POST['recorrenteTipo'] == 'semanalmente'){
                $x_semanas = $_POST['recorrenteSemanas'];
            }else if($_POST['recorrenteTipo'] == 'mensalmente'){
                $x_meses = $_POST['recorrenteMeses'];           
            }


            if(isset($x_meses))
            {
                for($i = 1; $i <= $x_meses; $i++ ){
                    if($i == 1){
                        $datestart7 = date('Y-m-d', strtotime($data_start_conv . ' +1 month'));
                        $dateend7 = date('Y-m-d', strtotime($data_end_conv . ' +1 month'));
                        
                        $sql = InsertFor($titulo,$cor,$formato,$datestart7,$start_time,$dateend7,$end_time,$post,$usuario);
                        $query = mysqli_query($conexao, $sql);
                    }else if($i == 2){
                        $datestart7 = date('Y-m-d', strtotime($data_start_conv . ' +2 month'));
                        $dateend7 = date('Y-m-d', strtotime($data_end_conv . ' +2 month'));
                        
                        $sql = InsertFor($titulo,$cor,$formato,$datestart7,$start_time,$dateend7,$end_time,$post,$usuario);
                        $query = mysqli_query($conexao, $sql);
                    }else if($i == 3){
                        $datestart7 = date('Y-m-d', strtotime($data_start_conv . ' +3 month'));
                        $dateend7 = date('Y-m-d', strtotime($data_end_conv . ' +3 month'));
                        
                        $sql = InsertFor($titulo,$cor,$formato,$datestart7,$start_time,$dateend7,$end_time,$post,$usuario);
                        $query = mysqli_query($conexao, $sql);
                    }else if($i == 4){
                        $datestart7 = date('Y-m-d', strtotime($data_start_conv . ' +4 month'));
                        $dateend7 = date('Y-m-d', strtotime($data_end_conv . ' +4 month'));
                        
                        $sql = InsertFor($titulo,$cor,$formato,$datestart7,$start_time,$dateend7,$end_time,$post,$usuario);
                        $query = mysqli_query($conexao, $sql);
                    }else if($i == 5){
                        $datestart7 = date('Y-m-d', strtotime($data_start_conv . ' +5 month'));
                        $dateend7 = date('Y-m-d', strtotime($data_end_conv . ' +5 month'));
                        
                        $sql = InsertFor($titulo,$cor,$formato,$datestart7,$start_time,$dateend7,$end_time,$post,$usuario);
                        $query = mysqli_query($conexao, $sql);
                    }else if($i == 6){
                        $datestart7 = date('Y-m-d', strtotime($data_start_conv . ' +6 month'));
                        $dateend7 = date('Y-m-d', strtotime($data_end_conv . ' +6 month'));
                        
                        $sql = InsertFor($titulo,$cor,$formato,$datestart7,$start_time,$dateend7,$end_time,$post,$usuario);
                        $query = mysqli_query($conexao, $sql);
                    }else if($i == 7){
                        $datestart7 = date('Y-m-d', strtotime($data_start_conv . ' +7 month'));
                        $dateend7 = date('Y-m-d', strtotime($data_end_conv . ' +7 month'));
                        
                        $sql = InsertFor($titulo,$cor,$formato,$datestart7,$start_time,$dateend7,$end_time,$post,$usuario);
                        $query = mysqli_query($conexao, $sql);
                    }else if($i == 8){
                        $datestart7 = date('Y-m-d', strtotime($data_start_conv . ' +8 month'));
                        $dateend7 = date('Y-m-d', strtotime($data_end_conv . ' +8 month'));
                        
                        $sql = InsertFor($titulo,$cor,$formato,$datestart7,$start_time,$dateend7,$end_time,$post,$usuario);
                        $query = mysqli_query($conexao, $sql);
                    }else if($i == 9){
                        $datestart7 = date('Y-m-d', strtotime($data_start_conv . ' +9 month'));
                        $dateend7 = date('Y-m-d', strtotime($data_end_conv . ' +9 month'));
                        
                        $sql = InsertFor($titulo,$cor,$formato,$datestart7,$start_time,$dateend7,$end_time,$post,$usuario);
                        $query = mysqli_query($conexao, $sql);
                    }else if($i == 10){
                        $datestart7 = date('Y-m-d', strtotime($data_start_conv . ' +10 month'));
                        $dateend7 = date('Y-m-d', strtotime($data_end_conv . ' +10 month'));
                        
                        $sql = InsertFor($titulo,$cor,$formato,$datestart7,$start_time,$dateend7,$end_time,$post,$usuario);
                        $query = mysqli_query($conexao, $sql);
                    }else if($i == 11){
                        $datestart7 = date('Y-m-d', strtotime($data_start_conv . ' +11 month'));
                        $dateend7 = date('Y-m-d', strtotime($data_end_conv . ' +11 month'));
                        
                        $sql = InsertFor($titulo,$cor,$formato,$datestart7,$start_time,$dateend7,$end_time,$post,$usuario);
                        $query = mysqli_query($conexao, $sql);
                    }else if($i == 12){
                        $datestart7 = date('Y-m-d', strtotime($data_start_conv . ' +12 month'));
                        $dateend7 = date('Y-m-d', strtotime($data_end_conv . ' +12 month'));
                        
                        $sql = InsertFor($titulo,$cor,$formato,$datestart7,$start_time,$dateend7,$end_time,$post,$usuario);
                        $query = mysqli_query($conexao, $sql);
                    }
                }
            }
            
            if(isset($x_semanas))
            {
                for($i = 1; $i <= $x_semanas; $i++ ){
                    if($i == 1){
                        $datestart7 = date('Y-m-d', strtotime($data_start_conv . ' +7 days'));
                        $dateend7 = date('Y-m-d', strtotime($data_end_conv . ' +7 days'));
                        
                        $sql = InsertFor2($titulo,$cor,$formato,$datestart7,$start_time,$dateend7,$end_time,$usuario);
                        $query = mysqli_query($conexao, $sql);
                    }else if($i == 2){
                        $datestart14 = date('Y-m-d', strtotime($data_start_conv . ' +14 days'));
                        $dateend14 = date('Y-m-d', strtotime($data_end_conv . ' +14 days'));
                        
                        $sql = InsertFor2($titulo,$cor,$formato,$datestart14,$start_time,$dateend14,$end_time,$usuario);
                        $query = mysqli_query($conexao, $sql);
                    }else if($i == 3){
                        $datestart21 = date('Y-m-d', strtotime($data_start_conv . ' +21 days'));
                        $dateend21 = date('Y-m-d', strtotime($data_end_conv . ' +21 days'));
                        
                        $sql = InsertFor2($titulo,$cor,$formato,$datestart21,$start_time,$dateend21,$end_time,$usuario);
                        $query = mysqli_query($conexao, $sql);
                    }else if($i == 4){
                        $datestart28 = date('Y-m-d', strtotime($data_start_conv . ' +28 days'));
                        $dateend28 = date('Y-m-d', strtotime($data_end_conv . ' +28 days'));
                        
                        $sql = InsertFor2($titulo,$cor,$formato,$datestart28,$start_time,$dateend28,$end_time,$usuario);
                        $query = mysqli_query($conexao, $sql);
                    }else if($i == 5){
                            $datestart28 = date('Y-m-d', strtotime($data_start_conv . ' +35 days'));
                            $dateend28 = date('Y-m-d', strtotime($data_end_conv . ' +35 days'));
                            
                            $sql = InsertFor2($titulo,$cor,$formato,$datestart28,$start_time,$dateend28,$end_time,$usuario);
                            $query = mysqli_query($conexao, $sql);
                        }else if($i == 6){
                            $datestart28 = date('Y-m-d', strtotime($data_start_conv . ' +42 days'));
                            $dateend28 = date('Y-m-d', strtotime($data_end_conv . ' +42 days'));
                            
                            $sql = InsertFor2($titulo,$cor,$formato,$datestart28,$start_time,$dateend28,$end_time,$usuario);
                            $query = mysqli_query($conexao, $sql);
                        }else if($i == 7){
                            $datestart28 = date('Y-m-d', strtotime($data_start_conv . ' +49 days'));
                            $dateend28 = date('Y-m-d', strtotime($data_end_conv . ' +49 days'));
                            
                            $sql = InsertFor2($titulo,$cor,$formato,$datestart28,$start_time,$dateend28,$end_time,$usuario);
                            $query = mysqli_query($conexao, $sql);
                        }else if($i == 8){
                            $datestart28 = date('Y-m-d', strtotime($data_start_conv . ' +56 days'));
                            $dateend28 = date('Y-m-d', strtotime($data_end_conv . ' +56 days'));
                            
                            $sql = InsertFor2($titulo,$cor,$formato,$datestart28,$start_time,$dateend28,$end_time,$usuario);
                            $query = mysqli_query($conexao, $sql);
                        }else if($i == 9){
                            $datestart28 = date('Y-m-d', strtotime($data_start_conv . ' +63 days'));
                            $dateend28 = date('Y-m-d', strtotime($data_end_conv . ' +63 days'));
                            
                            $sql = InsertFor2($titulo,$cor,$formato,$datestart28,$start_time,$dateend28,$end_time,$usuario);
                            $query = mysqli_query($conexao, $sql);
                        }else if($i == 10){
                            $datestart28 = date('Y-m-d', strtotime($data_start_conv . ' +70 days'));
                            $dateend28 = date('Y-m-d', strtotime($data_end_conv . ' +70 days'));
                            
                            $sql = InsertFor2($titulo,$cor,$formato,$datestart28,$start_time,$dateend28,$end_time,$usuario);
                            $query = mysqli_query($conexao, $sql);
                        }else if($i == 11){
                            $datestart28 = date('Y-m-d', strtotime($data_start_conv . ' +77 days'));
                            $dateend28 = date('Y-m-d', strtotime($data_end_conv . ' +77 days'));
                            
                            $sql = InsertFor2($titulo,$cor,$formato,$datestart28,$start_time,$dateend28,$end_time,$usuario);
                            $query = mysqli_query($conexao, $sql);
                        }else if($i == 12){
                            $datestart28 = date('Y-m-d', strtotime($data_start_conv . ' +84 days'));
                            $dateend28 = date('Y-m-d', strtotime($data_end_conv . ' +84 days'));
                            
                            $sql = InsertFor2($titulo,$cor,$formato,$datestart28,$start_time,$dateend28,$end_time,$usuario);
                            $query = mysqli_query($conexao, $sql);
                        }else if($i == 13){
                            $datestart28 = date('Y-m-d', strtotime($data_start_conv . ' +91 days'));
                            $dateend28 = date('Y-m-d', strtotime($data_end_conv . ' +91 days'));
                            
                            $sql = InsertFor2($titulo,$cor,$formato,$datestart28,$start_time,$dateend28,$end_time,$usuario);
                            $query = mysqli_query($conexao, $sql);
                        }else if($i == 14){
                            $datestart28 = date('Y-m-d', strtotime($data_start_conv . ' +98 days'));
                            $dateend28 = date('Y-m-d', strtotime($data_end_conv . ' +98 days'));
                            
                            $sql = InsertFor2($titulo,$cor,$formato,$datestart28,$start_time,$dateend28,$end_time,$usuario);
                            $query = mysqli_query($conexao, $sql);
                        }else if($i == 15){
                            $datestart28 = date('Y-m-d', strtotime($data_start_conv . ' +105 days'));
                            $dateend28 = date('Y-m-d', strtotime($data_end_conv . ' +105 days'));
                            
                            $sql = InsertFor2($titulo,$cor,$formato,$datestart28,$start_time,$dateend28,$end_time,$usuario);
                            $query = mysqli_query($conexao, $sql);
                        }else if($i == 16){
                            $datestart28 = date('Y-m-d', strtotime($data_start_conv . ' +112 days'));
                            $dateend28 = date('Y-m-d', strtotime($data_end_conv . ' +112 days'));
                            
                            $sql = InsertFor2($titulo,$cor,$formato,$datestart28,$start_time,$dateend28,$end_time,$usuario);
                            $query = mysqli_query($conexao, $sql);
                        }else if($i == 17){
                            $datestart28 = date('Y-m-d', strtotime($data_start_conv . ' +119 days'));
                            $dateend28 = date('Y-m-d', strtotime($data_end_conv . ' +119 days'));
                            
                            $sql = InsertFor2($titulo,$cor,$formato,$datestart28,$start_time,$dateend28,$end_time,$usuario);
                            $query = mysqli_query($conexao, $sql);
                        }else if($i == 18){
                            $datestart28 = date('Y-m-d', strtotime($data_start_conv . ' +126 days'));
                            $dateend28 = date('Y-m-d', strtotime($data_end_conv . ' +126 days'));
                            
                            $sql = InsertFor2($titulo,$cor,$formato,$datestart28,$start_time,$dateend28,$end_time,$usuario);
                            $query = mysqli_query($conexao, $sql);
                        }else if($i == 19){
                            $datestart28 = date('Y-m-d', strtotime($data_start_conv . ' +133 days'));
                            $dateend28 = date('Y-m-d', strtotime($data_end_conv . ' +133 days'));
                            
                            $sql = InsertFor2($titulo,$cor,$formato,$datestart28,$start_time,$dateend28,$end_time,$usuario);
                            $query = mysqli_query($conexao, $sql);
                        }else if($i == 20){
                            $datestart28 = date('Y-m-d', strtotime($data_start_conv . ' +140 days'));
                            $dateend28 = date('Y-m-d', strtotime($data_end_conv . ' +140 days'));
                            
                            $sql = InsertFor2($titulo,$cor,$formato,$datestart28,$start_time,$dateend28,$end_time,$usuario);
                            $query = mysqli_query($conexao, $sql);
                        }else if($i == 21){
                            $datestart28 = date('Y-m-d', strtotime($data_start_conv . ' +147 days'));
                            $dateend28 = date('Y-m-d', strtotime($data_end_conv . ' +147 days'));
                            
                            $sql = InsertFor2($titulo,$cor,$formato,$datestart28,$start_time,$dateend28,$end_time,$usuario);
                            $query = mysqli_query($conexao, $sql);
                        }else if($i == 22){
                            $datestart28 = date('Y-m-d', strtotime($data_start_conv . ' +154 days'));
                            $dateend28 = date('Y-m-d', strtotime($data_end_conv . ' +154 days'));
                            
                            $sql = InsertFor2($titulo,$cor,$formato,$datestart28,$start_time,$dateend28,$end_time,$usuario);
                            $query = mysqli_query($conexao, $sql);
                        }else if($i == 23){
                            $datestart28 = date('Y-m-d', strtotime($data_start_conv . ' +161 days'));
                            $dateend28 = date('Y-m-d', strtotime($data_end_conv . ' +161 days'));
                            
                            $sql = InsertFor2($titulo,$cor,$formato,$datestart28,$start_time,$dateend28,$end_time,$usuario);
                            $query = mysqli_query($conexao, $sql);
                        }else if($i == 24){
                            $datestart28 = date('Y-m-d', strtotime($data_start_conv . ' +168 days'));
                            $dateend28 = date('Y-m-d', strtotime($data_end_conv . ' +168 days'));
                            
                            $sql = InsertFor2($titulo,$cor,$formato,$datestart28,$start_time,$dateend28,$end_time,$usuario);
                            $query = mysqli_query($conexao, $sql);
                        }
                }
            }
        }

        if($formato == 'Remoto'){
            if(isset($_POST['link'])){
                $link = $_POST['link'];
                $sql = "INSERT INTO events (title, color, formato, start,start_time, end, end_time, link, usuario_id)
                    values ('$titulo', '$cor', '$formato' , '$data_start_conv','$start_time', '$data_end_conv', '$end_time', '$link', '$usuario')"; 
                $query = mysqli_query($conexao, $sql);
            }else{
                 $sql = "INSERT INTO events (title, color, formato, start,start_time, end, end_time, usuario_id)
                    values ('$titulo', '$cor', '$formato' , '$data_start_conv','$start_time', '$data_end_conv', '$end_time', '$usuario')"; 
                $query = mysqli_query($conexao, $sql);
            }
        }else if($formato == 'Presencial'){
            if(isset($_POST['sala-engeline'])){
                $sql = "INSERT INTO events (title, color, formato, start,start_time, end, end_time, sala_engeline, usuario_id)
                    values ('$titulo', '$cor', '$formato' , '$data_start_conv','$start_time', '$data_end_conv', '$end_time', 1, '$usuario')"; 
                $query = mysqli_query($conexao, $sql);
            }else{
                $sql = "INSERT INTO events (title, color, formato, start,start_time, end, end_time, usuario_id)
                    values ('$titulo', '$cor', '$formato' , '$data_start_conv','$start_time', '$data_end_conv', '$end_time', '$usuario')"; 
                $query = mysqli_query($conexao, $sql);
            }
        }else if($formato == 'Instalação' && isset($_POST['produto'])){

            $produto = $_POST['produto'];

            $validacao = validaRastreadores($produto);

            if($validacao === 1){
                $retorna = ['sit' => false, 'msg' => '<div class="alert alert-danger" role="alert">Erro: Faltam rastreadores no estoque!</div>'];
                echo json_encode($retorna);
                rotinaRastreador($produto);
                exit;
            }else if($validacao === 0){
                
                $sql = "INSERT INTO events (title, color, formato, start,start_time, end, end_time, produto, usuario_id)
                    values ('$titulo', '$cor', '$formato' , '$data_start_conv','$start_time', '$data_end_conv', '$end_time','$produto',  '$usuario')"; 
                $query = mysqli_query($conexao, $sql);

                if($query){
                    $retorna = ['warning' => true, 'msg' => '<div class="alert alert-success" role="alert">Evento cadastrado com sucesso!</div>'];
                    $issetRetorna = true;
                }

                curlEmail($produto, $data_start_conv);
                rotinaRastreador($produto);

            }else if($validacao == 2){
                $sql = "INSERT INTO events (title, color, formato, start,start_time, end, end_time, produto, usuario_id)
                    values ('$titulo', '$cor', '$formato' , '$data_start_conv','$start_time', '$data_end_conv', '$end_time','$produto',  '$usuario')"; 
                $query = mysqli_query($conexao, $sql);

                curlEmail($produto, $data_start_conv);
            }

        }else{
            $sql = "INSERT INTO events (title, color, formato, start,start_time, end, end_time, usuario_id)
                values ('$titulo', '$cor', '$formato' ,'$data_start_conv','$start_time', '$data_end_conv', '$end_time', '$usuario')"; 
            $query = mysqli_query($conexao, $sql);
        }
        
        if($query){
            //log
            $sqllog = "INSERT INTO logs(mensagem, usuario, target) values (
                'Cadastro de evento', 
                $usuario, '$titulo')";
            $querylog = mysqli_query($conexao, $sqllog);
            //
        }else{
            $retorna = ['sit' => false, 'msg' => '<div class="alert alert-danger" role="alert">Erro: Não foi possivel completar o registro</div>'];
            echo json_encode($retorna);
            exit;
        }

        if($querylog){

            if(isset($issetRetorna) && $issetRetorna === true){
                echo json_encode($retorna);    
            }else{
                $retorna = ['sucessful' => true, 'msg' => '<div class="alert alert-success" role="alert">Evento cadastrado com sucesso!</div>'];
                echo json_encode($retorna);    
            }
            
        }
    }

}else{
    $retorna = ['sit' => true, 'msg' => '<div class="alert alert-danger" role="alert">Digite um Titulo!</div>'];
    echo json_encode($retorna);
}
