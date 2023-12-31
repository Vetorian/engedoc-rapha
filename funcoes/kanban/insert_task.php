<?php 

require '../../conexao.php';
require '../../get_dados.php';
date_default_timezone_set('America/Sao_Paulo');

function logDragging($user, $task, $target, $source ){
    require '../../conexao.php';

    $sql = "INSERT INTO logs_kanban(usuario_id, task_id, target, source) values('$user', '$task','$target','$source')";
    mysqli_query($conexao, $sql);
    
}

function validaData($data_post){
    $dataAtual = date('Y-m-d H:i:s');
    if($dataAtual > $data_post){
        echo json_encode(array(
            'erro' => true,
            'msg' => 'Data da tarefa não pode ser anterior a data atual!'
        ));
        exit();
    }
}


function curlEmail($task_id){
    require '../../conexao.php';

    $sql = "SELECT * from tarefas_criadas where tarefa_id = $task_id";
    $query = mysqli_query($conexao, $sql);
    
    $array = mysqli_fetch_array($query);

    $arrayPost = array(
        'tarefa_id' => $task_id,
        'tarefa' => $array['titulo'],
        'ptc' => $array['ptc_num'],
        'descricao' => $array['descricao_tarefa'],
        'prioridade' => $array['prioridade'],
        'criador' => $array['criado_por'],
        'usuario' => $array['usuario_tarefa'],
        'data_criada' => $array['data_criada'],
        'data_final' => $array['data_final']
    );

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://127.0.0.1/engedoc_rapha/funcoes/kanban/email_cadastro.php',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($arrayPost),
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
        ),
    ));

    $ch = curl_exec($curl);
    // echo $ch;
    curl_close($curl);
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){

    if(isset($_POST['tarefa']) && isset($_POST['ptc']) && isset($_POST['prioridade']) && isset($_POST['descricao'])){
        $titulo = $_POST['tarefa'];    
        $ptc = $_POST['ptc']; 
        $prioridade = $_POST['prioridade']; 
        $descricao = $_POST['descricao'];
        $data_vencimento = $_POST['data_entrega'];
        $tempo_vencimento = $_POST['tempo_entrega'];
        
        $data = "$data_vencimento $tempo_vencimento";
        
        validaData($data);

        
        if(isset($_POST['usuarios'])){
            $usuarios = $_POST['usuarios'];
            if(is_array($usuarios)){

                foreach($usuarios as $usuario){
                    $sql = "INSERT INTO tarefas_criadas(titulo, prioridade, ptc_num, descricao_tarefa, criado_por, usuario_tarefa, data_final) 
                    values ('$titulo', '$prioridade', '$ptc', '$descricao' , '$userSession', '$usuario', '$data')";
                    $query = mysqli_query($conexao, $sql);
                    
                    if($query){
                        $last_inserted_id = mysqli_insert_id($conexao);
                        $sql = "INSERT INTO tarefas_todo(tarefa_id) values ('$last_inserted_id')";
                        $query = mysqli_query($conexao, $sql);

                        if($query){
                            curlEmail($last_inserted_id);
                            logDragging($userSession, $last_inserted_id, 'tarefas_todo', 'create');
                        }else{
                            echo json_encode(array(
                                'erro' => true,
                                'msg' => 'Ocorreu algum erro...'
                            ));
                            break;
                            die();
                        }
                    }
                }

                echo json_encode(array(
                    'erro' => false,
                    'msg' => 'Registro inserido com sucesso!'
                ));

            }else{
                exit();
            }

        }else{
            echo json_encode(array(
                'erro' => true,
                'msg' => 'Você deve selecionar ao menos um usuário.'
            ));
        }
    }else{
        exit();
    }

}