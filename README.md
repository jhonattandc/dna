# APLICACIÓN DE SINCRONIZACIÓN

Esta es una aplicación que se encarga de sincronizar la información de las tres plataformas clientify, thinkific y Q10. Esta escrita es PHP con el framework Laravel, su principio de funcionamiento se basa en crontab, comandos artisan y trabajos en colas para el consumo automático de las API. Cada plataforma tiene su propio comando y se ejecutan en un tiempo determinado.

En el archivo .env se encuentran las variables de entorno que se deben configurar para el funcionamiento de la aplicación. En el archivo .env.example se detallan las variables de entorno necesarias para la operación de la aplicación. En principio se debe configurar la conexión a la base de datos, la conexión a las plataformas y las credenciales de acceso a las mismas.

Principalmente esta aplicación de encarga de revisar las evaluaciones de los estudiantes registrados en Q10 y genera triggers para enviar correos electrónicos a los estudiantes que han aprobado, reprobado o faltado a clases. También se encarga de registrar a los estudiantes de Q10 en la plataforma de Thinkific y de enviar correos electrónicos a los estudiantes que se han registrado en la plataforma de Thinkific. Por último, a todos los usuarios registrados en Thinkific se les matricula en los cursos de thinkific marcados por defecto.

Adicionamlente, se incluyó la funcionalidad de conectarse a un servidor de correos IMAP y extraer las notificaciones de cierre y apertura de las sedes. Estas notificaciones se almacenan en la base de datos y se pueden exportar desde la interfaz web.

## Requisitos

- PHP >= 7.3
- Composer
- MySQL
- Laravel 8
- Redis

## Instalación

1. Clonar el repositorio
2. Ejecutar el comando `composer install`
3. Crear una base de datos en MySQL
4. Crear el archivo .env completando el contenido del archivo .env.example
5. Ejecutar el comando `php artisan key:generate`
6. Ejecutar el comando `php artisan migrate`
7. Ejecutar el comando `php artisan db:seed`
8. Ejecutar el comando `php artisan storage:link`
9. Ejecutar el comando `php artisan make:admin` para crear un usuario administrador. El comando solicitará el nombre, correo electrónico y contraseña del usuario administrador.

## Ambiente de desarrollo

Para ejecutar la aplicación en un ambiente de desarrollo se debe ejecutar el comando `php artisan serve` y acceder a la url que se muestra en la consola. Los comandos para ir sincronizando la información de las plataformas son:

- `php artisan sync:coursesQ10` sincroniza los cursos de Q10 con la base de datos local.
- `php artisan sync:coursesTK` sincroniza los cursos de Thinkific con la base de datos local.
- `php artisan sync:evaluationsQ10` sincroniza las evaluaciones de Q10 con la base de datos local.
- `php artisan sync:preAcademicQ10` sincroniza la información académica básica de Q10 con la base de datos local.
- `php artisan sync:preUsersQ10` sincroniza la información de los usuarios de Q10 con la base de datos local.
- `php artisan sync:staffQ10` sincroniza el personal de Q10 con la base de datos local.
- `php artisan sync:studentsTK` sincroniza los estudiantes de Thinkific con la base de datos local.
- `php artisan sync:usersQ10` sincroniza los usuarios de Q10 con la base de datos local.

Para ejecutar los comandos en un tiempo determinado se debe configurar el archivo crontab de la siguiente manera:

1. Ejecutar el comando `crontab -e`
2. Agregar las siguientes líneas al final del archivo:

    ``` bash
    * * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
    ```

    ... donde `path-to-your-project` es la ruta absoluta del proyecto.

3. Guardar los cambios

