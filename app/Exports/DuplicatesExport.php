<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Collection;

class DuplicatesExport implements FromCollection
{
    protected $duplicates;

    public function __construct(array $duplicates)
    {
        $this->duplicates = $duplicates;
    }

    public function collection(): Collection
    {
        return collect($this->duplicates);
    }
}
