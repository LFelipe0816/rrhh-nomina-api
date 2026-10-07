<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JobLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jobLevels = [
            [
                'name' => 'Pasante',
                'code' => 'PAS',
                'description' => 'Estudiante o recién graduado en formación práctica',
            ],
            [
                'name' => 'Trainee',
                'code' => 'TRN',
                'description' => 'Profesional en programa de entrenamiento inicial',
            ],
            [
                'name' => 'Junior',
                'code' => 'JUN',
                'description' => 'Profesional con experiencia básica (0-2 años)',
            ],
            [
                'name' => 'Analista',
                'code' => 'ANA',
                'description' => 'Profesional con experiencia intermedia (2-4 años)',
            ],
            [
                'name' => 'Senior',
                'code' => 'SEN',
                'description' => 'Profesional experto con amplia experiencia (4+ años)',
            ],
            [
                'name' => 'Líder',
                'code' => 'LID',
                'description' => 'Responsable de coordinar equipos de trabajo',
            ],
            [
                'name' => 'Supervisor',
                'code' => 'SUP',
                'description' => 'Encargado de supervisar operaciones y personal',
            ],
            [
                'name' => 'Coordinador',
                'code' => 'COO',
                'description' => 'Responsable de coordinar áreas o proyectos específicos',
            ],
            [
                'name' => 'Gerente',
                'code' => 'GER',
                'description' => 'Responsable de la gestión de un departamento o área',
            ],
            [
                'name' => 'Director',
                'code' => 'DIR',
                'description' => 'Responsable de la dirección estratégica de múltiples áreas',
            ],
            [
                'name' => 'Ejecutivo',
                'code' => 'EJE',
                'description' => 'Miembro del equipo ejecutivo de alta dirección',
            ],
        ];

        $now = now();

        foreach ($jobLevels as $level) {
            DB::table('job_levels')->updateOrInsert(
                ['name' => $level['name']],
                [
                    'code' => $level['code'],
                    'description' => $level['description'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }
}