Para ejecutar los trabajos en colas se debe ejecutar el comando `php artisan queue:work` y dejarlo ejecutando en una terminal. Se recomienda revisar la documentación de [Laravel Queues](https://laravel.com/docs/8.x/queues#main-content) para conocer los comandos disponibles.

Esta aplicación cuenta con el paquete Laravel Horizon para la administración de las colas. Para acceder a la interfaz de administración se debe ejecutar el comando `php artisan horizon` y acceder a la url del servidor local. Se recomienda revisar la documentación de [Laravel Horizon](https://laravel.com/docs/8.x/horizon#main-content) para conocer los comandos disponibles.

Para agilizar el desarrollo el proyecto cuenta con el paquete Laravel Sail que permite ejecutar la aplicación en un contenedor Docker. Para ejecutar la aplicación en un contenedor Docker se debe ejecutar el comando `./vendor/bin/sail up` y acceder a la url que se muestra en la consola. Se recomienda revisar la documentación de [Laravel Sail](https://laravel.com/docs/8.x/sail#main-content) para conocer los comandos disponibles.

## Ambiente de producción

### configuracion del servidor web

Para configurar el servidor web se debe ejecutar el comando `sudo nano /etc/apache2/sites-available/000-default.conf` y modificar las siguientes líneas:

``` bash
DocumentRoot /var/www/html/public
```

``` bash
<Directory /var/www/html/public>
    Options Indexes FollowSymLinks MultiViews
    AllowOverride All
    Order allow,deny
    allow from all
</Directory>
```

Con el comando `sudo a2enmod rewrite` se habilita el módulo rewrite de apache.

Con el comando `sudo systemctl restart apache2` se reinicia el servidor apache.

### Configuración del servidor mysql

Para configurar el servidor mysql se debe ejecutar el comando `sudo nano /etc/mysql/mysql.conf.d/mysqld.cnf` y modificar las siguientes líneas:

``` bash
bind-address =
```

``` bash
default-authentication-plugin = mysql_native_password
```

Con el comando `sudo systemctl restart mysql.service` se reinicia el servidor mysql.

### Configuración del servidor redis

Para configurar el servidor redis se debe ejecutar el comando `sudo nano /etc/redis/redis.conf` y modificar las siguientes líneas:

``` bash
supervised systemd
```

``` bash
dir /var/lib/redis
```

``` bash
bind
```

``` bash
requirepass yourpassword
```

... donde `yourpassword` es la contraseña que se desea utilizar para acceder al servidor redis..

Con el comando `sudo systemctl restart redis.service` se reinicia el servidor redis.

### Configuración de la aplicación

Para configurar la aplicación se debe ejecutar el comando `sudo nano /var/www/html/.env` y modificar las siguientes líneas:

``` bash
APP_ENV=production
```

``` bash
APP_DEBUG=false
```

``` bash
APP_URL=http://yourdomain.com
```

``` bash
DB_CONNECTION=mysql
DB_HOST=
DB_PORT=3306
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=
```

``` bash
REDIS_HOST=
REDIS_PASSWORD=
REDIS_PORT=6379
```

``` bash
QUEUE_CONNECTION=redis
```

``` bash
MAIL_MAILER=smtp
MAIL_HOST=
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=
MAIL_FROM_NAME="${APP_NAME}"
```

``` bash
Q10_URL=https://api.q10.com/v1/
# ------------ Q10 KEYS ------------
Q10_BOGOTA=
Q10_MEDELLIN=
Q10_CALI=
Q10_BARRANQUILLA=
Q10_PEREIRA=
```

``` bash
THINKIFIC_URL=https://api.thinkific.com/api/public/v1/
THINKIFIC_KEY=
THINKIFIC_SUBDOMAIN=
```

``` bash
CLIENTIFY_URL=https://api.clientify.net/v1/
CLIENTIFY_KEY=
```

``` bash
IMAP_PROTOCOL=imap
IMAP_HOST=
IMAP_PORT=993
IMAP_USERNAME=
IMAP_PASSWORD=
IMAP_ENCRYPTION=ssl
IMAP_VALIDATE_CERT=true
IMAP_DEFAULT_ACCOUNT=default
```

``` bash
TELEGRAM_LOGGER_BOT_TOKEN=
TELEGRAM_LOGGER_CHAT_ID=
TELEGRAM_LOGGER_TEMPLATE=laravel-telegram-logging::minimal
```

### Configuración del supervisor

Para configurar el supervisor se debe ejecutar el comando `sudo nano /etc/supervisor/conf.d/laravel-worker.conf` y agregar las siguientes líneas:

``` bash
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/html/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
user=root
numprocs=8
redirect_stderr=true
stdout_logfile=/var/www/html/storage/logs/worker.log
```

Con el comando `sudo supervisorctl reread` se recargan los archivos de configuración del supervisor.

Con el comando `sudo supervisorctl update` se actualizan los archivos de configuración del supervisor.

Con el comando `sudo supervisorctl start laravel-worker:*` se inician los trabajos en colas.

### Configuración de los certificados SSL

Para configurar los certificados SSL se debe ejecutar el comando `sudo nano /etc/apache2/sites-available/000-default-le-ssl.conf` y modificar las siguientes líneas:

``` bash
DocumentRoot /var/www/html/public
```

``` bash
<Directory /var/www/html/public>
    Options Indexes FollowSymLinks MultiViews
    AllowOverride All
    Order allow,deny
    allow from all
</Directory>
```

Con el comando `sudo a2enmod ssl` se habilita el módulo ssl de apache.

Con el comando `sudo a2enmod headers` se habilita el módulo headers de apache.

Con el comando `sudo systemctl restart apache2` se reinicia el servidor apache.

## Diagramas de base de datos

### Diagrama general

![Diagrama de base de datos](assets/database-diagram.png)

### Diagrama de la tabla de usuarios

![Diagrama de la tabla de usuarios](assets/users-diagram.png)

### Diagrama de la tabla de estudiantes

![Diagrama de la tabla de estudiantes](assets/students-diagram.png)

### Diagrama de la tabla de evaluaciones

![Diagrama de la tabla de evaluaciones](assets/evaluations-diagram.png)

## Posibles mejoras

- [ ] Implementar pruebas unitarias y de integración.
- [ ] Organizar mejor las políticas de acceso y presindir del uso de los gates.
- [ ] Implementar el uso de repositorios para la conexuón con las bases de datos.
- [ ] Implementar el uso de adaptadores para el paso de datos entre plataformas.
- [ ] Migrar algunas partes de la lógica de negocio a las clases de servicio.
- [ ] Implementar la autenticación por medio de tokens, al consular los endpoints de las datatables.
- [ ] Mejorar o presendir del uso los paquetes laravel/breeze, laravel/horizon, jeroennoten/laravel-adminlte, yyajra/laravel-datatables-oracle. Que se utilizaron para agilizar el desarrollo del frontend.
- [ ] Implementar el paquete [Laravel Nova](https://nova.laravel.com/) para la administración de la aplicación.
- [ ] Implementar el paquete [Laravel Telescope](https://laravel.com/docs/8.x/telescope#main-content) para la depuración de la aplicación.
- [ ] Implementar el paquete [Laravel Backup](https://docs.spatie.be/laravel-backup/v7/introduction/) para realizar copias de seguridad de la aplicación.
