<?php

// Parametros de entrada
$server->wsdl->addComplexType
(  
    'datos_persona_entrada', 
    'complexType', 
    'struct', 
    'all', 
    '',
    [
        'cedula'          => ['name' => 'cedula','type' => 'xsd:string'],
        'nombre'          => ['name' => 'nombre','type' => 'xsd:string'],
        'FechaNacimiento' => ['name' => 'FechaNacimiento','type' => 'xsd:string'],
    ]    
);

// Salida
$server->wsdl->addComplexType
(
        'Estructura', 
        'complexType', 
        'struct', 
        'all', 
        '',
        [
            'Nombre' => ['name' => 'Nombre','type' => 'xsd:string'],
            'Apellidos' => ['name' => 'Apellidos','type' => 'xsd:string'],
            'Edad' => ['name' => 'Edad','type' => 'xsd:string']
        ]
);

$server->wsdl->addComplexType
(
    'ArregloDeEstructuras',
    'complexType',
    'array',
    '', // 'sequence',
    'SOAP-ENC:Array', //'http://schemas.xmlsoap.org/soap/encoding/:Array',
    array(),
    [
        [
            'ref' => 'SOAP-ENC:arrayType',
            //'ref' => 'http://schemas.xmlsoap.org/soap/encoding/:arrayType',
            'wsdl:arrayType' => 'tns:Estructura[]'
        ]
    ],
    'tns:Estructura'
);