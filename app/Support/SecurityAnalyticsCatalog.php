<?php

namespace App\Support;

class SecurityAnalyticsCatalog
{
    public static function parameters(): array
    {
        return [
            ['number' => 1, 'name' => 'Entradas', 'description' => 'Número de personas que ingresan por una línea o zona virtual.'],
            ['number' => 2, 'name' => 'Salidas', 'description' => 'Número de personas que salen.'],
            ['number' => 3, 'name' => 'Ocupación actual', 'description' => 'Personas que se encuentran actualmente dentro del lugar.'],
            ['number' => 4, 'name' => 'Afluencia total', 'description' => 'Volumen total de visitantes durante un periodo.'],
            ['number' => 5, 'name' => 'Hora pico', 'description' => 'Hora o intervalo con mayor número de entradas.'],
            ['number' => 6, 'name' => 'Afluencia por hora', 'description' => 'Cantidad de visitantes agrupada por hora.'],
            ['number' => 7, 'name' => 'Afluencia por día', 'description' => 'Comparativo de visitantes entre días.'],
            ['number' => 8, 'name' => 'Tiempo de permanencia', 'description' => 'Tiempo estimado que una persona permanece dentro de una zona.'],
            ['number' => 9, 'name' => 'Visitantes simultáneos', 'description' => 'Máximo de personas presentes al mismo tiempo.'],
            ['number' => 10, 'name' => 'Densidad de personas', 'description' => 'Concentración de personas por área o zona.'],
            ['number' => 11, 'name' => 'Mapa de calor', 'description' => 'Identifica visualmente las áreas con mayor circulación o permanencia.'],
            ['number' => 12, 'name' => 'Flujo por dirección', 'description' => 'Determina hacia dónde se desplazan las personas.'],
            ['number' => 13, 'name' => 'Cruces por zona', 'description' => 'Número de personas que atraviesan una línea o región determinada.'],
            ['number' => 14, 'name' => 'Conversión entre zonas', 'description' => 'Porcentaje de personas que pasan de una zona A hacia una zona B.'],
            ['number' => 15, 'name' => 'Tiempo en fila', 'description' => 'Tiempo aproximado que las personas permanecen esperando.'],
            ['number' => 16, 'name' => 'Longitud de fila', 'description' => 'Número de personas esperando en una fila.'],
            ['number' => 17, 'name' => 'Velocidad de desplazamiento', 'description' => 'Rapidez aproximada con la que las personas circulan por una zona.'],
            ['number' => 18, 'name' => 'Detección de aglomeraciones', 'description' => 'Identifica concentraciones anormalmente altas de personas.'],
            ['number' => 19, 'name' => 'Zona restringida / intrusión', 'description' => 'Detecta cuando una persona entra a un área definida como restringida.'],
            ['number' => 20, 'name' => 'Permanencia inusual', 'description' => 'Detecta personas que permanecen más tiempo del establecido en determinada zona.'],
        ];
    }
}
