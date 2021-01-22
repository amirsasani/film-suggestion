<?php

namespace App\Imports;

use App\Jobs\UpdateTitle;
use App\Models\Title;
use App\Services\Imdb\Handler;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TitlesImport implements ToModel, WithHeadingRow, WithChunkReading, ShouldQueue
{
    use Importable;

    /**
     * @param  array  $row
     *
     * @return Model|null
     */
    public function model(array $row)
    {
        $row = $row['tconst_averagerating_numvotes'];
        $row = explode("\t", $row);
        $imdb_id = $row[0];

        $title = Title::updateOrCreate(compact('imdb_id'));

        UpdateTitle::dispatch($title);

        return $title;
    }

    public function chunkSize(): int
    {
        return 1000;
    }

}
