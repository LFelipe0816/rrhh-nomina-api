<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            [
                'company_id' => 1,
                'name' => 'Operaciones',
                'code' => 'OPS',
                'description' => 'Gestiona la ejecución de los procesos clave de la empresa.',
                'manager_name' => 'Ana Martínez',
                'status' => 1,
            ],
            [
                'company_id' => 1,
                'name' => 'Tecnología',
                'code' => 'IT',
                'description' => 'Desarrollo y soporte de plataformas digitales.',
                'manager_name' => 'Ricardo Lora',
                'status' => 1,
            ],
            [
                'company_id' => 2,
                'name' => 'Finanzas',
                'code' => 'FIN',
                'description' => 'Planificación financiera y control presupuestario.',
                'manager_name' => 'Luis Gómez',
                'status' => 1,
            ],
            [
                'company_id' => 3,
                'name' => 'Atención al Cliente',
                'code' => 'CS',
                'description' => 'Soporte y seguimiento a clientes corporativos.',
                'manager_name' => 'Verónica Jiménez',
                'status' => 1,
            ],
        ];

        foreach ($departments as $department) {
            Department::updateOrCreate(
                [
                    'company_id' => $department['company_id'],
                    'code' => $department['code'],
                ],
                $department
            );
        }
    }
}
