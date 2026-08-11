<?php

use App\Enums\DocumentType;

describe('DocumentType Enum', function () {

    it('has all expected cases', function () {
        $cases = DocumentType::cases();

        expect($cases)->toHaveCount(8);
        expect($cases)->toContain(DocumentType::BirthCertificate);
        expect($cases)->toContain(DocumentType::FamilyCard);
        expect($cases)->toContain(DocumentType::Kia);
        expect($cases)->toContain(DocumentType::Bpjs);
        expect($cases)->toContain(DocumentType::Passport);
        expect($cases)->toContain(DocumentType::Certificate);
        expect($cases)->toContain(DocumentType::ReportCard);
        expect($cases)->toContain(DocumentType::Other);
    });

    it('returns correct string values', function () {
        expect(DocumentType::BirthCertificate->value)->toBe('birth_certificate');
        expect(DocumentType::FamilyCard->value)->toBe('family_card');
        expect(DocumentType::Kia->value)->toBe('kia');
        expect(DocumentType::Bpjs->value)->toBe('bpjs');
        expect(DocumentType::Passport->value)->toBe('passport');
        expect(DocumentType::Certificate->value)->toBe('certificate');
        expect(DocumentType::ReportCard->value)->toBe('report_card');
        expect(DocumentType::Other->value)->toBe('other');
    });

    it('returns non-empty label for each case', function () {
        foreach (DocumentType::cases() as $type) {
            expect($type->label())->toBeString()->not->toBeEmpty();
        }
    });

    it('returns correct emoji for each case', function () {
        expect(DocumentType::BirthCertificate->emoji())->toBe('📜');
        expect(DocumentType::FamilyCard->emoji())->toBe('🏠');
        expect(DocumentType::Kia->emoji())->toBe('🪪');
        expect(DocumentType::Bpjs->emoji())->toBe('🏥');
        expect(DocumentType::Passport->emoji())->toBe('✈️');
        expect(DocumentType::Certificate->emoji())->toBe('🎓');
        expect(DocumentType::ReportCard->emoji())->toBe('📋');
        expect(DocumentType::Other->emoji())->toBe('📄');
    });

    it('returns array from options() with all cases', function () {
        $options = DocumentType::options();

        expect($options)->toBeArray();
        expect($options)->toHaveCount(8);
        expect($options)->toHaveKey('birth_certificate');
        expect($options)->toHaveKey('family_card');
        expect($options)->toHaveKey('other');
    });

    it('options values contain emoji and label', function () {
        $options = DocumentType::options();

        foreach (DocumentType::cases() as $type) {
            expect($options[$type->value])->toContain($type->emoji());
            expect($options[$type->value])->toContain($type->label());
        }
    });

    it('can be cast from string value', function () {
        $type = DocumentType::from('birth_certificate');

        expect($type)->toBe(DocumentType::BirthCertificate);
    });

});
