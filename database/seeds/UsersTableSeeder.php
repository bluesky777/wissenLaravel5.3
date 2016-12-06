<?php

use Illuminate\Database\Seeder;


use App\Models\User;
use App\Models\ImagenModel;



class UsersTableSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Creando usuario ...');
		DB::table('users')->delete();

		$Newuser = new User;
		$Newuser->id 				= 1;
		$Newuser->username 			= 'admin';
		$Newuser->nombres 			= 'Joseth David';
		$Newuser->apellidos 		= 'Guerrero Escobar';
		$Newuser->email 			= 'davidguerrero777@gmail.com';
		$Newuser->password 			= Hash::make('123');
		$Newuser->sexo 				= 'M';
		$Newuser->is_superuser 		= true;
		$Newuser->idioma_main_id	= 1;
		$Newuser->evento_selected_id = 1;
		$Newuser->save();


		// Usuarios prueba 
		$this->command->info('Creando usuarios de prueba ...');
		$Newuser = new User;
		$Newuser->id 				= 2;
		$Newuser->username 			= 'pr';
		$Newuser->nombres 			= 'Probador';
		$Newuser->apellidos 		= 'Del Sistema';
		$Newuser->email 			= 'pr@wissen.com';
		$Newuser->password 			= Hash::make('123');
		$Newuser->sexo 				= 'M';
		$Newuser->is_superuser 		= false;
		$Newuser->entidad_id 		= 1;
		$Newuser->idioma_main_id	= 1;
		$Newuser->save();

		$Newuser = new User;
		$Newuser->id 				= 3;
		$Newuser->username 			= 'inv';
		$Newuser->nombres 			= 'Invitado';
		$Newuser->apellidos 		= '';
		$Newuser->email 			= 'inv@wissen.com';
		$Newuser->password 			= Hash::make('123');
		$Newuser->sexo 				= 'M';
		$Newuser->is_superuser 		= false;
		$Newuser->entidad_id 		= 1;
		$Newuser->idioma_main_id	= 1;
		$Newuser->save();

		$NewuserP = new User;
		$NewuserP->id 				= 4;
		$NewuserP->username 		= 'sc';
		$NewuserP->nombres 			= 'Screen';
		$NewuserP->apellidos 		= 'Segunda';
		$NewuserP->password 		= Hash::make('123');
		$NewuserP->sexo 			= 'M';
		$NewuserP->is_superuser 	= false;
		$NewuserP->entidad_id 		= 1;
		$NewuserP->idioma_main_id	= 1;
		$NewuserP->save();




		$this->command->info('Creando imágenes por defecto ...');
		DB::table('images')->delete();


		$img1 = new ImagenModel;
		$img1->id 		= 1;
		$img1->nombre 	= 'system/avatars/female1.jpg';
		$img1->save();

		$img2 = new ImagenModel;
		$img2->id 		= 2;
		$img2->nombre 	= 'system/avatars/male1.jpg';
		$img2->save();

		$img3 = new ImagenModel;
		$img3->id 		= 3;
		$img3->nombre 	= 'system/avatars/female3.jpg';
		$img3->save();

		// Imagen por defecto de NO FOTO!!
		$img3 = new ImagenModel;
		$img3->id 		= 4;
		$img3->nombre 	= 'system/avatars/no-photo.jpg';
		$img3->save();


		// Imagen para roles pantalla
		$img3 = new ImagenModel;
		$img3->id 		= 5;
		$img3->nombre 	= 'system/avatars/proyector.jpg';
		$img3->save();

		$NewuserP->imagen_id = $img3->id;
		$NewuserP->save();

		

    }
}
