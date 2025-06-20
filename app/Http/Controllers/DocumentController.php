<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\Entreprise;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Carbon\Carbon;
class DocumentController extends Controller
{
    //

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function documents($employee_id){
     $documents = DB::table('documents')->where('user_id',$employee_id)->get();
     $user = User::find($employee_id);
     $entreprise = Entreprise::find($user->entreprise_id);
     $demandes = DB::table('demandes')->where('emetteur_id',Auth::user()->id)->get();
     return view('pages.documents',
                ['documents'=>$documents,
                'user'=>$user,
                'entreprise'=>$entreprise,
                'demandes'=>$demandes
            ]);

    }

    public function settings(){
        return view('pages.settings');
    }


    public function editDoc(){
        return view('pages.editDoc');
    }

    public function uploaderDocInFolder(Request $request,$section_id,$dossier_id){
        $originalName =  $request->file('document')->getClientOriginalName(); 
        $taille  = $request->document->getSize()/1024;
        $extension = $request->document->extension();
        
        $type="";
        if($extension=="doc" || $extension=="docx"){
            $type = "fichier Word";
        }elseif($extension=="xls" || $extension=="xlsx"){
            $type = "fichier Excel";
        }elseif($extension=="exe"){
            $type = "Fichier exécutable";
        }elseif($extension=="htm" || $extension=="html"){
            $type = "Fichier HTML";
        }elseif($extension=="jpeg" || $extension=="png" || $extension=="jpg"){
            $type = "Fichier image";
        }elseif($extension=="pdf"){
            $type = "Fichier PDF";
        }elseif($extension=="ppt" || $extension=="pptx"){
           $type = "Fichier PowerPoint";
        }elseif($extension=="txt"){
            $type = "Fichier texte";
        }else{
            $type = "Fichier ".$extension;
        }

        $name = $request->file('document')->storeAs(
             'Documents',
             $originalName
        );
        
         Document::create([
           'nom' => $originalName,
           'chemin' => $name,
           'taille' => round($taille, 2), // Utiliser round() au lieu de number_format()
           'type' => $type,
           'logo_path'=>'default_logo',
           'content'=>$name,
           'user_id'=>Auth::user()->id,
           'dossier_id'=>$dossier_id,
           'visibility'=>$request->visibility,
           'section_id'=>$section_id,
           'entreprise_id'=>Auth::user()->entreprise_id
         ]);
         
         $documents = DB::table('documents')->where('entreprise_id',Auth::user()->entreprise_id)->get();
         return redirect()->back()->with('documents',$documents);
         
    }    public function uploaderDoc(Request $request,$section_id){ 
        // Vérifier les permissions
        if (!Auth::user()->hasPermission('modification') && !Auth::user()->isAdmin()) {
            return redirect()->back()->with('error', 'Vous n\'avez pas les permissions pour uploader des documents.');
        }
        
        $request->validate([
            'document' => 'required|file|max:51200', // 50MB max
            'client_name' => 'nullable|string|max:255',
            'dossier_number' => 'nullable|string|max:100',
            'status' => 'required|in:brouillon,en_cours,valide',
            'visibility' => 'required|in:public,private,admin_only'
        ]);

        $originalName =  $request->file('document')->getClientOriginalName(); 
        $taille  = $request->document->getSize()/1024;
        $extension = $request->document->extension();
        
        $type="";
        if($extension=="doc" || $extension=="docx"){
            $type = "Fichier Word";
        }elseif($extension=="xls" || $extension=="xlsx"){
            $type = "Fichier Excel";
        }elseif($extension=="exe"){
            $type = "Fichier exécutable";
        }elseif($extension=="htm" || $extension=="html"){
            $type = "Fichier HTML";
        }elseif($extension=="jpeg" || $extension=="png" || $extension=="jpg"){
            $type = "Fichier image";
        }elseif($extension=="pdf"){
            $type = "Fichier PDF";
        }elseif($extension=="ppt" || $extension=="pptx"){
           $type = "Fichier PowerPoint";
        }elseif($extension=="txt"){
            $type = "Fichier texte";
        }else{
            $type = "Fichier ".$extension;
        }

        $name = $request->file('document')->storeAs(
             'Documents',
             $originalName
        );
        
        $document = Document::create([
           'nom' => $originalName,
           'chemin' => $name,
           'taille' => round($taille, 2),
           'type' => $type,
           'logo_path'=>'default_logo',
           'content'=>$name,
           'user_id'=>Auth::user()->id,
           'dossier_id'=>0,
           'visibility'=>$request->visibility,
           'client_name'=>$request->client_name,
           'dossier_number'=>$request->dossier_number,
           'status'=>$request->status ?? 'brouillon',
           'section_id'=>$section_id,
           'entreprise_id'=>Auth::user()->entreprise_id
         ]);
         
         // Tracer l'upload (selon cahier des charges)
         $document->logConsultation();
         
         $documents = DB::table('documents')->where('entreprise_id',Auth::user()->entreprise_id)->get();
         return redirect()->back()->with('success', 'Document uploadé avec succès!')->with('documents',$documents);
         
    }
    public function download($id_doc){
        // Vérifier les permissions de téléchargement
        if (!Auth::user()->hasPermission('telecharger') && !Auth::user()->isAdmin()) {
            return redirect()->back()->with('error', 'Vous n\'avez pas les permissions pour télécharger des documents.');
        }
        
        $document = Document::findOrFail($id_doc);
        
        // Vérifier la visibilité du document
        if ($document->visibility === 'private' && $document->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            return redirect()->back()->with('error', 'Vous n\'avez pas accès à ce document privé.');
        }
        
        if ($document->visibility === 'admin_only' && !Auth::user()->isAdmin()) {
            return redirect()->back()->with('error', 'Ce document est réservé aux administrateurs.');
        }
        
        // Tracer le téléchargement (selon cahier des charges)
        $document->logDownload();
        
        return Storage::download($document->chemin, $document->nom);
    }    public function delete($id_doc){
        // Vérifier les permissions de suppression
        if (!Auth::user()->hasPermission('suppression') && !Auth::user()->isAdmin()) {
            return redirect()->back()->with('error', 'Vous n\'avez pas les permissions pour supprimer des documents.');
        }
        
        $document = Document::findOrFail($id_doc);
        
        // Seul le propriétaire ou l'admin peut supprimer
        if ($document->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            return redirect()->back()->with('error', 'Vous ne pouvez supprimer que vos propres documents.');
        }
        
        // Archiver avant suppression (selon cahier des charges - possibilité de restauration)
        if (!$document->is_archived) {
            $document->archive();
        }
        
        // Supprimer le fichier physique
        Storage::delete($document->chemin);
        
        // La suppression est tracée automatiquement par le trait Traceable
        $document->delete();
        
        return redirect()->back()->with('success', 'Document supprimé avec succès. Une copie archivée est conservée.');
    }


