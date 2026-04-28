<?php
function get_schedule_storage_path()
{
    return __DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'schedule.json';
}

function get_schedule_time_slots()
{
    return ['8:00 - 10:00', '10:00 - 12:00', '1:00 - 3:00'];
}

function get_default_schedule()
{
    return [
        'الأحد' => ['أمن الشبكات', '---', 'برمجة الويب'],
        'الاثنين' => ['---', 'تحليل النظم', 'قواعد بيانات'],
        'الثلاثاء' => ['تشفير المعلومات', '---', 'مختبر شبكات'],
    ];
}

function normalize_schedule($schedule)
{
    $normalized = [];
    $defaultSchedule = get_default_schedule();

    foreach ($defaultSchedule as $day => $defaultSlots) {
        $normalized[$day] = [];

        for ($index = 0; $index < count($defaultSlots); $index++) {
            $value = $schedule[$day][$index] ?? $defaultSlots[$index];
            $value = trim((string) $value);
            $normalized[$day][$index] = $value === '' ? '---' : $value;
        }
    }

    return $normalized;
}

function load_schedule()
{
    $storagePath = get_schedule_storage_path();

    if (!file_exists($storagePath)) {
        return get_default_schedule();
    }

    $json = file_get_contents($storagePath);

    if ($json === false) {
        return get_default_schedule();
    }

    $decoded = json_decode($json, true);

    if (!is_array($decoded)) {
        return get_default_schedule();
    }

    return normalize_schedule($decoded);
}

function save_schedule($schedule)
{
    $storagePath = get_schedule_storage_path();
    $directoryPath = dirname($storagePath);

    if (!is_dir($directoryPath) && !mkdir($directoryPath, 0777, true) && !is_dir($directoryPath)) {
        return false;
    }

    $payload = json_encode(normalize_schedule($schedule), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    if ($payload === false) {
        return false;
    }

    return file_put_contents($storagePath, $payload) !== false;
}
?>