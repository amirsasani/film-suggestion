<?php

namespace App\Jobs;

use App\Models\Title;
use App\Services\Imdb\Handler;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateTitle implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Title
     */
    protected $title;

    /**
     * Create a new job instance.
     *
     * @param  Title  $title
     */
    public function __construct(Title $title)
    {
        $this->title = $title;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Handler::insertTitle($this->title->imdb_id);
    }
}
