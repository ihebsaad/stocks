@extends('layouts.admin')

<style>
    sup, sub {
    vertical-align: baseline;
    position: relative;
    top: -0.4em;
    }
    sub { 
    top: 0.4em; 
    }

    .bg-1{ background-color: #0da598; color: white;   }
    .bg-2{ background-color: #ef6f28; color: white;   }
    .bg-3{ background-color: #227ac2; color: white;   }
    .bg-4{ background-color: #6c757d; color: white;   }
    .bg-5{ background-color: #fd9883; color: white;   }

        .status-history {
        max-height: 200px;
        overflow-y: auto;
    }
    .timeline-item {
        position: relative;
        padding-left: 30px;
        margin-bottom: 15px;
    }
    .timeline-item:before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        width: 2px;
        background-color: #e0e0e0;
    }
    .timeline-item:after {
        content: '';
        position: absolute;
        left: -4px;
        top: 0;
        height: 10px;
        width: 10px;
        border-radius: 50%;
        background-color: #007bff;
    }
   

        
    .status-history {
        max-height: 200px;
        overflow-y: auto;
    }

    .timeline-item {
        position: relative;
        padding-left: 30px;
        margin-bottom: 15px;
    }

    .timeline-item:before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        width: 2px;
        background-color: #e0e0e0;
    }

    .timeline-item:after {
        content: '';
        position: absolute;
        left: -4px;
        top: 0;
        height: 10px;
        width: 10px;
        border-radius: 50%;
        background-color: #007bff;
    }
</style>

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="float-left">
            <h2>Détails du colis</h2>
        </div>
        <div class="float-right">
            <a class="btn btn-primary" href="{{ route('parcels.index') }}"> Retour</a>
        </div>
    </div>
</div>
                
<div class="card mt-3">
    <div class="card-header">
        <h4>{{ $parcel->reference }}</h4> 
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <strong>Date:</strong> {{ $parcel->created_at->format('d/m/Y H:i') }}
                <strong>Client:</strong> {{  $parcel->nom_client }}
                <strong>Tél:</strong> {{  $parcel->tel_l }}
                <strong>Tél 2:</strong> {{ $parcel->tel2_l }}
                <strong>Adresse:</strong> {{  $parcel->adresse_l }}
                <strong>Ville:</strong> {{  $parcel->ville_cl }}
                <strong>Délégation:</strong> {{ $parcel->gov_l  }}
            </div>
  
            <div class="col-md-4">
                <span class="badge bg-{{$parcel->company->id}}">{{ucfirst($parcel->company->name)}} </span>
                <strong>Libellé :</strong> {{$parcel->libelle }}
                <strong>nb_piece :</strong> {{$parcel->nb_piece }}
                <strong>COD :</strong> {{$parcel->cod }} <sup>TND</sup>
                <strong>Service :</strong> {{$parcel->service == 'Livraison' ? '<b>Livraison</b>' : '<i>Échange</i>'}}
                <strong>Remarque :</strong>{{ $parcel->remarque }}
                
            </div>
            <div class="col-md-4">
                <strong>Historiques:</strong>
                @foreach($historiques as $history)
                <div class="timeline-item">
                <div class="small text-muted">{{ $history->created_at->format('d/m/Y H:i') }}</div>
                    <div class="fw-bold">
                        @if($history->old_status)
                            {{ $statusOptions[$history->old_status] ?? $history->old_status }} → 
                        @endif
                            {{ $statusOptions[$history->new_status] ?? $history->new_status }}
                    </div>
                    @if($history->comment)
                        <div>{{ $history->comment }}</div>
                    @endif
                    <div class="small">par {{ $history->user->name ?? 'API' }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@endsection