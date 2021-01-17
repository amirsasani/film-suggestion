<?php

namespace App\Http\Controllers;

use App\Imports\TitlesImport;
use App\Models\Title;
use App\Services\Imdb\Handler;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Excel;

class ImdbPopulateController extends Controller
{
    private $dataset_file_name = "";
    private $dataset_file_path = "";

    public function __construct()
    {
        $this->dataset_file_name = "title.ratings.tsv.gz";

        $this->dataset_file_path = storage_path('app/');
        $this->dataset_file_path .= $this->dataset_file_name;
    }

    public function download()
    {
        $disk = Storage::disk('local');

        $should_download_dataset = true;

        if ($disk->exists($this->dataset_file_name)) {
            $dataset_file_last_downloaded_time = $disk->lastModified($this->dataset_file_name);
            $dataset_file_last_downloaded_time = Carbon::createFromTimestamp($dataset_file_last_downloaded_time);
            $dataset_file_last_downloaded_time = $dataset_file_last_downloaded_time->subHours(10);

            $should_download_dataset = Carbon::now()->diffInHours($dataset_file_last_downloaded_time) >= 20;
        }

        if ($should_download_dataset) {
            $contents = file_get_contents("https://datasets.imdbws.com/".$this->dataset_file_name);
            $disk->put($this->dataset_file_name, $contents);


            $file_name = $this->dataset_file_path;

            $buffer_size = 4096; // read 4kb at a time
            $out_file_name = str_replace('.gz', '', $file_name);

            $file = gzopen($file_name, 'rb');
            $out_file = fopen($out_file_name, 'wb');

            while (!gzeof($file)) {
                fwrite($out_file, gzread($file, $buffer_size));
            }

            fclose($out_file);
            gzclose($file);
        }


        return response()->json('downloaded');
    }

    public function populate()
    {
        $titles_importer = new TitlesImport();
        $file_name = $this->dataset_file_name;
        $out_file_name = str_replace('.gz', '', $file_name);

        $titles_importer->queue($out_file_name, 'local');

        return response()->json('queued');
    }
}
