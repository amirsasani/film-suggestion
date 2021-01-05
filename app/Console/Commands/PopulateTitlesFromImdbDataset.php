<?php

namespace App\Console\Commands;

use App\Services\Imdb\Handler;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PopulateTitlesFromImdbDataset extends Command
{
//    private $dataset_url = "https://datasets.imdbws.com/title.ratings.tsv.gz";
    private $dataset_url = "https://datasets.imdbws.com/title.ratings.tsv.gz";
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'imdb:populate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate titles from IMDB dataset';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $this->alert('Downloading rating dataset');
        Log::alert('Downloading rating dataset');

        $input = gzopen($this->dataset_url, 'r');

        $i = 0;
        while ($row = fgetcsv($input, 0, "\t")) {
            if ($i > 0) {
                $row[] = $i;
                $id = $row[0];

                $imdb = new \Imdb\Title($id);
                $title = Handler::insertTitle($imdb);

                $log_msg = sprintf('title #%s added', $title->id);
                $this->info($log_msg);
                Log::info($log_msg);
            }
            $i++;
        }

        return 0;
    }
}
