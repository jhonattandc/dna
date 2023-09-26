@extends('adminlte::page')

@section('title', 'Inicio')

@section('content')
    <div class="container">
        <article>
            <header>    
                <div class="row">
                    <h1 class="col-8 col-sm-8 col-md-8">Introducción</h1>
                    <span class="col-2 col-sm-2 col-md-4 text-right">Last updated: 07-09-2023</span>
                </div>
                <section>
                    <p>
                        Esta es una aplicación que se encarga de sincronizar la información de las tres plataformas clientify,
                        thinkific y Q10. Esta escrita es PHP con el framework Laravel, su principio de funcionamiento se basa en
                        crontab, comandos artisan y trabajos en colas para el consumo automático de las API.
                    </p>
                    <p>
                        Principalmente esta aplicación de encarga de revisar las evaluaciones de los estudiantes registrados en Q10 y
                        genera triggers para enviar correos electrónicos a los estudiantes que han aprobado, reprobado o faltado a
                        clases. También se encarga de registrar a los estudiantes de Q10 en la plataforma de Thinkific y de enviar
                        correos electrónicos a los estudiantes que se han registrado en la plataforma de Thinkific. Por último, a
                        todos los usuarios registrados en Thinkific se les matricula en los cursos de thinkific marcados por
                        defecto. Cada plataforma tiene su propio comando y se ejecutan con  los siguientes tiempos:
                    </p>
                    <ul>
                        <li>Cada 5 minutos:</li>
                            <ul>
                                <li>Se sincronizan los tipos de roles de usuarios en Q10.</li>
                                <li>Se sincronizan los tipos de perfiles de usuarios en Q10.</li>
                                <li>Se crean cuentas de usuario en Thinkific a los estudiantes sincronizados de Q10. (50 estudiantes sin usuario como máximo)</li>
                            </ul>
                        <li>Cada 10 minutos:</li>
                            <ul>
                                <li>Se sincronizan las asignaturas existentes en Q10.</li>
                                <li>Se sincronizan las jornadas existentes en Q10.</li>
                                <li>Se sincronizan las sedes existentes en Q10.</li>
                                <li>Se asocian las jornadas con las sedes, necesario para la consulta de estudiantes y cursos.</li>
                                <li>Se sincronizan los perídodos académicos existentes en Q10.</li>
                                <li>Se sincronizan los programas académicos en Q10.</li>
                            </ul>
                        <li>Cada 15 minutos:</li>
                            <ul>
                                <li>Se sincronizan los cursos existentes en Q10.</li>
                                <li>Se asocian los cursos con las sedes-jonadas, programa y período, necesario para la consulta de evaluaciones.</li>
                            </ul>
                        <li>Cada 30 minutos:</li>
                            <ul>
                                <li>Se sincronizan los usuarios con rol de administrativos existentes en Q10.</li>
                                <li>Se sincronizan los usuarios con rol de docentes existentes en Q10.</li>
                            </ul>
                        <li>Cada hora:</li>
                            <ul>
                                <li>Se sincronizan los usuarios con rol de estudiantes existentes en Q10.</li>
                                <li>Se sincronizan los cursos existentes en Thinkific, necesarios para la matriculación en los cursos marcado como iniciales al momento de registrar un usuario en la aplicación.</li>
                            </ul>
                        <li>Una vez al día :</li>
                            <ul>
                                <li>Se sincronizan las evalauciones de los programas existentes en Q10.</li>
                                <li>Se asocian las evalauciones con los estudiantes y se emitén los triggers programados:</li>
                                    <ul>
                                        <li>Materia aprobada.</li>
                                        <li>Materia reprobada.</li>
                                        <li>Una inasistencia.</li>
                                        <li>Tres inasistencias.</li>
                                        <li>Cinco inasistencias.</li>
                                    </ul>
                            </ul>
                    </ul>
                    <p>
                        Adicionamlente, se incluyó la funcionalidad de conectarse a un servidor de correos IMAP y extraer las
                        notificaciones de cierre y apertura de las sedes. Estas notificaciones se almacenan en la base de datos y se
                        pueden exportar en pdf y csv.
                    </p>
                    <p>
                        A continuación se explican las vistas de la aplicación:
                    </p>
                </section><!--//docs-intro-->
            </header>

            <section>
                <h2>Login</h2>
                <div class="row">
                        <img src="{{ asset('img/docs/login_form.png') }}" class="rounded mx-auto d-block" alt="Login View">
                </div>
                <p>
                    Esta vista es la primera que se muestra al ingresar a la aplicación. Se debe ingresar el usuario y la contraseña
                    en el formulario mostrado. Si el usuario y la contraseña son correctos, no se permite el acceso a la aplicación.
                    Si se selecciona el botón de "recordarme" se almacena una cookie en el navegador para mantener la sesión activa.
                    Si en algún momento el usuario olvida la contraseña, puede hacer clic en el enlace "¿Olvidaste tu contraseña?"
                    para ingresar a la siguiente vista:
                </p>
            </section>

            <section>
                <h2>Recuperar contraseña</h2>
                <div class="row">
                        <img src="{{ asset('img/docs/forgot_password_form.png') }}" class="rounded mx-auto d-block" alt="Forgot Password View">
                </div>
                <p>
                    Esta vista permite recuperar la contraseña de un usuario. Se debe ingresar el correo electrónico en el formulario
                    mostrado. Si el correo electrónico es correcto, se envía un correo electrónico con un enlace para restablecer la
                    contraseña. Si el correo electrónico no es correcto, se muestra un mensaje de error. A continuación se presenta
                    un ejemplo del correo electrónico enviado:
                </p>
                <div class="row">
                    <img src="{{ asset('img/docs/forgot_password_email.png') }}" class="rounded mx-auto d-block" alt="Forgot Password Email">
                </div>
                <p>
                    Al hacer clic en el enlace del correo electrónico, se ingresa a la siguiente vista:
                </p>
            </section>

            <section>
                <h2>Restablecer contraseña</h2>
                <div class="row">
                    <img src="{{ asset('img/docs/reset_password_form.png') }}" class="rounded mx-auto d-block" alt="Reset Password View">
                </div>
                <p>
                    Esta vista permite restablecer la contraseña de un usuario. Se debe ingresar la nueva contraseña en el formulario
                    mostrado. Si la contraseña es correcta, se muestra un mensaje de éxito. Si la contraseña no es correcta, se
                    muestra un mensaje de error.
                </p>
            </section>

            <section>
                <h2>Inicio</h2>
                <div class="row">
                    <img src="{{ asset('img/docs/home.png') }}" class="rounded img-fluid" alt="Home View">
                </div>
                <p>
                    Esta vista es la primera que se muestra al ingresar a la aplicación. Se muestra el manual de usuario de la 
                    aplicación. Donde encontrarás la información necesaria para el uso de la aplicación.
                </p>
                <div class="row">
                    <img src="{{ asset('img/docs/navbar.png') }}" class="rounded img-fluid" alt="Nav bar">
                </div>
                <p>
                    En la parte superior de la vista se encuentra la barra de navegación, la cual contiene los siguientes elementos.
                    Un botón para mostrar el menú lateral y un botón para salir de la aplicación. El menú lateral es el siguiente:
                </p>
                <div class="row">
                    <img src="{{ asset('img/docs/sidebar.png') }}" class="rounded mx-auto d-block" alt="Side bar">
                </div>
                <p>
                    El menú lateral es el encargado de permitir el acceso a cada una de las vistar disponibles de la aplicaicón. Dependiendo
                    de los permisos que tenga cada usuario dispondrá de un menú lateral diferente. A continuación se describen todos los
                    posibles botones del menú lateral:
                </p>
                <ul>
                    <li>Inicio: Muestra la vista de inicio.</li>
                    <li>Usuarios: Muestra la vista de usuarios.</li>
                    <li>Panel de control: Abre en una pestaña nueva el panel de control de las colas de procesos.</li>
                    <li>Base de datos - Prosegur.</li>
                    <ul>
                        <li>Alarmas: Muesta las información parseada de los correos del servidor IMAP.</li>  
                    </ul>
                    <li>Base de datos - Thinkific.</li>
                    <ul>
                        <li>Cursos: Muestra la lista de cursos existentes en Thinkific.</li>  
                    </ul>
                    <li>Base de datos - Q10. Esta sección posee varias listas desplegables de las sedes disponibles</li>
                    <ul>
                        <li>Usuarios: Lista desplegable para seleccionar que tipo de usuario se quiere visualizar.</li> 
                        <ul>
                            <li>Administrativos: Muestra la lista de usuarios con roles de administrativo en Q10</li>
                            <li>Docentes: Muestra la lista de usuarios con roles de docente en Q10</li>
                            <li>Estudiantes: Muestra la Lista de usuarios con roles de estudiante en Q10</li>
                        </ul> 
                        <li>Periodos: Muesta la lista de periodos existentes en Q10.</li>  
                        <li>Programas: Muestra la lista de programas existentes en Q10.</li>  
                        <li>Asignaturas: Muestra la lista de asignaturas existentes en Q10.</li>  
                    </ul>
                </ul>
            </section>

            <section>
                <h2>Usuarios</h2>
                <div class="row">
                    <img src="{{ asset('img/docs/users/index.png') }}" class="rounded img-fluid" alt="Users View">
                </div>
                <p>
                    Esta vista permite visualizar la lista de usuarios registrados en la aplicación. Se puede crear un nuevo usuario 
                    haciendo clic en el botón "Crear usuario". Además, Se puede editar un usuario haciendo clic en el botón "Editar" en la 
                    tarjeta del usuario y se puede eliminar un usuario haciendo clic en el botón "Eliminar" en la tarjeta del usuario.
                </p>
            </section>
        </article>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <style>
        
    </style>
@stop
