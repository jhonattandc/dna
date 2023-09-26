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
                        Esta es una aplicación que se encarga de sincronizar la información de las tres plataformas
                        clientify,
                        thinkific y Q10. Esta escrita es PHP con el framework Laravel, su principio de funcionamiento se
                        basa en
                        crontab, comandos artisan y trabajos en colas para el consumo automático de las API.
                    </p>
                    <p>
                        Principalmente esta aplicación de encarga de revisar las evaluaciones de los estudiantes registrados
                        en Q10 y
                        genera triggers para enviar correos electrónicos a los estudiantes que han aprobado, reprobado o
                        faltado a
                        clases. También se encarga de registrar a los estudiantes de Q10 en la plataforma de Thinkific y de
                        enviar
                        correos electrónicos a los estudiantes que se han registrado en la plataforma de Thinkific. Por
                        último, a
                        todos los usuarios registrados en Thinkific se les matricula en los cursos de thinkific marcados por
                        defecto. Cada plataforma tiene su propio comando y se ejecutan con los siguientes tiempos:
                    </p>
                    <ul>
                        <li>Cada 5 minutos:</li>
                        <ul>
                            <li>Se sincronizan los tipos de roles de usuarios en Q10.</li>
                            <li>Se sincronizan los tipos de perfiles de usuarios en Q10.</li>
                            <li>Se crean cuentas de usuario en Thinkific a los estudiantes sincronizados de Q10. (50
                                estudiantes sin usuario como máximo)</li>
                        </ul>
                        <li>Cada 10 minutos:</li>
                        <ul>
                            <li>Se sincronizan las asignaturas existentes en Q10.</li>
                            <li>Se sincronizan las jornadas existentes en Q10.</li>
                            <li>Se sincronizan las sedes existentes en Q10.</li>
                            <li>Se asocian las jornadas con las sedes, necesario para la consulta de estudiantes y cursos.
                            </li>
                            <li>Se sincronizan los perídodos académicos existentes en Q10.</li>
                            <li>Se sincronizan los programas académicos en Q10.</li>
                        </ul>
                        <li>Cada 15 minutos:</li>
                        <ul>
                            <li>Se sincronizan los cursos existentes en Q10.</li>
                            <li>Se asocian los cursos con las sedes-jonadas, programa y período, necesario para la consulta
                                de evaluaciones.</li>
                        </ul>
                        <li>Cada 30 minutos:</li>
                        <ul>
                            <li>Se sincronizan los usuarios con rol de administrativos existentes en Q10.</li>
                            <li>Se sincronizan los usuarios con rol de docentes existentes en Q10.</li>
                        </ul>
                        <li>Cada hora:</li>
                        <ul>
                            <li>Se sincronizan los usuarios con rol de estudiantes existentes en Q10.</li>
                            <li>Se sincronizan los cursos existentes en Thinkific, necesarios para la matriculación en los
                                cursos marcado como iniciales al momento de registrar un usuario en la aplicación.</li>
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
                        Adicionamlente, se incluyó la funcionalidad de conectarse a un servidor de correos IMAP y extraer
                        las
                        notificaciones de cierre y apertura de las sedes. Estas notificaciones se almacenan en la base de
                        datos y se
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
                    <img src="{{ asset('img/docs/login_form.png') }}" class="rounded mx-auto d-block img-fluid"
                        alt="Login View">
                </div>
                <p>
                    Esta vista es la primera que se muestra al ingresar a la aplicación. Se debe ingresar el usuario y la
                    contraseña
                    en el formulario mostrado. Si el usuario y la contraseña son correctos, no se permite el acceso a la
                    aplicación.
                    Si se selecciona el botón de "recordarme" se almacena una cookie en el navegador para mantener la sesión
                    activa.
                    Si en algún momento el usuario olvida la contraseña, puede hacer clic en el enlace "¿Olvidaste tu
                    contraseña?"
                    para ingresar a la siguiente vista:
                </p>
            </section>

            <section>
                <h2>Recuperar contraseña</h2>
                <div class="row">
                    <img src="{{ asset('img/docs/forgot_password_form.png') }}" class="rounded mx-auto d-block img-fluid"
                        alt="Forgot Password View">
                </div>
                <p>
                    Esta vista permite recuperar la contraseña de un usuario. Se debe ingresar el correo electrónico en el
                    formulario
                    mostrado. Si el correo electrónico es correcto, se envía un correo electrónico con un enlace para
                    restablecer la
                    contraseña. Si el correo electrónico no es correcto, se muestra un mensaje de error. A continuación se
                    presenta
                    un ejemplo del correo electrónico enviado:
                </p>
                <div class="row">
                    <img src="{{ asset('img/docs/forgot_password_email.png') }}" class="rounded mx-auto d-block img-fluid"
                        alt="Forgot Password Email">
                </div>
                <p>
                    Al hacer clic en el enlace del correo electrónico, se ingresa a la siguiente vista:
                </p>
            </section>

            <section>
                <h2>Restablecer contraseña</h2>
                <div class="row">
                    <img src="{{ asset('img/docs/reset_password_form.png') }}" class="rounded mx-auto d-block img-fluid"
                        alt="Reset Password View">
                </div>
                <p>
                    Esta vista permite restablecer la contraseña de un usuario. Se debe ingresar la nueva contraseña en el
                    formulario
                    mostrado. Si la contraseña es correcta, se muestra un mensaje de éxito. Si la contraseña no es correcta,
                    se
                    muestra un mensaje de error.
                </p>
            </section>

            <section>
                <h2>Inicio</h2>
                <div class="row">
                    <img src="{{ asset('img/docs/home.png') }}" class="rounded img-fluid" alt="Home View">
                </div>
                <p>
                    Esta vista es la primera que se muestra al ingresar a la aplicación. Se muestra el manual de usuario de
                    la
                    aplicación. Donde encontrarás la información necesaria para el uso de la aplicación.
                </p>
                <div class="row">
                    <img src="{{ asset('img/docs/navbar.png') }}" class="rounded img-fluid" alt="Nav bar">
                </div>
                <p>
                    En la parte superior de la vista se encuentra la barra de navegación, la cual contiene los siguientes
                    elementos.
                    Un botón para mostrar el menú lateral y un botón para salir de la aplicación. El menú lateral es el
                    siguiente:
                </p>
                <div class="row">
                    <img src="{{ asset('img/docs/sidebar.png') }}" class="rounded mx-auto d-block" alt="Side bar">
                </div>
                <p>
                    El menú lateral es el encargado de permitir el acceso a cada una de las vistar disponibles de la
                    aplicaicón. Dependiendo
                    de los permisos que tenga cada usuario dispondrá de un menú lateral diferente. A continuación se
                    describen todos los
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
                    Esta vista permite visualizar la lista de usuarios registrados en la aplicación. Se puede crear un nuevo
                    usuario
                    haciendo clic en el botón "Crear usuario". Además, Se puede editar un usuario haciendo clic en el botón
                    "Editar" en la
                    tarjeta del usuario y se puede eliminar un usuario haciendo clic en el botón "Eliminar" en la tarjeta
                    del usuario.
                </p>
            </section>

            <section>
                <h2>Tabla de datos</h2>
                <div>
                    <img src="{{ asset('img/docs/datatables/index.png') }}" class="rounded img-fluid" alt="Datatable">
                </div>
                <p>
                    Esta vista permite visualizar la lista de registros de una tabla de la base de datos. Donde se puede
                    exportar la
                    información en formato pdf y csv. Además, se puede filtrar la información por cada una de las columnas
                    de la tabla.
                </p>
                <div>
                    <img src="{{ asset('img/docs/datatables/export_buttons.png') }}" class="rounded img-fluid"
                        alt="Export buttons">
                </div>
                <p>
                    Este compoenente permite exportar la información de la tabla en formato pdf, csv y copiar la información
                    disponible en
                    la tabla. Además, se pueden ocultar o mostrar la información de las columnas de la tabla.
                </p>
                <div>
                    <img src="{{ asset('img/docs/datatables/search_field.png') }}" class="rounded img-fluid"
                        alt="Search field">
                </div>
                <p>
                    Este componente permite filtrar la información de la tabla por cada una de las columnas de la tabla.
                </p>
                <div>
                    <img src="{{ asset('img/docs/datatables/pagination.png') }}" class="rounded img-fluid"
                        alt="Pagination">
                </div>
                <p>
                    Este componente permite paginar la información de la tabla.
                </p>
            </section>

            <section>
                <h2>Panel de control</h2>
                <div class="row">
                    <img src="{{ asset('img/docs/horizon/Horizon-Dashboard.png') }}" class="rounded img-fluid"
                        alt="Queues View">
                </div>
                <p>
                    En esta vista se presenta un resumen con las estadísticas del procesamiento de tareas, con los valores
                    de: trabajos
                    por minuto, trabajos procesados en la última hora, los trabajos fallidos en los últimos 7 días, el
                    estado del servidor,
                    el total de trabajos procesados, el máximo tiempo de espera y el máximo tiempo de ejecución por un
                    trabajo.
                </p>
                <div class="row">
                    <img src="{{ asset('img/docs/horizon/Horizon-Monitoring.png') }}" class="rounded img-fluid"
                        alt="Queues View">
                </div>
                <p>La vista de "Monitoring" te permite supervisar y controlar el rendimiento y el estado
                    de tus trabajadores de cola en tiempo real. Aquí tienes más detalles:</p>

                <h3>Uso de la Vista de "Monitoring":</h3>
                <ul>
                    <li><strong>Supervisión en Tiempo Real:</strong> La vista de "Monitoring" te proporciona información en
                        tiempo real sobre el estado de tus trabajadores de cola. Puedes ver cuántos trabajos están siendo
                        procesados, cuántos están en espera y cuántos han fallado.</li>
                    <li><strong>Gráficos e Información Visual:</strong> Esta vista a menudo incluye gráficos y
                        visualizaciones que te ayudan a comprender mejor el rendimiento de tus colas. Puedes observar
                        tendencias y patrones fácilmente.</li>
                    <li><strong>Acciones en Vivo:</strong> Desde la vista de "Monitoring," puedes realizar acciones en
                        tiempo real, como detener o reiniciar trabajadores de cola, lo que te permite mantener el control
                        sobre el rendimiento de tu sistema de colas.</li>
                </ul>
                <p>En resumen, la vista de "Monitoring" es una herramienta poderosa para monitorear y
                    administrar en tiempo real el rendimiento de tus trabajadores de cola. Te ayuda a garantizar que tus
                    colas funcionen de manera eficiente y que puedas tomar medidas inmediatas cuando sea necesario para
                    mantener tu aplicación en un estado óptimo.</p>

                <div class="row">
                    <img src="{{ asset('img/docs/horizon/Horizon-Metrics.png') }}" class="rounded img-fluid"
                        alt="Queues View">
                </div>
                <p>La vista de "Metrics" te proporciona información detallada sobre el rendimiento y el
                    comportamiento de tu sistema de colas. Aquí tienes más detalles:</p>

                <h3>Uso de la Vista de "Metrics":</h3>
                <ul>
                    <li><strong>Métricas de Rendimiento:</strong> La vista de "Metrics" te muestra métricas clave
                        relacionadas con el rendimiento de tus trabajadores de cola, incluyendo el número de trabajos
                        procesados, el tiempo de procesamiento promedio y otros indicadores de rendimiento.</li>
                    <li><strong>Métricas de Colas:</strong> Puedes obtener información detallada sobre el estado de tus
                        colas, como el número de trabajos en cola, el número de trabajos en cola pendientes de ejecución y
                        el número de trabajos fallidos.</li>
                    <li><strong>Gráficos Interactivos:</strong> La vista de "Metrics" a menudo incluye gráficos interactivos
                        que te permiten visualizar tendencias y patrones en el rendimiento de tus colas a lo largo del
                        tiempo.</li>
                </ul>
                <p>En resumen, la vista de "Metrics" es una herramienta valiosa para obtener información
                    detallada sobre el rendimiento y el comportamiento de tus colas de trabajos. Te ayuda a supervisar la
                    salud de tu sistema de colas y a identificar áreas que puedan necesitar ajustes o mejoras para
                    garantizar un funcionamiento óptimo.</p>

                <div class="row">
                    <img src="{{ asset('img/docs/horizon/Horizon-Batches.png') }}" class="rounded img-fluid"
                        alt="Queues View">
                </div>
                <p>La funcionalidad de "Batches" es una característica que
                    permite a los desarrolladores supervisar y gestionar lotes de trabajos en la cola de Laravel de una
                    manera más organizada y eficiente. A continuación, te explico con más detalle:</p>
                <h3>Uso de la vista "Batches":</h3>
                <ul>
                    <li><strong>Supervisión de Lotes:</strong> La función de "Batches" te permite supervisar los lotes de
                        trabajos en tu aplicación. Un lote es un conjunto de trabajos relacionados que se envían a la cola
                        para su procesamiento conjunto. Puedes ver información detallada sobre cada lote desde el panel de
                        Horizon.</li>
                    <li><strong>Estado del Lote:</strong> La vista de lotes te proporciona información sobre el estado de
                        cada lote, incluyendo si el lote está en proceso, completado o si ha fallado. Esto te permite
                        identificar rápidamente cualquier problema con lotes específicos y tomar medidas para solucionarlos.
                    </li>
                    <li><strong>Progreso del Lote:</strong> Puedes ver el progreso de un lote en tiempo real. Esto es
                        especialmente útil cuando tienes lotes que contienen una gran cantidad de trabajos, ya que te
                        permite seguir su avance y saber cuántos trabajos se han completado y cuántos quedan por procesar.
                    </li>
                    <li><strong>Reintentos y Errores:</strong> Si un lote contiene trabajos que han fallado, puedes ver los
                        detalles de los errores específicos que ocurrieron en cada trabajo. También puedes reiniciar lotes
                        que han fallado o detener lotes en proceso si es necesario.</li>
                    <li><strong>Administración de Lotes:</strong> La vista de lotes te brinda la capacidad de administrar
                        lotes de manera más efectiva. Puedes detener lotes que aún están en proceso, reintentar trabajos
                        individuales dentro de un lote y tomar decisiones informadas sobre cómo gestionar tus procesos en
                        cola.</li>
                </ul>

                <p>En resumen, la función de "View Batches" (Vista de Lotes) es una característica
                    importante que te permite supervisar y administrar de manera eficiente los lotes de trabajos en la cola
                    de tu aplicación Laravel. Proporciona información valiosa sobre el estado y el progreso de los lotes, lo
                    que facilita la identificación y solución de problemas en tu sistema de colas.</p>
                <div class="row">
                    <img src="{{ asset('img/docs/horizon/Horizon-PendingJobs.png') }}" class="rounded img-fluid"
                        alt="Queues View">
                </div>
                <p>La vista de "Pending Jobs" te proporciona información sobre los trabajos que están en
                    cola y aún no han sido procesados. Aquí tienes más detalles:</p>

                <h3>Uso de la Vista de "Pending Jobs":</h3>
                <ul>
                    <li><strong>Supervisión de Trabajos en Cola:</strong> La vista de "Pending Jobs" te muestra una lista de
                        trabajos que están en espera para ser procesados por tus trabajadores de cola. Esto te permite
                        mantener un seguimiento en tiempo real de los trabajos que aún no se han completado.</li>
                    <li><strong>Detalles de los Trabajos:</strong> Puedes ver información detallada sobre cada trabajo
                        pendiente, como su ID, el tipo de trabajo, la fecha de creación y otros detalles relevantes.</li>
                    <li><strong>Opciones de Gestión:</strong> Desde esta vista, puedes tomar acciones como reiniciar
                        trabajos específicos, eliminar trabajos de la cola o realizar otras operaciones de administración
                        según sea necesario.</li>
                </ul>

                <p>En resumen, la vista de "Pending Jobs" es una herramienta valiosa para supervisar y
                    administrar los trabajos que están en cola y aún no se han procesado en tu aplicación. Te ayuda a
                    mantener un control efectivo sobre la cola de trabajos y a tomar decisiones informadas sobre la
                    administración de los mismos.</p>
                <div class="row">
                    <img src="{{ asset('img/docs/horizon/Horizon-CompletedJobs.png') }}" class="rounded img-fluid"
                        alt="Queues View">
                </div>
                <p>La vista de "Completed Jobs" te proporciona información sobre los trabajos que han
                    sido procesados exitosamente. Aquí tienes más detalles:</p>

                <h3>Uso de la Vista de "Completed Jobs":</h3>
                <ul>
                    <li><strong>Supervisión de Trabajos Completados:</strong> La vista de "Completed Jobs" te muestra una
                        lista de trabajos que han sido procesados con éxito por tus trabajadores de cola. Esto te permite
                        mantener un registro de los trabajos que se han completado correctamente.</li>
                    <li><strong>Detalles de los Trabajos:</strong> Puedes ver información detallada sobre cada trabajo
                        completado, como su ID, el tipo de trabajo, la fecha de inicio y finalización, y otros detalles
                        relevantes.</li>
                    <li><strong>Información de Rendimiento:</strong> Esta vista puede ser útil para evaluar el rendimiento
                        de tu sistema de colas al rastrear cuántos trabajos se han completado y en qué momento.</li>
                </ul>

                <p>En resumen, la vista de "Completed Jobs" es una herramienta valiosa para supervisar y
                    rastrear los trabajos que han sido procesados con éxito en tu aplicación. Te proporciona información
                    importante sobre el rendimiento de tus trabajadores de cola y te permite mantener un registro de los
                    trabajos completados de manera efectiva.</p>

                <div class="row">
                    <img src="{{ asset('img/docs/horizon/Horizon-FailedJobs.png') }}" class="rounded img-fluid"
                        alt="Queues View">
                </div>

                <p>La vista de "Failed Jobs" te proporciona información sobre los trabajos que han
                    fallado durante el procesamiento. Aquí tienes más detalles:</p>

                <h3>Uso de la Vista de "Failed Jobs":</h3>
                <ul>
                    <li><strong>Supervisión de Trabajos Fallidos:</strong> La vista de "Failed Jobs" te muestra una lista de
                        trabajos que han experimentado fallos durante su procesamiento por parte de tus trabajadores de
                        cola. Esto te permite identificar y abordar problemas de manera proactiva.</li>
                    <li><strong>Detalles de los Trabajos Fallidos:</strong> Puedes ver información detallada sobre cada
                        trabajo fallido, como su ID, el tipo de trabajo, la fecha y hora del fallo, y los detalles del error
                        que causó el fallo.</li>
                    <li><strong>Reintentos y Solución de Problemas:</strong> Desde esta vista, puedes tomar medidas para
                        solucionar los trabajos fallidos, como volver a intentar un trabajo específico o depurar el motivo
                        del fallo para evitar que vuelva a ocurrir en el futuro.</li>
                </ul>

                <p>En resumen, la vista de "Failed Jobs" es una herramienta importante para supervisar y
                    gestionar los trabajos que han fallado durante el procesamiento en tu aplicación. Te ayuda a identificar
                    y abordar problemas rápidamente, así como a tomar medidas para solucionar y mejorar el rendimiento de
                    tus trabajadores de cola.</p>
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
