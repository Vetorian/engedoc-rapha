<?php
header('Content-Type: application/json');

include_once '../../get_dados.php';
include_once '../../conexao.php';

$dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
$json = json_encode($dados);
file_put_contents('jsonEdit.txt', $json);
// file_put_contents('../logs/jsonEdit.txt', $json);


if(isset($_POST['edit_select']) && $_POST['title'] != null){

    $titulo = $_POST['title'] ;
    $id = $dados['id'];
    $cor = $dados['formatoedit'];
    $select = $_POST['edit_select'];
    $post = implode(",", $select);
    $destinatario = explode(",", $post);
    
    $data_start = str_replace('/', '-', $dados['start']);
    $data_end = str_replace('/', '-', $dados['end']);

    $end_time = $dados['end-time'];
    $start_time = $dados['start-time'];

    $end_time = date("H:i", strtotime($dados['end-time']));
    $start_time = date("H:i", strtotime($dados['start-time']));

    $data_start_conv = date("Y-m-d", strtotime($data_start));
    $data_end_conv = date("Y-m-d", strtotime($data_end));


    if($data_start == $data_end && $start_time == $end_time){
        $retorna = ['sit' => false, 'msg' => '<div class="alert alert-danger" role="alert">Selecione horas diferentes!</div>'];
        echo json_encode($retorna);
        exit;
    }else if($data_start_conv > $data_end_conv || ($data_start_conv == $data_end_conv && $start_time > $end_time)){
        $retorna = ['sit' => false, 'msg' => '<div class="alert alert-danger" role="alert">Data de inicio deve ser menor que data de término!</div>'];
        echo json_encode($retorna);
        exit;
    }else{

            if($cor === '#A020F0'){
                $formato = 'Presencial';
            }else if($cor === '#FF4500'){
                $formato = 'Remoto';
            }
            else if($cor === '#808080'){
                $formato = 'Compromisso Pessoal';
            }
            else if($cor === '#FFCCCC'){
                $formato = 'Presencial em Campo';
            }

            $sql = "SELECT * from events where convidados LIKE '%$post%' 
            and start = '$data_start_conv' and (start_time between time('$data_start_conv') and time('$data_end_conv') 
            or end_time between time('$start_time') and time('$end_time'))";
            $query = mysqli_query($conexao, $sql);
            $i = mysqli_num_rows($query);
            
            if($i > 0)
            {
                $retorna = ['sit' => false, 'msg' => '<div class="alert alert-danger" role="alert">Erro: Convidado(s) em reunião neste horário!</div>'];
                echo json_encode($retorna); 
                exit;
            }else{

                if($formato == 'Remoto'){
                    if(isset($_POST['link-edit'])){
                        $link = $_POST['link-edit'];
                        $sql = "UPDATE events set title = '$titulo', color = '$cor', start = '$data_start_conv', start_time = '$start_time',  
                            end = '$data_end_conv', end_time = '$end_time', convidados = '$post', formato = '$formato', link = '$link' where id = $id";
                        
                        $query = mysqli_query($conexao, $sql);
                    }
                }else{
                    $sql = "UPDATE events set title = '$titulo', color = '$cor',
                        start = '$data_start_conv', start_time = '$start_time', end = '$data_end_conv', end_time = '$end_time',
                        convidados = '$post', formato = '$formato' where id = $id";

                    $query = mysqli_query($conexao, $sql);
                }

                if($query){
                    $sqllog = "INSERT INTO logs(mensagem, target, usuario) values ('Editou o evento', '$titulo' , $userSession )";
                    $querylog = mysqli_query($conexao, $sqllog);
                }
                
                if($querylog){
                    $retorna = ['sucessful' => true, 'msg' => '<div class="alert alert-success" role="alert">Evento editado com sucesso!</div>'];
                    echo json_encode($retorna);
                }

            foreach($destinatario as $user){
        
                $sql = "SELECT * from usuario where nome  = '$user' limit 1";
                $query2 = mysqli_query($conexao, $sql);
                
                while($array = mysqli_fetch_assoc($query2)){
                    $email = $array['email'];
                }

                $url =  "127.0.0.1/engedoc_rapha/funcoes/calendario/email.php";
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
                curl_close($ch);
            }
        }
            
        }
    }else if($_POST['title'] != null){

        $titulo = $_POST['title'];
        $id = $dados['id'];
        $cor = $dados['formatoedit'];

        $data_start = str_replace('/', '-', $dados['start']);
        $data_end = str_replace('/', '-', $dados['end']);

        $end_time = $dados['end-time'];
        $start_time = $dados['start-time'];

        $end_time = date("H:i", strtotime($dados['end-time']));
        $start_time = date("H:i", strtotime($dados['start-time']));

        $data_start_conv = date("Y-m-d", strtotime($data_start));
        $data_end_conv = date("Y-m-d", strtotime($data_end));

        if($data_start == $data_end && $start_time == $end_time){
            $retorna = ['sit' => false, 'msg' => '<div class="alert alert-danger" role="alert">Selecione horas diferentes!</div>'];
            echo json_encode($retorna);
            exit;
        }else if($data_start_conv > $data_end_conv || ($data_start_conv == $data_end_conv && $start_time > $end_time)){
            $retorna = ['sit' => false, 'msg' => '<div class="alert alert-danger" role="alert">Data de inicio deve ser menor que data de término!</div>'];
            echo json_encode($retorna);
            exit;
        }else{
            
            if($cor === '#A020F0'){
                $formato = 'Presencial';
            }else if($cor === '#FF4500'){
                $formato = 'Remoto';
            }
            else if($cor === '#808080'){
                $formato = 'Compromisso Pessoal';
            }
            else if($cor === '#FFCCCC'){
                $formato = 'Presencial em Campo';
            }

            if($formato == 'Remoto'){
                if(isset($_POST['link-edit'])){
                    $link = $_POST['link-edit'];
                    $sql = "UPDATE events set title = '$titulo', color = '$cor', 
                        start = '$data_start_conv', start_time = '$start_time',  end = '$data_end_conv', end_time = '$end_time',
                        formato = '$formato', link = '$link' where id = $id";
                        
                    $query = mysqli_query($conexao, $sql);
                }
            }else{
                    $sql = "UPDATE events set title = '$titulo', color = '$cor', start = '$data_start_conv',start_time = '$start_time',
                    end = '$data_end_conv', end_time = '$end_time', formato = '$formato' where id = $id";
                    $query = mysqli_query($conexao, $sql);
                }
            if($query){
                $sqllog = "INSERT INTO logs(mensagem, target, usuario) values ('Editou o evento', '$titulo' , $userSession )";
                $querylog = mysqli_query($conexao, $sqllog);
            }
            if($querylog){
                $retorna = ['sucessful' => true, 'msg' => '<div class="alert alert-success" role="alert">Evento editado com sucesso!</div>'];
                echo json_encode($retorna);
            }
        }
    }else{
        $retorna = ['sit' => true, 'msg' => '<div class="alert alert-danger" role="alert">Digite um Titulo!</div>'];
        echo json_encode($retorna);
}

?>