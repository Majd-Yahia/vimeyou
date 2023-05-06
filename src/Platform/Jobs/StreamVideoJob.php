<?php

namespace Awesomchu\Vimeo\Platform\Jobs;

use Awesomchu\Vimeo\Services\ClientService;
use Illuminate\Contracts\Queue\ShouldQueue;

class StreamVideoJob implements ShouldQueue
{
    public function __construct(
        protected string $uri,
        protected string $filePath,
        protected int $chunkSize,
        protected int $offset = 0
    ) {
    }

    public function handle(ClientService $clientService)
    {
        $result = $clientService->streamFile($this->uri, $this->filePath, $this->chunkSize, $this->offset);

        dd($result);
        
        // Process the result or perform any additional actions if needed
        // For example, you can store the result in the database for later retrieval

        // You can also fire events or dispatch other jobs as needed

        // Example: Store the result in the database
        // YourDatabaseModel::create(['result' => $result]);
    }
}
