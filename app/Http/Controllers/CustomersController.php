<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;

class CustomersController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (auth()->user()->user_type == 'admin')
            $customers = Customer::orderBy('id','desc')->get();
        else
            $customers = Customer::where('commercial',auth()->user()->id)->orderBy('id','desc')->get();

        return view('customers.index',compact('customers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $countries=$this->countries();
        $commercials=User::where('user_type','<>','admin')->get();
        return view('customers.create',compact('countries','commercials'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required_if:company,null'
        ]);

        $customer=Customer::create($request->all());

        if( $request->get('source')=='quote' )
            return redirect()->route('quotes.add',['customer_id'=>$customer->id])->with('success','Client créé avec succès.');

        elseif( $request->get('source')=='invoice' )
            return redirect()->route('invoices.add',['customer_id'=>$customer->id])->with('success','Client créé avec succès.');

        else
        return redirect()->route('customers.index')->with('success','Client créé avec succès.');

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function show(Customer $customer)
    {
        return view('customers.show',compact('customer'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function edit(Customer $customer)
    {
        $countries=$this->countries();
        return view('customers.edit',compact('customer','countries'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name' => 'required',
        ]);
        if (auth()->user()->user_type == 'admin' || $customer->commercial== auth()->user()->id )
            $customer->update($request->all());

        return redirect()->route('customers.index')
                        ->with('success','Client modifié avec succès');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Customer $customer)
    {
        if (auth()->user()->user_type == 'admin' || $customer->commercial== auth()->user()->id )
            $customer->delete();

        return redirect()->route('customers.index')
                        ->with('success','Client supprimé avec succès');
    }


	public static function countries() {
        $countries = array(
          'AF' => 'Afghanistan',
          'ZA' => 'Afrique Du Sud',
          'AX' => 'Åland, Îles',
          'AL' => 'Albanie',
          'DZ' => 'Algérie',
          'DE' => 'Allemagne',
          'AD' => 'Andorre',
          'AO' => 'Angola',
          'AI' => 'Anguilla',
          'AQ' => 'Antarctique',
          'AG' => 'Antigua-Et-Barbuda',
          'SA' => 'Arabie Saoudite',
          'AR' => 'Argentine',
          'AM' => 'Arménie',
          'AW' => 'Aruba',
          'AU' => 'Australie',
          'AT' => 'Autriche',
          'AZ' => 'Azerbaïdjan',
          'BS' => 'Bahamas',
          'BH' => 'Bahreïn',
          'BD' => 'Bangladesh',
          'BB' => 'Barbade',
          'BY' => 'Bélarus',
          'BE' => 'Belgique',
          'BZ' => 'Belize',
          'BJ' => 'Bénin',
          'BM' => 'Bermudes',
          'BT' => 'Bhoutan',
          'BO' => 'Bolivie, L\'état Plurinational De',
          'BQ' => 'Bonaire, Saint-Eustache Et Saba',
          'BA' => 'Bosnie-Herzégovine',
          'BW' => 'Botswana',
          'BV' => 'Bouvet, Île',
          'BR' => 'Brésil',
          'BN' => 'Brunei Darussalam',
          'BG' => 'Bulgarie',
          'BF' => 'Burkina Faso',
          'BI' => 'Burundi',
          'KY' => 'Caïmans, Îles',
          'KH' => 'Cambodge',
          'CM' => 'Cameroun',
          'CA' => 'Canada',
          'CV' => 'Cap-Vert',
          'CF' => 'Centrafricaine, République',
          'CL' => 'Chili',
          'CN' => 'Chine',
          'CX' => 'Christmas, Île',
          'CY' => 'Chypre',
          'CC' => 'Cocos (Keeling), Îles',
          'CO' => 'Colombie',
          'KM' => 'Comores',
          'CG' => 'Congo',
          'CD' => 'Congo, La République Démocratique Du',
          'CK' => 'Cook, Îles',
          'KR' => 'Corée, République De',
          'KP' => 'Corée, République Populaire Démocratique De',
          'CR' => 'Costa Rica',
          'CI' => 'Côte D\'ivoire',
          'HR' => 'Croatie',
          'CU' => 'Cuba',
          'CW' => 'Curaçao',
          'DK' => 'Danemark',
          'DJ' => 'Djibouti',
          'DO' => 'Dominicaine, République',
          'DM' => 'Dominique',
          'EG' => 'Égypte',
          'SV' => 'El Salvador',
          'AE' => 'Émirats Arabes Unis',
          'EC' => 'Équateur',
          'ER' => 'Érythrée',
          'ES' => 'Espagne',
          'EE' => 'Estonie',
          'US' => 'États-Unis',
          'ET' => 'Éthiopie',
          'FK' => 'Falkland, Îles (Malvinas)',
          'FO' => 'Féroé, Îles',
          'FJ' => 'Fidji',
          'FI' => 'Finlande',
          'FR' => 'France',
          'GA' => 'Gabon',
          'GM' => 'Gambie',
          'GE' => 'Géorgie',
          'GS' => 'Géorgie Du Sud-Et-Les Îles Sandwich Du Sud',
          'GH' => 'Ghana',
          'GI' => 'Gibraltar',
          'GR' => 'Grèce',
          'GD' => 'Grenade',
          'GL' => 'Groenland',
          'GP' => 'Guadeloupe',
          'GU' => 'Guam',
          'GT' => 'Guatemala',
          'GG' => 'Guernesey',
          'GN' => 'Guinée',
          'GW' => 'Guinée-Bissau',
          'GQ' => 'Guinée Équatoriale',
          'GY' => 'Guyana',
          'GF' => 'Guyane Française',
          'HT' => 'Haïti',
          'HM' => 'Heard-Et-Îles Macdonald, Île',
          'HN' => 'Honduras',
          'HK' => 'Hong Kong',
          'HU' => 'Hongrie',
          'IM' => 'Île De Man',
          'UM' => 'Îles Mineures Éloignées Des États-Unis',
          'VG' => 'Îles Vierges Britanniques',
          'VI' => 'Îles Vierges Des États-Unis',
          'IN' => 'Inde',
          'ID' => 'Indonésie',
          'IR' => 'Iran, République Islamique D\'',
          'IQ' => 'Iraq',
          'IE' => 'Irlande',
          'IS' => 'Islande',
          'IL' => 'Israël',
          'IT' => 'Italie',
          'JM' => 'Jamaïque',
          'JP' => 'Japon',
          'JE' => 'Jersey',
          'JO' => 'Jordanie',
          'KZ' => 'Kazakhstan',
          'KE' => 'Kenya',
          'KG' => 'Kirghizistan',
          'KI' => 'Kiribati',
          'KW' => 'Koweït',
          'LA' => 'Lao, République Démocratique Populaire',
          'LS' => 'Lesotho',
          'LV' => 'Lettonie',
          'LB' => 'Liban',
          'LR' => 'Libéria',
          'LY' => 'Libye',
          'LI' => 'Liechtenstein',
          'LT' => 'Lituanie',
          'LU' => 'Luxembourg',
          'MO' => 'Macao',
          'MK' => 'Macédoine, L\'ex-République Yougoslave De',
          'MG' => 'Madagascar',
          'MY' => 'Malaisie',
          'MW' => 'Malawi',
          'MV' => 'Maldives',
          'ML' => 'Mali',
          'MT' => 'Malte',
          'MP' => 'Mariannes Du Nord, Îles',
          'MA' => 'Maroc',
          'MH' => 'Marshall, Îles',
          'MQ' => 'Martinique',
          'MU' => 'Maurice',
          'MR' => 'Mauritanie',
          'YT' => 'Mayotte',
          'MX' => 'Mexique',
          'FM' => 'Micronésie, États Fédérés De',
          'MD' => 'Moldova, République De',
          'MC' => 'Monaco',
          'MN' => 'Mongolie',
          'ME' => 'Monténégro',
          'MS' => 'Montserrat',
          'MZ' => 'Mozambique',
          'MM' => 'Myanmar',
          'NA' => 'Namibie',
          'NR' => 'Nauru',
          'NP' => 'Népal',
          'NI' => 'Nicaragua',
          'NE' => 'Niger',
          'NG' => 'Nigéria',
          'NU' => 'Niué',
          'NF' => 'Norfolk, Île',
          'NO' => 'Norvège',
          'NC' => 'Nouvelle-Calédonie',
          'NZ' => 'Nouvelle-Zélande',
          'IO' => 'Océan Indien, Territoire Britannique De L\'',
          'OM' => 'Oman',
          'UG' => 'Ouganda',
          'UZ' => 'Ouzbékistan',
          'PK' => 'Pakistan',
          'PW' => 'Palaos',
          'PS' => 'Palestinien Occupé, Territoire',
          'PA' => 'Panama',
          'PG' => 'Papouasie-Nouvelle-Guinée',
          'PY' => 'Paraguay',
          'NL' => 'Pays-Bas',
          'PE' => 'Pérou',
          'PH' => 'Philippines',
          'PN' => 'Pitcairn',
          'PL' => 'Pologne',
          'PF' => 'Polynésie Française',
          'PR' => 'Porto Rico',
          'PT' => 'Portugal',
          'QA' => 'Qatar',
          'RE' => 'Réunion',
          'RO' => 'Roumanie',
          'GB' => 'Royaume-Uni',
          'RU' => 'Russie, Fédération De',
          'RW' => 'Rwanda',
          'EH' => 'Sahara Occidental',
          'BL' => 'Saint-Barthélemy',
          'SH' => 'Sainte-Hélène, Ascension Et Tristan Da Cunha',
          'LC' => 'Sainte-Lucie',
          'KN' => 'Saint-Kitts-Et-Nevis',
          'SM' => 'Saint-Marin',
          'MF' => 'Saint-Martin(Partie Française)',
          'SX' => 'Saint-Martin (Partie Néerlandaise)',
          'PM' => 'Saint-Pierre-Et-Miquelon',
          'VA' => 'Saint-Siège (État De La Cité Du Vatican)',
          'VC' => 'Saint-Vincent-Et-Les Grenadines',
          'SB' => 'Salomon, Îles',
          'WS' => 'Samoa',
          'AS' => 'Samoa Américaines',
          'ST' => 'Sao Tomé-Et-Principe',
          'SN' => 'Sénégal',
          'RS' => 'Serbie',
          'SC' => 'Seychelles',
          'SL' => 'Sierra Leone',
          'SG' => 'Singapour',
          'SK' => 'Slovaquie',
          'SI' => 'Slovénie',
          'SO' => 'Somalie',
          'SD' => 'Soudan',
          'SS' => 'Soudan Du Sud',
          'LK' => 'Sri Lanka',
          'SE' => 'Suède',
          'CH' => 'Suisse',
          'SR' => 'Suriname',
          'SJ' => 'Svalbard Et Île Jan Mayen',
          'SZ' => 'Swaziland',
          'SY' => 'Syrienne, République Arabe',
          'TJ' => 'Tadjikistan',
          'TW' => 'Taïwan, Province De Chine',
          'TZ' => 'Tanzanie, République-Unie De',
          'TD' => 'Tchad',
          'CZ' => 'Tchèque, République',
          'TF' => 'Terres Australes Françaises',
          'TH' => 'Thaïlande',
          'TL' => 'Timor-Leste',
          'TG' => 'Togo',
          'TK' => 'Tokelau',
          'TO' => 'Tonga',
          'TT' => 'Trinité-Et-Tobago',
          'TN' => 'Tunisie',
          'TM' => 'Turkménistan',
          'TC' => 'Turks-Et-Caïcos, Îles',
          'TR' => 'Turquie',
          'TV' => 'Tuvalu',
          'UA' => 'Ukraine',
          'UY' => 'Uruguay',
          'VU' => 'Vanuatu',
          'VE' => 'Venezuela, République Bolivarienne Du',
          'VN' => 'Viet Nam',
          'WF' => 'Wallis Et Futuna',
          'YE' => 'Yémen',
          'ZM' => 'Zambie',
          'ZW' => 'Zimbabwe',
        );

        // Sort the list.
        natcasesort($countries);
        return $countries;
      }
}