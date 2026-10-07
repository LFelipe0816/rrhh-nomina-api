<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Department;
use App\Models\Employee;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = Company::all();
        $departments = Department::all();

        $employees = [
            [
                'company_name' => 'Tech Nova',
                'department_name' => 'Operaciones',
                'name' => 'Ana',
                'lastname' => 'Martínez',
                'email' => 'ana.martinez@example.com',
                'phone' => '+1-809-555-0101',
                'job_position' => 'Gerente de Operaciones',
                'address' => 'Av. Winston Churchill 123',
                'city' => 'Santo Domingo',
                'state' => 'DN',
                'postal_code' => '10147',
                'country' => 'República Dominicana',
                'date_of_birth' => '1988-03-12',
                'hire_date' => '2015-06-01',
                'monthly_salary' => 150000,
                'employment_status' => 'active',
                'emergency_contact_name' => 'Javier Martínez',
                'emergency_contact_phone' => '+1-809-555-0199',
                'notes' => 'Responsable del equipo de operaciones.',
            ],
            [
                'company_name' => 'Tech Nova',
                'department_name' => 'Tecnología',
                'name' => 'Luis',
                'lastname' => 'Gómez',
                'email' => 'luis.gomez@example.com',
                'phone' => '+1-829-555-0202',
                'job_position' => 'Analista Financiero',
                'address' => 'C/ Paseo de los Locutores 45',
                'city' => 'Santo Domingo',
                'state' => 'DN',
                'postal_code' => '10129',
                'country' => 'República Dominicana',
                'date_of_birth' => '1992-07-20',
                'hire_date' => '2019-02-15',
                'monthly_salary' => 95000,
                'employment_status' => 'active',
                'emergency_contact_name' => 'María Gómez',
                'emergency_contact_phone' => '+1-829-555-0254',
                'notes' => 'Especialista en proyecciones de flujo de caja.',
            ],
            [
                'company_name' => 'Tech Nova',
                'department_name' => 'Recursos Humanos',
                'name' => 'Carla',
                'lastname' => 'Pérez',
                'email' => 'carla.perez@example.com',
                'phone' => '+1-849-555-0303',
                'job_position' => 'Coordinadora de RRHH',
                'address' => 'C/ Max Henríquez Ureña 78',
                'city' => 'Santo Domingo',
                'state' => 'DN',
                'postal_code' => '10124',
                'country' => 'República Dominicana',
                'date_of_birth' => '1990-11-05',
                'hire_date' => '2018-09-10',
                'monthly_salary' => 80000,
                'employment_status' => 'active',
                'emergency_contact_name' => 'José Pérez',
                'emergency_contact_phone' => '+1-849-555-0377',
                'notes' => 'Gestiona nóminas y beneficios.',
            ],
            [
                'company_name' => 'Tech Nova',
                'department_name' => 'Tecnología',
                'name' => 'Ricardo',
                'lastname' => 'Lora',
                'email' => 'ricardo.lora@example.com',
                'phone' => '+1-809-555-0404',
                'job_position' => 'Desarrollador Senior',
                'address' => 'Av. 27 de Febrero 456',
                'city' => 'Santo Domingo',
                'state' => 'DN',
                'postal_code' => '10118',
                'country' => 'República Dominicana',
                'date_of_birth' => '1986-05-28',
                'hire_date' => '2016-04-18',
                'monthly_salary' => 130000,
                'employment_status' => 'active',
                'emergency_contact_name' => 'Paola Lora',
                'emergency_contact_phone' => '+1-809-555-0488',
                'notes' => 'Lidera el equipo backend.',
            ],
            [
                'company_name' => 'Caribbean Foods',
                'department_name' => 'Atención al Cliente',
                'name' => 'Verónica',
                'lastname' => 'Jiménez',
                'email' => 'veronica.jimenez@example.com',
                'phone' => '+1-829-555-0505',
                'job_position' => 'Especialista de Soporte',
                'address' => 'C/ Euclides Morillo 12',
                'city' => 'Santo Domingo',
                'state' => 'DN',
                'postal_code' => '10135',
                'country' => 'República Dominicana',
                'date_of_birth' => '1995-01-18',
                'hire_date' => '2021-03-22',
                'monthly_salary' => 60000,
                'employment_status' => 'active',
                'emergency_contact_name' => 'Laura Jiménez',
                'emergency_contact_phone' => '+1-829-555-0580',
                'notes' => 'Atiende clientes clave corporativos.',
            ],
        ];

        foreach ($employees as $employeeData) {
            $company = $companies->firstWhere('name', $employeeData['company_name']);
            $department = $departments->firstWhere('name', $employeeData['department_name']);
            
            if (!$company) {
                $this->command->warn("Company '{$employeeData['company_name']}' not found. Skipping employee {$employeeData['name']} {$employeeData['lastname']}.");
                continue;
            }

            $employee = Employee::create([
                'company_id' => $company->id,
                'department_id' => $department ? $department->id : null,
                'name' => $employeeData['name'],
                'lastname' => $employeeData['lastname'],
                'email' => $employeeData['email'],
                'phone' => $employeeData['phone'],
                'job_position' => $employeeData['job_position'],
                'address' => $employeeData['address'],
                'city' => $employeeData['city'],
                'state' => $employeeData['state'],
                'postal_code' => $employeeData['postal_code'],
                'country' => $employeeData['country'],
                'date_of_birth' => $employeeData['date_of_birth'],
                'hire_date' => $employeeData['hire_date'],
                'monthly_salary' => $employeeData['monthly_salary'],
                'employment_status' => $employeeData['employment_status'],
                'emergency_contact_name' => $employeeData['emergency_contact_name'],
                'emergency_contact_phone' => $employeeData['emergency_contact_phone'],
                'notes' => $employeeData['notes'],
            ]);
        }
    }
}
