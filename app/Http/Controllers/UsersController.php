<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;
use App\Http\Requests;

class UsersController extends AppController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      return view('backend/users/index',  [
              'users' => User::all(),
                                            ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function create(Request $request)
    {
      $email = $request->input('email');
      $alreadyExist = User::where('email',$email)->first();
      if ($alreadyExist){
        return redirect()->back()->withErrors("El usuario $email ya existe en la base de datos");
      }
      $user = new User();

      $user->name = $request->input('name');
      $user->email = $email;
      $user->phone = $request->input('phone');
      $user->role = $request->input('role');
      $user->remember_token = str_random(60);
      $user->password = bcrypt($request->input('password'));
      $user->iban = $request->input('iban');

      if ($user->save()) {
          return redirect()->action('UsersController@index');
      }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function update($id)
    {
        $user = User::find($id);

        return view('backend/users/_form',  [
                                                'user' => $user
                                            ]);
    }

    public function saveUpdate(Request $request)
    {
        $id                   = $request->input('id');
        $userUpadate          = User::find($id);
        
        
        $userUpadate->name = $request->input('name');
        $userUpadate->email = $request->input('email');
        $userUpadate->phone = $request->input('phone');
        $userUpadate->role = $request->input('role');
        $userUpadate->remember_token = str_random(60);
        $userUpadate->iban = $request->input('iban');
        $userUpadate->nif = $request->input('nif');
        
        $psw = $request->input('password',null);
        $re_psw = $request->input('repassword',null);
        if ($psw && trim($psw) != '' && $re_psw == $psw)
          $userUpadate->password = bcrypt($psw);

        $userUpadate->name_business = $request->input('name_business');
        $userUpadate->nif_business = $request->input('nif_business');
        $userUpadate->address_business = $request->input('address_business');
        $userUpadate->zip_code_business = $request->input('zip_code_business');


        if ($userUpadate->save()) {
            return redirect()->action('UsersController@index');
        }
    }

    public function saveAjax(Request $request)
    {
        $id                   = $request->input('id');
        $userUpadate          = User::find($id);
        $userUpadate->name    = $request->input('name');
        $userUpadate->email    = $request->input('email');

        if ($userUpadate->save()) {
            echo "Cambiada!!";
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        $user = User::find($id);
        if ( $user->delete() ) {
            return redirect()->action('UsersController@index');
        }
    }


    public function createPasswordUser(Request $request, $email)
    {

        if (request()->getMethod() == 'POST') {

            $data = $request->input();
            $user = User::where('email', $data['email'])->first();
            
            if ($data['password'] == $data['rep-password']) {
                $user->password = bcrypt($data['password']);

                if ($user->save()) {
                    return redirect('/login');
                }


            }else{

                $message[] = 'Error';
                $message[] = 'Las contraseñas no coinciden';
                return view('loginToOwneds', ['user' => $user->email , 'message' => $message]);

            }
            

        }else{


            $email = base64_decode($email);
            $user = User::where('email', $email)->first();

            if ($user > 0) {
                
                if ( preg_match('/propietario/i', $user->role) ) {



                    return view('loginToOwneds', ['user' => $user->email ]);


                }else{
                    $message[] = 'Error';
                    $message[] = 'este usuario no es un propietario';
                    return view('loginToOwneds', ['user' => $user->email , 'message' => $message]);

                }


            }else{
                $message[] = 'Error';
                $message[] = 'no hay ningun usuario con este email';
                return view('loginToOwneds', ['user' => $email , 'message' => $message]);

            }

        }

        
    }

  public function searchUserByName(Request $request) {
    if (empty($request->input('search'))) {
      $users = User::whereIn('role', User::getRolesLst())->get();
    } else {
      $users = User::where('name', 'LIKE', "%" . $request->input('search') . "%")
              ->orWhere('email', 'LIKE', "%" . $request->input('search') . "%")->get();
    }
    return view('backend/users/_tableUser', [
        'users' => $users
    ]);
  }

}
