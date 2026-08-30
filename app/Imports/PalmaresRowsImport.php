<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

/**
 * Reads a palmarès CSV/XLSX into associative rows keyed by normalized
 * header (matricule, nom, postnom, prenom, sexe, date_naissance, mention,
 * moyenne). WithHeadingRow lower-cases and snake_cases the first row for us.
 */
class PalmaresRowsImport implements ToCollection, WithHeadingRow
{
    public Collection $rows;

    public function collection(Collection $rows): void
    {
        $this->rows = $rows;
    }
}
