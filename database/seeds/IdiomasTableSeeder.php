<?php

use Illuminate\Database\Seeder;

use App\Models\Idioma;



class IdiomasTableSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Creando idiomas para el programa ...');
		DB::table('ws_idiomas')->delete();


		$idiomas = [
			array('id' => 1, 'nombre' => 'Español', 'abrev' => 'ES', 'original' => 'Español', 'used_by_system' => true),
			array('id' => 2, 'nombre' => 'Inglés', 'abrev' => 'EN', 'original' => 'English', 'used_by_system' => true),

			array('id' => 3, 'nombre' => 'Portugués', 'abrev' => 'PT', 'original' => 'Português', 'used_by_system' => true),
			array('id' => 4, 'nombre' => 'Francés', 'abrev' => 'FR', 'original' => 'Français', 'used_by_system' => true),
			array('id' => 5, 'nombre' => 'Chino', 'abrev' => 'ZH', 'original' => '中文', 'used_by_system' => false),
			array('id' => 6, 'nombre' => 'Árabe', 'abrev' => 'AR', 'original' => 'العربية', 'used_by_system' => false),
			array('id' => 7, 'nombre' => 'Ruso', 'abrev' => 'RU', 'original' => 'Русский', 'used_by_system' => false),
			array('id' => 8, 'nombre' => 'Alemán', 'abrev' => 'DE', 'original' => 'Deutsch', 'used_by_system' => false),
			array('id' => 9, 'nombre' => 'Hindi', 'abrev' => 'HI', 'original' => 'हिन्दी', 'used_by_system' => false),
			array('id' => 10, 'nombre' => 'Japonés', 'abrev' => 'JA', 'original' => '日本語', 'used_by_system' => false),
			array('id' => 11, 'nombre' => 'Coreano', 'abrev' => 'KO', 'original' => '한국어', 'used_by_system' => false),
			array('id' => 12, 'nombre' => 'Italiano', 'abrev' => 'IT', 'original' => 'Italiano', 'used_by_system' => false),
					
		];

		foreach ($idiomas as $key => $idioma) {
			

			$Newidioma = new Idioma;
			$Newidioma->id = $idioma['id'];
			$Newidioma->nombre = $idioma['nombre'];
			$Newidioma->abrev = $idioma['abrev'];
			$Newidioma->original = $idioma['original'];
			$Newidioma->used_by_system = $idioma['used_by_system'];

			$Newidioma->save();


		}



    }
}
