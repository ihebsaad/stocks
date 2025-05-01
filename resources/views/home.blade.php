@extends('layouts.admin')

@section('content')
<style>
	/* ---------------------------------- */
	/* Dashboard Stats Boxes
------------------------------------- */
	.dashboard-stat {
		display: inline-block;
		padding: 0;
		height: 160px;
		background-color: #444;
		color: #fff;
		border-radius: 4px;
		width: 100%;
		position: relative;
		margin-bottom: 20px;
		overflow: hidden;
		transition: 0.3s;
		cursor: default;
	}

	.dashboard-stat:hover {
		transform: translateY(-4px);
	}

	.dashboard-stat-content {
		position: absolute;
		left: 32px;
		top: 50%;
		width: 45%;
		transform: translateY(-50%);
	}

	.dashboard-stat-content h4 {
		font-size: 42px;
		font-weight: 600;
		padding: 0;
		margin: 0;
		color: #fff;
		letter-spacing: -1px;
	}

	.dashboard-stat-content span {
		font-size: 18px;
		margin-top: 4px;
		line-height: 24px;
		font-weight: 300;
		display: inline-block;
	}

	.dashboard-stat-content a {
		color: white;
	}

	.dashboard-stat-icon {
		position: absolute;
		right: 32px;
		top: 50%;
		transform: translateY(-40%);
		font-size: 80px;
		opacity: 0.3;
	}


	/* Colors */
	.dashboard-stat.color-1 {
		background: linear-gradient(to left, rgba(255, 255, 255, 0) 25%, rgba(255, 255, 255, 0.2));
		background-color: #64bc36;
	}

	.dashboard-stat.color-2 {
		background: linear-gradient(to left, rgba(255, 255, 255, 0) 25%, rgba(255, 255, 255, 0.1));
		background-color: #363841;
	}

	.dashboard-stat.color-3 {
		background: linear-gradient(to left, rgba(255, 255, 255, 0) 25%, rgba(255, 255, 255, 0.3));
		background-color: #ffae00;
	}

	.dashboard-stat.color-4 {
		background: linear-gradient(to left, rgba(255, 255, 255, 0) 25%, rgba(255, 255, 255, 0.1));
		background-color: #f3103c;
	}
</style>
<div class="container">
	<div class="row">

		<!-- Item -->
		<div class="col-lg-3 col-md-6">
			<div class="dashboard-stat color-1">
				<div class="dashboard-stat-content"><a href="{{route('orders.index')}}">
						<h4>20</h4> <span>Commandes</span>
					</a></div>
				<div class="dashboard-stat-icon"><i class="fas fa-address-card"></i></div>
			</div>
		</div>
		@can('isAdmin')
		<!-- Item -->
		<div class="col-lg-3 col-md-6">
			<div class="dashboard-stat color-2">
				<div class="dashboard-stat-content"><a href="{{route('products.index')}}">
						<h4>50</h4> <span>Produits</span>
					</a></div>
				<div class="dashboard-stat-icon"><i class="fas fa-cubes"></i></div>
			</div>
		</div>
		@endcan

		<!-- Item -->
		<div class="col-lg-3 col-md-6">
			<div class="dashboard-stat color-3">
				<div class="dashboard-stat-content"><a href="{{route('quotes.index')}}">
						<h4>100</h4> <span>Devis</span>
					</a></div>
				<div class="dashboard-stat-icon"><i class="fas fa-file-invoice"></i></div>
			</div>
		</div>
		@can('isAdmin')
		<!-- Item -->
		<div class="col-lg-3 col-md-6">
			<div class="dashboard-stat color-4">
				<div class="dashboard-stat-content"><a href="{{route('invoices.index')}}">
						<h4>100</h4> <span>Factures</span>
					</a></div>
				<div class="dashboard-stat-icon"><i class="fas fa-file-invoice-dollar"></i></div>
			</div>
		</div>
		@endcan
	</div>

   


	<!-- Modal -->
	<div class="modal fade" id="invoiceModal" tabindex="-1" role="dialog" aria-labelledby="invoiceModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="invoiceModalLabel">Factures contenant ce produit</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<table class="table table-bordered">
						<thead class="bg-primary">
							<tr>
								<th>Numéro de facture</th>
								<th>Date</th>
								<th>Client</th>
								<th>Montant TTC</th>
							</tr>
						</thead>
						<tbody id="invoiceDetails">
							<!-- Les données des factures seront chargées ici via AJAX -->
						</tbody>
					</table>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
				</div>
			</div>
		</div>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>

	

	

</div>
@endsection