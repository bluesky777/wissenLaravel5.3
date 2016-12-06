<?php namespace App\Http\Controllers;


use App\Http\Requests;
use App\Http\Controllers\Controller;



use App\Models\User;
use App\Models\Role;
use App\Models\Permission;


use Illuminate\Http\Request;


class RolesController extends Controller {


	public function getIndex()
	{
		$roles = Role::with('perms')->get();
		return $roles;

	}
	public function getRolesconpermisos()
	{
		$roles = Role::allConPermisos();
		return $roles;
	}

	public function putAddpermission(Request $request)
	{
		$rol = Role::find($id);
		$per = Permission::find($request->input('permission_id'));

		$rol->attachPermission($per);

		return $per;

	}

	public function putAddRoleToUser(Request $request)
	{
		$rol = Role::find($request->input('role_id'));
		$user = User::find($request->input('user_id'));

		if ($user->hasRole($rol->name)) {
			App::abort(400, 'Usuario ya tiene ese role.');
		}else{
			$user->attachRole($rol);
			$user->save();
		}
		

		return $user;

	}

	public function putRemoveRoleToUser(Request $request)
	{

		$rol = Role::find($request->input('role_id'));
		$user = User::find($request->input('user_id'));

		if (!$user->hasRole($rol->name)) {
			App::abort(400, 'Usuario no tiene ese role para eliminar.');
		}else{
			$user->detachRole($rol);
			$user->save();
		}

		return $user;

	}

	public function putRemovepermission($id)
	{
		//$rol = Role::find($id)->permissions()->detach(Input::get('permission_id'));
		$res = DB::delete('delete from permission_role where permission_id = ? AND role_id = ? ', array(Input::get('permission_id'), $id));
		return $res;

	}


}