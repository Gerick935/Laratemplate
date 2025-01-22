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

        foreach ($fileNames as $fileName) {
            $file = storage_path('app/private/public/uploads/html/'. $fileName);
            $newFileName = storage_path('app/private/public/uploads/html/'. str_replace('.html', '.blade.php', $fileName));

            if (file_exists($file)) {

                $content = file_get_contents($file);
                
                $content = preg_replace(
                    '/<link\s+rel="stylesheet"\s+href="(.*?)"/',
                    '<link rel="stylesheet" href="{{ asset(\'$1\') }}"',
                    $content
                );
            
                // Remplacer les liens JS par des directives asset
                $content = preg_replace(
                    '/<script\s+src="(.*?)"/',
                    '<script src="{{ asset(\'$1\') }}"',
                    $content
                );
            
                // Remplacer les images par des directives asset
                $content = preg_replace_callback(
                    '/<img\s+[^>]*src="([^"]+)"/',
                    function ($matches) {
                        // Extraire uniquement le nom du fichier et son extension
                        $pathParts = pathinfo($matches[1]); // pathinfo renvoie un tableau avec dirname, basename, etc.
                        $fileName = $pathParts['basename']; // Nom du fichier avec l'extension
                        return '<img src="{{ asset(\'' . $fileName . '\') }}"';
                    },
                    $content
                );
            
                // Remplacer les URL par des directives route
                $content = preg_replace_callback(
                    '/<a\s+href="(.*?)"/',
                    function ($matches) {
                        $url = $matches[1];
                        if (preg_match('/\.html$/', $url)) {
                            // Remplacer les fichiers HTML par une route
                            $routeName = basename($url, '.html');
                            return '<a href="{{ route(\'' . $routeName . '\') }}"';
                        }
                        return $matches[0]; // Conserver les autres URLs telles quelles
                    },
                    $content
                );
            
                // Remplacer les attributs action dans les formulaires par des routes
                $content = preg_replace_callback(
                    '/<form\s+action="(.*?)"/',
                    function ($matches) {
                        $url = $matches[1];
                        if (preg_match('/\.html$/', $url)) {
                            $routeName = basename($url, '.html');
                            return '<form action="{{ route(\'' . $routeName . '\') }}"';
                        }
                        return $matches[0];
                    },
                    $content
                );
            
                // Ajouter @csrf dans tous les formulaires
                $content = preg_replace(
                    '/<form(.*?)>/',
                    '<form$1>@csrf',
                    $content
                );

                file_put_contents($newFileName, $content);

                unlink($file);

                // dd(file_get_contents($newFileName));

                
            } else {
                dd("Le fichier n'existe pas");
            }
        }

        $routes = "<?php

        use Illuminate\Support\Facades\Route;";
        foreach ($fileNames as $fileName){
            
            $routes.= "\n\n        Route::get('/". str_replace('.html', '', $fileName). "', function () {return view('". str_replace('.html', '', $fileName). "');});";
        }
        $fileRoute = storage_path('app/private/public/uploads/route/web.php');
        $directory = dirname($fileRoute);
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        file_put_contents($fileRoute, $routes);
        dd($routes);

        // Retourner les fichiers uploadés ou un message de succès
        return back()->with('success', 'Files uploaded successfully!')->with('uploadedFiles', $uploadedFiles);
    }
}
