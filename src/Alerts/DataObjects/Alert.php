<?php

namespace ProjectSaturnStudios\Weatherman\Alerts\DataObjects;

use ProjectSaturnStudios\Weatherman\Contracts\HydratesFromArray;
use ProjectSaturnStudios\Weatherman\DataObjects\Geometry;
use ProjectSaturnStudios\Weatherman\Enums\AlertCertainty;
use ProjectSaturnStudios\Weatherman\Enums\AlertMessageType;
use ProjectSaturnStudios\Weatherman\Enums\AlertSeverity;
use ProjectSaturnStudios\Weatherman\Enums\AlertStatus;
use ProjectSaturnStudios\Weatherman\Enums\AlertUrgency;
use ProjectSaturnStudios\Weatherman\Support\HydratesNwsData;
use Voyager\NutsAndBolts\Collection;

final readonly class Alert implements HydratesFromArray
{
    use HydratesNwsData;

    /**
     * @param  Collection<int, string>  $affected_zones
     * @param  Collection<int, AlertReference>  $references
     * @param  array<string, list<string>>  $geocode
     * @param  array<string, list<string>>  $event_code
     * @param  array<string, list<string>>  $parameters
     */
    public function __construct(
        public string $id,
        public ?string $url,
        public ?string $area_desc,
        public array $geocode,
        public Collection $affected_zones,
        public Collection $references,
        public ?string $sent,
        public ?string $effective,
        public ?string $onset,
        public ?string $expires,
        public ?string $ends,
        public ?string $status,
        public ?string $message_type,
        public ?string $category,
        public ?string $severity,
        public ?string $certainty,
        public ?string $urgency,
        public ?string $event,
        public ?string $sender,
        public ?string $sender_name,
        public ?string $headline,
        public ?string $description,
        public ?string $instruction,
        public ?string $response,
        public ?string $note,
        public ?string $language,
        public ?string $scope,
        public ?string $web,
        public ?string $code,
        public array $event_code,
        public array $parameters,
        public ?Geometry $geometry,
    ) {}

    public static function fromArray(array $data): static
    {
        $properties = self::properties($data);

        return new self(
            id: self::text($properties, 'id'),
            url: self::optionalText($properties, '@id'),
            area_desc: self::optionalText($properties, 'areaDesc'),
            geocode: self::stringMap($properties['geocode'] ?? null),
            affected_zones: self::stringList($properties['affectedZones'] ?? null),
            references: self::collectionOf($properties['references'] ?? [], AlertReference::class),
            sent: self::optionalText($properties, 'sent'),
            effective: self::optionalText($properties, 'effective'),
            onset: self::optionalText($properties, 'onset'),
            expires: self::optionalText($properties, 'expires'),
            ends: self::optionalText($properties, 'ends'),
            status: self::optionalText($properties, 'status'),
            message_type: self::optionalText($properties, 'messageType'),
            category: self::optionalText($properties, 'category'),
            severity: self::optionalText($properties, 'severity'),
            certainty: self::optionalText($properties, 'certainty'),
            urgency: self::optionalText($properties, 'urgency'),
            event: self::optionalText($properties, 'event'),
            sender: self::optionalText($properties, 'sender'),
            sender_name: self::optionalText($properties, 'senderName'),
            headline: self::optionalText($properties, 'headline'),
            description: self::optionalText($properties, 'description'),
            instruction: self::optionalText($properties, 'instruction'),
            response: self::optionalText($properties, 'response'),
            note: self::optionalText($properties, 'note'),
            language: self::optionalText($properties, 'language'),
            scope: self::optionalText($properties, 'scope'),
            web: self::optionalText($properties, 'web'),
            code: self::optionalText($properties, 'code'),
            event_code: self::stringMap($properties['eventCode'] ?? null),
            parameters: self::stringMap($properties['parameters'] ?? null),
            geometry: self::geometry($data),
        );
    }

    public function knownStatus(): ?AlertStatus
    {
        return is_null($this->status) ? null : AlertStatus::tryFrom($this->status);
    }

    public function knownMessageType(): ?AlertMessageType
    {
        return is_null($this->message_type) ? null : AlertMessageType::tryFrom($this->message_type);
    }

    public function knownSeverity(): ?AlertSeverity
    {
        return is_null($this->severity) ? null : AlertSeverity::tryFrom($this->severity);
    }

    public function knownUrgency(): ?AlertUrgency
    {
        return is_null($this->urgency) ? null : AlertUrgency::tryFrom($this->urgency);
    }

    public function knownCertainty(): ?AlertCertainty
    {
        return is_null($this->certainty) ? null : AlertCertainty::tryFrom($this->certainty);
    }
}
