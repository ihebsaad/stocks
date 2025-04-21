@extends('layouts.admin')

@section('content')
<style>
	.tab-content{
		min-height:400px;
	}
	.w-1{
		width:100px;
	}
	.summernote{
		min-height:150px;
		width:100%;
		min-width:600px;

	}
</style>
<div class="container">
	<ul class="nav nav-tabs" id="custom-tabs-three-tab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="custom-tabs-three-details-tab" data-toggle="pill" href="#coeff-tab" role="tab" aria-controls="custom-tab-coeff" aria-selected="true">Fenêtres PVC</a>
        </li>
		<li class="nav-item">
            <a class="nav-link" id="custom-tabs-three-alu-tab" data-toggle="pill" href="#alu-tab" role="tab" aria-controls="custom-tab-alu" aria-selected="true">Fenêtres ALU</a>
        </li>
    	<li class="nav-item">
            <a class="nav-link" id="custom-tabs-three-volets-tab" data-toggle="pill" href="#volets-tab" role="tab" aria-controls="custom-tab-volets" aria-selected="false">Volets</a>
        </li>
		<li class="nav-item">
            <a class="nav-link" id="custom-tabs-three-textes-tab" data-toggle="pill" href="#textes-tab" role="tab" aria-controls="custom-tab-textes" aria-selected="false">Textes</a>
        </li>
    </ul>

	<div class="tab-content pt-3 pb-3 pl-3 pr-3" id="custom-tabs-three-tabContent">
		<div class="tab-pane fade active show" id="coeff-tab" role="tabpanel" aria-labelledby="custom-tabs-three-details-tab">
			<div class="row">
				<div class="col-xs-4 col-sm-4 col-md-4">
					<div class="form-group">
						<strong>Coefficient Fenêtre à souflet:</strong>
						<input type="number" name="name" class="form-control w-1" step="0.1" min="0" placeholder="" value="{!! \App\Models\Setting::where('Model','Modele')->where('model_id','1')->where('genre','1')->first()->value !!}" onchange="update_setting(this,'Coefficient Fenêtre à souflet', 'Modele','1')">
					</div>
				</div>
				<div class="col-xs-4 col-sm-4 col-md-4">
					<div class="form-group">
						<strong>Coefficient Fenêtre 1V:</strong>
						<input type="number" name="name" class="form-control w-1" step="0.1" min="0"placeholder="" value="{!! \App\Models\Setting::where('Model','Modele')->where('model_id','2')->where('genre','1')->first()->value !!}" onchange="update_setting(this,'Coefficient Fenêtre 1V', 'Modele','2')">
					</div>
				</div>
				<div class="col-xs-4 col-sm-4 col-md-4">
					<div class="form-group">
						<strong>Coefficient Fenêtre 2V:</strong>
						<input type="number" name="name" class="form-control w-1" step="0.1" min="0" placeholder="" value="{!! \App\Models\Setting::where('Model','Modele')->where('model_id','3')->where('genre','1')->first()->value !!}" onchange="update_setting(this,'Coefficient Fenêtre 2V', 'Modele','3')">
					</div>
				</div>
				<div class="col-xs-4 col-sm-4 col-md-4">
					<div class="form-group">
						<strong>Coefficient Fenêtre 3V:</strong>
						<input type="number" name="name" class="form-control w-1" step="0.1" min="0" placeholder="" value="{!! \App\Models\Setting::where('Model','Modele')->where('model_id','4')->where('genre','1')->first()->value !!}" onchange="update_setting(this,'Coefficient Fenêtre 3V', 'Modele','4')">
					</div>
				</div>
				<div class="col-xs-4 col-sm-4 col-md-4">
					<div class="form-group">
						<strong>Coefficient Fenêtre 4V:</strong>
						<input type="number" name="name" class="form-control w-1" step="0.1" min="0" placeholder="" value="{!! \App\Models\Setting::where('Model','Modele')->where('model_id','11')->where('genre','1')->first()->value !!}"  onchange="update_setting(this,'Coefficient Fenêtre 4V', 'Modele','11')">
					</div>
				</div>
				<div class="col-xs-4 col-sm-4 col-md-4">
					<div class="form-group">
						<strong>Coefficient Fenêtre fixe:</strong>
						<input type="number" name="name" class="form-control w-1" step="0.1" min="0" placeholder="" value="{!! \App\Models\Setting::where('Model','Modele')->where('model_id','5')->where('genre','1')->first()->value !!}" onchange="update_setting(this,'Coefficient Fenêtre fixe', 'Modele','5')">
					</div>
				</div>
				<div class="col-xs-4 col-sm-4 col-md-4">
					<div class="form-group">
						<strong>Coefficient Porte fenêtre 1V:</strong>
						<input type="number" name="name" class="form-control w-1" step="0.1" min="0" placeholder="" value="{!! \App\Models\Setting::where('Model','Modele')->where('model_id','6')->where('genre','1')->first()->value !!}" onchange="update_setting(this,'Coefficient Porte fenêtre 1V', 'Modele','6')">
					</div>
				</div>
				<div class="col-xs-4 col-sm-4 col-md-4">
					<div class="form-group">
						<strong>Coefficient Porte fenêtre 2V:</strong>
						<input type="number" name="name" class="form-control w-1" step="0.1" min="0" placeholder="" value="{!! \App\Models\Setting::where('Model','Modele')->where('model_id','7')->where('genre','1')->first()->value !!}" onchange="update_setting(this,'Coefficient Porte fenêtre 2V', 'Modele','7')">
					</div>
				</div>

				<div class="col-xs-4 col-sm-4 col-md-4">
					<div class="form-group">
						<strong>Coefficient Coulissant 2:</strong>
						<input type="number" name="name" class="form-control w-1" step="0.1" min="0" placeholder="" value="{!! \App\Models\Setting::where('Model','Modele')->where('model_id','9')->where('genre','1')->first()->value !!}" onchange="update_setting(this,'Coefficient Coulissant 2', 'Modele','9')">
					</div>
				</div>
				<!--
				<div class="col-xs-4 col-sm-4 col-md-4">
					<div class="form-group">
						<strong>Coefficient Porte fenêtre serrure 1V: </strong>
						<input type="number" name="name" class="form-control w-1" step="0.1" min="0" placeholder="" value="{!! \App\Models\Setting::where('Model','Modele')->where('model_id','12')->first()->value !!}" onchange="update_setting(this,'Coefficient', 'Modele','12')">
					</div>
				</div>-->
			</div>
			<h4 class="mt-3">Addition en €</h4>
			<div class="row ">
				<div class="col-xs-4 col-sm-4 col-md-4">
					<div class="form-group">
						<strong>Ext couleur & intérieur blanc Groupe 1 :</strong>
						<input type="number" name="name" class="form-control w-1" step="10" min="0" placeholder="" value="{!! \App\Models\Setting::where('Model','Color')->where('model_id','1')->where('genre','1')->first()->value !!}" onchange="update_setting(this,'Coefficient ', 'Color','1')">
					</div>
				</div>

				<div class="col-xs-4 col-sm-4 col-md-4">
					<div class="form-group">
						<strong>Ext couleur & intérieur blanc Groupe 2 :</strong>
						<input type="number" name="name" class="form-control w-1" step="10" min="0" placeholder="" value="{!! \App\Models\Setting::where('Model','Color')->where('model_id','2')->where('genre','1')->first()->value !!}" onchange="update_setting(this,'Coefficient ', 'Color','2')">
					</div>
				</div>

				<div class="col-xs-4 col-sm-4 col-md-4">
					<div class="form-group">
						<strong>Ext couleur & intérieur blanc Groupe 3 :</strong>
						<input type="number" name="name" class="form-control w-1" step="10" min="0" placeholder="" value="{!! \App\Models\Setting::where('Model','Color')->where('model_id','3')->where('genre','1')->first()->value !!}" onchange="update_setting(this,'Coefficient', 'Color','3')">
					</div>
				</div>

				<div class="col-xs-4 col-sm-4 col-md-4">
					<div class="form-group">
						<strong>Ext & intérieur couleur Groupe 1 :</strong>
						<input type="number" name="name" class="form-control w-1" step="10" min="0" placeholder="" value="{!! \App\Models\Setting::where('Model','Color')->where('model_id','4')->where('genre','1')->first()->value !!}" onchange="update_setting(this,'Coefficient', 'Color','4')">
					</div>
				</div>

				<div class="col-xs-4 col-sm-4 col-md-4">
					<div class="form-group">
						<strong>Ext & intérieur couleur Groupe 2 :</strong>
						<input type="number" name="name" class="form-control w-1" step="10" min="0" placeholder="" value="{!! \App\Models\Setting::where('Model','Color')->where('model_id','5')->where('genre','1')->first()->value !!}" onchange="update_setting(this,'Coefficient', 'Color','5')">
					</div>
				</div>

				<div class="col-xs-4 col-sm-4 col-md-4">
					<div class="form-group">
						<strong>Ext & intérieur couleur Groupe 3 :</strong>
						<input type="number" name="name" class="form-control w-1" step="10" min="0" placeholder="" value="{!! \App\Models\Setting::where('Model','Color')->where('model_id','6')->where('genre','1')->first()->value !!}" onchange="update_setting(this,'Coefficient', 'Color','6')">
					</div>
				</div>
			</div>
			<div class="row mt-4">
				<div class="col-xs-4 col-sm-4 col-md-4">
					<div class="form-group">
						<strong>Coût Pose :</strong>
						<input type="number" name="name" class="form-control w-1" step="1" min="0" placeholder="" value="{!! \App\Models\Setting::where('Model','Pose')->where('model_id','1')->first()->value !!}" onchange="update_setting(this,'Pose menuiserie', 'Pose','1')">
					</div>
				</div>
				<div class="col-xs-4 col-sm-4 col-md-4">
					<div class="form-group">
						<strong>Coefficient Cintrage :</strong>
						<input type="number" name="name" class="form-control w-1" step="0.1" min="0" placeholder="" value="{!! \App\Models\Setting::where('Model','Bending')->where('model_id','1')->first()->value !!}" onchange="update_setting(this,'Coefficient cintrage', 'Bending','1')">
					</div>
				</div>
			</div>
		</div>

		<div class="tab-pane fade" id="alu-tab" role="tabpanel" aria-labelledby="custom-tabs-three-alu-tab">
			<div class="row">
				<div class="col-xs-4 col-sm-4 col-md-4">
					<div class="form-group">
						<strong>Coefficient Fenêtre à souflet:</strong>
						<input type="number" name="name" class="form-control w-1" step="0.1" min="0" placeholder="" value="{!! \App\Models\Setting::where('Model','Modele')->where('model_id','1')->where('genre','2')->first()->value !!}" onchange="update_setting(this,'Coefficient Fenêtre à souflet', 'Modele','1')">
					</div>
				</div>
				<div class="col-xs-4 col-sm-4 col-md-4">
					<div class="form-group">
						<strong>Coefficient Fenêtre 1V:</strong>
						<input type="number" name="name" class="form-control w-1" step="0.1" min="0"placeholder="" value="{!! \App\Models\Setting::where('Model','Modele')->where('model_id','2')->where('genre','2')->first()->value !!}" onchange="update_setting(this,'Coefficient Fenêtre 1V', 'Modele','2')">
					</div>
				</div>
				<div class="col-xs-4 col-sm-4 col-md-4">
					<div class="form-group">
						<strong>Coefficient Fenêtre 2V:</strong>
						<input type="number" name="name" class="form-control w-1" step="0.1" min="0" placeholder="" value="{!! \App\Models\Setting::where('Model','Modele')->where('model_id','3')->where('genre','2')->first()->value !!}" onchange="update_setting(this,'Coefficient Fenêtre 2V', 'Modele','3')">
					</div>
				</div><!--
				<div class="col-xs-4 col-sm-4 col-md-4">
					<div class="form-group">
						<strong>Coefficient Fenêtre 3V:</strong>
						<input type="number" name="name" class="form-control w-1" step="0.1" min="0" placeholder="" value="{!! \App\Models\Setting::where('Model','Modele')->where('model_id','4')->first()->value !!}" onchange="update_setting(this,'Coefficient Fenêtre 3V', 'Modele','4')">
					</div>
				</div>
				<div class="col-xs-4 col-sm-4 col-md-4">
					<div class="form-group">
						<strong>Coefficient Fenêtre 4V:</strong>
						<input type="number" name="name" class="form-control w-1" step="0.1" min="0" placeholder="" value="{!! \App\Models\Setting::where('Model','Modele')->where('model_id','11')->first()->value !!}"  onchange="update_setting(this,'Coefficient Fenêtre 4V', 'Modele','11')">
					</div>
				</div>-->
				<div class="col-xs-4 col-sm-4 col-md-4">
					<div class="form-group">
						<strong>Coefficient Fenêtre fixe:</strong>
						<input type="number" name="name" class="form-control w-1" step="0.1" min="0" placeholder="" value="{!! \App\Models\Setting::where('Model','Modele')->where('model_id','5')->where('genre','2')->first()->value !!}" onchange="update_setting(this,'Coefficient Fenêtre fixe', 'Modele','5')">
					</div>
				</div>
				<div class="col-xs-4 col-sm-4 col-md-4">
					<div class="form-group">
						<strong>Coefficient Porte fenêtre 1V:</strong>
						<input type="number" name="name" class="form-control w-1" step="0.1" min="0" placeholder="" value="{!! \App\Models\Setting::where('Model','Modele')->where('model_id','6')->where('genre','2')->first()->value !!}" onchange="update_setting(this,'Coefficient Porte fenêtre 1V', 'Modele','6')">
					</div>
				</div>
				<div class="col-xs-4 col-sm-4 col-md-4">
					<div class="form-group">
						<strong>Coefficient Porte fenêtre 2V:</strong>
						<input type="number" name="name" class="form-control w-1" step="0.1" min="0" placeholder="" value="{!! \App\Models\Setting::where('Model','Modele')->where('model_id','7')->where('genre','2')->first()->value !!}" onchange="update_setting(this,'Coefficient Porte fenêtre 2V', 'Modele','7')">
					</div>
				</div><!--
				<div class="col-xs-4 col-sm-4 col-md-4">
					<div class="form-group">
						<strong>Coefficient Coulissant 1:</strong>
						<input type="number" name="name" class="form-control w-1" step="0.1" min="0" placeholder="" value="{!! \App\Models\Setting::where('Model','Modele')->where('model_id','8')->first()->value !!}" onchange="update_setting(this,'Coefficient Coulissant 1', 'Modele','8')">
					</div>
				</div>-->
				<div class="col-xs-4 col-sm-4 col-md-4">
					<div class="form-group">
						<strong>Coefficient Coulissant 2:</strong>
						<input type="number" name="name" class="form-control w-1" step="0.1" min="0" placeholder="" value="{!! \App\Models\Setting::where('Model','Modele')->where('model_id','9')->where('genre','2')->first()->value !!}" onchange="update_setting(this,'Coefficient Coulissant 2', 'Modele','9')">
					</div>
				</div><!--
				<div class="col-xs-4 col-sm-4 col-md-4">
					<div class="form-group">
						<strong>Coefficient Coulissant 3:</strong>
						<input type="number" name="name" class="form-control w-1" step="0.1" min="0" placeholder="" value="{!! \App\Models\Setting::where('Model','Modele')->where('model_id','10')->first()->value !!}" onchange="update_setting(this,'Coefficient Coulissant 3', 'Modele','10')">
					</div>
				</div>-->
				<div class="col-xs-4 col-sm-4 col-md-4">
					<div class="form-group">
						<strong>Coefficient Porte fenêtre serrure 1V: </strong>
						<input type="number" name="name" class="form-control w-1" step="0.1" min="0" placeholder="" value="{!! \App\Models\Setting::where('Model','Modele')->where('model_id','12')->where('genre','2')->first()->value !!}" onchange="update_setting(this,'Coefficient', 'Modele','12')">
					</div>
				</div>
			</div>
			<h4 class="mt-3">Addition en €</h4>
			<div class="row ">
				<div class="col-xs-4 col-sm-4 col-md-4">
					<div class="form-group">
						<strong>Ext couleur & intérieur blanc Groupe 1 :</strong>
						<input type="number" name="name" class="form-control w-1" step="10" min="0" placeholder="" value="{!! \App\Models\Setting::where('Model','Color')->where('model_id','1')->where('genre','2')->first()->value !!}" onchange="update_setting(this,'Coefficient ', 'Color','1')">
					</div>
				</div>

				<div class="col-xs-4 col-sm-4 col-md-4">
					<div class="form-group">
						<strong>Ext couleur & intérieur blanc Groupe 2 :</strong>
						<input type="number" name="name" class="form-control w-1" step="10" min="0" placeholder="" value="{!! \App\Models\Setting::where('Model','Color')->where('model_id','2')->where('genre','2')->first()->value !!}" onchange="update_setting(this,'Coefficient ', 'Color','2')">
					</div>
				</div>

				<div class="col-xs-4 col-sm-4 col-md-4">
					<div class="form-group">
						<strong>Ext couleur & intérieur blanc Groupe 3 :</strong>
						<input type="number" name="name" class="form-control w-1" step="10" min="0" placeholder="" value="{!! \App\Models\Setting::where('Model','Color')->where('model_id','3')->where('genre','2')->first()->value !!}" onchange="update_setting(this,'Coefficient', 'Color','3')">
					</div>
				</div>

				<div class="col-xs-4 col-sm-4 col-md-4">
					<div class="form-group">
						<strong>Ext & intérieur couleur Groupe 1 :</strong>
						<input type="number" name="name" class="form-control w-1" step="10" min="0" placeholder="" value="{!! \App\Models\Setting::where('Model','Color')->where('model_id','4')->where('genre','2')->first()->value !!}" onchange="update_setting(this,'Coefficient', 'Color','4')">
					</div>
				</div>

				<div class="col-xs-4 col-sm-4 col-md-4">
					<div class="form-group">
						<strong>Ext & intérieur couleur Groupe 2 :</strong>
						<input type="number" name="name" class="form-control w-1" step="10" min="0" placeholder="" value="{!! \App\Models\Setting::where('Model','Color')->where('model_id','5')->where('genre','2')->first()->value !!}" onchange="update_setting(this,'Coefficient', 'Color','5')">
					</div>
				</div>

				<div class="col-xs-4 col-sm-4 col-md-4">
					<div class="form-group">
						<strong>Ext & intérieur couleur Groupe 3 :</strong>
						<input type="number" name="name" class="form-control w-1" step="10" min="0" placeholder="" value="{!! \App\Models\Setting::where('Model','Color')->where('model_id','6')->where('genre','2')->first()->value !!}" onchange="update_setting(this,'Coefficient', 'Color','6')">
					</div>
				</div>
			</div>


		</div>

		<div class="tab-pane fade" id="volets-tab" role="tabpanel" aria-labelledby="custom-tabs-three-volets-tab">
			<div class="row">
				<div class="col-xs-4 col-sm-4 col-md-4">
					<div class="form-group">
						<strong>Coefficient Multicom :</strong>
						<input type="number" name="name" class="form-control w-1" step="0.1" min="0" placeholder="" value="{!! \App\Models\Setting::where('Model','Shutter')->where('model_id','1')->first()->value !!}" onchange="update_setting(this,'Coefficient Multicom', 'Shutter','1')">
					</div>
				</div>

				<div class="col-xs-4 col-sm-4 col-md-4">
					<div class="form-group">
						<strong>Coefficient Futurcom simple:</strong>
						<input type="number" name="name" class="form-control w-1" step="0.1" min="0" placeholder="" value="{!! \App\Models\Setting::where('Model','Shutter')->where('model_id','2')->first()->value !!}" onchange="update_setting(this,'Coefficient Futurcom', 'Shutter','2')">
					</div>
				</div>

				<div class="col-xs-4 col-sm-4 col-md-4">
					<div class="form-group">
						<strong>Coefficient Futurcom avec moustiquaire:</strong>
						<input type="number" name="name" class="form-control w-1" step="0.1" min="0" placeholder="" value="{!! \App\Models\Setting::where('Model','Shutter')->where('model_id','3')->first()->value !!}" onchange="update_setting(this,'Coefficient Futurcom avec moustiquaire', 'Shutter','3')">
					</div>
				</div>
			</div>
		</div>

		<div class="tab-pane fade" id="textes-tab" role="tabpanel" aria-labelledby="custom-tabs-three-textes-tab">
			<div class="row">
				<div class="form-group">
					<strong>Texte Portes:</strong>
					<textarea  class="form-control summernote"   data-model="Door" data-model-id="1"   >{!! \App\Models\Setting::where('Model','Door')->where('model_id','1')->first()->text !!}</textarea>
				</div>
			</div>
			<hr>
			<div class="row">
				<div class="form-group">
					<strong>Texte Volet Multicom:</strong>
					<textarea  class="form-control summernote" data-model="Shutter" data-model-id="1"   >{!! \App\Models\Setting::where('Model','Shutter')->where('model_id','1')->first()->text !!}</textarea>
				</div>
				<div class="form-group">
					<strong>Texte Volet Futurcom sans moustiquaire:</strong>
					<textarea  class="form-control summernote"  data-model="Shutter" data-model-id="2"  >{!! \App\Models\Setting::where('Model','Shutter')->where('model_id','2')->first()->text !!}</textarea>
				</div>
				<div class="form-group">
					<strong>Texte Volet Futurcom avec moustiquaire:</strong>
					<textarea  class="form-control summernote"  data-model="Shutter" data-model-id="3"  >{!! \App\Models\Setting::where('Model','Shutter')->where('model_id','3')->first()->text !!}</textarea>
				</div>
			</div>
			<hr>
			<div class="row">
				<div class="form-group">
					<strong>Texte Fenêtre à souflet:</strong>
					<textarea  class="form-control summernote"  data-model="Modele" data-model-id="1"  >{!! \App\Models\Setting::where('Model','Modele')->where('model_id','1')->first()->text !!}</textarea>
				</div>
				<div class="form-group">
					<strong>Texte Fenêtre 1V:</strong>
					<textarea  class="form-control summernote"  data-model="Modele" data-model-id="2" >{!! \App\Models\Setting::where('Model','Modele')->where('model_id','2')->first()->text !!}</textarea>
				</div>
				<div class="form-group">
					<strong>Texte Fenêtre 2V:</strong>
					<textarea  class="form-control summernote"  data-model="Modele" data-model-id="3" >{!! \App\Models\Setting::where('Model','Modele')->where('model_id','3')->first()->text !!}</textarea>
				</div>
				<div class="form-group">
					<strong>Texte Fenêtre 3V:</strong>
					<textarea  class="form-control summernote"  data-model="Modele" data-model-id="4" >{!! \App\Models\Setting::where('Model','Modele')->where('model_id','4')->first()->text !!}</textarea>
				</div>
				<div class="form-group">
					<strong>Texte Fenêtre 4V:</strong>
					<textarea  class="form-control summernote" data-model="Modele" data-model-id="11" >{!! \App\Models\Setting::where('Model','Modele')->where('model_id','11')->first()->text !!}</textarea>
				</div>
				<div class="form-group">
					<strong>Texte Fenêtre fixe:</strong>
					<textarea  class="form-control summernote"  data-model="Modele" data-model-id="5" >{!! \App\Models\Setting::where('Model','Modele')->where('model_id','5')->first()->text !!}</textarea>
				</div>
				<div class="form-group">
					<strong>Texte Porte fenêtre 1V:</strong>
					<textarea  class="form-control summernote"  data-model="Modele" data-model-id="6" >{!! \App\Models\Setting::where('Model','Modele')->where('model_id','6')->first()->text  !!}</textarea>
				</div>
				<div class="form-group">
					<strong>Texte Porte fenêtre 2V:</strong>
					<textarea  class="form-control summernote"  data-model="Modele" data-model-id="7" >{!! \App\Models\Setting::where('Model','Modele')->where('model_id','7')->first()->text !!}</textarea>
				</div>
				<div class="form-group">
					<strong>Texte Coulissant 1:</strong>
					<textarea  class="form-control summernote"  data-model="Modele" data-model-id="8" >{!! \App\Models\Setting::where('Model','Modele')->where('model_id','8')->first()->text !!}</textarea>
				</div>
				<div class="form-group">
					<strong>Texte Coulissant 2:</strong>
					<textarea  class="form-control summernote"  data-model="Modele" data-model-id="9" >{!! \App\Models\Setting::where('Model','Modele')->where('model_id','9')->first()->text !!}</textarea>
				</div>
				<div class="form-group">
					<strong>Texte Coulissant 3:</strong>
					<textarea  class="form-control summernote" data-model="Modele" data-model-id="10" >{!! \App\Models\Setting::where('Model','Modele')->where('model_id','10')->first()->text !!}</textarea>
				</div>
				<div class="form-group">
					<strong>Texte Porte fenêtre serrure 1V:</strong>
					<textarea  class="form-control summernote"  data-model="Modele" data-model-id="12" >{!! \App\Models\Setting::where('Model','Modele')->where('model_id','12')->first()->text !!}</textarea>
				</div>

			</div>

		</div>


	</div>


