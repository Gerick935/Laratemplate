<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MainController extends Controller
{
    public function uploadFiles(Request $request)
    {

        // Validation des fichiers uploadés
        $validated = $request->validate([
            'html_files.*' => 'mimes:html|max:10240',
            'css_files.*' => 'mimes:css|max:10240',
            'image_files.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:20480',
            'js_files.*' => 'mimes:js|max:10240',
        ]);

        // Tableau pour stocker les fichiers uploadés
        $uploadedFiles = [];
        $fileNames = [];

        // Upload des fichiers HTML
        if ($request->hasFile('html_files')) {
            foreach ($request->file('html_files') as $file) {
                $fileName = $file->getClientOriginalName();
                array_push($fileNames, $fileName);
                $filePath = $file->storeAs('public/uploads/html', $fileName);
            }
        }
        dd($fileNames);

        // Upload des fichiers CSS
        if ($request->hasFile('css_files')) {
            foreach ($request->file('css_files') as $file) {
                $fileName = $file->getClientOriginalName();
                $filePath = $file->storeAs('public/uploads/css', $fileName);
            }
        }

        // Upload des fichiers Images
        if ($request->hasFile('image_files')) {
            foreach ($request->file('image_files') as $file) {
                $fileName = $file->getClientOriginalName();
                $filePath = $file->storeAs('public/uploads/images', $fileName);
            }
        }

        // Upload des fichiers JS
        if ($request->hasFile('js_files')) {
            foreach ($request->file('js_files') as $file) {
                $fileName = $file->getClientOriginalName();
                $filePath = $file->storeAs('public/uploads/js', $fileName);
            }
        }

        // Chemin vers le dossier dans storage
        $directory = storage_path('app/public/uploads'); // Modifier le chemin selon votre structure

        // Vérifier si le dossier existe
        if (!is_dir($directory)) {
            return response()->json(['message' => 'Le dossier spécifié n\'existe pas'], 404);
        }

        // Récupérer tous les fichiers HTML dans le dossier
        $files = glob($directory . '/*.html');
        dd($files);

        foreach ($files as $filePath) {
            // Récupérer le contenu du fichier
            $content = file_get_contents($filePath);

            // Exemple de modification du contenu
            $modifiedContent = str_replace('old_string', 'new_string', $content);

            // Changer le nom du fichier et son extension
            $fileName = pathinfo($filePath, PATHINFO_FILENAME); // Nom sans extension
            $newFileName = $fileName . '_modified.txt'; // Nouveau nom et extension
            $newFilePath = $directory . '/' . $newFileName;

            // Sauvegarder le fichier avec le nouveau nom et contenu
            file_put_contents($newFilePath, $modifiedContent);

            // Supprimer l'ancien fichier (facultatif)
            unlink($filePath);
        }

        return response()->json(['message' => 'Tous les fichiers ont été traités avec succès']);

        // Retourner les fichiers uploadés ou un message de succès
        return back()->with('success', 'Files uploaded successfully!')->with('uploadedFiles', $uploadedFiles);
    }
}
