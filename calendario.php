<?php require_once 'get_dados.php';
    if(!isset($_GET['id']) && !isset($_GET['f'])){
        $id_get = 'all';
        $formato = 0;
    }else{
        $id_get = $_GET['id'];
        $formato = $_GET['f'];
    }

?>



<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Calendário</title>
    
    <link href="css/font-face.css" rel="stylesheet" media="all">
    <link href="vendor/font-awesome-4.7/css/font-awesome.min.css" rel="stylesheet" media="all">
	<link href="vendor/font-awesome-5/css/fontawesome-all.min.css" rel="stylesheet" media="all">
	<link href="vendor/mdi-font/css/material-design-iconic-font.min.css" rel="stylesheet" media="all">
	<link href="vendor/select2/select2.min.css" rel="stylesheet" media="all">
	<link href="vendor/animsition/animsition.min.css" rel="stylesheet" media="all">
    <link href="vendor/wow/animate.css" rel="stylesheet" media="all">
	<link href="vendor/bootstrap/bootstrap.min.css" rel="stylesheet" media="all">
    <link href="css/theme.css" rel="stylesheet" media="all">
    <link rel="stylesheet" href="assets/css/personalizado.css">
    <link href="assets/css/calendario.css" rel="stylesheet" media="all">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/hamburgers/1.2.1/hamburgers.min.css">
    
    
</head>
<style>
    iframe{ 
        height:250px;
        border: none;
    } 

    a:hover{
        text-decoration: none;
    }
</style>

