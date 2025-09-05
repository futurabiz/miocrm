<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Municipality;
use App\Models\Street;
use Exception;

class ImportStreetsCommand extends Command
{
    protected $signature = 'app:import-streets';
    protected $description = 'Import streets from the official ISTAT CSV file into the database';

    public function handle()
    {
        $this->info('Starting street import process...');
        $filePath = storage_path('app/STRAD_ITA_20250701.csv');

        if (!file_exists($filePath)) {
            $this->error("File not found at: {$filePath}");
            return 1;
        }

        $this->line('Caching municipalities...');
        $municipalitiesMap = Municipality::pluck('id', 'city_code');
        $this->info('Municipalities cached.');

        $this->line('Truncating streets table...');
        DB::table('streets')->truncate();
        $this->info('Table truncated.');

        try {
            $fileHandle = fopen($filePath, 'r');
            if (!$fileHandle) {
                throw new Exception('Could not open the CSV file.');
            }

            // Usiamo il delimitatore e i nomi delle colonne scoperti con il debug
            $header = fgetcsv($fileHandle, 0, ';');
            $codComuneIndex = array_search('CODICE_COMUNE', $header);
            $odonimoIndex = array_search('ODONIMO', $header);

            if ($codComuneIndex === false || $odonimoIndex === false) {
                throw new Exception('CSV header columns not found. Expected: CODICE_COMUNE, ODONIMO');
            }

            $progressBar = $this->output->createProgressBar();
            $batchSize = 1000;
            $batch = [];
            
            $this->info('Reading CSV and preparing data for import. This may take several minutes...');

            // Conteggio righe per una progress bar più precisa
            $lineCount = 0;
            while(!feof($fileHandle)){ fgets($fileHandle); $lineCount++; }
            rewind($fileHandle);
            fgetcsv($fileHandle, 0, ';'); // Salta di nuovo l'header

            $progressBar->start($lineCount > 0 ? $lineCount -1 : 0);

            while (($row = fgetcsv($fileHandle, 0, ';')) !== false) {
                // Assicuriamoci che la riga e le colonne esistano
                if (isset($row[$codComuneIndex]) && isset($row[$odonimoIndex])) {
                    $istatCode = $row[$codComuneIndex];

                    if (isset($municipalitiesMap[$istatCode])) {
                        $batch[] = [
                            'municipality_id' => $municipalitiesMap[$istatCode],
                            'name' => trim($row[$odonimoIndex]),
                        ];
                    }
                }

                if (count($batch) >= $batchSize) {
                    Street::insert($batch);
                    $batch = [];
                    $progressBar->advance($batchSize);
                }
            }

            if (!empty($batch)) {
                Street::insert($batch);
                $progressBar->advance(count($batch));
            }

            fclose($fileHandle);
            $progressBar->finish();
            $this->info("\nStreet import completed successfully!");

        } catch (Exception $e) {
            $this->error("\nAn error occurred: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}