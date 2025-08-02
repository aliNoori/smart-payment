<?php

namespace SmartPayment\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * Class FixModelNamespace
 *
 * This command scans the application's Models directory and replaces
 * any references to the default package namespace (OtpLogin\Models)
 * with the application's namespace (App\Models).
 *
 * Intended to be run after publishing the package to ensure proper integration.
 */
class FixModelNamespace extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'smart-payment:fix-model-namespaces';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Replace model namespaces after publishing';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $path = app_path('Models');

        // Check if the Models directory exists
        if (!File::exists($path)) {
            $this->warn("⚠️ Models path does not exist: $path");
            return;
        }

        // Retrieve all files within the Models directory
        $files = File::allFiles($path);

        foreach ($files as $file) {
            $content = File::get($file->getPathname());

            // Look for the default package namespace
            if (str_contains($content, 'namespace SmartPayment\Models')) {
                // Replace with the application's namespace
                $newContent = str_replace(
                    'namespace SmartPayment\Models',
                    'namespace App\Models',
                    $content
                );

                File::put($file->getPathname(), $newContent);
                $this->info("✅ Updated namespace in: " . $file->getFilename());
            }
        }

        $this->info('🎉 All namespaces updated successfully.');
    }
}
