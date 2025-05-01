@extends('layouts.admin')
  
@section('title', 'Création rapide de commande')

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
<style>
    .dropzone {
        border: 2px dashed #0087F7;
        border-radius: 5px;
        background: #F5F5F5;
    }
    .dropzone .dz-message {
        font-weight: 400;
    }
    .dropzone .dz-preview .dz-image {
        border-radius: 5px;
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h4>Création rapide de commande</h4>
                </div>
                
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                    
                    <form action="{{ route('orders.store') }}" method="POST" enctype="multipart/form-data" id="orderForm">
                        @csrf
                        
                        <div class="form-group mb-4">
                            <label for="notes">Notes</label>
                            <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="4">{{ old('notes') }}</textarea>
                            @error('notes')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group mb-4">
                            <label>Images de la commande (captures d'écran...)</label>
                            <div class="dropzone" id="dropzoneForm"></div>
                            <small class="form-text text-muted">Faites glisser vos images ou cliquez pour sélectionner</small>
                            @error('images')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group mb-4">
                            <label>Après la création :</label>
                            <div class="form-check">
                                <input type="radio" class="form-check-input" id="redirect_create" name="redirect_preference" value="create_another" {{ $redirectPreference == 'create_another' ? 'checked' : '' }}>
                                <label class="form-check-label" for="redirect_create">Créer une autre commande</label>
                            </div>
                            <div class="form-check">
                                <input type="radio" class="form-check-input" id="redirect_edit" name="redirect_preference" value="finalize" {{ $redirectPreference == 'finalize' ? 'checked' : '' }}>
                                <label class="form-check-label" for="redirect_edit">Finaliser cette commande</label>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Enregistrer la commande</button>
                            <a href="{{ route('orders.index') }}" class="btn btn-secondary">Annuler</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


@section('footer-scripts')
<script src="{{ asset('plugins/summernote/summernote-bs4.min.js') }}" ></script>

<script>
  
  $(function () {
    // Summernote
    $('#notes').summernote()
  });
  
</script>
<script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
<script>
    Dropzone.autoDiscover = false;
    
    $(document).ready(function() {
        var myDropzone = new Dropzone("#dropzoneForm", {
            url: "{{ route('orders.store') }}",
            autoProcessQueue: false,
            uploadMultiple: true,
            parallelUploads: 5,
            maxFiles: 10,
            maxFilesize: 5, // MB
            acceptedFiles: "image/*",
            addRemoveLinks: true,
            dictRemoveFile: "Supprimer",
            dictFileTooBig: "Le fichier est trop volumineux (5 Mo). Taille max: 5Mo",
            dictInvalidFileType: "Type de fichier non autorisé",
            dictCancelUpload: "Annuler",
            dictUploadCanceled: "Upload annulé",
            dictMaxFilesExceeded: "Vous ne pouvez pas charger plus de fichiers",
        });
        
        // Enregistrement du formulaire
        $("#orderForm").submit(function(e) {
            e.preventDefault();
            
            // Si pas d'images, soumettre directement le formulaire
            if (myDropzone.getQueuedFiles().length === 0) {
                this.submit();
                return;
            }
            
            // Sinon, processer la queue Dropzone
            myDropzone.processQueue();
        });
        
        // Configuration Dropzone pour soumettre le formulaire
        myDropzone.on("sendingmultiple", function(files, xhr, formData) {
            // Ajouter les autres champs du formulaire
            var form = $('#orderForm');
            
            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

            // Append all form inputs to the formData
            var formInputs = form.serializeArray();
            $.each(formInputs, function(i, field) {
                formData.append(field.name, field.value);
            });
        });
        
        myDropzone.on("successmultiple", function(files, response) {
            // Redirection selon la réponse du serveur
            window.location.href = response.redirect || "{{ route('orders.index') }}";
        });
        
        myDropzone.on("errormultiple", function(files, response) {
            console.log(response);
            alert("Erreur lors de l'envoi des fichiers. Veuillez réessayer.");
        });
    });
</script>
@endsection