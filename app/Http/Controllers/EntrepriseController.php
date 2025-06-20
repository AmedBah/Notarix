<?php

namespace App\Http\Controllers;

use App\Models\Entreprise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
class EntrepriseController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request){

          // dd($request);
         $entreprise = new Entreprise();
         $user = new User();
         $entreprise->nom = $request->nom;
         $entreprise->telephone = $request->telephone;
         $entreprise->email = $request->email;
         $entreprise->pays = $request->pays;
         $entreprise->industrie = $request->industrie;
         $entreprise->password = Crypt::encryptString($request->password);
         $entreprise->save();
         $entreprise_id = DB::table('entreprises')->where('email', $entreprise->email)->value('id');;
         $user->nom = 'admin';
         $user->telephone = $entreprise->telephone;
         $user->email = $entreprise->email;
         $user->est_admin = 1;
         $user->password = $entreprise->password;
         $user->entreprise_id = $entreprise_id; 
         $user->save();
         return redirect()->route('login');
    }


    public function compte(){

        $user = Auth::user();
        $entreprises = Entreprise::all();
        $entreprise = $entreprises->find($user->entreprise_id);
        $sections = DB::table('sections')->where('user_id',$user->entreprise_id)->get();
        return view('pages.compte',[
            'user'=>$user,
            'entreprise'=>$entreprise,
            'sections'=>$sections
        ]);
        
    }


    public function listeUsers(){
        $user = Auth::user();
        $entreprises = Entreprise::all();
        $users = DB::table('users')->where('entreprise_id',$user->entreprise_id)->paginate(6);
        $entreprise = $entreprises->find($user->entreprise_id);
        $sections = DB::table('sections')->where('user_id',$user->entreprise_id)->get();
        return view('pages.listeUsers',[
            'user'=>$user,
            'entreprise'=>$entreprise,
            'users'=>$users,
            'sections'=>$sections
        ]); 

    }    public function supprimerUser($user_id){
        $user = Auth::user();
        $entreprises = Entreprise::all();
        $users = DB::table('users')->where('entreprise_id',$user->entreprise_id)->paginate(6);
        $entreprise = $entreprises->find($user->entreprise_id);
        $sections = DB::table('sections')->where('user_id',$user->entreprise_id)->get();
        $userToDelete = User::find($user_id);
        return view('pages.listeUsers',[
            'user'=>$user,
            'entreprise'=>$entreprise,
            'users'=>$users,
            'sections'=>$sections,
            'userToDelete' =>$userToDelete
        ]); 
    }    public function oui($user_id){
        // Vérifier que l'utilisateur connecté est admin
        if (!Auth::user()->est_admin && !Auth::user()->isAdmin()) {
            return redirect()->back()->with('error', 'Vous n\'avez pas les permissions pour supprimer un utilisateur');
        }
        
        $userToDelete = User::find($user_id);
        
        if (!$userToDelete) {
            return redirect()->back()->with('error', 'Utilisateur non trouvé');
        }
        
        // Empêcher la suppression de son propre compte
        if ($userToDelete->id === Auth::id()) {
            return redirect()->back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte');
        }
        
        // Vérifier que l'utilisateur à supprimer appartient à la même entreprise
        if ($userToDelete->entreprise_id !== Auth::user()->entreprise_id) {
            return redirect()->back()->with('error', 'Vous ne pouvez pas supprimer un utilisateur d\'une autre entreprise');
        }
        
        try {
            $userToDelete->delete();
            return redirect()->route('listeUsers')->with('success', 'Utilisateur supprimé avec succès');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de la suppression de l\'utilisateur');
        }
    }

    public function updateEntreprise(Request $request){
        $user = Auth::user();
        
        if (Hash::check($request->AncienPassword, $user->password)){
           if(empty($request->newPassword)==false){
               DB::table('entreprises')->where('id', $user->entreprise_id)->update(['nom'=>$request->nom]);
               DB::table('entreprises')->where('id', $user->entreprise_id)->update(['telephone'=>$request->telephone]);
               DB::table('entreprises')->where('id',$user->entreprise_id)->update(['email'=>$request->email]);
               DB::table('entreprises')->where('id',$user->entreprise_id)->update(['pays'=>$request->pays]);
               DB::table('entreprises')->where('id',$user->entreprise_id)->update(['industrie'=>$request->industrie]);
               DB::table('entreprises')->where('id', $user->id)->update(['password'=>Hash::make($request->newPassword)]);
               DB::table('users')->where('id', $user->id)->update(['password'=>Hash::make($request->newPassword)]);
              }
      
              $entreprises = Entreprise::all();
              $entreprise = $entreprises->find($user->entreprise_id);
              $sections = DB::table('sections')->where('user_id',$user->entreprise_id)->get();
              $user = User::find($user->id);
              return view('pages.compte',['user'=>$user,
                                        'entreprise'=>$entreprise,
                                        'sections'=>$sections,
                                        'change'=>1]);
        }else{   
              $entreprises = Entreprise::all();
              $entreprise = $entreprises->find($user->entreprise_id);
              $sections = DB::table('sections')->where('user_id',$user->entreprise_id)->get();
              $user = User::find($user->id);
              return view('pages.compte',['user'=>$user,
                                     'entreprise'=>$entreprise,
                                     'sections'=>$sections,
                                     'change'=>0]);
        }

       
    }
}
