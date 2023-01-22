# APLICACIÓN DE SINCRONIZACIÓN

Esta es una aplicación que se encarga de sincronizar la información de las tres plataformas clientify, thinkific y Q10. Esta escrita es PHP con el framework Laravel, su principio de funcionamiento se basa en crontab y comandos para el consumo automático de las API. La estructura de la aplicación es la siguiente:

- APP

## Instalación
Ante dudas consultar [Laravel Documentación](https://laravel.com/docs/8.x/releases)

## Installation

Seguir pasos de instalación de: [Laravel instalación](https://laravel.com/docs/8.x/releases)

**Se recomienda el uso de SAIL**

## Iniciar proyecto

~~~ bash
sail up -d
sail php artisan migrate
sail php artisan db:seed
~~~

## fix bug admin lte
you can make changes in
1- build\scss\mixins_backgrounds.scss line 7:
2 - build\scss\mixins_toasts.scss line line 7:
from
&.bg-#{$name} {
to
#{if(&, '&.bg-#{$name}','.bg-#{$name}')} {