<script>
	function update_setting(field,name,model,model_id){
		var _token = $('input[name="_token"]').val();
		var value = $(field).val();

		$.ajax({
			url: "{{ route('update_setting') }}",
			method: "POST",
			data: { value:value,model:model,model_id:model_id, _token:_token},
			success: function (data) {
				$(field).animate({opacity: 0.1}, 500);
				$(field).animate({opacity: 1}, 500);
			}
		});
	}

	function update_text(value,model,model_id,field){
		var _token = $('input[name="_token"]').val();
		//var value = $(field).val();
		$.ajax({
			url: "{{ route('update_text') }}",
			method: "POST",
			data: { value:value,model:model,model_id:model_id, _token:_token},
			success: function (data) {
				//$(field).animate({opacity: 0.1}, 500);
				//$(field).animate({opacity: 1}, 500);
				/*
				toastr.options =
				{
					"closeButton" : true,
					"progressBar" : true
				}
						toastr.success("Texte modifié");*/
				}

		});
	}
</script>

@endsection

@section('footer-scripts')
<script src="{{ asset('plugins/summernote/summernote-bs4.min.js') }}" ></script>

<script>

	$(function () {
		// Summernote
		$('.summernote').each(function() {
			var $textarea = $(this);
			var model = $textarea.data('model');
			var model_id = $textarea.data('model-id');

			$textarea.summernote({
				callbacks: {
					onChange: function (contents, $editable) {
						// Call the update_setting function with appropriate values
						update_text(contents, model, model_id,this);
					}
				}
			});
		});

	});


</script>

@endsection