# Fitur Quiz + Nilai Quiz

Prasyarat: fitur Praktik (zip `fitur-praktik-nilai`) sudah terpasang & dimigrasi.

## File baru
database/migrations/2026_11_09_000001_create_quiz_lms_tables.php
app/Models/Lms/{Quiz,SoalQuiz,PercobaanQuiz,JawabanQuiz}.php
app/Services/Lms/{QuizTemplateService,QuizService}.php
app/Http/Controllers/Lms/Guru/QuizController.php
app/Http/Controllers/Lms/Siswa/QuizController.php
resources/views/lms/guru/quiz/{index,show}.blade.php
resources/views/lms/siswa/quiz/{index,kerjakan,hasil}.blade.php

## File yang DITIMPA
app/Services/Lms/NilaiAkhirService.php
app/Http/Controllers/Lms/Guru/NilaiController.php
resources/views/lms/guru/nilai.blade.php
resources/views/lms/guru/kelas.blade.php      (tombol Quiz)
resources/views/lms/layouts/app.blade.php     (menu Quiz siswa)

## Perintah
php artisan migrate
php artisan route:clear
php artisan view:clear
npm run build     # jika Tailwind di-build
Lalu tempel isi routes-quiz.txt ke routes/web.php.
