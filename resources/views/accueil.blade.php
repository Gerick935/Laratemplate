<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('main.css') }}">
    <link rel="stylesheet" href="{{ asset('utilities.css') }}">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .dropzone {
            border: 2px dashed #ccc;
            border-radius: 10px;
            padding: 20px;
            background-color: #f9f9f9;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .dropzone:hover {
            background-color: #f0f0f0;
        }

        .dropzone .dz-message {
            margin: 0;
            padding: 20px 0;
        }

        .dropzone .icon {
            font-size: 48px;
            color: #333;
        }

        .dropzone.dragover {
            background-color: #e9ecef;
        }
    </style>
</head>

<body>
    <div class="container py-5">
        <h1 class="text-center mb-4">Upload Your Files</h1>
        <form action={{ "submit.post"}} method="post" enctype="multipart/form-data" id="dropzoneForm" onsubmit="disableButton()">
            @csrf
            <div class="row">
                <!-- HTML Dropzone -->
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-header text-center bg-primary text-white">HTML Files</div>
                        <div class="card-body">
                            <div id="htmlDropzone" class="dropzone">
                                <input type="file" name="html_files[]" id="htmlFileInput" class="form-control d-none" accept=".html" multiple>
                                <div class="dz-message text-center">
                                    <img src="{{ asset('logos/upload.webp') }}" style="width: 100px;" alt="Drag Drop Icon">
                                    <h6 class="mt-2">Drag & Drop HTML Files Here or Click to Select</h6>
                                    <small class="text-muted">Formats accepted: .html</small>
                                </div>
                            </div>
                            <ul class="list-group mt-3 file-list" id="htmlFileList"></ul>
                        </div>
                    </div>
                </div>

                <!-- CSS Dropzone -->
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-header text-center bg-success text-white">CSS Files</div>
                        <div class="card-body">
                            <div id="cssDropzone" class="dropzone">
                                <input type="file" name="css_files[]" id="cssFileInput" class="form-control d-none" accept=".css" multiple>
                                <div class="dz-message text-center">
                                    <img src="{{ asset('logos/upload.webp') }}" style="width: 100px;" alt="Drag Drop Icon">
                                    <h6 class="mt-2">Drag & Drop CSS Files Here or Click to Select</h6>
                                    <small class="text-muted">Formats accepted: .css</small>
                                </div>
                            </div>
                            <ul class="list-group mt-3 file-list" id="cssFileList"></ul>
                        </div>
                    </div>
                </div>

                <!-- Image Dropzone -->
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-header text-center bg-info text-white">Image Files</div>
                        <div class="card-body">
                            <div id="imageDropzone" class="dropzone">
                                <input type="file" name="image_files[]" id="imageFileInput" class="form-control d-none" accept="image/*" multiple>
                                <div class="dz-message text-center">
                                    <img src="{{ asset('logos/drag-and-drop.png') }}" style="width: 100px;" alt="Drag Drop Icon">
                                    <h6 class="mt-2">Drag & Drop Image Files Here or Click to Select</h6>
                                    <small class="text-muted">Formats accepted: Images</small>
                                </div>
                            </div>
                            <ul class="list-group mt-3 file-list" id="imageFileList"></ul>
                        </div>
                    </div>
                </div>

                <!-- JavaScript Dropzone -->
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-header text-center bg-warning text-white">JavaScript Files</div>
                        <div class="card-body">
                            <div id="jsDropzone" class="dropzone">
                                <input type="file" name="js_files[]" id="jsFileInput" class="form-control d-none" accept=".js" multiple>
                                <div class="dz-message text-center">
                                    <img src="{{ asset('logos/upload.webp') }}" style="width: 100px;" alt="Drag Drop Icon">
                                    <h6 class="mt-2">Drag & Drop JS Files Here or Click to Select</h6>
                                    <small class="text-muted">Formats accepted: .js</small>
                                </div>
                            </div>
                            <ul class="list-group mt-3 file-list" id="jsFileList"></ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer mt-4">
                <button type="button" class="btn btn-sm btn-neutral" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-sm btn-success">Submit</button>
            </div>
        </form>
    </div>

    <script>
       document.querySelectorAll('.dropzone').forEach(dropzone => {
    const input = dropzone.querySelector('input[type=file]');
    const fileList = dropzone.parentElement.querySelector('.file-list');

    // Gérer le clic sur la dropzone
    dropzone.addEventListener('click', () => input.click());

    // Gérer le drag-and-drop
    dropzone.addEventListener('dragover', e => {
        e.preventDefault();
        dropzone.classList.add('dragover');
    });

    dropzone.addEventListener('dragleave', () => dropzone.classList.remove('dragover'));

    dropzone.addEventListener('drop', e => {
        e.preventDefault();
        dropzone.classList.remove('dragover');

        const files = Array.from(e.dataTransfer.files);
        const dataTransfer = new DataTransfer();

        // Ajoutez les fichiers dans la liste et dans l'input
        files.forEach(file => {
            if (input.accept.includes(file.type) || file.name.endsWith(input.accept.split('.').pop())) {
                dataTransfer.items.add(file);
                addFileToList(file, fileList); // Ajouter le fichier à la liste
            }
        });

        input.files = dataTransfer.files;
    });

    // Gérer la sélection via l'input
    input.addEventListener('change', () => {
        const files = Array.from(input.files);
        fileList.innerHTML = ''; // Réinitialisez la liste pour éviter les doublons
        files.forEach(file => addFileToList(file, fileList));
    });
});

// Fonction pour ajouter un fichier à la liste
function addFileToList(file, fileList) {
    const li = document.createElement('li');
    li.classList.add('list-group-item', 'd-flex', 'justify-content-between', 'align-items-center');
    li.textContent = file.name;

    // Bouton pour supprimer le fichier
    const removeButton = document.createElement('button');
    removeButton.classList.add('btn', 'btn-sm', 'btn-danger');
    removeButton.textContent = 'Remove';
    removeButton.addEventListener('click', () => {
        li.remove();
        removeFileFromInput(file, fileList);
    });

    li.appendChild(removeButton);
    fileList.appendChild(li);
}

// Fonction pour retirer un fichier de l'input
function removeFileFromInput(file, fileList) {
    const input = fileList.parentElement.querySelector('input[type=file]');
    const dataTransfer = new DataTransfer();

    Array.from(input.files).forEach(currentFile => {
        if (currentFile.name !== file.name) {
            dataTransfer.items.add(currentFile);
        }
    });

    input.files = dataTransfer.files;
}

    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>




