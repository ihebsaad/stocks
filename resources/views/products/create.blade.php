@extends('layouts.admin') 

<link rel="stylesheet" href="{{asset('plugins/select2/css/select2.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
<style>
.hidden{
    display:none;
}
</style>
@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="float-left">
            <h2>Ajouter un produit</h2>
        </div>
        <div class="float-right">
            <a class="btn btn-primary" href="{{ route('products.index') }}"> Retour</a>
        </div>
    </div>
</div>

<form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="row">
        <div class="col-lg-8">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-6">
                    <div class="form-group">
                        <strong>Image(s):</strong>
                        <input type="file" name="file[]" class="form-control" multiple>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-6">
                    <div class="form-group">
                        <strong>Nom:</strong>
                        <input type="text" name="name" required class="form-control" placeholder="Nom" value="{{old('name')}}">
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-6">
                    <div class="form-group">
                        <strong>Type de produit:</strong>
                        <select name="type" id="product_type" class="form-control" onchange="toggleVariableProduct()">
                            <option value="0">Produit simple</option>
                            <option value="1">Produit variable</option>
                        </select>
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-6">
                    <div class="form-group">
                        <strong>Référence:</strong>
                        <input type="text" name="reference" id="reference" class="form-control" placeholder="Référence" value="{{old('reference')}}" required>
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-6">
                    <div class="form-group">
                        <strong>Catégorie:</strong>
                        <select name="categorie_id" class="form-control">
                            <option></option>
                            @foreach($categories as $categorie)
                            <option value="{{$categorie->id}}">{{$categorie->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-6">
                    <div class="form-group">
                        <strong>Fournisseur:</strong>
                        <select name="provider_id" class="form-control">
                            <option></option>
                            @foreach($providers as $provider)
                            <option value="{{$provider->id}}">{{$provider->company}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-6">
                    <div class="form-group">
                        <strong>Description:</strong>
                        <textarea class="form-control summernote" rows="3" name="description" placeholder="Description">{{old('description')}}</textarea>
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-6">
                    <div class="form-group">
                        <strong>Quantité minimum:</strong>
                        <input type="number" name="min_qty" class="form-control" value="{{old('min_qty', 1)}}" min="1">
                    </div>
                </div>

                <!-- Section Produit Simple -->
                <div id="simple_product_section" class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Informations du produit</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <strong>Prix d'achat TTC:</strong>
                                        <input id="prix_achat" type="number" name="prix_achat" class="form-control" step="0.01" min="0" value="{{old('prix_achat')}}" required>
                                    </div>
                                </div>
                                <div class="col-md-3 hidden">
                                    <div class="form-group">
                                        <strong>TVA(%):</strong>
                                        <input id="tva" type="number" name="tva" class="form-control" step="0.1" min="0" value="{{old('tva', 19)}}" onchange="calculateTTC()" oninput="calculateHT()" >
                                    </div>
                                </div>
                                <div class="col-md-3 hidden">
                                    <div class="form-group">
                                        <strong>Prix de vente HT(Dt):</strong>
                                        <input id="prix_ht" type="number" name="prix_ht" class="form-control" step="0.01" min="0" value="{{old('prix_ht')}}" onchange="calculateTTC()" oninput="calculateHT()">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <strong>Prix de vente TTC(Dt):</strong>
                                        <input id="prix_ttc" type="number" name="prix_ttc" class="form-control" step="0.01" min="0" value="{{old('prix_ttc')}}" onchange="calculateHT()" oninput="calculateHT()">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section Produit Variable -->
                <div id="variable_product_section" class="col-12" style="display: none;">
                    <div class="card">
                        <div class="card-header">
                            <h4>Attributs du produit variable</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="has_colors" name="has_colors" onchange="toggleAttributeSection('colors_section')">
                                            <label class="custom-control-label" for="has_colors">Utiliser les couleurs</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="has_dimensions" name="has_dimensions" onchange="toggleAttributeSection('dimensions_section')">
                                            <label class="custom-control-label" for="has_dimensions">Utiliser les dimensions</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section Couleurs -->
                            <div id="colors_section" class="attribute-section" style="display: none;">
                                <div class="form-group">
                                    <strong>Couleurs disponibles:</strong>
                                    <select name="colors[]" id="colors" class="select2 form-control" multiple="multiple">
                                        @foreach($colors as $color)
                                        <option value="{{$color->id}}">{{$color->value}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Section Dimensions -->
                            <div id="dimensions_section" class="attribute-section" style="display: none;">
                                <div class="form-group">
                                    <strong>Dimensions disponibles:</strong>
                                    <select name="dimensions[]" id="dimensions" class="select2 form-control" multiple="multiple">
                                        @foreach($dimensions as $dimension)
                                        <option value="{{$dimension->id}}">{{$dimension->value}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="button" class="btn btn-info" id="generate_variations_btn" onclick="generateVariations()" disabled >Générer les variations</button>
                            </div>

                            <div id="variations_container" class="mt-4" style="display: none;">
                                <h5>Variations du produit</h5>
                                <div id="variations_list">
                                    <!-- Les variations seront ajoutées ici dynamiquement -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Informations supplémentaires si nécessaire -->
        </div>
        <div class="col-xs-12 col-sm-12 col-lg-12 mt-3">
            <button type="submit" class="btn btn-primary">Enregistrer</button>
        </div>
    </div>
</form>
@endsection

@section('footer-scripts')
<script src="{{asset('plugins/select2/js/select2.full.min.js')}}"></script>
<script src="{{ asset('plugins/summernote/summernote-bs4.min.js') }}"></script>

<script>
    $(function() {
        // Summernote
        $('.summernote').summernote();

        // Select2
        $('.select2').select2({
            tags: true,
            placeholder: 'Sélectionnez ou ajoutez une valeur',
            allowClear: true,
            createTag: function(params) {
                var term = $.trim(params.term);
                if (term === '') {
                    return null;
                }
                return {
                    id: term,
                    text: term,
                    newTag: true
                };
            }
        });
    });

    function toggleVariableProduct() {
        const productType = document.getElementById('product_type').value;
        
        if (productType == 1) { // Variable product
            //document.getElementById('simple_product_section').style.display = 'none';
            document.getElementById('variable_product_section').style.display = 'block';
        } else { // Simple product
            document.getElementById('simple_product_section').style.display = 'block';
            document.getElementById('variable_product_section').style.display = 'none';
        }
    }

    function toggleAttributeSection(sectionId) {
        const section = document.getElementById(sectionId);
        section.style.display = section.style.display === 'none' ? 'block' : 'none';
    }

    function calculateTTC() {
        const prixHT = parseFloat(document.getElementById('prix_ht').value) || 0;
        const tva = parseFloat(document.getElementById('tva').value) || 0;
        
        if (prixHT > 0) {
            const prixTTC = prixHT * (1 + tva / 100);
            document.getElementById('prix_ttc').value = prixTTC.toFixed(2);
            $('#generate_variations_btn').prop('disabled',false);
        }else{
            $('#generate_variations_btn').prop('disabled',true);
        }
    }

    function calculateHT() {
        const prixTTC = parseFloat(document.getElementById('prix_ttc').value) || 0;
        const tva = parseFloat(document.getElementById('tva').value) || 0;
        
        if (prixTTC > 0) {
            const prixHT = prixTTC / (1 + tva / 100);
            document.getElementById('prix_ht').value = prixHT.toFixed(2);
            $('#generate_variations_btn').prop('disabled',false);
        }else{
            $('#generate_variations_btn').prop('disabled',true);
        }
        
    }

    function generateVariations() {
        const hasColors = document.getElementById('has_colors').checked;
        const hasDimensions = document.getElementById('has_dimensions').checked;
        
        if (!hasColors && !hasDimensions) {
            alert('Veuillez sélectionner au moins un attribut (couleurs ou dimensions)');
            return;
        }

        let variations = [];
        
        if (hasColors && hasDimensions) {
            // Générer toutes les combinaisons possibles de couleurs et dimensions
            const selectedColors = $('#colors').select2('data');
            const selectedDimensions = $('#dimensions').select2('data');
            
            selectedColors.forEach(color => {
                selectedDimensions.forEach(dimension => {
                    variations.push({
                        color: { id: color.id, name: color.text },
                        dimension: { id: dimension.id, name: dimension.text }
                    });
                });
            });
        } else if (hasColors) {
            // Générer variations par couleur uniquement
            const selectedColors = $('#colors').select2('data');
            selectedColors.forEach(color => {
                variations.push({
                    color: { id: color.id, name: color.text },
                    dimension: null
                });
            });
        } else if (hasDimensions) {
            // Générer variations par dimension uniquement
            const selectedDimensions = $('#dimensions').select2('data');
            selectedDimensions.forEach(dimension => {
                variations.push({
                    color: null,
                    dimension: { id: dimension.id, name: dimension.text }
                });
            });
        }

        // Afficher les variations dans le conteneur
        renderVariations(variations);
    }

    function renderVariations(variations) {
        const container = document.getElementById('variations_list');
        container.innerHTML = '';
        let ref=$("#reference").val();
        let prix_achat=$("#prix_achat").val();
        let prix_ht=$("#prix_ht").val();
        let prix_ttc=$("#prix_ttc").val();
        
        variations.forEach((variation, index) => {
            let variationName = '';
            
            if (variation.color && variation.dimension) {
                variationName = `${variation.color.name}-${variation.dimension.name}`;
            } else if (variation.color) {
                variationName = variation.color.name;
            } else if (variation.dimension) {
                variationName = variation.dimension.name;
            }

            const variationHtml = `
                <div class="card mb-3">
                    <div class="card-header">
                        <h5>Variation: ${variationName}</h5>
                    </div>
                    <div class="card-body">
                        <input type="hidden" name="variations[${index}][color_id]" value="${variation.color ? variation.color.id : ''}">
                        <input type="hidden" name="variations[${index}][dimension_id]" value="${variation.dimension ? variation.dimension.id : ''}">
                        
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Référence:</label>
                                    <input type="text" name="variations[${index}][reference]" value="${ref}-${variationName}" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Prix d'achat:</label>
                                    <input type="number" name="variations[${index}][prix_achat]" class="form-control" step="0.01" min="0"  value="${prix_achat}"  >
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Prix HT:</label>
                                    <input type="number" name="variations[${index}][prix_ht]" class="form-control variation-prix-ht" value="${prix_ht}" step="0.01" min="0" required
                                        onchange="calculateVariationTTC(${index})">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Prix TTC:</label>
                                    <input type="number" name="variations[${index}][prix_ttc]" class="form-control variation-prix-ttc" value="${prix_ttc}" step="0.01" min="0" required
                                        onchange="calculateVariationHT(${index})">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Stock:</label>
                                    <input type="number" name="variations[${index}][stock_quantity]" class="form-control" min="0" value="0">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            container.innerHTML += variationHtml;
        });

        document.getElementById('variations_container').style.display = 'block';
    }

    function calculateVariationTTC(index) {
        const variationPrixHT = document.querySelector(`input[name="variations[${index}][prix_ht]"]`).value;
        const tva = parseFloat(document.getElementById('tva').value) || 19;
        
        if (variationPrixHT > 0) {
            const variationPrixTTC = parseFloat(variationPrixHT) * (1 + tva / 100);
            document.querySelector(`input[name="variations[${index}][prix_ttc]"]`).value = variationPrixTTC.toFixed(2);
        }
    }

    function calculateVariationHT(index) {
        const variationPrixTTC = document.querySelector(`input[name="variations[${index}][prix_ttc]"]`).value;
        const tva = parseFloat(document.getElementById('tva').value) || 19;
        
        if (variationPrixTTC > 0) {
            const variationPrixHT = parseFloat(variationPrixTTC) / (1 + tva / 100);
            document.querySelector(`input[name="variations[${index}][prix_ht]"]`).value = variationPrixHT.toFixed(2);
        }
    }
</script>
@endsection