<?php

return [
    /*--------------------------------------------------------------------------
    | Prosegur Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used by the Prosegur application.
    |
    */
    "header" => "Lista de usuarios registrados",
    "title" => "Usuarios",
    "edit" => [
        "button" => "Editar usuario",
        "title" => "Editar usuario",
        "header" => "Editar usuario :name",
        "name" => "Nombre",
        "email" => "Correo electrónico",
        "password" => "Contraseña",
        "password_confirmation" => "Confirmar contraseña",
        "submit" => "Actualizar",
    ],
    "delete" => [
        "button" => "Eliminar usuario",
        "title" => "Eliminar usuario",
        "text" => "¿Está seguro que desea eliminar este usuario?",
        "confirm" => "Eliminar usuario",
        "cancel" => "Cancelar",
        "success" => [
            "title" => "Usuario eliminado",
            "text" => "El usuario ha sido eliminado exitosamente.",
        ],
        "error" => [
            "title" => "Error al eliminar usuario",
            "text" => "El usuario no pudo ser eliminado.",
        ],
    ],
    "name" => [
        "title" => "Nombre",
        "header" => "Nombre",
        "label" => "Nombre",
        "placeholder" => "Ingrese el nombre completo del usuario",
    ],
    "email" => [
        "title" => "Correo electrónico",
        "header" => "Correo electrónico",
        "label" => "Correo electrónico",
        "placeholder" => "Ingrese el correo electrónico del usuario",
    ],
    "password" => [
        "title" => "Contraseña",
        "header" => "Contraseña",
        "label" => "Contraseña",
        "placeholder" => "Ingrese la contraseña del usuario",
    ],
    "password_confirmation" => [
        "title" => "Confirmar contraseña",
        "header" => "Confirmar contraseña",
        "label" => "Confirmar contraseña",
        "placeholder" => "Ingrese la confirmación de la contraseña del usuario",
    ],
    "permissions" => [
        "title" => "Permisos",
        "header" => "Permisos",
        "label" => "Permisos",
        "placeholder" => "Seleccione los permisos del usuario",
    ],
    "register" => [
        "title" => "Registro de usuario",
        "header" => "Registro de usuario",
        "name" => "Nombre",
        "email" => "Correo electrónico",
        "password" => "Contraseña",
        "password_confirmation" => "Confirmar contraseña",
        "button" => "Crear usuario",
        "submit" => "Registrar",
    ],
    "admins" => [
        "menu" => "Administrativos",
        "header" => "Lista de administrativos registrados en la sede de :campus",
        "title" => "Administrativos",
        "table" => [
            "title" => "Administrativos-:campus",
            "headers" => [
                "code" => "Código",
                "first_name" => "Nombre",
                "last_name" => "Apellido",
                "ci" => "Numero de identificación",
                "email" => "Correo electrónico",
                "phone" => "Celular",
            ],
        ],
    ],
    "teachers" => [
        "menu" => "Docentes",
        "header" => "Lista de docentes registrados en la sede de :campus",
        "title" => "Docentes",
        "table" => [
            "title" => "Docentes-:campus",
            "headers" => [
                "code" => "Código",
                "first_name" => "Nombre",
                "last_name" => "Apellido",
                "ci" => "Numero de identificación",
                "email" => "Correo electrónico",
                "phone" => "Celular",
            ],
        ],
    ],
    "students" => [
        "menu" => "Estudiantes",
        "header" => "Lista de estudiantes registrados en la sede de :campus",
        "title" => "Estudiantes",
        "table" => [
            "title" => "Estudiantes-:campus",
            "headers" => [
                "code" => "Código",
                "first_name" => "Nombre",
                "last_name" => "Apellido",
                "ci" => "Numero de identificación",
                "email" => "Correo electrónico",
                "phone" => "Celular",
            ],
        ],
    ],
];