<body class="animsition">
    <div class="page-wrapper">
        <?php include_once 'subtelas/header-mobile.php';?>
        <?php include_once 'subtelas/sidebar.php';?>
        <div class="page-container">
            <?php include_once 'subtelas/header.php';?>
            <div class="main-content">
                <?php
                if (isset($_SESSION['msg'])) {
                    echo $_SESSION['msg'];
                    unset($_SESSION['msg']);
                }
                if (isset($_GET['recusado'])) {
                    echo '<div class="alert alert-success" role="alert"> Você recusou o evento!</div>';
                }
                ?>
                <div class="section__content section__content--p30">
                    <div class="container-fluid">
                        <div class="row">
                            
                            <div class="col-lg-9">
                                <div id='calendar' style="padding: 10px;"></div>
                            </div>

                            <div class="col-sm-3">
                                <center><strong>PROXIMAS REUNIÕES</strong></center>
                                <br>
                                <iframe scrolling="yes" src="funcoes/calendario/iframe.php"></iframe>
                                <br><br>
                                <center><strong>SUAS TAREFAS</strong></center>
                                <br>
                                <iframe scrolling="yes" src="funcoes/kanban/iframe.php?id=<?=$userSession?>"></iframe>
                                <br>
                                <div class="select">
                                    <div class="select-interativos">
                                        <select class="form-control" id="trocacalendario" onmousedown="if(this.options.length>8){this.size=8;}"  onchange="javascript:location.href = this.value;" onblur="this.size=0;">>
                                            <?php
                                            $sql = "SELECT * FROM usuario ORDER BY FIND_IN_SET(id,'".$id_get."') DESC, id asc";
                                            $retorno = mysqli_query($conexao,$sql);
                                            if($id_get == 'all' ){
                                                echo '<option>Todos</option>';
                                            }
                                            while($array = mysqli_fetch_array($retorno, MYSQLI_ASSOC)){
                                                if($array['nome'] != 'Tv'){
                                                    $id = $array['id'];
                                                    $nome = $array["nome"];
                                                }
                                            ?>
                                            <option value="?id=<?=$id?>&f=<?=$formato?>"><?=$nome?></option> <?php }?>
                                        </select>


                                        <select class="form-control" style="margin-left: auto;" id="filtro-calendario" onchange="javascript:location.href = this.value;" >
                                            <?php
                                            echo ($formato == '0') ? '<option disabled selected>Filtre por formato</option>' : '<option disabled>Filtre por formato</option>';

                                            echo ($formato == '1') ? "<option selected value='?id=$id_get&f=1'>Presencial</option>" : "<option value='?id=$id_get&f=1'>Presencial</option>";
                                            
                                            echo ($formato == '2') ? "<option selected value='?id=$id_get&f=2'>Remoto</option>" : "<option value='?id=$id_get&f=2'>Remoto</option> " ;
                                            
                                            echo ($formato == '3') ? "<option selected value='?id=$id_get&f=3'>Presencial em Campo</option>" : "<option value='?id=$id_get&f=3'>Presencial em Campo</option>" ;
                                            
                                            echo ($formato == '4') ? "<option selected value='?id=$id_get&f=4'>Presencial na Engeline</option>" : "<option value='?id=$id_get&f=4'>Presencial na Engeline</option>" ;

                                            echo ($formato == '5') ? "<option selected value='?id=$id_get&f=5'>Compromisso Pessoal</option>" : "<option value='?id=$id_get&f=5'>Compromisso Pessoal</option>" ;
                                            ?>
                                        </select>
                                        <?php 
                                        if(isset($_GET['id']) || isset($_GET['f'])){
                                            ?>
                                            <button value="calendario" onclick="javascript:location.href= this.value;" class="btn btn-warning btn-edit">Remover filtros</button>
                                            <?php
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
        </div>
    </div>

    <div class="modal fade" id="visualizar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Detalhes do Evento</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="visevent">
                        <dl class="row">

                            <dt class="col-sm-3">Título do evento</dt>
                            <dd class="col-sm-9" id="title"></dd>

                            <dt class="col-sm-3">Data evento</dt>
                            <dd class="col-sm-9" id="start"></dd>
                            <!-- <dd class="col-sm-4" id="start-time"></dd> -->

                            <dt class="col-sm-3">Data fim evento</dt>
                            <dd class="col-sm-9" id="end"></dd>
                            <!-- <dd class="col-sm-4" id="end-time"></dd> -->
                            
                            <dt class="col-sm-3">Convidados</dt>
                            <dd class="col-sm-9" id="convidados"></dd>

                            <dt class="col-sm-3">Cadastrado por</dt>
                            <dd class="col-sm-9" id="usuario_id"></dd> 
                            
                            <dt class="col-sm-3">Formato</dt>
                            <dd class="col-sm-9" id="formato"></dd>
                            
                            <div id="link-container" style="display: none;">
                                <dt class="col-sm-3">Link</dt>
                                <a id="link-href">
                                    <dd class="col-sm-9" id="link">
                                        Clique Aqui
                                    </dd>
                                </a>
                            </div>

                            <div id="sala-container" style="display: none;">
                                <dt class="col-sm-3">Sala</dt>
                                    <dd class="col-sm-9" id="sala"></dd>
                            </div>

                        </dl>
                        <button class="btn btn-warning btn-canc-vis">Editar</button>
                        <a href="" id="apagar_evento" class="btn btn-danger">Apagar</a>
                    </div>


                    <!-- editar -->
                    <div class="formedit">
                        <span id="msg-edit"></span>
                        <form id="editevent" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="id" id="id">
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Título</label>
                                <div class="col-sm-10">
                                    <input type="text" name="title" class="form-control" id="title" placeholder="Título do evento">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Início do evento</label>
                                <div class="col-sm-3">
                                    <input type="date" name="start" class="form-control" id="start">
                                </div>
                                <div class="col-sm-2">
                                    <input type="time" name="start-time" class="form-control" id="start-time">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Final do evento</label>
                                <div class="col-sm-3">
                                    <input type="date" name="end" class="form-control" id="end">
                                </div>
                                <div class="col-sm-2">
                                    <input type="time" name="end-time" class="form-control" id="end-time">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Formato</label>
                                <div class="col-sm-10">
                                    <select class="form-control" name="formatoedit" id="formatoedit">
                                        <option value="#A020F0">Presencial</option>
                                        <option value="#FF4500">Remoto</option>
                                        <option value="#808080">Compromisso Pessoal</option>
                                        <option value="#FFCCCC">Presencial em Campo</option>
                                    </select>
                                </div>
                            </div>
                            <div id="sala-engeline-edit">
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Compromisso na sala da engeline?</label>
                                <div class="col-sm-2">
                                    <input type="checkbox" id="salaengelineedit" name="sala-engeline-edit" value="sala-engeline">
                                    <label for="salaengelineedit">Sim</label>
                                </div>
                            </div>
                        </div>
                            <div id="link-reuniao-edit">
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Link da reunião</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="link-edit" class="form-control" id="link-edit" placeholder="Link do evento" style:"display:none;">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Convidar</label>
                                    <select size="5" name="edit_select[]" class="form-control" multiple="multiple" id="edit_select">
                                        <?php 
                                        
                                        $sql = "SELECT * from usuario order by id ASC";
                                        $query = mysqli_query($conexao,$sql);

                                        while($array = mysqli_fetch_array($query)){
                                            $nome = $array['nome'];
                                            if($nome != 'Tv'){
                                                echo '<option>'. $nome . '</option>';
                                            }
                                        }	
                                        ?>
                                    </select>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-10">
                                    <button type="button" class="btn btn-primary btn-canc-edit">Cancelar</button>
                                    <button type="submit" name="CadEvent" id="CadEvent" value="CadEvent" class="btn btn-warning btn-edit">Salvar</button>                                    
                                </div>
                            </div>
                        </form>                            
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- cadastrar -->
    <div class="modal fade" id="cadastrar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Cadastrar Evento</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <span id="msg-cad"></span>
                    <form id="addevent" method="POST" enctype="multipart/form-data">
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Título</label>
                            <div class="col-sm-10">
                                <input type="text" name="title" class="form-control" id="title" placeholder="Título do evento">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Início do evento</label>
                            <div class="col-sm-3">
                                <input type="date" name="start" class="form-control" id="start">
                            </div>
                            <div class="col-sm-2">
                                <input type="time" name="start-time" class="form-control" id="start-time">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Final do evento</label>
                            <div class="col-sm-3">
                                <input type="date" name="end" class="form-control" id="end">
                            </div>
                            <div class="col-sm-2">
                                <input type="time" name="end-time" class="form-control" id="end-time">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Formato</label>
                            <div class="col-sm-10">
                            <select name="formato_cad" id="formato_cad" class="form-control">
                                <option value="#A020F0">Presencial</option>
                                <option value="#FF4500">Remoto</option>
                                <option value="#808080">Compromisso Pessoal</option>
                                <option value="#FFCCCC">Presencial em Campo</option>
                                <option class="instalacao" value="#1A3B5C">Instalação</option>
                            </select>
                            </div>
                        </div>

                        <div id="produtos">
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Produtos</label>
                                <div class="col-sm-10">
                                    <select name="produto" id="produto" class="form-control">
                                        <option value="ST310UC2">ST310UC2</option>
                                        <option value="ST340UR">ST340UR</option>
                                        <option value="ANTENA_ST340">ANTENA + ST340</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div id="sala-engeline">
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Compromisso na sala da engeline?</label>
                                <div class="col-sm-2">
                                    <input type="checkbox" id="salaengeline" name="sala-engeline" value="sala-engeline">
                                    <label for="salaengeline">Sim</label>
                                </div>
                            </div>
                        </div>
                        <div id="link-reuniao">
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Link da reunião</label>
                                <div class="col-sm-10">
                                    <input type="text" name="link" class="form-control" id="link" placeholder="Link do evento" style:"display:none;">
                                </div>
                            </div>
                        </div>
                        <div id="compromisso-recorrente">
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Compromisso Recorrente?</label>
                            <div class="col-sm-2">
                                <input type="checkbox" id="recorrente" name="recorrente" value="recorrente">
                                <label for="recorrente">Sim</label>
                            </div>
                            <div class="col-sm-4">
                                <select name="recorrenteTipo" id="recorrenteTipo" class="form-control">
                                    <option value="semanalmente">Semanalmente</option>
                                    <option value="mensalmente">Mensalmente</option>
                                </select>
                            </div>
                            <div class="col-sm-4">
                                <input type="number" name="recorrenteSemanas" id="recorrenteValorSemana" min="1" max="24" class="form-control" placeholder="Por quantas semanas?">
                                <input type="number" name="recorrenteMeses" id="recorrenteValorMes" min="1" max="12" class="form-control" placeholder="Por quantos meses?">
                            </div>
                        </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Convidar</label>
                            <div class="col-sm-10">
                            <select size="5" name="current_select[]" class="form-control" multiple="multiple" id="current_select">
                                <?php 
                                
                                $sql = "SELECT * from usuario order by id ASC";
                                $query = mysqli_query($conexao,$sql);

                                while($array = mysqli_fetch_array($query)){
                                    $nome = $array['nome'];
                                    if($nome != 'Tv'){
                                        echo '<option>'. $nome . '</option>';
                                    }
                                }	
                                ?>
                            </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-10">
                                <button type="submit" name="CadEvent" id="CadEvent" value="CadEvent" class="btn btn-success btn-cad">Cadastrar</button>  
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script src='js/index.global.js'></script>
    
	<!-- <script src="vendor/jquery-3.2.1.min.js"></script> -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src="vendor/bootstrap/popper.min.js"></script>
	<script src="vendor/bootstrap/bootstrap.min.js"></script>
	<script src="vendor/select2/select2.min.js"></script>
	<script src="vendor/animsition/animsition.min.js"></script>
	<script src="js/main.js"></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js'></script>   
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/calendario.js"></script>
</body>


<script>

    document.addEventListener('DOMContentLoaded', function () {
    var calendarEl = document.getElementById('calendar');
    var sala_engeline = 1;
    var calendar = new FullCalendar.Calendar(calendarEl, {
        headerToolbar: {
            left: 'prev,next,today',
            center: 'title',
            right: 'multiMonthYear,dayGridMonth,dayGridWeek,dayGridDay listWeek'
        },
        locale: 'pt-br',
        buttonText: {
            today:'hoje',
            month:'mes',
            week:'semana',
            day: 'dia',
            listWeek: 'lista da semana',
            year: 'ano'
        },
        themeSystem: 'bootstrap',
        allDayText: 'dia todo',
        moreLinkText: 'mais',
        noEventsText: 'Sem compromissos',
        height: 800,
        contentHeight: 800,
        nowIndicator: true, 
        navLinks: true,
        editable: true,
        dayMaxEvents: true, 
        // initialView: 'listWeek',
        events: 'funcoes/calendario/list_eventos.php?id=<?=$id_get?>&formato=<?=$formato?>',

        eventDidMount: function(info) {
            $(info.el).tooltip({
                    title: info.event.extendedProps.formato,
                    container: 'body',
                    delay: { "show": 50, "hide": 50 }
                });
            },
        eventClick: function (info) {
            
            $("#apagar_evento").attr("href", "funcoes/calendario/proc_apagar_evento.php?id=" + info.event.id + "&titulo="+ info.event.title);
            info.jsEvent.preventDefault(); 
            $('#visualizar #id').text(info.event.id);
            $('#visualizar #id').val(info.event.id);
            $('#visualizar #title').text(info.event.title);
            $('#visualizar #title').val(info.event.title);
            $('#visualizar #convidados').text(info.event.extendedProps.convidados);
            $('#visualizar #convidados').val(info.event.extendedProps.convidados);
            $('#visualizar #formato').text(info.event.extendedProps.formato);
            $('#visualizar #formato').val(info.event.extendedProps.formato);
            $('#visualizar #formatoedit option[value='+info.event.backgroundColor +']').prop('selected', true);

            
            
            if(info.event.extendedProps.link != null && info.event.extendedProps.link != "" ){
                $('#visualizar #link-container').show();
                // $('#visualizar #link').text(info.event.extendedProps.link);
                $('#visualizar #link').val(info.event.extendedProps.link);
                $('#visualizar #link-href').attr('href', info.event.extendedProps.link);
                $('#visualizar #link-href').attr('target', '_blank');
            }else{
                $('#visualizar #link-container').hide();
            }

            if(info.event.extendedProps.sala_engeline != false && info.event.extendedProps.formato == 'Presencial'){
                $('#visualizar #sala-container').show();
                $('#visualizar #sala').text(info.event.extendedProps.sala_engeline);
                if(info.event.extendedProps.sala_engeline == 'Compromisso na sala engeline'){
                    var sala_engeline = 1 ;
                    // console.log(sala_engeline);
                }
                $('#visualizar #sala-engeline-edit').prop('checked', sala_engeline);
            }else{
                sala_engeline = 0;
                // console.log(sala_engeline);
                $('#visualizar #sala-engeline-edit').prop('checked', sala_engeline);
                $('#visualizar #sala-container').hide();
            }


            
            $('#visualizar #usuario_id').text(info.event.extendedProps.usuario_id);
            $('#visualizar #usuario_id').val(info.event.extendedProps.usuario_id);
            const dataStart = moment(info.event.extendedProps['start-time'], 'HH:mm:ss').toDate();
            const dataEnd = moment(info.event.extendedProps['end-time'], 'HH:mm:ss').toDate();
            $('#visualizar #start').val(info.event.extendedProps['start-edit']);
            $('#visualizar #start').text(moment(info.event.start).format('DD/MM/YYYY') + ' às ' + moment(dataStart).format('HH:mm'));
            $('#visualizar #start-time').text(info.event.extendedProps['start-time']);
            $('#visualizar #start-time').val(info.event.extendedProps['start-time']);
            if(info.event.endStr == ''){
                $('#visualizar #end').val(info.event.extendedProps['end-edit']);
                $('#visualizar #end').text(moment(info.event.start).format('DD/MM/YYYY') + ' às ' +  moment(dataEnd).format('HH:mm'));
            }else{
                $('#visualizar #end').val(info.event.extendedProps['end-edit']);
                $('#visualizar #end').text(moment(info.event.end).format('DD/MM/YYYY') + ' às ' +  moment(dataEnd).format('HH:mm'));
            }
            $('#visualizar #end-time').text(info.event.extendedProps['end-time']);
            $('#visualizar #end-time').val(info.event.extendedProps['end-time']);
            $('#visualizar #color').val(info.event.backgroundColor);
            $('#visualizar').modal('show');

        },
        
        selectable: true,
        select: function (info) {
            console.log(info);
            var start = moment(info.startStr).format("YYYY-MM-DD");
            // var end = moment(info.endStr).format("YYYY-MM-DD");
            var end = moment(info.endStr).subtract(1, 'days').format("YYYY-MM-DD");
            if(start >= moment().format('YYYY-MM-DD')){
                $('.instalacao').css('display', 'block');
            }else{
                $('#formato_cad').prop('selectedIndex', 0);
                $('.instalacao').css('display', 'none');
            }
            $('#cadastrar #start').val(start);
            $('#cadastrar #end').val(end);
            $('#cadastrar').modal('show');
        }
    });

    calendar.render();

});

</script>
