<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;
use DB ;


class UsersController extends Controller
{

	public function __construct()
    {
        $this->middleware('auth', ['except' => ['registration' ]]);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (auth()->user()->user_type != 'admin')
            return  redirect('/home');

		    $users = User::all();
            return view('users.index',compact('users'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (auth()->user()->user_type != 'admin')
            return  redirect('/home');

        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        ]);
        $data=$request->all();
        $data['password']=bcrypt($request->password);


        User::create($data);

        return redirect()->route('users.index')
                        ->with('success','Utilisateur créé avec succès.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        if (auth()->user()->user_type != 'admin')
			return  redirect('/home');

        return view('users.show',compact('user'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        if (auth()->user()->user_type != 'admin')
            return  redirect('/home');

        return view('users.edit',compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {

        $request->validate([
            'name' => 'required',
            'email' => 'required',
        ]);

        $data=$request->all();

        $user->update($data);

        return redirect()->route('users.index')
                        ->with('success','Utilisateur modifié avec succès');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('users.index')
                        ->with('success','Utilisateur supprimé avec succès');
    }



	public function profile()
	{
        $user=auth()->user();
        return view('users.profile',['user'=>$user,'id'=>$user->id]);
 	}



     public function updateuser(Request $request)
     {
         $id= $request->get('user');
         $name= $request->get('name');
         $lastname= $request->get('lastname');
         //$naissance= $request->get('naissance');
         //$adresse= $request->get('adresse');
         $password= $request->get('password');
         $confirmation= $request->get('confirmation');
         $user_type= $request->get('user_type');
          if($password !=''  && (strlen($password )>5) ){

         if($password == $confirmation )
         {  $password= bcrypt(trim($request->get('password')));


         DB::table('users')->where('id', $id)->update(array(
          'name' => $name,
         'lastname' => $lastname,
         //'naissance' => $naissance,
         //'adresse' => $adresse,
         'password' => $password,
         'user_type' => $user_type,

         ));
          }
         }else{

          DB::table('users')->where('id', $id)->update(array(
          'name' => $name,
         'lastname' => $lastname,
         //'naissance' => $naissance,
         //'adresse' => $adresse,
         'user_type' => $user_type,

         ));

         }

       return back()->with('success', ' modifié avec succès');


     }


	public function updating(Request $request)
    {
        $id= $request->get('user');
        $champ= strval($request->get('champ'));
        if($champ=='password'){
            $val= bcrypt(trim($request->get('val')));

        }else{
            $val= $request->get('val');
        }
         User::where('id', $id)->update(array($champ => $val));
    }

	public static function  ChampById($champ,$id)
    {
     $user = User::find($id);
	 return  isset($user[$champ]) ? $user[$champ] : '';
    }


	public function loginAs($id)
	{
    //if session exists remove it and return login to original user
    if (session()->get('hasClonedUser') == 1) {
         session()->remove('hasClonedUser');
        auth()->loginUsingId(session()->remove('previoususer'));
        session()->remove('previoususer');
        return redirect()->back();
    }

    //only run for developer, clone selected user and create a cloned session
    if (auth()->user()->user_type == 'admin') {
		Session::put('hasClonedUser', 1);
		Session::put('previoususer', auth()->user()->id);
         auth()->loginUsingId($id);
		return redirect('/home');
		}
	}


    public function ajoutimage(Request $request)
    {
        $id= $request->get('user');

        $name='';
        if($request->file('file')!=null)
        {$image=$request->file('file');
         $name =  $image->getClientOriginalName();
                 $path = public_path()."/img/users/";

          $image->move($path, $name);
        }
          User::where('id', $id)->update(array('thumb' => $name));
    }



    public function activer($id)
    {
        $user = User::where('id',$id)->first();

        if(auth()->user()->user_type == 'admin'|| auth()->user()->id==$user->parent  )
        {
            User::where('id', $id)->update(array('status' => 1));
            $user->status=1;
            $user->save();
            return back()->with('success', 'Utilisateur approuvé');

        }

    }

    public function desactiver($id)
    {
        $user = User::where('id',$id)->first();

        if(auth()->user()->user_type == 'admin'|| auth()->user()->id==$user->parent  )
        {
         //   User::where('id', $id)->update(array('status' => 0));
            $user->status=0;
            $user->save();
            return back()->with('success', 'Utilisateur désactivé');

        }

    }

}