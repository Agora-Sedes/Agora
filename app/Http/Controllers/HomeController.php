<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $conferences = collect([
            [
                'id'          => 1,
                'name'      => 'Jornada de Inteligencia Artificial y Ética',
                'description' => 'Un espacio de reflexión sobre el impacto de la IA en la sociedad, sus desafíos éticos y las oportunidades para el campo académico.',
                'date'       => '2026-07-31',
                'place'       => 'Aula Magna – Sedes Sapientiae',
                'talk_count' => 4,
            ],
            [
                'id'          => 2,
                'name'      => 'Jornada de Neurociencias y Conducta',
                'description' => 'Exploramos los últimos avances en neurociencia cognitiva y su relación con la conducta humana y los trastornos mentales.',
                'date'       => '2026-08-21',
                'place'       => 'Salón Azul – Sedes Sapientiae',
                'talk_count' => 3,
            ],
            [
                'id'          => 3,
                'name'      => 'Jornada de Innovación Educativa',
                'description' => 'Metodologías activas, tecnología en el aula y nuevos paradigmas de evaluación para la educación superior del siglo XXI.',
                'date'       => '2026-09-15',
                'place'       => 'Aula Magna – Sedes Sapientiae',
                'talk_count' => 5,
            ],
        ]);

        return view('home', compact('conferences'));
    }
}
