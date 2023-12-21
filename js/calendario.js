// multiselect
$('#current_select').multiselect({		
        nonSelectedText: 'Selecione o convidado ',
        allSelectedText: 'Todos selecionados',			
});


$('#edit_select').multiselect({		
    nonSelectedText: 'Selecione o convidado ',
    allSelectedText: 'Todos selecionados',			
});

// 

// toggles 
$('.btn-canc-vis').on("click", function(){
    $('.visevent').slideToggle();
    $('.formedit').slideToggle();
});

$('.btn-canc-edit').on("click", function(){
    $('.formedit').slideToggle();
    $('.visevent').slideToggle();
});
// 


$('#formato_cad').on('change', function() {
    var y = document.getElementById('link-reuniao');
    var x = document.getElementById('sala-engeline');
    var c = document.getElementById('compromisso-recorrente');
    var p = document.getElementById('produtos');
    
    $("#link").show();
    var valor = this.value;
    console.log(valor);
    if(valor == '#FF4500'){
        y.style.display = 'block';
    }else{
        y.style.display = 'none';
    }
    
    if(valor == '#1A3B5C'){
        c.style.display = 'none'
        p.style.display = 'block'
    }else{
        p.style.display = 'none'
        c.style.display = 'block'
    }

    if(valor == '#A020F0'){
        x.style.display = 'block';
    }else{
        x.style.display = 'none';
    }

    
});

$('#formatoedit').on('change', function() {
    var y = document.getElementById('link-reuniao-edit');
    var x = document.getElementById('sala-engeline-edit');
    $("#link").show();
    var valor = this.value;
    if(valor == '#FF4500'){
        y.style.display = 'block';
    }else{
        y.style.display = 'none';
    }

    if(valor == '#A020F0'){
        x.style.display = 'block';
    }else{
        x.style.display = 'none';
    }

});


$("#recorrente").change(function() {

    var tipo = document.getElementById('recorrenteTipo');
    var semana = document.getElementById('recorrenteValorSemana');
    var mes = document.getElementById('recorrenteValorMes');

    
    if ($(this).is(":checked")) {
        
        tipo.style.display = 'block';
        semana.style.display = 'block';
        $('#recorrenteTipo').on('change', function() {
            
            var valorSelecionado = this.value;
            
            if(valorSelecionado == 'mensalmente'){
                
                mes.style.display = 'block';
                semana.style.display = 'none';

            }else if(valorSelecionado == 'semanalmente'){
                
                semana.style.display = 'block';
                mes.style.display = 'none';

            }
        });
    } else {
        // quando deselecionar o checkbox, esconder todas divs
        tipo.style.display = 'none';
        semana.style.display = 'none';
        mes.style.display = 'none';
    }
});

// formularios

$("#addevent").on("submit", function (event) {
        
    event.preventDefault();
    $.ajax({
        method: "POST",
        url: "funcoes/cad_event.php",
        data: new FormData(this),
        contentType: false,
        processData: false,
        beforeSend: function () {
            Swal.fire({
                title: 'Aguarde...',
                text: 'Cadastrando evento...',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });
        },
        success: function (retorna) {
            console.log(retorna);
            Swal.close();
            if(retorna['sucessful']){
                Swal.fire({
                    title: 'Evento cadastrado!',
                    html: 'A página se auto-reiniciará em 5 segundos.',
                    icon: 'success',
                    didOpen: () => {
                        Swal.showLoading()
                    },
                })
                setTimeout(function() {
                    location.reload();
                }, 5000)
            }
            if(retorna['warning']){
                Swal.fire({
                    icon: 'warning',
                    title: 'Atenção!',
                    text: 'Evento cadastrado porem temos poucos rastreadores no estoque!',
                    didOpen: () => {
                        Swal.showLoading()
                    },
                })
                setTimeout(function() {
                    location.reload();
                }, 5000)
            }
            if (retorna['sit']) {
                $("#msg-cad").html(retorna['msg']);
            } else {
                $("#msg-cad").html(retorna['msg']);
            }
        }
    })
});

$("#editevent").on("submit", function (event) {
    event.preventDefault();

    $.ajax({
        method: "POST",
        url: "funcoes/edit_event.php",
        data: new FormData(this),
        contentType: false,
        processData: false,
        beforeSend: function () {
            Swal.fire({
                title: 'Aguarde...',
                text: 'Editando evento...',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });
        },
        success: function (retorna) {
            console.log(retorna);
            Swal.close();
            if(retorna['sucessful']){
                Swal.fire({
                    title: 'Evento cadastrado!',
                    html: 'A página se auto-reiniciará em 5 segundos.',
                    icon: 'success',
                    didOpen: () => {
                        Swal.showLoading()
                    },
                })
                setTimeout(function() {
                    location.reload();
                    }, 5000)
            }

            if (retorna['sit']) {
                $("#msg-edit").html(retorna['msg']);
                // console.log('sucess');
            } else {
                $("#msg-edit").html(retorna['msg']);
                // console.log('erro');
            }
        }
    })
});