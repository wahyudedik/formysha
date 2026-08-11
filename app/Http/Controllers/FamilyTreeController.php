<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Services\FamilyTreeService;
use Illuminate\View\View;

class FamilyTreeController extends Controller
{
    public function __construct(
        private FamilyTreeService $familyTreeService,
    ) {}

    /**
     * Display the family tree visualization for a child.
     */
    public function index(Child $child): View
    {
        $tree = $this->familyTreeService->getTree($child);
        $familyMembers = $this->familyTreeService->getFamilyMembers($child);
        $connections = $this->familyTreeService->getConnections($child);
        $organizations = $this->familyTreeService->getOrganizations($child);
        $recentActivity = $this->familyTreeService->getAccessHistory($child, 10);

        return view('family-tree.index', [
            'child' => $child,
            'tree' => $tree,
            'familyMembers' => $familyMembers,
            'connections' => $connections,
            'organizations' => $organizations,
            'recentActivity' => $recentActivity,
        ]);
    }
}
