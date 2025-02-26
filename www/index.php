<?php 
  if(!isset($_GET["folio"])){
    $Folio = "";
  }else{
    $Folio = $_GET["folio"];
  }
?>

<input hidden id="FolioCotizacionHeader"  value="<?= $Folio ?>">

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asesoramiento Raga</title>
    <link rel="stylesheet" href="plantilla.css">
    <link rel="stylesheet" href="citas.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>

<div id="myAlert" class="alert alert-warning alert-dismissible fade show d-none" role="alert">
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $('.modal').modal();
        //$('#BModalCargando').click();
        $('#RegimenFiscal').show();
        $('#UsoCFDI').show();
        $('#Estado').show();
        $('#Pais').show();
        
        var FolioCotizacionHeader = $('#FolioCotizacionHeader').val();
        if(FolioCotizacionHeader != ''){
            BuscarDatosAutomaticos(FolioCotizacionHeader);
        }
    });

    function BuscarDatosAutomaticos(FolioCotizacion){
        let datos = new FormData();
        datos.append('Operacion', 'BuscarDatosAutomaticos');
        datos.append('FolioCotizacion', FolioCotizacion);

        $.ajax(
        {
            type: "POST",
            url: "OperacionesAsesor.php",
            data: datos,
            contentType: false,
            processData: false,
        })
        .done(function(log) {
            console.log(log);
        })
        .fail(function(e) {
            console.log(e);
        })
        .always(function(Respuesta) {
            $('#NombreCompleto').val(Respuesta.split("|")[0]);
            $('#NumeroCelular').val(Respuesta.split("|")[1]);
            $('#FolioCotizacion').val(Respuesta.split("|")[2]);
        }); 
    }

    function Alerta(newText){
        var alertElement = document.getElementById('myAlert');
        alertElement.innerHTML = `${newText}`;
        alertElement.classList.remove('d-none');
        setTimeout(function() {
            alertElement.classList.add('d-none');
            alertElement.innerHTML = '';
        }, 3000);
    }

    function GuardarAsesor(){   
        var Nombre = $('#NombreCompleto').val();
        var Celular = $('#NumeroCelular').val();
        var Correo = $('#CorreoElectronico').val();
        var Folio = $('#FolioCotizacion').val();
        var Mensaje = $('#Mensaje').val();

        if(Nombre == "") return Alerta('Favor de Ingresar el Nombre');
        if(Celular == "") return Alerta('Favor de Ingresar el Numero Celular');
        if(Correo == "") return Alerta('Favor de Ingresar tu Correo Electronico');
        if(Folio == "") return Alerta('Favor de Ingresar tu Folio de Cotizacion');
        if(Mensaje == "") return Alerta('Favor de Ingresar el un Mensaje de Ayuda');

        Nombre = Nombre.trim();
        Celular = Celular.trim();
        Correo = Correo.trim();
        Folio = Folio.trim();
        Mensaje = Mensaje.trim();

        //Validaciones
        if(!/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/.test(Correo)) return Alerta('El Email no tiene el formato correcto');
        if(!/^[0-9]+$/.test(Celular)) return Alerta('El Telefono solo debe contener Numero');
        if(Celular.length != 10) return Alerta('El Telefono debe tener 10 digitos');

        let datos = new FormData();
        datos.append('Operacion', 'InsertarDatos');
        datos.append('Nombre', Nombre);
        datos.append('Celular', Celular);
        datos.append('Correo', Correo);
        datos.append('Folio', Folio);
        datos.append('Mensaje', Mensaje);

        $.ajax(
        {
            type: "POST",
            url: "OperacionesAsesor.php",
            data: datos,
            contentType: false,
            processData: false,
        })
        .done(function(log) {
            console.log(log);
        })
        .fail(function(e) {
            console.log(e);
        })
        .always(function(Respuesta) {
            AbrirWhatsapp(Respuesta);
            Limpiar(Respuesta);
        }); 
    }

    function AbrirWhatsapp(Respuesta){
        if(Respuesta == "Exito"){
            var Nombre = $('#NombreCompleto').val();
            var Celular = $('#NumeroCelular').val();
            var Correo = $('#CorreoElectronico').val();
            var Folio = $('#FolioCotizacion').val();
            var Mensaje = $('#Mensaje').val();

            Nombre = Nombre.replaceAll(' ', '%20')
            Celular = Celular.replaceAll(' ', '%20')
            Correo = Correo.replaceAll(' ', '%20')
            Folio = Folio.replaceAll(' ', '%20')
            Mensaje = Mensaje.replaceAll(' ', '%20')

            var URL = 'https://web.whatsapp.com/send?phone=528130870417&text='
            var Body = 'Buen Dia,%0aMi nombre es: ' + Nombre +
                        '%0aCon el folio de Cotizacion: ' + Folio + 
                        '%0aMensaje: ' + Mensaje + 
                        '%0a%0aMe Gustaria Solicitar Asesoramiento';
                                    
            URL = URL + Body;
            window.open(URL)
        }
    }

    function Limpiar(Respuesta){
        if(Respuesta == "Exito"){
            $('#NombreCompleto').val('');
            $('#NumeroCelular').val('');
            $('#CorreoElectronico').val('');
            $('#FolioCotizacion').val('');
            $('#Mensaje').val('');
        }else{
            Alerta('Error al Enviar los Datos, Intente Nuevamente');
        }
    }
