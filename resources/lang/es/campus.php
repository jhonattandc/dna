<?php

return [
    'welcome' => 'Bienvenido a nuestra aplicación',
    "academic" => [
        "programs" => [
            "menu" => "Programas",
            "header" => "Lista de programas registrados en la sede de :campus",
            "title" => "Programas",
            "table" => [
                "title" => "Programas-:campus",
                "headers" => [
                    "code" => "Código",
                    "name" => "Nombre",
                    "evaluation" => "Tipo de evaluación",
                    "status" => "Estado",
                    "preregistration" => "Aplica preinscripción",
                ],
            ],
        ],
        "subjects" => [
            "menu" => "Asignaturas",
            "header" => "Lista de asignaturas registradas en la sede de :campus",
            "title" => "Asignaturas",
            "table" => [
                "title" => "Asignaturas-:campus",
                "headers" => [
                    "code" => "Código",
                    "name" => "Nombre",
                    "abrevation" => "Abreviatura",
                    "status" => "Estado",
                ],
            ],
        ],
        "terms" => [
            "menu" => "Periodos",
            "header" => "Lista de periodos registrados en la sede de :campus",
            "title" => "Periodos",
            "table" => [
                "title" => "Periodos-:campus",
                "headers" => [
                    "code" => "Código",
                    "name" => "Nombre",
                    "status" => "Estado",
                    "date_start" => "Fecha de inicio",
                    "date_end" => "Fecha de finalización",
                ],
            ],
        ],
    ],
];