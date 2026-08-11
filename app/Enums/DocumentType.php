<?php

namespace App\Enums;

enum DocumentType: string
{
    case BirthCertificate = 'birth_certificate';
    case FamilyCard = 'family_card';
    case Kia = 'kia';
    case Bpjs = 'bpjs';
    case Passport = 'passport';
    case Certificate = 'certificate';
    case ReportCard = 'report_card';
    case Other = 'other';

    /**
     * Get the human-readable label for the document type.
     */
    public function label(): string
    {
        return match ($this) {
            self::BirthCertificate => __('document_types.birth_certificate'),
            self::FamilyCard => __('document_types.family_card'),
            self::Kia => __('document_types.kia'),
            self::Bpjs => __('document_types.bpjs'),
            self::Passport => __('document_types.passport'),
            self::Certificate => __('document_types.certificate'),
            self::ReportCard => __('document_types.report_card'),
            self::Other => __('document_types.other'),
        };
    }

    /**
     * Get the emoji for the document type.
     */
    public function emoji(): string
    {
        return match ($this) {
            self::BirthCertificate => '📜',
            self::FamilyCard => '🏠',
            self::Kia => '🪪',
            self::Bpjs => '🏥',
            self::Passport => '✈️',
            self::Certificate => '🎓',
            self::ReportCard => '📋',
            self::Other => '📄',
        };
    }

    /**
     * Get all options as key => label pairs for form selects.
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (DocumentType $type) => [$type->value => $type->emoji().' '.$type->label()])
            ->all();
    }
}
