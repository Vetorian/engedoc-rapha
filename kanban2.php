<?php require_once 'get_dados.php';
$cadastro = null;
if(isset($_GET['cadastro'])){
    if($nivelSession == 1){?>
    <?php
        if($_GET['cadastro'] != 'sequencial' && $_GET['cadastro'] != 'padrao'){
            $cadastro = true;
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
    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" /> -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <link rel="stylesheet" href="assets/css/style_kanban.css"/>
    <link href="css/font-face.css" rel="stylesheet" media="all">
    <link href="vendor/font-awesome-4.7/css/font-awesome.min.css" rel="stylesheet" media="all">
	<link href="vendor/font-awesome-5/css/fontawesome-all.min.css" rel="stylesheet" media="all">
	<link href="vendor/mdi-font/css/material-design-iconic-font.min.css" rel="stylesheet" media="all">
	<link href="vendor/select2/select2.min.css" rel="stylesheet" media="all">
	<link href="vendor/animsition/animsition.min.css" rel="stylesheet" media="all">
    <link href="vendor/wow/animate.css" rel="stylesheet" media="all">
	<link href="vendor/bootstrap-4.1/bootstrap.min.css" rel="stylesheet" media="all">
    <link href="css/theme.css" rel="stylesheet" media="all">
    <link rel="stylesheet" href="assets/js/jkanban.min.css"/>
    <script src="assets/js/renderizar_cadastro.js"></script>
    
    
    
</head>
<style>
    iframe{ 
        height:250px;
        border: none;
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
                                }else{
                                    echo '<div id="myKanban"></div>';
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
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
<!-- <script src="vendor/jquery-3.2.1.min.js"></script> -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script> -->
<script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js'></script>
<script src="vendor/bootstrap-4.1/popper.min.js"></script>
<script src="vendor/bootstrap-4.1/bootstrap.min.js"></script>
<script src="vendor/select2/select2.min.js"></script>
<script src="vendor/animsition/animsition.min.js"></script>
<script src="js/main.js"></script>
<script src="assets/js/jkanban.js"></script>
<script src="js/kanban.js"></script>
