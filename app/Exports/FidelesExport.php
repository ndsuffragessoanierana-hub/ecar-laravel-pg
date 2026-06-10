<?php

namespace App\Exports;

use App\Models\Fidele;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class FidelesExport implements FromCollection, WithHeadings
{
    public function __construct(private array $filters = [])
    {
    }

    public function collection(): Collection
    {
        $query = Fidele::query()->select([
            'MATRICULE', 'NOM', 'PRENOM', 'NOM_BAPTEME',
            'SEXE', 'STATUT', 'IDFARITRA', 'IDAPV'
        ]);

        $search = trim((string) ($this->filters['q'] ?? ''));
        $faritra = $this->filters['faritra'] ?? null;
        $apv = $this->filters['apv'] ?? null;

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('MATRICULE', 'like', "%{$search}%")
                  ->orWhere('NOM', 'like', "%{$search}%")
                  ->orWhere('PRENOM', 'like', "%{$search}%");
            });
        }

        if ($faritra) $query->where('IDFARITRA', $faritra);
        if ($apv) $query->where('IDAPV', $apv);

        return $query->orderBy('IDFARITRA')->orderBy('IDAPV')->orderBy('NOM')->get();
    }

    public function headings(): array
    {
        return ['MATRICULE', 'NOM', 'PRENOM', 'NOM_BAPTEME', 'SEXE', 'STATUT', 'IDFARITRA', 'IDAPV'];
    }
}