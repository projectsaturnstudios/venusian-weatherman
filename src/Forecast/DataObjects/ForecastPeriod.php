<?php

namespace ProjectSaturnStudios\Weatherman\Forecast\DataObjects;

use ProjectSaturnStudios\Weatherman\Contracts\HydratesFromArray;
use ProjectSaturnStudios\Weatherman\DataObjects\QuantitativeValue;
use ProjectSaturnStudios\Weatherman\Enums\TemperatureTrend;
use ProjectSaturnStudios\Weatherman\Support\HydratesNwsData;

final readonly class ForecastPeriod implements HydratesFromArray
{
    use HydratesNwsData;

    public function __construct(
        public ?int $number,
        public string $name,
        public ?string $start_time,
        public ?string $end_time,
        public bool $is_daytime,
        public ?float $temperature,
        public ?string $temperature_unit,
        public ?QuantitativeValue $temperature_reading,
        public ?string $temperature_trend,
        public ?QuantitativeValue $probability_of_precipitation,
        public ?QuantitativeValue $dewpoint,
        public ?QuantitativeValue $relative_humidity,
        public ?string $wind_speed,
        public ?QuantitativeValue $wind_speed_reading,
        public ?string $wind_direction,
        public ?string $icon,
        public string $short_forecast,
        public string $detailed_forecast,
    ) {}

    public static function fromArray(array $data): static
    {
        [$temperature, $temperature_unit, $temperature_reading] = self::temperatureReading($data);
        [$wind_speed, $wind_speed_reading] = self::windReading($data);

        return new self(
            number: self::optionalInt($data, 'number'),
            name: self::text($data, 'name'),
            start_time: self::optionalText($data, 'startTime'),
            end_time: self::optionalText($data, 'endTime'),
            is_daytime: (bool) ($data['isDaytime'] ?? false),
            temperature: $temperature,
            temperature_unit: $temperature_unit,
            temperature_reading: $temperature_reading,
            temperature_trend: self::optionalText($data, 'temperatureTrend'),
            probability_of_precipitation: self::quantitative($data['probabilityOfPrecipitation'] ?? null),
            dewpoint: self::quantitative($data['dewpoint'] ?? null),
            relative_humidity: self::quantitative($data['relativeHumidity'] ?? null),
            wind_speed: $wind_speed,
            wind_speed_reading: $wind_speed_reading,
            wind_direction: self::optionalText($data, 'windDirection'),
            icon: self::optionalText($data, 'icon'),
            short_forecast: self::text($data, 'shortForecast'),
            detailed_forecast: self::text($data, 'detailedForecast'),
        );
    }

    public function knownTrend(): ?TemperatureTrend
    {
        if (is_null($this->temperature_trend)) {
            return null;
        }

        return TemperatureTrend::tryFrom($this->temperature_trend);
    }

    public function windSpeedText(): ?string
    {
        if (! is_null($this->wind_speed)) {
            return $this->wind_speed;
        }

        if (is_null($this->wind_speed_reading) || is_null($this->wind_speed_reading->value)) {
            return null;
        }

        return trim($this->wind_speed_reading->value.' '.($this->wind_speed_reading->unit_code ?? ''));
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{0: ?float, 1: ?string, 2: ?QuantitativeValue}
     */
    protected static function temperatureReading(array $data): array
    {
        $temperature = $data['temperature'] ?? null;

        if (is_array($temperature)) {
            $reading = QuantitativeValue::fromArray($temperature);

            return [$reading->value, $reading->unit_code, $reading];
        }

        if (is_null($temperature) || $temperature === '') {
            return [null, self::optionalText($data, 'temperatureUnit'), null];
        }

        return [(float) $temperature, self::optionalText($data, 'temperatureUnit'), null];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{0: ?string, 1: ?QuantitativeValue}
     */
    protected static function windReading(array $data): array
    {
        $wind = $data['windSpeed'] ?? null;

        if (is_array($wind)) {
            return [null, QuantitativeValue::fromArray($wind)];
        }

        if (is_null($wind) || $wind === '') {
            return [null, null];
        }

        return [(string) $wind, null];
    }
}
