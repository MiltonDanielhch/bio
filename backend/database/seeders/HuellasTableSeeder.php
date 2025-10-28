<?php

namespace Database\Seeders;

use App\Models\Huella;
use Illuminate\Database\Seeder;

class HuellasTableSeeder extends Seeder
{
    public function run(): void
    {
        Huella::query()->delete();

        $data = [];
        $zk   = 1;                  // zk_user_id secuencial
        foreach (range(1, 8) as $empId) {
            // 1 ó 2 dedos por empleado (aleatorio)
            foreach (range(1, rand(1, 2)) as $dedo) {
                $data[] = [
                    'empleado_id'        => $empId,
                    'zk_user_id'         => $zk++,
                    'template_huella'    => $this->fakeTemplate(),
                    'numero_dedo'        => $dedo,
                    'calidad'            => collect(['alta', 'media'])->random(),
                    'formato_template'   => 'zk',
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ];
            }
        }
        Huella::insert($data);
        $this->command->info('Huellas creadas: ' . count($data));
    }

    private function fakeTemplate(): string
    {
        return random_bytes(512);   // template binario fake
    }
}