</script>

<body>
    <header>
        <div id="Promo">
            <Label>Envíos sin costo en 3 a 5 días hábiles. Paga a meses sin intereses.</Label>
        </div>

        <div id="Imagen">
            <img src="raga-logo.png" alt="IMGRaga">
        </div>

        <nav id="Navbar" class="navbar navbar-expand-lg bg-body-tertiary">
            <div class="container-fluid">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                    <li class="nav-item dropdown">
                    <a id="TitulosNav" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Llantas
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="https://raga.com.mx/collections/llantas">Llantas</a></li>
                        <li><a class="dropdown-item" href="https://raga.com.mx/collections/bridgestone">Bridgestone</a></li>
                        <li><a class="dropdown-item" href="https://raga.com.mx/collections/firestone">Firestone</a></li>
                        <li><a class="dropdown-item" href="https://raga.com.mx/collections/gt-radial">GT Radial</a></li>
                        <li><a class="dropdown-item" href="https://raga.com.mx/collections/imperial">Imperial</a></li>
                        <li><a class="dropdown-item" href="https://raga.com.mx/collections/sportrak">Sportrak</a></li>
                        <li><a class="dropdown-item" href="https://raga.com.mx/collections/hankook">Hankook</a></li>
                    </ul>
                    </li>

                    <li class="nav-item dropdown">
                    <a id="TitulosNav" class="nav-link dropdown-toggle" href="https://raga.com.mx/collections/refacciones" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Refacciones
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="https://raga.com.mx/collections/refacciones">Refacciones</a></li>
                        <li><a class="dropdown-item" href="https://raga.com.mx/collections/balatas">Balatas</a></li>
                        <li><a class="dropdown-item" href="https://raga.com.mx/collections/amortiguadores">Amortiguadores</a></li>
                        <li><a class="dropdown-item" href="https://raga.com.mx/collections/filtros">Filtros</a></li>
                    </ul>
                    </li>

                    <li class="nav-item dropdown">
                    <a id="TitulosNav" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Servicios
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="https://raga.com.mx/pages/servicios">Servicios</a></li>
                        <li><a class="dropdown-item" href="https://raga.com.mx/pages/afinacion">Afinacion</a></li>
                        <li><a class="dropdown-item" href="https://raga.com.mx/pages/alineacionybalanceo">Alineacion y balanceo</a></li>
                        <li><a class="dropdown-item" href="https://raga.com.mx/pages/amortiguadores">Amortiguadores</a></li>
                        <li><a class="dropdown-item" href="https://raga.com.mx/pages/autoclima">Autoclima</a></li>
                        <li><a class="dropdown-item" href="https://raga.com.mx/pages/cambio-de-aceite">cambio de aceite</a></li>
                        <li><a class="dropdown-item" href="https://raga.com.mx/pages/cremalleras">Cremalleras</a></li>
                        <li><a class="dropdown-item" href="https://raga.com.mx/pages/frenos">Frenos</a></li>
                        <li><a class="dropdown-item" href="https://raga.com.mx/pages/suspension">Suspension</a></li>
                        <li><a class="dropdown-item" href="https://raga.com.mx/pages/reparacionderin">Reparación de rin</a></li>
                    </ul>
                    </li>

                    <li class="nav-item">
                        <a id="TitulosNav" class="nav-link" href="https://raga.com.mx/pages/centrosdeservicio">Centros de Servicio</a>
                    </li>

                    <li class="nav-item">
                        <a id="TitulosNav" class="nav-link" href="https://raga.com.mx/blogs/noticias">Blog</a>
                    </li>
                            
                </ul>
                <form class="d-flex" role="search">
                    <a href="https://buscador.raga.com.mx" id="BotonBuscarLlanta">Buscar Llantas</a>
                    <a href="https://facturacion.raga.com.mx" id="BotonFacturacionEnLinea">Facturacion En Linea</a>
                </form>
                </div>
            </div>
        </nav>

    </header>
    
    <main>
        <div class="container">

            <h1>Agendar Cita | Ayuda de un Asesor</h1>

            <div class="row row-cols-1 row-cols-sm-1 row-cols-md-2">
                <div class="col" id="car-image">
                    <img id="car-img-id" src="sucursal.png" alt="Sucursal" >
                </div>
                <div class="col">
                    <div class="form-container">

                        <div class="row row-cols-1 row-cols-sm-1 row-cols-md-1 FilaInput">
                            <div class="col">
                                <label for="basic-url" class="form-label">Nombre Completo</label>
                                <input type="text" class="form-control" id="NombreCompleto" placeholder="Nombre Completo">
                            </div>
                        </div>

                        <div class="row row-cols-1 row-cols-sm-1 row-cols-md-1 FilaInput">
                            <div class="col">
                                <label for="basic-url" class="form-label">Numero Celular</label>
                                <input type="text" class="form-control" id="NumeroCelular" placeholder="Numero Celular">
                            </div>
                        </div>
                        
                        <div class="row row-cols-1 row-cols-sm-1 row-cols-md-1 FilaInput">
                            <div class="col">
                                <label for="basic-url" class="form-label">Correo Electronico</label>
                                <input type="text" class="form-control" id="CorreoElectronico" placeholder="Correo Electronico">
                            </div>
                        </div>

                        <div class="row row-cols-1 row-cols-sm-1 row-cols-md-1 FilaInput">
                            <div class="col">
                                <label for="basic-url" class="form-label">Folio de Cotizacion</label>
                                <input type="text" class="form-control" id="FolioCotizacion" placeholder="Folio de Cotizacion">
                            </div>
                        </div>

                        <div class="row row-cols-1 row-cols-sm-1 row-cols-md-1 FilaInput">
                            <div class="col">
                                <label for="basic-url" class="form-label">¿Cómo podemos ayudarte?</label>
                                <textarea class="form-control" id="Mensaje" aria-label="With textarea"></textarea>
                            </div>
                        </div>

                        <div class="row row-cols-1 row-cols-sm-1 row-cols-md-1 FilaInput">
                            <button id="buscarboton" type="button" onclick="GuardarAsesor()">Guardar</button>
                        </div>

                    </div>
                </div>
            </div>

            <div class="logo">
                <center><img src="raga-logo.png" alt="Grupo Raga"></center>
            </div>
        </div>
    </main>
    
    <footer>
        <div class="container">
            <div class="row row-cols-1 row-cols-sm-1 row-cols-md-4">
                <div class="col">
                    <label id="TituloFooter">Suscríbete a nuestro <br>Newsletter</label>
                    <br><label id="SubTituloFooter">Recibe todas nuestras mejores</label>
                    <br><label id="SubTituloFooter">recomendaciones para cuidar tu auto</label>
                    <input id="EmailInput" type="text" class="form-control" placeholder="Direccion de correo electronico" aria-label="Direccion de correo electronico" aria-describedby="basic-addon1">
                </div>
                <div class="col">
                    <label id="TituloFooter">Menú principal</label>
                    <br><label href="https://raga.com.mx/collections/llantas" id="SubTituloFooter">Llantas</label>
                    <br><label href="https://raga.com.mx/collections/refacciones" id="SubTituloFooter">Refacciones</label>
                    <br><label href="https://raga.com.mx/pages/servicios" id="SubTituloFooter">Servicios</label>
                    <br><label href="https://raga.com.mx/pages/centrosdeservicio" id="SubTituloFooter">Centro de Servicio</label>
                    <br><label href="https://raga.com.mx/blogs/noticias" id="SubTituloFooter">Blog</label>
                </div>
                <div class="col">
                    <label id="TituloFooter">Conoce más</label>
                    <br><label href="https://raga.com.mx/policies/terms-of-service" id="SubTituloFooter">Términos del servicio</label>
                    <br><label href="https://raga.com.mx/policies/refund-policy" id="SubTituloFooter">Política de rembolso</label>
                    <br><label href="https://raga.com.mx/pages/envios-y-devoluciones" id="SubTituloFooter">Envío y devoluciones</label>
                    <br><label href="https://raga.com.mx/pages/nosotros" id="SubTituloFooter">Nosotros</label>
                    <br><label href="https://raga.com.mx/pages/contactanos" id="SubTituloFooter">Contactanos</label>
                </div>
                <div class="col">
                    <label id="TituloFooter">Síganos</label>

                    <div class="site-footer-block-content">
                        <div class="social-icons">
                            
                            <a class="social-link" title="Correo electrónico" href="mailto:contacto@raga.com.mx" target="_blank">
                            <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">      <path d="M14.5314 16.52C14.4762 16.5754 14.4101 16.6189 14.3374 16.6477C14.2647 16.6765 14.1868 16.6901 14.1086 16.6876C13.9588 16.6855 13.8158 16.6246 13.7105 16.5181L6 8.80762V20.1371H22V8.83619L14.5314 16.52Z" fill="currentColor"></path>      <path d="M21.2171 8H6.80762L14.1143 15.3086L21.2171 8Z" fill="currentColor"></path>    </svg>
                                <span class="visually-hidden">Encuéntrenos en Correo electrónico</span>
                            </a>

                            <a class="social-link" title="Facebook" href="https://www.facebook.com/GrupoRagaOficial" target="_blank">
                            <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">      <path fill-rule="evenodd" clip-rule="evenodd" d="M13.591 6.00441C11.5868 6.11515 9.75158 6.92966 8.34448 8.333C7.44444 9.23064 6.78641 10.2982 6.39238 11.5002C6.01229 12.6596 5.90552 13.9193 6.08439 15.1343C6.18456 15.8146 6.36736 16.4631 6.63981 17.1046C6.71166 17.2738 6.89438 17.6476 6.98704 17.815C7.22995 18.2538 7.52906 18.6904 7.84853 19.0725C8.16302 19.4486 8.56717 19.8479 8.94482 20.1556C9.6776 20.7526 10.5183 21.2186 11.4085 21.5211C11.8412 21.6681 12.259 21.7723 12.7342 21.8517L12.751 21.8545V19.0664V16.2783H11.7348H10.7186V15.1231V13.9678H11.7344H12.7503L12.7531 12.9265C12.756 11.8203 12.7553 11.845 12.7927 11.5862C12.9306 10.6339 13.3874 9.91646 14.1198 9.50212C14.4564 9.31168 14.8782 9.18341 15.331 9.13374C15.791 9.0833 16.55 9.12126 17.351 9.23478C17.4659 9.25105 17.5612 9.26437 17.5629 9.26437C17.5646 9.26437 17.566 9.70662 17.566 10.2472V11.2299L16.9679 11.233C16.3284 11.2363 16.299 11.2379 16.1298 11.2771C15.6926 11.3785 15.4015 11.6608 15.2983 12.0834C15.2566 12.2542 15.256 12.2685 15.256 13.1531V13.9678H16.3622C17.3606 13.9678 17.4685 13.9689 17.4685 13.9795C17.4685 13.9921 17.1263 16.2236 17.1191 16.2578L17.1148 16.2783H16.1854H15.256V19.0647V21.8511L15.2954 21.8459C15.4396 21.8271 15.8337 21.7432 16.0548 21.6844C16.5933 21.5411 17.079 21.3576 17.581 21.1076C19.3154 20.2441 20.6895 18.7615 21.4192 16.9663C21.7498 16.153 21.936 15.3195 21.9915 14.4052C22.0028 14.2197 22.0028 13.7268 21.9916 13.5415C21.9403 12.6947 21.7817 11.9389 21.4942 11.1712C20.8665 9.49533 19.6589 8.05123 18.1135 7.12853C17.7376 6.90413 17.2813 6.68103 16.8985 6.53456C16.1262 6.23908 15.3815 6.07432 14.5323 6.01114C14.3897 6.00053 13.7447 5.99591 13.591 6.00441Z" fill="currentColor"></path>    </svg>
                                <span class="visually-hidden">Encuéntrenos en Facebook</span>
                            </a>

                            <a class="social-link" title="Instagram" href="https://www.instagram.com/grupo_raga/" target="_blank">
                            <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">      <path fill-rule="evenodd" clip-rule="evenodd" d="M11.1213 6.00507C10.5981 6.02559 9.96558 6.0872 9.6431 6.14903C7.77505 6.50724 6.50522 7.77703 6.14804 9.644C6.0118 10.3562 6 10.7031 6 14.0006C6 17.298 6.0118 17.6449 6.14804 18.3572C6.50522 20.2241 7.776 21.4948 9.6431 21.852C10.3554 21.9882 10.7023 22 14 22C17.2977 22 17.6446 21.9882 18.3569 21.852C20.224 21.4948 21.4948 20.2241 21.852 18.3572C21.9882 17.6449 22 17.298 22 14.0006C22 10.7031 21.9882 10.3562 21.852 9.644C21.4935 7.77016 20.2144 6.49675 18.3396 6.14716C17.6551 6.01955 17.3874 6.00985 14.334 6.00234C12.707 5.99836 11.2612 5.99957 11.1213 6.00507ZM17.6262 7.50836C18.2783 7.59344 18.7654 7.73848 19.1427 7.95992C19.3813 8.09994 19.9011 8.61966 20.0411 8.85821C20.2728 9.253 20.4142 9.74012 20.4952 10.4223C20.5551 10.9261 20.5551 17.075 20.4952 17.5789C20.4142 18.261 20.2728 18.7482 20.0411 19.143C19.9011 19.3815 19.3813 19.9012 19.1427 20.0412C18.7479 20.2729 18.2608 20.4143 17.5785 20.4953C17.0747 20.5552 10.9253 20.5552 10.4215 20.4953C9.73923 20.4143 9.25207 20.2729 8.85726 20.0412C8.61869 19.9012 8.09893 19.3815 7.9589 19.143C7.72724 18.7482 7.58578 18.261 7.50476 17.5789C7.44493 17.075 7.44493 10.9261 7.50476 10.4223C7.56313 9.93096 7.62729 9.63856 7.74686 9.31938C7.88402 8.95319 8.02204 8.72965 8.28724 8.44428C8.87822 7.8083 9.55222 7.55184 10.8191 7.48098C11.5114 7.44227 17.2981 7.46552 17.6262 7.50836ZM17.9602 8.80646C17.7222 8.8876 17.4343 9.18659 17.358 9.43194C17.1268 10.175 17.8258 10.874 18.569 10.6429C18.8334 10.5606 19.1165 10.2776 19.1987 10.013C19.2689 9.78758 19.251 9.52441 19.1511 9.31187C19.071 9.14148 18.8248 8.90306 18.6554 8.83162C18.4699 8.75347 18.1498 8.74189 17.9602 8.80646ZM13.6183 9.8962C12.6459 9.99712 11.7694 10.4112 11.0899 11.0907C9.99978 12.1807 9.61075 13.7764 10.076 15.2492C10.4746 16.5107 11.4897 17.5257 12.7513 17.9243C13.5638 18.1809 14.4362 18.1809 15.2487 17.9243C16.5103 17.5257 17.5254 16.5107 17.924 15.2492C18.1806 14.4367 18.1806 13.5644 17.924 12.752C17.5254 11.4904 16.5103 10.4754 15.2487 10.0769C14.7428 9.91709 14.1016 9.84604 13.6183 9.8962ZM14.6362 11.4119C14.9255 11.4811 15.4416 11.7393 15.6794 11.9337C15.9731 12.1738 16.2113 12.4794 16.3856 12.8396C16.5969 13.2766 16.6509 13.5128 16.6509 14.0006C16.6509 14.4884 16.5969 14.7246 16.3856 15.1615C16.1137 15.7235 15.7253 16.1118 15.161 16.3855C14.7247 16.5972 14.4883 16.6513 14 16.6513C13.5117 16.6513 13.2753 16.5972 12.839 16.3855C12.2747 16.1118 11.8863 15.7235 11.6144 15.1615C11.5298 14.9866 11.4355 14.7433 11.4049 14.6208C11.3288 14.3169 11.3288 13.6843 11.4049 13.3803C11.482 13.0724 11.7369 12.5611 11.933 12.3213C12.3447 11.8177 12.9934 11.449 13.6224 11.3611C13.8845 11.3244 14.3734 11.3489 14.6362 11.4119Z" fill="currentColor"></path>    </svg>
                                <span class="visually-hidden">Encuéntrenos en Instagram</span>
                            </a>

                            <a class="social-link" title="LinkedIn" href="https://www.linkedin.com/company/35863575/admin/" target="_blank">
                            <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">      <path d="M14.96 12.4356C15.4151 11.7244 16.2293 10.7307 18.0516 10.7307C20.3076 10.7307 22 12.2044 22 15.3742V21.2889H18.5707V15.7778C18.5707 14.3911 18.0747 13.4436 16.8338 13.4436C15.8862 13.4436 15.3227 14.0818 15.0738 14.6987C14.9856 14.968 14.9471 15.2511 14.96 15.5342V21.2889H11.5289C11.5289 21.2889 11.5751 11.9413 11.5289 10.9778H14.96V12.4356ZM7.94133 6C6.768 6 6 6.76978 6 7.77778C6 8.78578 6.74489 9.55556 7.89511 9.55556H7.91822C9.11467 9.55556 9.85956 8.76267 9.85956 7.77778C9.85956 6.79289 9.11467 6 7.94133 6ZM6.20444 21.2889H9.63378V10.9778H6.20444V21.2889Z" fill="currentColor"></path>    </svg>
                                <span class="visually-hidden">Encuéntrenos en LinkedIn</span>
                            </a>

                            <a class="social-link" title="YouTube" href="https://www.youtube.com/channel/UCWEcQdN7a8YyZQko0aatq5Q" target="_blank">
                            <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">      <path d="M21.68 10.7286C22 11.9386 22 14.5 22 14.5C22 14.5 22 17.0614 21.68 18.2714C21.5909 18.6122 21.4103 18.9233 21.157 19.1721C20.9037 19.4208 20.587 19.5982 20.24 19.6857C19.008 20 14 20 14 20C14 20 8.992 20 7.76 19.6857C7.41301 19.5982 7.09631 19.4208 6.843 19.1721C6.58968 18.9233 6.40906 18.6122 6.32 18.2714C6 17.0614 6 14.5 6 14.5C6 14.5 6 11.9386 6.32 10.7286C6.512 10.0371 7.056 9.50286 7.76 9.31429C8.992 9 14 9 14 9C14 9 19.008 9 20.24 9.31429C20.944 9.50286 21.488 10.0371 21.68 10.7286ZM12.4 16.8571L16.56 14.5L12.4 12.1429V16.8571Z" fill="currentColor"></path>    </svg>
                                <span class="visually-hidden">Encuéntrenos en YouTube</span>
                            </a>

                        </div>
                    </div>
    
                </div>
            </div>

            <div id="separadorfooter">

            </div>

            <div class="row row-cols-1 row-cols-sm-1 row-cols-md-1">
                <div class="col">
                    <label href="https://raga.com.mx/collections/llantas" id="SubTituloFooter">Llantas</label>
                    <label id="SubTituloFooter"> | </label>
                    <label href="https://raga.com.mx/collections/refacciones" id="SubTituloFooter">Refacciones</label>
                    <label id="SubTituloFooter"> | </label>
                    <label href="https://raga.com.mx/pages/servicios" id="SubTituloFooter">Servicios</label>
                    <label id="SubTituloFooter"> | </label>
                    <label href="https://raga.com.mx/pages/centrosdeservicio" id="SubTituloFooter">Centro de Servicio</label>
                    <label id="SubTituloFooter"> | </label>
                    <label href="https://raga.com.mx/blogs/noticias" id="SubTituloFooter">Blog</label>
                    <br>
                    <label id="SubTituloFooter">Propiedad artística © 2024 grupo-raga.</label>
                    <br>
                    <label id="SubTituloFooter">Tecnología de Shopify</label>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>



