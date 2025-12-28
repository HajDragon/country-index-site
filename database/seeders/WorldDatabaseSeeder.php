<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class WorldDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sqlFilePath = database_path('../world-db/world.sql');

        if (! File::exists($sqlFilePath)) {
            $this->command->error('World database SQL file not found at: '.$sqlFilePath);

            return;
        }

        $this->command->info('Truncating existing world tables...');

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('countrylanguage')->truncate();
        DB::table('city')->truncate();
        DB::table('country')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $this->command->info('Importing country data...');
        $this->importTableData('country', $sqlFilePath);

        $this->command->info('Importing city data...');
        $this->importTableData('city', $sqlFilePath);

        $this->command->info('Importing country language data...');
        $this->importTableData('countrylanguage', $sqlFilePath);

        $this->command->info('World database imported successfully!');
    }

    protected function importTableData(string $tableName, string $filePath): void
    {
        $sql = File::get($filePath);
        preg_match_all("/INSERT INTO `{$tableName}` VALUES \([^;]+;/", $sql, $matches);

        foreach ($matches[0] as $insert) {
            if ($tableName === 'country') {
                $insert = str_replace(
                    'INSERT INTO `country` VALUES',
                    'INSERT INTO `country` (`Code`,`Name`,`Continent`,`Region`,`SurfaceArea`,`IndepYear`,`Population`,`LifeExpectancy`,`GNP`,`GNPOld`,`LocalName`,`GovernmentForm`,`HeadOfState`,`Capital`,`Code2`) VALUES',
                    $insert
                );
            }

            DB::unprepared($insert);
        }
    }
}
