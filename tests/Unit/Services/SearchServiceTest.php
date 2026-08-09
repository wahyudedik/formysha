<?php

use App\Models\Child;
use App\Models\Diary;
use App\Models\Document;
use App\Models\Event;
use App\Models\FamilyMember;
use App\Models\Growth;
use App\Models\HealthRecord;
use App\Models\Timeline;
use App\Models\User;
use App\Services\SearchService;

describe('SearchService', function () {
    beforeEach(function () {
        $this->service = new SearchService;
        $this->user = User::factory()->create();
    });

    it('has correct constants', function () {
        expect(SearchService::MIN_QUERY_LENGTH)->toBe(2);
        expect(SearchService::MAX_RESULTS)->toBe(20);
        expect(SearchService::TYPES)->toContain('child');
        expect(SearchService::TYPES)->toContain('timeline');
        expect(SearchService::TYPES)->toContain('diary');
        expect(SearchService::TYPES)->toContain('document');
        expect(SearchService::TYPES)->toContain('event');
        expect(SearchService::TYPES)->toContain('health');
        expect(SearchService::TYPES)->toContain('growth');
        expect(SearchService::TYPES)->toContain('family');
    });

    it('searches children by name', function () {
        Child::factory()->create(['user_id' => $this->user->id, 'name' => 'Aisyah']);
        Child::factory()->create(['user_id' => $this->user->id, 'name' => 'Bintang']);

        $results = $this->service->search($this->user, 'aisyah');

        expect($results['children'])->toHaveCount(1);
        expect($results['children']->first()->name)->toBe('Aisyah');
        expect($results['counts']['child'])->toBe(1);
    });

    it('searches children by nickname', function () {
        Child::factory()->create(['user_id' => $this->user->id, 'name' => 'Muhammad Raka', 'nickname' => 'Raka']);

        $results = $this->service->search($this->user, 'raka');

        expect($results['children'])->toHaveCount(1);
    });

    it('searches timelines by title', function () {
        $child = Child::factory()->create(['user_id' => $this->user->id]);
        Timeline::factory()->create(['child_id' => $child->id, 'title' => 'Hari Pertama Sekolah']);

        $results = $this->service->search($this->user, 'sekolah');

        expect($results['timelines'])->toHaveCount(1);
        expect($results['counts']['timeline'])->toBe(1);
    });

    it('searches diaries by content', function () {
        $child = Child::factory()->create(['user_id' => $this->user->id]);
        Diary::factory()->create(['child_id' => $child->id, 'title' => 'Catatan Hari Ini', 'content' => 'Anak saya demam']);

        $results = $this->service->search($this->user, 'demam');

        expect($results['diaries'])->toHaveCount(1);
    });

    it('searches documents by name', function () {
        $child = Child::factory()->create(['user_id' => $this->user->id]);
        Document::factory()->create(['child_id' => $child->id, 'name' => 'Akta Kelahiran']);

        $results = $this->service->search($this->user, 'akta');

        expect($results['documents'])->toHaveCount(1);
    });

    it('searches events by title', function () {
        $child = Child::factory()->create(['user_id' => $this->user->id]);
        Event::factory()->create(['child_id' => $child->id, 'title' => 'Ulang Tahun Pertama']);

        $results = $this->service->search($this->user, 'ulang tahun');

        expect($results['events'])->toHaveCount(1);
    });

    it('searches health records by doctor name', function () {
        $child = Child::factory()->create(['user_id' => $this->user->id]);
        HealthRecord::factory()->create(['child_id' => $child->id, 'name' => 'Imunisasi PCV', 'doctor' => 'dr. Sari']);

        $results = $this->service->search($this->user, 'sari');

        expect($results['health'])->toHaveCount(1);
    });

    it('searches growth records by notes', function () {
        $child = Child::factory()->create(['user_id' => $this->user->id]);
        Growth::factory()->create(['child_id' => $child->id, 'notes' => 'Pertumbuhan normal']);

        $results = $this->service->search($this->user, 'normal');

        expect($results['growths'])->toHaveCount(1);
    });

    it('searches family members by name', function () {
        $child = Child::factory()->create(['user_id' => $this->user->id]);
        FamilyMember::factory()->create(['child_id' => $child->id, 'name' => 'Papa Budi']);

        $results = $this->service->search($this->user, 'budi');

        expect($results['families'])->toHaveCount(1);
        expect($results['counts']['family'])->toBe(1);
    });

    it('searches with type filter', function () {
        $child = Child::factory()->create(['user_id' => $this->user->id, 'name' => 'Aisyah']);
        Timeline::factory()->create(['child_id' => $child->id, 'title' => 'Timeline Aisyah']);

        $results = $this->service->search($this->user, 'aisyah', 'child');

        expect($results['children'])->toHaveCount(1);
        expect($results['timelines'])->toHaveCount(0);
    });

    it('counts all results correctly', function () {
        $child = Child::factory()->create(['user_id' => $this->user->id, 'name' => 'Aisyah']);
        Timeline::factory()->create(['child_id' => $child->id, 'title' => 'Aisyah bermain']);
        Diary::factory()->create(['child_id' => $child->id, 'title' => 'Aisyah ceria', 'content' => 'test']);

        $results = $this->service->search($this->user, 'aisyah');

        expect($results['counts']['all'])->toBeGreaterThanOrEqual(2);
    });

    it('returns empty results for short query via search method', function () {
        $results = $this->service->search($this->user, 'a');

        expect($results['children'])->toHaveCount(0);
    });
});
