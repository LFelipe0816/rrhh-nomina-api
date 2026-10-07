# rrhh-nomina-api

API REST en **Laravel 12** para gestión de **recursos humanos y nómina** de varias empresas: empresas, departamentos jerárquicos, empleados, niveles y puestos, requisiciones de personal, períodos de nómina y renglones de pago.

> **Alcance, dicho con claridad.** Es una base de referencia y portafolio, no un sistema de nómina listo para producción. El cálculo es deliberadamente simple (ver [Cálculo de nómina](#cálculo-de-nómina)) y **no implementa** las reglas legales dominicanas (TSS, ISR, prestaciones). Todos los datos de ejemplo son ficticios.

## Qué incluye

| Módulo | Recursos |
|---|---|
| **Autenticación** | Login / registro / perfil / logout con tokens Bearer (Laravel Sanctum) |
| **Empresas** | CRUD de empresas y asignación de departamentos con gerente |
| **Departamentos** | Catálogo global y árbol jerárquico por empresa (`tree`, `children`) |
| **Empleados** | CRUD anidado por empresa, historial de pagos por empleado |
| **Puestos** | Niveles (`job-levels`), puestos por empresa y requisiciones (`job-requisitions`) |
| **Nómina** | Períodos por empresa y renglones (`payroll-items`) con totales calculados al guardar |

Todas las colecciones tienen una variante `/all` sin paginación para llenar selectores.

## Stack

PHP 8.2+, Laravel 12, Sanctum, MySQL 8 (Docker) o SQLite (por defecto), PHPUnit 11.

## Puesta en marcha

Con SQLite, sin Docker:

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
php artisan serve
```

La API queda en `http://127.0.0.1:8000/api/v1`. El seeder crea 10 empresas con datos ficticios (puede tardar un poco) y un usuario de prueba:

| Correo | Contraseña |
|---|---|
| `test@example.com` | `password` |

Úsalo **solo en local**.

Con Docker (MySQL + Nginx en `http://localhost:8001`):

```bash
cp .env.example .env   # y ajusta DB_CONNECTION=mysql, DB_HOST=db, DB_DATABASE, DB_USERNAME, DB_PASSWORD
docker compose up -d --build
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
```

## Ejemplo de uso

```bash
# 1. Token
curl -s -X POST http://127.0.0.1:8000/api/v1/auth/login \
  -H 'Accept: application/json' -H 'Content-Type: application/json' \
  -d '{"email":"test@example.com","password":"password"}'

# 2. Empresas (reemplaza TOKEN)
curl -s http://127.0.0.1:8000/api/v1/companies/all \
  -H 'Accept: application/json' -H 'Authorization: Bearer TOKEN'
```

Lista completa de rutas: `php artisan route:list --path=api`.

## Cálculo de nómina

Al guardar un `PayrollItem` se recalculan `gross_pay`, `total_deductions` y `net_pay` (ver `app/Models/PayrollItem.php`):

```
tarifa_hora   = salario_base / 160
horas_extra   = tarifa_hora × horas_extra × factor (por defecto 1.5)
bruto         = salario_base + horas_extra + bono
deducciones   = bruto × tax_percentage/100 + social_security + other_deductions + deductions
neto          = bruto − deducciones
```

`tax_percentage`, `social_security` y las demás deducciones se **ingresan** por renglón; no se derivan de tablas de ISR ni de topes de la TSS.

## Pruebas

```bash
php artisan test
```

Incluye pruebas unitarias del cálculo de nómina (`tests/Unit/PayrollItemCalculationTest.php`).

## Limitaciones conocidas

- `POST /api/v1/auth/register` es público y cualquier usuario autenticado ve **todas** las empresas: no hay multi-tenencia por usuario ni roles. Antes de exponerlo, restringe el registro y agrega autorización por empresa (policies).
- Sin reglas fiscales ni laborales dominicanas (ver arriba).
- Sin auditoría de cambios ni cierre de períodos.

## Configuración

`CORS_ALLOWED_ORIGINS` (opcional): orígenes adicionales separados por coma, además de `localhost:3000` y `localhost:5173`.

## Licencia

MIT. Ver [LICENSE](./LICENSE).
