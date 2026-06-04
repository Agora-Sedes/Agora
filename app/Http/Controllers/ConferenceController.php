<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ConferenceController extends Controller
{
    public function show(int $id)
    {
      // Datos hardcodeados de prueba a reemplazar mas proximo a la fecha del evento futuro
        $jornadas = [
            1 => [
                'id'          => 1,
                'nombre'      => 'Jornada de Inteligencia Artificial y Ética',
                'descripcion' => 'Un espacio de reflexión sobre el impacto de la IA en la sociedad, sus desafíos éticos y las oportunidades para el campo académico.',
                'fecha'       => '2026-07-31',
                'lugar'       => 'Aula Magna – Sedes Sapientiae',
                'charlas'     => [
                    [
                        'id'          => 1,
                        'titulo'      => '¿Puede una máquina ser justa? Sesgos algorítmicos en la toma de decisiones',
                        'ponente'     => 'Dra. María Eugenia Torres',
                        'hora_inicio' => '09:00',
                        'hora_fin'    => '10:00',
                        'abstract'    => 'En esta charla analizamos cómo los algoritmos de aprendizaje automático pueden reproducir y amplificar sesgos existentes en la sociedad. Exploraremos casos reales de discriminación algorítmica en sistemas de contratación, justicia penal y crédito financiero, y discutiremos marcos éticos y técnicos para mitigarlos.',
                    ],
                    [
                        'id'          => 2,
                        'titulo'      => 'IA Generativa en la educación: oportunidades y riesgos',
                        'ponente'     => 'Lic. Juan Ignacio Méndez',
                        'hora_inicio' => '10:15',
                        'hora_fin'    => '11:15',
                        'abstract'    => 'Desde ChatGPT hasta modelos multimodales, la IA generativa está transformando cómo los estudiantes aprenden y cómo los docentes enseñan. Abordaremos los riesgos académicos (plagio, desinformación) y las posibilidades pedagógicas reales que ofrecen estas herramientas.',
                    ],
                    [
                        'id'          => 3,
                        'titulo'      => 'Regulación de la IA: el debate global y el rol de las universidades',
                        'ponente'     => 'Dr. Carlos Ferreyra',
                        'hora_inicio' => '11:30',
                        'hora_fin'    => '12:30',
                        'abstract'    => 'Un recorrido por los principales marcos regulatorios internacionales (EU AI Act, iniciativas latinoamericanas) y el rol que pueden y deben cumplir las instituciones académicas en la definición de políticas públicas sobre inteligencia artificial.',
                    ],
                    [
                        'id'          => 4,
                        'titulo'      => 'Panel de cierre: ¿Hacia dónde va la IA?',
                        'ponente'     => 'Mesa redonda – Todos los expositores',
                        'hora_inicio' => '14:00',
                        'hora_fin'    => '15:30',
                        'abstract'    => 'Espacio abierto de preguntas y debate entre los expositores y el público asistente. Se abordarán las preguntas surgidas durante la jornada y se reflexionará colectivamente sobre el futuro de la inteligencia artificial en el ámbito académico y social.',
                    ],
                ],
            ],
            2 => [
                'id'          => 2,
                'nombre'      => 'Jornada de Neurociencias y Conducta',
                'descripcion' => 'Exploramos los últimos avances en neurociencia cognitiva y su relación con la conducta humana y los trastornos mentales.',
                'fecha'       => '2026-08-21',
                'lugar'       => 'Salón Azul – Sedes Sapientiae',
                'charlas'     => [
                    [
                        'id'          => 1,
                        'titulo'      => 'Neuroplasticidad y aprendizaje: lo que la ciencia nos dice',
                        'ponente'     => 'Dr. Alejandro Vidal',
                        'hora_inicio' => '09:00',
                        'hora_fin'    => '10:00',
                        'abstract'    => 'La neuroplasticidad es la capacidad del cerebro de reorganizarse formando nuevas conexiones neuronales. En esta charla revisaremos la evidencia científica más reciente sobre cómo aprendemos, qué condiciones favorecen la plasticidad y qué implicancias tiene esto para la práctica docente.',
                    ],
                    [
                        'id'          => 2,
                        'titulo'      => 'Trastornos del espectro autista: diagnóstico temprano y abordajes actuales',
                        'ponente'     => 'Lic. Sofía Ramírez',
                        'hora_inicio' => '10:30',
                        'hora_fin'    => '11:30',
                        'abstract'    => 'Actualización sobre criterios diagnósticos actuales del TEA, herramientas de detección temprana y los abordajes terapéuticos con mayor evidencia científica. Se hará especial énfasis en el trabajo interdisciplinario y en la inclusión educativa.',
                    ],
                    [
                        'id'          => 3,
                        'titulo'      => 'Mindfulness y regulación emocional: evidencia y práctica',
                        'ponente'     => 'Dra. Laura Piccini',
                        'hora_inicio' => '12:00',
                        'hora_fin'    => '13:00',
                        'abstract'    => 'Revisión de la evidencia científica sobre las prácticas de mindfulness y su impacto en la regulación emocional, el estrés y el bienestar psicológico. Incluye una demostración práctica y análisis de su aplicación en contextos clínicos y educativos.',
                    ],
                ],
            ],
            3 => [
                'id'          => 3,
                'nombre'      => 'Jornada de Innovación Educativa',
                'descripcion' => 'Metodologías activas, tecnología en el aula y nuevos paradigmas de evaluación para la educación superior del siglo XXI.',
                'fecha'       => '2026-09-15',
                'lugar'       => 'Aula Magna – Sedes Sapientiae',
                'charlas'     => [
                    [
                        'id'          => 1,
                        'titulo'      => 'Aula invertida: experiencias y resultados en educación superior',
                        'ponente'     => 'Mg. Roberto Acosta',
                        'hora_inicio' => '09:00',
                        'hora_fin'    => '10:00',
                        'abstract'    => 'Presentación de experiencias concretas de implementación del modelo de aula invertida (flipped classroom) en carreras de grado. Se analizarán resultados de aprendizaje, desafíos de implementación y percepciones de docentes y estudiantes.',
                    ],
                    [
                        'id'          => 2,
                        'titulo'      => 'Evaluación auténtica: más allá del examen tradicional',
                        'ponente'     => 'Dra. Marcela Gómez',
                        'hora_inicio' => '10:15',
                        'hora_fin'    => '11:15',
                        'abstract'    => 'Las evaluaciones tradicionales miden memorización, no competencias. Exploraremos formatos alternativos de evaluación auténtica: portafolios, rúbricas, proyectos integradores y autoevaluación, con ejemplos aplicables en distintas disciplinas.',
                    ],
                    [
                        'id'          => 3,
                        'titulo'      => 'Gamificación en el aula: diseño y resultados',
                        'ponente'     => 'Lic. Diego Santillán',
                        'hora_inicio' => '11:30',
                        'hora_fin'    => '12:30',
                        'abstract'    => 'La gamificación no es solo "jugar en clase". Aprenderemos a diseñar experiencias gamificadas coherentes con objetivos de aprendizaje, analizaremos plataformas disponibles y revisaremos evidencia sobre su impacto en motivación y rendimiento académico.',
                    ],
                    [
                        'id'          => 4,
                        'titulo'      => 'Tecnologías emergentes para la inclusión educativa',
                        'ponente'     => 'Dra. Ana Belén Herrera',
                        'hora_inicio' => '14:00',
                        'hora_fin'    => '15:00',
                        'abstract'    => 'Herramientas digitales accesibles, lectura fácil, subtitulado automático y otras tecnologías de asistencia que permiten diseñar entornos de aprendizaje verdaderamente inclusivos para estudiantes con diversas necesidades.',
                    ],
                    [
                        'id'          => 5,
                        'titulo'      => 'Cierre y trabajo en red: construyendo una comunidad de práctica',
                        'ponente'     => 'Equipo organizador',
                        'hora_inicio' => '15:15',
                        'hora_fin'    => '16:00',
                        'abstract'    => 'Espacio de encuentro para generar redes de colaboración entre docentes e investigadores interesados en la innovación educativa. Presentación de la propuesta de comunidad de práctica de la facultad.',
                    ],
                ],
            ],
        ];

        if (! isset($jornadas[$id])) {
            abort(404);
        }

        $jornada = $jornadas[$id];

        return view('show', compact('jornada'));
    }
}
