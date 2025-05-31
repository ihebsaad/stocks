<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Liste des Colis</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            color: #333;
            font-size: 20px;
        }
        .info {
            margin-bottom: 20px;
            text-align: right;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #333;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #666;
            font-size: 10px;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
        .reference {
            font-weight: bold;
         }
        .client-info {
            line-height: 1.2;
        }
        .cod {
            font-weight: bold;
            color: #28a745;
        }
        sup, sub {
        vertical-align: baseline;
        position: relative;
        top: -0.4em;
        }
        .bg-1{ color: #0da598;  }
        .bg-2{ color: #ef6f28;  }
        .bg-3{ color: #227ac2;  }
        .bg-4{ color: #6c757d;  }
        .bg-5{ color: #fd9883;  }
    </style>
</head>
<body>
    <!--
    <div class="header">
        <h1>Liste des colis ...</h1>
    </div>
    -->
    <div class="info">
        <strong>Généré le:</strong> {{ $generated_at }}<br>
        <strong>Nombre total:</strong> {{ $total_count }} colis
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">#</th>
                <th width="15%">Référence</th>
                <th width="15%">Client</th>
                <th width="10%">Tél</th>
                <th width="10%">Nb Pièces</th>
                <th width="25%">Libellé</th>
                <th width="10%">COD</th>
            </tr>
        </thead>
        <tbody>
            @foreach($parcels as $index => $parcel)
            <tr>
                <td class="text-center">
                    {{ $index + 1 }}<br>
                    <input type="checkbox" />
                </td>                
                <td>
                    <span class="reference bg-{{$parcel->company->id}}">
                        {{ $parcel->reference ?: '#' . $parcel->id }}
                    </span>
                    <div class="barcode">
                        <div class="barcode-container">
                            {!! $barcodes[$parcel->id] ?? '' !!}
                        </div>
                    </div>                    
                </td>
                <td>
                    <div class="client-info">
                        @if($parcel->nom_client)
                            {{ $parcel->nom_client }}
                        @elseif($parcel->order && $parcel->order->client)
                            {{ $parcel->order->client->full_name }}
                        @else
                            <em>Non défini</em>
                        @endif 
                        @php
                        if ($parcel->service && $parcel->service!='Livraison') {
                            echo ' <b style="color:#d1202a"><i> Échange</i></b></span>'; 
                        }   
                        @endphp                   
                    </div>
                </td>                
                <td>
                    {{ $parcel->tel_l ?: ($parcel->order && $parcel->order->client ? $parcel->order->client->phone : '-') }}
                    @if($parcel->tel2_l)
                        <br><small>{{ $parcel->tel2_l }}</small>
                    @endif 
                </td>
                <td class="text-center">
                    {{ $parcel->nb_piece ?: '1' }}
                </td>
                <td>
                    @foreach($parcel->order->items as  $item)
                        <small>{{$item->product->name}}  X {{$item->quantity}}</small>
                    @endforeach
                    @if($parcel->remarque!='')<br><b style="color:#d1202a">{{$parcel->remarque}}</b> @endif
                </td>
                <td class="text-right">
                    <span class="cod">{{ number_format($parcel->cod, 2) }} </span>
                </td>

            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #e9ecef; font-weight: bold;">
                <td class="text-center">{{ $total_count }}</td>
                <td colspan="5" class="text-right">Total COD:</td>
                <td class="text-right">
                    <span class="cod">{{ number_format($parcels->sum('cod'), 2) }} <sup>TND</sup></span>
                </td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        Document généré le {{ $generated_at }}
    </div>
</body>
</html>