    public function deleteEmp($id){
        $user = User::all()->find($id);
        $user->delete();
        return redirect()->back();
    }

    public function detailDocument(Request $request){
        $document = null;
        $user = null;
        if($request->ajax()){
            $document = Document::find($request->id);
            $user = User::find($document->user_id);
        }
        $response = " <strong>Nom du document : </strong>";
        $response .= $document->nom."<br>";
        $response .= " <strong>Type du document : </strong>";
        $response .= $document->type."<br>";
        $response .= " <strong>Taille du document : </strong>";
        $response .= $document->taille." ko<br>";
        $response .= " <strong>visibilité : </strong>";
        $response .= $document->visibility=='private'? 'privé' : 'public';
        $response .= "<br><strong>partager Le : </strong>";
        $response .=  Carbon::createFromFormat('Y-m-d H:i:s', $user->created_at)->format('d/m/Y à H:i')."<br>";
        $response .= " <strong>Modifié Le : </strong>";
        $response .=  Carbon::createFromFormat('Y-m-d H:i:s', $user->updated_at)->format('d/m/Y à H:i')."<br>";
        $response .= "<strong> partager par : </strong>";
        $response .= $user->nom."<br>";
       
        return $response;
    }

    // Méthode pour archivage automatique (selon cahier des charges)
    public function archiveDocument($id_doc)
    {
        if (!Auth::user()->hasPermission('modification') && !Auth::user()->isAdmin()) {
            return redirect()->back()->with('error', 'Vous n\'avez pas les permissions pour archiver des documents.');
        }
        
        $document = Document::findOrFail($id_doc);
        
        if ($document->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            return redirect()->back()->with('error', 'Vous ne pouvez archiver que vos propres documents.');
        }
        
        $document->archive();
        
        return redirect()->back()->with('success', 'Document archivé avec succès!');
    }
    
