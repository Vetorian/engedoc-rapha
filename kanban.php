<?php require_once 'get_dados.php';
$cadastro = null;
$padrao = null;
$sequencial = null;
if(isset($_GET['cadastro'])){
    if($nivelSession == 1){?>
    <?php
        if($_GET['cadastro'] != 'sequencial' && $_GET['cadastro'] != 'padrao'){
            $cadastro = true;
        }elseif($_GET['cadastro'] == 'sequencial'){
            $sequencial = true;
        }elseif($_GET['cadastro'] == 'padrao'){
            $padrao = true;
        }
    }
}else if(!isset($_GET['id'])){
    header('Location: ?id='. $userSession);
}
?>
<!DOCTYPE html>
<html lang="pt-br">


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Kanban</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <link rel="stylesheet" href="assets/css/style_kanban.css"/>
    <link href="css/font-face.css" rel="stylesheet" media="all">
    <link href="vendor/font-awesome-4.7/css/font-awesome.min.css" rel="stylesheet" media="all">
	<link href="vendor/font-awesome-5/css/fontawesome-all.min.css" rel="stylesheet" media="all">
	<link href="vendor/mdi-font/css/material-design-iconic-font.min.css" rel="stylesheet" media="all">
	<!-- <link href="vendor/select2/select2.min.css" rel="stylesheet" media="all"> -->
	<link href="vendor/animsition/animsition.min.css" rel="stylesheet" media="all">
    <link href="vendor/wow/animate.css" rel="stylesheet" media="all">
	<link href="vendor/bootstrap/bootstrap.min.css" rel="stylesheet" media="all">
    <link href="css/theme.css" rel="stylesheet" media="all">
    <link rel="stylesheet" href="assets/js/jkanban.min.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/hamburgers/1.2.1/hamburgers.min.css">
    <script src="assets/js/renderizar_cadastro.js"></script>
    <script src="assets/js/register-task-step.js"></script> 
    
    
    
</head>
<style>
    iframe{ 
        height:250px;
        border: none;
    } 

    a {
        text-decoration: none;
    }

    .visevent{
        display: block;
    }
    .formedit{
        display: none;
    }
</style>



<body class="animsition">
    <div class="page-wrapper">
        <?php include_once 'subtelas/header-mobile.php';?>
        <?php include_once 'subtelas/sidebar.php';?>
        <div class="page-container">
            <?php include_once 'subtelas/header.php';?>
            <div class="main-content">
                <div class="section__content section__content--p30">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-9">
                                <?php 
                                if($cadastro == true){
                                    echo '<div id="container"></div>
                                    <script>renderizarTela()</script>';
                                }else if($padrao == true){
                                    echo '<div id="cadastro_task_pd"></div>';
                                }else if($sequencial == true){
                                    echo '<div id="cadastro_task_sq"></div>';
                                }else{
                                    echo '<div id="myKanban"></div>';
                                }
                                ?>
                            </div>

                            <div class="col-sm-3">
                                <?php 
                                if($nivelSession == 1 && isset($_GET['id']) && $_GET['id'] === $userSession){
                                    echo '<button class="au-btn au-btn-icon au-btn--green au-btn--small" id="adicionar-tarefa">
                                        <i class="zmdi zmdi-plus"></i>Adicionar uma tarefa</button>
                                    <button class="au-btn au-btn-icon au-btn--blue au-btn--small" id="filtrar-usuario">
                                        <i class="zmdi zmdi-eye"></i>Visualizar outros kanbans</button>';    
                                }else if($nivelSession == 1 && isset($_GET['id']) && $_GET['id'] !== $userSession){
                                    echo '<button class="au-btn au-btn-icon au-btn--blue au-btn--small" id="filtrar-usuario">
                                    <i class="zmdi zmdi-eye"></i>Visualizar outros kanbans</button>';
                                    echo '<button class="au-btn au-btn-icon au-btn--blue2 au-btn--small" id="voltar-kanban">
                                    <i class="zmdi zmdi-home"></i>Voltar para meu kanban</button>';
                                }
                                ?>    
                            </div>
                            
                        </div>
                        
                        </div>
                    </div>
                </div>  
            </div>
        </div>
    </div>

    <div class="modal fade" id="visualizar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Informações da Tarefa - <dd id="titulo_tarefa"></dd> </h5> 
                </div>
                <div class="modal-body">
                    <div class="visevent">
                        <dl class="row">
                          <dt class="col-sm-3">Prioridade da tarefa</dt>
                          <dd class="col-sm-9" id="prioridade"></dd>
                        </dl>
                        <dl class="row">
                          <dt class="col-sm-3">Data criada</dt>
                          <dd class="col-sm-9" id="data_criada"></dd>
                        </dl>
                        <dl class="row">
                          <dt class="col-sm-3">Data de término</dt>
                          <dd class="col-sm-9" id="data_termino"></dd>
                        </dl>
                        <dl class="row">
                          <dt class="col-sm-3">Quem criou a tarefa</dt>
                          <dd class="col-sm-9" id="created_by"></dd>
                        </dl>
                        <dl class="row">
                          <dt class="col-sm-3">Número PTC</dt>
                          <dd class="col-sm-9" id="ptc"></dd>
                        </dl>
                        <dl class="row">
                          <dt class="col-sm-3">Descricao da tarefa</dt>
                          <dd class="col-sm-9" id="descricao"></dd>
                        </dl>
                        <?php 
                        if($nivelSession == 1){?>
                          <button id="apagar_evento" class="btn btn-danger">Apagar</button><?php
                        }?>
                        
                    </div>
                </div>
            </div>
        </div>
      </div>

</body>


<script src='js/index.global.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js'></script>
<script src="vendor/bootstrap/popper.min.js"></script>
<script src="vendor/bootstrap/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/js-cookie@3.0.5/dist/js.cookie.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="vendor/animsition/animsition.min.js"></script>
<script src="js/main.js"></script>
<script src="assets/js/jkanban.js"></script>
<script src="js/kanban.js"></script>