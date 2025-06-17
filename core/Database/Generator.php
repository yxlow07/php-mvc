<?php

namespace core\Database;

use Faker\Factory;
use Faker\Generator as Faker;

class Generator
{
    public Faker $faker;

    public function __construct()
    {
        $this->faker = Factory::create('ms_MY');
    }

    public function id(): string
    {
        return $this->faker->regexify('[A-D]{1}[0-9]{4}');
    }

    public function phone()
    {
        return $this->faker->voipNumber(countryCodePrefix: false, formatting: true);
    }

    public function email(): string
    {
        return $this->faker->freeEmail();
    }

    public function password(): string
    {
        return $this->faker->password(6, 6);
    }

    public function name(): string
    {
        return $this->faker->firstName() . ' ' . $this->faker->lastName();
    }

    public function class(): string
    {
        return $this->faker->regexify('[4-5]{1}[S]{1}[ABEK]{1}[0-6]{1}');
    }

    public function date(): string
    {
        return $this->faker->date();
    }

    public function address()
    {
        return $this->faker->townState();
    }

    public function image(): string
    {
        return $this->faker->imageUrl(category: 'animals');
    }

    public function status()
    {
        return $this->faker->randomElement(['active', 'offline', 'unverified']);
    }

    public function bool(): bool
    {
        return $this->faker->boolean();
    }

    public function language(): string
    {
        return strtoupper($this->faker->languageCode());
    }

    public function ip(): string
    {
        return $this->faker->ipv4();
    }

    public function client(): string
    {
        return $this->faker->userAgent();
    }

    public function sanitiseString(string $str): string
    {
        return nl2br(htmlspecialchars($str, ENT_QUOTES, 'UTF-8'));
    }

    public function generateCSVFile($data, $outputFile, $headers = []): void
    {
        $file = fopen($outputFile, 'w');

        if (!empty($headers)) {
            fputcsv($file, $headers);
        }

        foreach ($data as $key => $datum) {
            $datum = $this->sanitiseString($datum);
            fputcsv($file, [$key, $datum]);
        }

        fclose($file);
    }

    public function generateInsertSQL(string $table, array $data): string
    {
        $columns = implode(', ', array_keys($data));
        $values = implode(', ', array_map(fn($value) => "'" . $this->sanitiseString($value) . "'", array_values($data)));
        return "INSERT INTO {$table} ({$columns}) VALUES ({$values});";
    }
}