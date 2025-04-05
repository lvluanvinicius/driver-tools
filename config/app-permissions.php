<?php

return [
    "frontend" => [
        "files" => [
            "name"        => "Arquivos",
            "permissions" => [
                "index"    => "Listar arquivos",
                "create"   => "Criar arquivo",
                "edit"     => "Editar arquivo",
                "delete"   => "Deletar arquivo",
                "upload"   => "Enviar arquivo",
                "download" => "Baixar arquivo",
            ],
        ],
        "users" => [
            "name"        => "Usuários",
            "permissions" => [
                "index"  => "Listar usuários",
                "create" => "Criar usuário",
                "edit"   => "Editar usuário",
                "delete" => "Deletar usuário",
            ],
        ],
    ],
    "backend"  => [
    ],
];
