<?php

namespace App\Http\Controllers;

use App\Models\Conference;
use Illuminate\Http\Request;

class ConferenceController extends Controller
{
    public function list() {
        $conferences = Conference::all();
        return view('home', [
            'conferences' => $conferences,
        ]);
    }
    public function show(int $id)
    {
      // Datos hardcodeados de prueba a reemplazar mas proximo a la fecha del evento futuro
        $conferences = [
            1 => [
                'id'          => 1,
                'name'      => 'Jornada de Inteligencia Artificial y Ética',
                'description' => 'Un espacio de reflexión sobre el impacto de la IA en la sociedad, sus desafíos éticos y las oportunidades para el campo académico.',
                'date'       => '2026-07-31',
                'place'       => 'Aula Magna – Sedes Sapientiae',
                'talks'     => [
                    [
                        'id'          => 1,
                        'title'      => '¿Puede una máquina ser justa? Sesgos algorítmicos en la toma de decisiones',
                        'talker'     => 'Dra. María Eugenia Torres',
                        'hour_begin' => '09:00',
                        'hour_end'    => '10:00',
                        'abstract'    => 'En esta charla analizamos cómo los algoritmos de aprendizaje automático pueden reproducir y amplificar sesgos existentes en la sociedad. Exploraremos casos reales de discriminación algorítmica en sistemas de contratación, justicia penal y crédito financiero, y discutiremos marcos éticos y técnicos para mitigarlos.',
                    ],
                    [
                        'id'          => 2,
                        'title'      => 'IA Generativa en la educación: oportunidades y riesgos',
                        'talker'     => 'Lic. Juan Ignacio Méndez',
                        'hour_begin' => '10:15',
                        'hour_end'    => '11:15',
                        'abstract'    => 'Desde ChatGPT hasta modelos multimodales, la IA generativa está transformando cómo los estudiantes aprenden y cómo los docentes enseñan. Abordaremos los riesgos académicos (plagio, desinformación) y las posibilidades pedagógicas reales que ofrecen estas herramientas.',
                    ],
                    [
                        'id'          => 3,
                        'title'      => 'Regulación de la IA: el debate global y el rol de las universidades',
                        'talker'     => 'Dr. Carlos Ferreyra',
                        'hour_begin' => '11:30',
                        'hour_end'    => '12:30',
                        'abstract'    => 'Un recorrido por los principales marcos regulatorios internacionales (EU AI Act, iniciativas latinoamericanas) y el rol que pueden y deben cumplir las instituciones académicas en la definición de políticas públicas sobre inteligencia artificial.',
                    ],
                    [
                        'id'          => 4,
                        'title'      => 'Panel de cierre: ¿Hacia dónde va la IA?',
                        'talker'     => 'Mesa redonda – Todos los expositores',
                        'hour_begin' => '14:00',
                        'hour_end'    => '15:30',
                        'abstract'    => 'Espacio abierto de preguntas y debate entre los expositores y el público asistente. Se abordarán las preguntas surgidas durante la jornada y se reflexionará colectivamente sobre el futuro de la inteligencia artificial en el ámbito académico y social.',
                    ],
                ],
            ],
            2 => [
                'id'          => 2,
                'name'      => 'Jornada de Neurociencias y Conducta',
                'description' => 'Exploramos los últimos avances en neurociencia cognitiva y su relación con la conducta humana y los trastornos mentales.',
                'date'       => '2026-08-21',
                'place'       => 'Salón Azul – Sedes Sapientiae',
                'talks'     => [
                    [
                        'id'          => 1,
                        'title'      => 'Neuroplasticidad y aprendizaje: lo que la ciencia nos dice',
                        'talker'     => 'Dr. Alejandro Vidal',
                        'hour_begin' => '09:00',
                        'hour_end'    => '10:00',
                        'abstract'    => 'La neuroplasticidad es la capacidad del cerebro de reorganizarse formando nuevas conexiones neuronales. En esta charla revisaremos la evidencia científica más reciente sobre cómo aprendemos, qué condiciones favorecen la plasticidad y qué implicancias tiene esto para la práctica docente.',
                    ],
                    [
                        'id'          => 2,
                        'title'      => 'Trastornos del espectro autista: diagnóstico temprano y abordajes actuales',
                        'talker'     => 'Lic. Sofía Ramírez',
                        'hour_begin' => '10:30',
                        'hour_end'    => '11:30',
                        'abstract'    => 'Actualización sobre criterios diagnósticos actuales del TEA, herramientas de detección temprana y los abordajes terapéuticos con mayor evidencia científica. Se hará especial énfasis en el trabajo interdisciplinario y en la inclusión educativa.',
                    ],
                    [
                        'id'          => 3,
                        'title'      => 'Mindfulness y regulación emocional: evidencia y práctica',
                        'talker'     => 'Dra. Laura Piccini',
                        'hour_begin' => '12:00',
                        'hour_end'    => '13:00',
                        'abstract'    => 'Revisión de la evidencia científica sobre las prácticas de mindfulness y su impacto en la regulación emocional, el estrés y el bienestar psicológico. Incluye una demostración práctica y análisis de su aplicación en contextos clínicos y educativos.',
                    ],
                ],
            ],
            3 => [
                'id'          => 3,
                'name'      => 'Jornada de Innovación Educativa',
                'description' => 'Metodologías activas, tecnología en el aula y nuevos paradigmas de evaluación para la educación superior del siglo XXI.',
                'date'       => '2026-09-15',
                'place'       => 'Aula Magna – Sedes Sapientiae',
                'talks'     => [
                    [
                        'id'          => 1,
                        'title'      => 'Aula invertida: experiencias y resultados en educación superior',
                        'talker'     => 'Mg. Roberto Acosta',
                        'hour_begin' => '09:00',
                        'hour_end'    => '10:00',
                        'abstract'    => 'Presentación de experiencias concretas de implementación del modelo de aula invertida (flipped classroom) en carreras de grado. Se analizarán resultados de aprendizaje, desafíos de implementación y percepciones de docentes y estudiantes.',
                    ],
                    [
                        'id'          => 2,
                        'title'      => 'Evaluación auténtica: más allá del examen tradicional',
                        'talker'     => 'Dra. Marcela Gómez',
                        'hour_begin' => '10:15',
                        'hour_end'    => '11:15',
                        'abstract'    => 'Las evaluaciones tradicionales miden memorización, no competencias. Exploraremos formatos alternativos de evaluación auténtica: portafolios, rúbricas, proyectos integradores y autoevaluación, con ejemplos aplicables en distintas disciplinas.',
                    ],
                    [
                        'id'          => 3,
                        'title'      => 'Gamificación en el aula: diseño y resultados',
                        'talker'     => 'Lic. Diego Santillán',
                        'hour_begin' => '11:30',
                        'hour_end'    => '12:30',
                        'abstract'    => 'La gamificación no es solo "jugar en clase". Aprenderemos a diseñar experiencias gamificadas coherentes con objetivos de aprendizaje, analizaremos plataformas disponibles y revisaremos evidencia sobre su impacto en motivación y rendimiento académico.',
                    ],
                    [
                        'id'          => 4,
                        'title'      => 'Tecnologías emergentes para la inclusión educativa',
                        'talker'     => 'Dra. Ana Belén Herrera',
                        'hour_begin' => '14:00',
                        'hour_end'    => '15:00',
                        'abstract'    => 'Herramientas digitales accesibles, lectura fácil, subtitulado automático y otras tecnologías de asistencia que permiten diseñar entornos de aprendizaje verdaderamente inclusivos para estudiantes con diversas necesidades.',
                    ],
                    [
                        'id'          => 5,
                        'title'      => 'Cierre y trabajo en red: construyendo una comunidad de práctica',
                        'talker'     => 'Equipo organizador',
                        'hour_begin' => '15:15',
                        'hour_end'    => '16:00',
                        'abstract'    => 'Espacio de encuentro para generar redes de colaboración entre docentes e investigadores interesados en la innovación educativa. Presentación de la propuesta de comunidad de práctica de la facultad.',
                    ],
                ],
            ],
        ];

        if (! isset($conferences[$id])) {
            abort(404);
        }

        $conference = $conferences[$id];

        return view('show', compact('conference'));
    }
}
