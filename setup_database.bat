@echo off
echo Execution des migrations restantes...

cd "C:\Users\AMED BAH\Desktop\GED\Notarix"

echo.
echo === Status des migrations ===
php artisan migrate:status

echo.
echo === Execution de la migration people ===
php artisan migrate --path=database/migrations/2025_06_19_223231_create_people_table.php

echo.
echo === Execution du seeder de test ===
php artisan db:seed --class=NotarialTestDataSeeder

echo.
echo === Status final des migrations ===
php artisan migrate:status

echo.
echo === Test de la base de donnees ===
php artisan tinker --execute="echo 'Nombre d entreprises: ' . App\Models\Entreprise::count(); echo PHP_EOL; echo 'Nombre d utilisateurs: ' . App\Models\User::count(); echo PHP_EOL; echo 'Nombre de sections: ' . App\Models\Section::count(); echo PHP_EOL;"

pause
