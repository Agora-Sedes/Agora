<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Listado de jornadas próximas.
     * TODO: Reemplazar con consulta a DB cuando esté disponible.
     */
    public function index()
    {
        $jornadas = collect([
            [
                'id'          => 1,
                'nombre'      => 'Jornada de Inteligencia Artificial y Ética',
                'descripcion' => 'Un espacio de reflexión sobre el impacto de la IA en la sociedad, sus desafíos éticos y las oportunidades para el campo académico.',
                'fecha'       => '2026-07-31',
                'lugar'       => 'Aula Magna – Sedes Sapientiae',
                'charlas_count' => 4,
            ],
            [
                'id'          => 2,
                'nombre'      => 'Jornada de Neurociencias y Conducta',
                'descripcion' => 'Exploramos los últimos avances en neurociencia cognitiva y su relación con la conducta humana y los trastornos mentales.',
                'fecha'       => '2026-08-21',
                'lugar'       => 'Salón Azul – Sedes Sapientiae',
                'charlas_count' => 3,
            ],
            [
                'id'          => 3,
                'nombre'      => 'Jornada de Innovación Educativa',
                'descripcion' => 'Metodologías activas, tecnología en el aula y nuevos paradigmas de evaluación para la educación superior del siglo XXI.',
                'fecha'       => '2026-09-15',
                'lugar'       => 'Aula Magna – Sedes Sapientiae',
                'charlas_count' => 5,
            ],
        ]);

        return view('home', compact('jornadas'));
    }
}
