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

        // Upload des fichiers HTML
        if ($request->hasFile('html_files')) {
            foreach ($request->file('html_files') as $file) {
                $uploadedFiles['html'][] = $file->store('uploads/html', 'public');
            }
        }

        // Upload des fichiers CSS
        if ($request->hasFile('css_files')) {
            foreach ($request->file('css_files') as $file) {
                $uploadedFiles['css'][] = $file->store('uploads/css', 'public');
            }
        }

        // Upload des fichiers Images
        if ($request->hasFile('image_files')) {
            foreach ($request->file('image_files') as $file) {
                $uploadedFiles['images'][] = $file->store('uploads/images', 'public');
            }
        }

        // Upload des fichiers JS
        if ($request->hasFile('js_files')) {
            foreach ($request->file('js_files') as $file) {
                $uploadedFiles['js'][] = $file->store('uploads/js', 'public');
            }
        }

        // Retourner les fichiers uploadés ou un message de succès
        return back()->with('success', 'Files uploaded successfully!')->with('uploadedFiles', $uploadedFiles);
    }
}
