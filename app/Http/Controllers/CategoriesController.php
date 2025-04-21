<?php
  
namespace App\Http\Controllers;
   
use App\Models\Categorie;
use Illuminate\Http\Request;
  
class CategoriesController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
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
        
        $categories = Categorie::all();
    
        return view('categories.index',compact('categories'));
        //    ->with('i', (request()->input('page', 1) - 1) * 5);
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

        return view('categories.create');
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
        //    'description' => 'required',
        ]);
    
        Categorie::create($request->all());
     
        return redirect()->route('categories.index')
                        ->with('success','Categorie créé avec succès.');
    }
     
    /**
     * Display the specified resource.
     *
     * @param  \App\Categorie  $categorie
     * @return \Illuminate\Http\Response
     */
    public function show(Categorie $categorie)
    {
        if (auth()->user()->user_type != 'admin') 
            return  redirect('/home');

        return view('categories.show',compact('categorie'));
    } 
     
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Categorie  $categorie
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {   
        if (auth()->user()->user_type != 'admin') 
            return  redirect('/home');

        $categorie= Categorie::find($id);
        return view('categories.edit',compact('categorie'));
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Categorie  $categorie
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $categorie= Categorie::find($id);
        
        $request->validate([
            'name' => 'required',
        //    'description' => 'required',
        ]);
    
        $categorie->update($request->all());
    
        return redirect()->route('categories.index')
                        ->with('success','Categorie modifiée avec succès');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Categorie  $categorie
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $categorie= Categorie::find($id);

        $categorie->delete();
    
        return redirect()->route('categories.index')
                        ->with('success','Categorie supprimée avec succès');
    }
	

	
    public static function ChampById($champ,$id)
    {
     $categorie = Categorie::find($id);
	 return  isset($categorie[$champ]) ? $categorie[$champ] : '';
    }
	
}