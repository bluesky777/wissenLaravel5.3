<?php

use Illuminate\Database\Seeder;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;



class RolesTableSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Creando roles y permisos ...');
		DB::table('role_user')->delete();
		DB::table('permission_role')->delete();
		DB::table('roles')->delete();
		DB::table('permissions')->delete();

		
		// Rol de administrador
		$Admin = new Role();
		$Admin->id = 1;
		$Admin->name = 'Admin';
		$Admin->display_name = 'Admin';
		$Admin->description = 'Es el master del sistema';
		$Admin->save();

		$userAdmin = User::where('username', '=', 'admin')->get()[0];
		$userAdmin->attachRole($Admin);


		// Los demás roles
		$Profesor = new Role();
		$Profesor->id = 2;
		$Profesor->name = 'Profesor';
		$Profesor->display_name = 'Profesor';
		$Profesor->save();

		$Asesor = new Role();
		$Asesor->id = 3;
		$Asesor->name = 'Asesor';
		$Asesor->display_name = 'Asesor';
		$Asesor->save();

		$Presentador = new Role();
		$Presentador->id = 4;
		$Presentador->name = 'Presentador';
		$Presentador->display_name = 'Presentador';
		$Presentador->save();

		$Participante = new Role();
		$Participante->id = 5;
		$Participante->name = 'Participante';
		$Participante->display_name = 'Participante';
		$Participante->save();

		$Invitado = new Role();
		$Invitado->id = 6;
		$Invitado->name = 'Invitado';
		$Invitado->display_name = 'Invitado';
		$Invitado->save();

		$Pantalla = new Role();
		$Pantalla->id = 7;
		$Pantalla->name = 'Pantalla';
		$Pantalla->display_name = 'Pantalla';
		$Pantalla->description = 'Usuario que no interactúa, solo mostrará al Video Beam';
		$Pantalla->save();



		// Establecemos permisos
		$perm_teacher = new Permission();
		$perm_teacher->id = 1;
		$perm_teacher->name = 'like_teacher';
		$perm_teacher->display_name = 'Puede ver actuar como profesor';
		$perm_teacher->description = 'Interactúa en la plataforma como un profesor';
		$perm_teacher->save();

		$perm_asesor = new Permission();
		$perm_asesor->id = 2;
		$perm_asesor->name = 'like_asesor';
		$perm_asesor->display_name = 'Puede ver actuar como asesor';
		$perm_asesor->description = 'Interactúa en la plataforma como un asesor';
		$perm_asesor->save();

		$perm_presentador = new Permission();
		$perm_presentador->id = 3;
		$perm_presentador->name = 'like_presentador';
		$perm_presentador->display_name = 'Puede ver actuar como presentador';
		$perm_presentador->description = 'Interactúa en la plataforma como un presentador';
		$perm_presentador->save();

		$perm_participante = new Permission();
		$perm_participante->id = 4;
		$perm_participante->name = 'like_participante';
		$perm_participante->display_name = 'Puede ver actuar como participante';
		$perm_participante->description = 'Interactúa en la plataforma como un participante';
		$perm_participante->save();

		$perm_invitado = new Permission();
		$perm_invitado->id = 5;
		$perm_invitado->name = 'like_invitado';
		$perm_invitado->display_name = 'Puede ver actuar como invitado';
		$perm_invitado->description = 'Interactúa en la plataforma como un invitado';
		$perm_invitado->save();

		$perm_pantalla = new Permission();
		$perm_pantalla->id = 6;
		$perm_pantalla->name = 'like_pantalla';
		$perm_pantalla->display_name = 'Puede mostrar todo';
		$perm_pantalla->description = 'Interactúa en la plataforma como una pantalla';
		$perm_pantalla->save();


		// Asignamos los permisos a sus respectivos roles
		$Profesor->attachPermission($perm_teacher);
		$Asesor->attachPermission($perm_asesor);
		$Presentador->attachPermission($perm_presentador);
		$Participante->attachPermission($perm_participante);
		$Invitado->attachPermission($perm_invitado);
		$Pantalla->attachPermission($perm_pantalla);



    }
}