    // Méthode pour marquer qu'un document nécessite une mise à jour
    public function markForUpdate($id_doc)
    {
        if (!Auth::user()->isAdmin()) {
            return redirect()->back()->with('error', 'Seuls les administrateurs peuvent marquer les documents pour mise à jour.');
        }
        
        $document = Document::findOrFail($id_doc);
        $document->markForUpdate();
        
        return redirect()->back()->with('success', 'Document marqué pour mise à jour.');
    }
    
    /**
     * Consulter un document et tracer l'action
     */
    public function viewDocument($id)
    {
        $document = Document::findOrFail($id);
        
        // Vérifier les permissions
        if (!Auth::user()->hasPermission('lecture') && !Auth::user()->isAdmin()) {
            abort(403, 'Vous n\'avez pas les permissions pour consulter ce document');
        }
        
        // Vérifier la visibilité
        if ($document->visibility === 'private' && $document->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'Ce document est privé');
        }
        
        if ($document->visibility === 'admin_only' && !Auth::user()->isAdmin()) {
            abort(403, 'Ce document est réservé aux administrateurs');
        }
        
        // Tracer la consultation
        $document->logActivity('consultation', 'Document consulté');
        
        // Retourner la vue du document ou rediriger vers le téléchargement
        return response()->file(storage_path('app/public/' . $document->chemin));
    }
    
    /**
     * Enregistrer la consultation d'un document
     */
    public function logView($id)
    {
        $document = Document::findOrFail($id);
        
        // Vérifier les permissions
        if (!Auth::user()->hasPermission('lecture') && !Auth::user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Permissions insuffisantes']);
        }
        
        // Tracer la consultation
        $document->logActivity('consultation', 'Document consulté');
        
        return response()->json(['success' => true]);
    }
    
    /**
     * Demander l'accès à un document privé
     */
    public function requestAccess($id)
    {
        $document = Document::findOrFail($id);
        
        // Vérifier si une demande n'existe pas déjà
        $existingRequest = DB::table('demandes')
            ->where('document_id', $id)
            ->where('emetteur_id', Auth::id())
            ->first();
            
        if ($existingRequest) {
            return response()->json(['success' => false, 'message' => 'Une demande existe déjà']);
        }
        
        // Créer la demande
        DB::table('demandes')->insert([
            'document_id' => $id,
            'emetteur_id' => Auth::id(),
            'receveur_id' => $document->user_id,
            'etat' => 0, // En attente
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        // Tracer l'action
        $document->logActivity('demande_acces', 'Demande d\'accès formulée');
        
        return response()->json(['success' => true, 'message' => 'Demande envoyée']);
    }
    
    /**
     * Annuler une demande d'accès
     */
    public function cancelRequest($id)
    {
        $document = Document::findOrFail($id);
        
        // Supprimer la demande
        $deleted = DB::table('demandes')
            ->where('document_id', $id)
            ->where('emetteur_id', Auth::id())
            ->delete();
            
        if ($deleted) {
            // Tracer l'action
            $document->logActivity('annulation_demande', 'Demande d\'accès annulée');
            
            return response()->json(['success' => true, 'message' => 'Demande annulée']);
        }
        
        return response()->json(['success' => false, 'message' => 'Demande non trouvée']);
    }
    
    /**
     * Restaurer un document archivé
     */
    public function restore($id)
    {
        $document = Document::findOrFail($id);
        
        // Vérifier les permissions
        if (!Auth::user()->hasPermission('modification') && !Auth::user()->isAdmin()) {
            return redirect()->back()->with('error', 'Vous n\'avez pas les permissions pour restaurer ce document');
        }
        
        // Vérifier si l'utilisateur peut restaurer ce document
        if ($document->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            return redirect()->back()->with('error', 'Vous ne pouvez pas restaurer ce document');
        }
        
        // Restaurer le document
        $document->update([
            'is_archived' => false,
            'archived_at' => null,
            'archived_by' => null
        ]);
        
        // Tracer l'action
        $document->logActivity('restauration', 'Document restauré');
        
        return redirect()->back()->with('success', 'Document restauré avec succès');
    }
}
