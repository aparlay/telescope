<?php

namespace Aparlay\Core\Database\Seeders;

use Aparlay\Core\Models\Country;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CountrySeeder extends Seeder
{
    public function run()
    {
        $countries = [
            [
                "name" =>  "Abkhazia",
                "alpha2" => "ab",
                "alpha3" => "abk",
            ],
            [
                "name" => "American Samoa",
                "alpha2" => "as",
                "alpha3" => "asm"
            ],
            [
                "name" => "Anguilla",
                "alpha2" => "ai",
                "alpha3" => "aia"
            ],
            [
                "name" => "Aruba",
                "alpha2" => "aw",
                "alpha3" => "abw"
            ],
            [
                "name" => "Bermuda",
                "alpha2" => "bm",
                "alpha3" => "bmu"
            ],
            [
                "name" => "British Virgin Islands",
                "alpha2" => "vg",
                "alpha3" => "vgb"
            ],
            [
                "name" => "Hong Kong",
                "alpha2" => "hk",
                "alpha3" => "hkg"
            ],
            [
                "name" => "Macao",
                "alpha2" => "mo",
                "alpha3" => "mac"
            ],
            [
                "name" => "Faroe Islands",
                "alpha2" => "fo",
                "alpha3" => "fro"
            ],
            [
                "name" => "French Guiana",
                "alpha2" => "gf",
                "alpha3" => "guf"
            ],
            [
                "name" => "French Polynesia",
                "alpha2" => "pf",
                "alpha3" => "pyf"
            ],
            [
                "name" => "Greenland",
                "alpha2" => "gl",
                "alpha3" => "grl"
            ],
            [
                "name" => "Guadeloupe",
                "alpha2" => "gp",
                "alpha3" => "glp"
            ],
            [
                "name" => "Guam",
                "alpha2" => "gu",
                "alpha3" => "gum"
            ],
            [
                "name" => "Martinique",
                "alpha2" => "mq",
                "alpha3" => "mtq"
            ],
            [
                "name" => "Montserrat",
                "alpha2" => "ms",
                "alpha3" => "msr"
            ],
            [
                "name" => "Netherlands Antilles",
                "alpha2" => "an",
                "alpha3" => "ant"
            ],
            [
                "name" => "New Caledonia",
                "alpha2" => "nc",
                "alpha3" => "ncl"
            ],
            [
                "name" => "Northern Mariana Islands",
                "alpha2" => "mp",
                "alpha3" => "mnp"
            ],
            [
                "name" => "Pitcairn",
                "alpha2" => "pn",
                "alpha3" => "pcn"
            ],
            [
                "name" => "Puerto Rico",
                "alpha2" => "pr",
                "alpha3" => "pri"
            ],
            [
                "name" => "Réunion",
                "alpha2" => "re",
                "alpha3" => "reu"
            ],
            [
                "name" => "Virgin Islands, US",
                "alpha2" => "vi",
                "alpha3" => "vir"
            ],

            [
                'name' => 'Afghanistan',
                'alpha2' => 'af',
                'alpha3' => 'afg',
            ],
            [
                'name' => 'Albania',
                'alpha2' => 'al',
                'alpha3' => 'alb',
            ],
            [
                'name' => 'Algeria',
                'alpha2' => 'dz',
                'alpha3' => 'dza',
            ],
            [
                'name' => 'Andorra',
                'alpha2' => 'ad',
                'alpha3' => 'and',
            ],
            [
                'name' => 'Angola',
                'alpha2' => 'ao',
                'alpha3' => 'ago',
            ],
            [
                'name' => 'Antigua and Barbuda',
                'alpha2' => 'ag',
                'alpha3' => 'atg',
            ],
            [
                'name' => 'Argentina',
                'alpha2' => 'ar',
                'alpha3' => 'arg',
            ],
            [
                'name' => 'Armenia',
                'alpha2' => 'am',
                'alpha3' => 'arm',
            ],
            [
                'name' => 'Australia',
                'alpha2' => 'au',
                'alpha3' => 'aus',
            ],
            [
                'name' => 'Austria',
                'alpha2' => 'at',
                'alpha3' => 'aut',
            ],
            [
                'name' => 'Azerbaijan',
                'alpha2' => 'az',
                'alpha3' => 'aze',
            ],
            [
                'name' => 'Bahamas',
                'alpha2' => 'bs',
                'alpha3' => 'bhs',
            ],
            [
                'name' => 'Bahrain',
                'alpha2' => 'bh',
                'alpha3' => 'bhr',
            ],
            [
                'name' => 'Bangladesh',
                'alpha2' => 'bd',
                'alpha3' => 'bgd',
            ],
            [
                'name' => 'Barbados',
                'alpha2' => 'bb',
                'alpha3' => 'brb',
            ],
            [
                'name' => 'Belarus',
                'alpha2' => 'by',
                'alpha3' => 'blr',
            ],
            [
                'name' => 'Belgium',
                'alpha2' => 'be',
                'alpha3' => 'bel',
            ],
            [
                'name' => 'Belize',
                'alpha2' => 'bz',
                'alpha3' => 'blz',
            ],
            [
                'name' => 'Benin',
                'alpha2' => 'bj',
                'alpha3' => 'ben',
            ],
            [
                'name' => 'Bhutan',
                'alpha2' => 'bt',
                'alpha3' => 'btn',
            ],
            [
                'name' => 'Bolivia (Plurinational State of)',
                'alpha2' => 'bo',
                'alpha3' => 'bol',
            ],
            [
                'name' => 'Bosnia and Herzegovina',
                'alpha2' => 'ba',
                'alpha3' => 'bih',
            ],
            [
                'name' => 'Botswana',
                'alpha2' => 'bw',
                'alpha3' => 'bwa',
            ],
            [
                'name' => 'Brazil',
                'alpha2' => 'br',
                'alpha3' => 'bra',
            ],
            [
                'name' => 'Brunei Darussalam',
                'alpha2' => 'bn',
                'alpha3' => 'brn',
            ],
            [
                'name' => 'Bulgaria',
                'alpha2' => 'bg',
                'alpha3' => 'bgr',
            ],
            [
                'name' => 'Burkina Faso',
                'alpha2' => 'bf',
                'alpha3' => 'bfa',
            ],
            [
                'name' => 'Burundi',
                'alpha2' => 'bi',
                'alpha3' => 'bdi',
            ],
            [
                'name' => 'Cabo Verde',
                'alpha2' => 'cv',
                'alpha3' => 'cpv',
            ],
            [
                'name' => 'Cambodia',
                'alpha2' => 'kh',
                'alpha3' => 'khm',
            ],
            [
                'name' => 'Cameroon',
                'alpha2' => 'cm',
                'alpha3' => 'cmr',
            ],
            [
                'name' => 'Canada',
                'alpha2' => 'ca',
                'alpha3' => 'can',
            ],
            [
                'name' => 'Central African Republic',
                'alpha2' => 'cf',
                'alpha3' => 'caf',
            ],
            [
                'name' => 'Chad',
                'alpha2' => 'td',
                'alpha3' => 'tcd',
            ],
            [
                'name' => 'Chile',
                'alpha2' => 'cl',
                'alpha3' => 'chl',
            ],
            [
                'name' => 'China',
                'alpha2' => 'cn',
                'alpha3' => 'chn',
            ],
            [
                'name' => 'Colombia',
                'alpha2' => 'co',
                'alpha3' => 'col',
            ],
            [
                'name' => 'Comoros',
                'alpha2' => 'km',
                'alpha3' => 'com',
            ],
            [
                'name' => 'Congo',
                'alpha2' => 'cg',
                'alpha3' => 'cog',
            ],
            [
                'name' => 'Congo, Democratic Republic of the',
                'alpha2' => 'cd',
                'alpha3' => 'cod',
            ],
            [
                'name' => 'Costa Rica',
                'alpha2' => 'cr',
                'alpha3' => 'cri',
            ],
            [
                'name' => 'Côte d\'Ivoire',
                'alpha2' => 'ci',
                'alpha3' => 'civ',
            ],
            [
                'name' => 'Croatia',
                'alpha2' => 'hr',
                'alpha3' => 'hrv',
            ],
            [
                'name' => 'Cuba',
                'alpha2' => 'cu',
                'alpha3' => 'cub',
            ],
            [
                'name' => 'Cyprus',
                'alpha2' => 'cy',
                'alpha3' => 'cyp',
            ],
            [
                'name' => 'Czechia',
                'alpha2' => 'cz',
                'alpha3' => 'cze',
            ],
            [
                'name' => 'Denmark',
                'alpha2' => 'dk',
                'alpha3' => 'dnk',
            ],
            [
                'name' => 'Djibouti',
                'alpha2' => 'dj',
                'alpha3' => 'dji',
            ],
            [
                'name' => 'Dominica',
                'alpha2' => 'dm',
                'alpha3' => 'dma',
            ],
            [
                'name' => 'Dominican Republic',
                'alpha2' => 'do',
                'alpha3' => 'dom',
            ],
            [
                'name' => 'Ecuador',
                'alpha2' => 'ec',
                'alpha3' => 'ecu',
            ],
            [
                'name' => 'Egypt',
                'alpha2' => 'eg',
                'alpha3' => 'egy',
            ],
            [
                'name' => 'El Salvador',
                'alpha2' => 'sv',
                'alpha3' => 'slv',
            ],
            [
                'name' => 'Equatorial Guinea',
                'alpha2' => 'gq',
                'alpha3' => 'gnq',
            ],
            [
                'name' => 'Eritrea',
                'alpha2' => 'er',
                'alpha3' => 'eri',
            ],
            [
                'name' => 'Estonia',
                'alpha2' => 'ee',
                'alpha3' => 'est',
            ],
            [
                'name' => 'Eswatini',
                'alpha2' => 'sz',
                'alpha3' => 'swz',
            ],
            [
                'name' => 'Ethiopia',
                'alpha2' => 'et',
                'alpha3' => 'eth',
            ],
            [
                'name' => 'Fiji',
                'alpha2' => 'fj',
                'alpha3' => 'fji',
            ],
            [
                'name' => 'Finland',
                'alpha2' => 'fi',
                'alpha3' => 'fin',
            ],
            [
                'name' => 'France',
                'alpha2' => 'fr',
                'alpha3' => 'fra',
            ],
            [
                'name' => 'Gabon',
                'alpha2' => 'ga',
                'alpha3' => 'gab',
            ],
            [
                'name' => 'Gambia',
                'alpha2' => 'gm',
                'alpha3' => 'gmb',
            ],
            [
                'name' => 'Georgia',
                'alpha2' => 'ge',
                'alpha3' => 'geo',
            ],
            [
                'name' => 'Germany',
                'alpha2' => 'de',
                'alpha3' => 'deu',
            ],
            [
                'name' => 'Ghana',
                'alpha2' => 'gh',
                'alpha3' => 'gha',
            ],
            [
                'name' => 'Greece',
                'alpha2' => 'gr',
                'alpha3' => 'grc',
            ],
            [
                'name' => 'Grenada',
                'alpha2' => 'gd',
                'alpha3' => 'grd',
            ],
            [
                'name' => 'Guatemala',
                'alpha2' => 'gt',
                'alpha3' => 'gtm',
            ],
            [
                'name' => 'Guinea',
                'alpha2' => 'gn',
                'alpha3' => 'gin',
            ],
            [
                'name' => 'Guinea-Bissau',
                'alpha2' => 'gw',
                'alpha3' => 'gnb',
            ],
            [
                'name' => 'Guyana',
                'alpha2' => 'gy',
                'alpha3' => 'guy',
            ],
            [
                'name' => 'Haiti',
                'alpha2' => 'ht',
                'alpha3' => 'hti',
            ],
            [
                'name' => 'Honduras',
                'alpha2' => 'hn',
                'alpha3' => 'hnd',
            ],
            [
                'name' => 'Hungary',
                'alpha2' => 'hu',
                'alpha3' => 'hun',
            ],
            [
                'name' => 'Iceland',
                'alpha2' => 'is',
                'alpha3' => 'isl',
            ],
            [
                'name' => 'India',
                'alpha2' => 'in',
                'alpha3' => 'ind',
            ],
            [
                'name' => 'Indonesia',
                'alpha2' => 'id',
                'alpha3' => 'idn',
            ],
            [
                'name' => 'Iran (Islamic Republic of)',
                'alpha2' => 'ir',
                'alpha3' => 'irn',
            ],
            [
                'name' => 'Iraq',
                'alpha2' => 'iq',
                'alpha3' => 'irq',
            ],
            [
                'name' => 'Ireland',
                'alpha2' => 'ie',
                'alpha3' => 'irl',
            ],
            [
                'name' => 'Israel',
                'alpha2' => 'il',
                'alpha3' => 'isr',
            ],
            [
                'name' => 'Italy',
                'alpha2' => 'it',
                'alpha3' => 'ita',
            ],
            [
                'name' => 'Jamaica',
                'alpha2' => 'jm',
                'alpha3' => 'jam',
            ],
            [
                'name' => 'Japan',
                'alpha2' => 'jp',
                'alpha3' => 'jpn',
            ],
            [
                'name' => 'Jordan',
                'alpha2' => 'jo',
                'alpha3' => 'jor',
            ],
            [
                'name' => 'Kazakhstan',
                'alpha2' => 'kz',
                'alpha3' => 'kaz',
            ],
            [
                'name' => 'Kenya',
                'alpha2' => 'ke',
                'alpha3' => 'ken',
            ],
            [
                'name' => 'Kiribati',
                'alpha2' => 'ki',
                'alpha3' => 'kir',
            ],
            [
                'name' => 'Korea (Democratic People\'s Republic of)',
                'alpha2' => 'kp',
                'alpha3' => 'prk',
            ],
            [
                'name' => 'Korea, Republic of',
                'alpha2' => 'kr',
                'alpha3' => 'kor',
            ],
            [
                'name' => 'Kuwait',
                'alpha2' => 'kw',
                'alpha3' => 'kwt',
            ],
            [
                'name' => 'Kyrgyzstan',
                'alpha2' => 'kg',
                'alpha3' => 'kgz',
            ],
            [
                'name' => 'Lao People\'s Democratic Republic',
                'alpha2' => 'la',
                'alpha3' => 'lao',
            ],
            [
                'name' => 'Latvia',
                'alpha2' => 'lv',
                'alpha3' => 'lva',
            ],
            [
                'name' => 'Lebanon',
                'alpha2' => 'lb',
                'alpha3' => 'lbn',
            ],
            [
                'name' => 'Lesotho',
                'alpha2' => 'ls',
                'alpha3' => 'lso',
            ],
            [
                'name' => 'Liberia',
                'alpha2' => 'lr',
                'alpha3' => 'lbr',
            ],
            [
                'name' => 'Libya',
                'alpha2' => 'ly',
                'alpha3' => 'lby',
            ],
            [
                'name' => 'Liechtenstein',
                'alpha2' => 'li',
                'alpha3' => 'lie',
            ],
            [
                'name' => 'Lithuania',
                'alpha2' => 'lt',
                'alpha3' => 'ltu',
            ],
            [
                'name' => 'Luxembourg',
                'alpha2' => 'lu',
                'alpha3' => 'lux',
            ],
            [
                'name' => 'Madagascar',
                'alpha2' => 'mg',
                'alpha3' => 'mdg',
            ],
            [
                'name' => 'Malawi',
                'alpha2' => 'mw',
                'alpha3' => 'mwi',
            ],
            [
                'name' => 'Malaysia',
                'alpha2' => 'my',
                'alpha3' => 'mys',
            ],
            [
                'name' => 'Maldives',
                'alpha2' => 'mv',
                'alpha3' => 'mdv',
            ],
            [
                'name' => 'Mali',
                'alpha2' => 'ml',
                'alpha3' => 'mli',
            ],
            [
                'name' => 'Malta',
                'alpha2' => 'mt',
                'alpha3' => 'mlt',
            ],
            [
                'name' => 'Marshall Islands',
                'alpha2' => 'mh',
                'alpha3' => 'mhl',
            ],
            [
                'name' => 'Mauritania',
                'alpha2' => 'mr',
                'alpha3' => 'mrt',
            ],
            [
                'name' => 'Mauritius',
                'alpha2' => 'mu',
                'alpha3' => 'mus',
            ],
            [
                'name' => 'Mexico',
                'alpha2' => 'mx',
                'alpha3' => 'mex',
            ],
            [
                'name' => 'Micronesia (Federated States of)',
                'alpha2' => 'fm',
                'alpha3' => 'fsm',
            ],
            [
                'name' => 'Moldova, Republic of',
                'alpha2' => 'md',
                'alpha3' => 'mda',
            ],
            [
                'name' => 'Monaco',
                'alpha2' => 'mc',
                'alpha3' => 'mco',
            ],
            [
                'name' => 'Mongolia',
                'alpha2' => 'mn',
                'alpha3' => 'mng',
            ],
            [
                'name' => 'Montenegro',
                'alpha2' => 'me',
                'alpha3' => 'mne',
            ],
            [
                'name' => 'Morocco',
                'alpha2' => 'ma',
                'alpha3' => 'mar',
            ],
            [
                'name' => 'Mozambique',
                'alpha2' => 'mz',
                'alpha3' => 'moz',
            ],
            [
                'name' => 'Myanmar',
                'alpha2' => 'mm',
                'alpha3' => 'mmr',
            ],
            [
                'name' => 'Namibia',
                'alpha2' => 'na',
                'alpha3' => 'nam',
            ],
            [
                'name' => 'Nauru',
                'alpha2' => 'nr',
                'alpha3' => 'nru',
            ],
            [
                'name' => 'Nepal',
                'alpha2' => 'np',
                'alpha3' => 'npl',
            ],
            [
                'name' => 'Netherlands',
                'alpha2' => 'nl',
                'alpha3' => 'nld',
            ],
            [
                'name' => 'New Zealand',
                'alpha2' => 'nz',
                'alpha3' => 'nzl',
            ],
            [
                'name' => 'Nicaragua',
                'alpha2' => 'ni',
                'alpha3' => 'nic',
            ],
            [
                'name' => 'Niger',
                'alpha2' => 'ne',
                'alpha3' => 'ner',
            ],
            [
                'name' => 'Nigeria',
                'alpha2' => 'ng',
                'alpha3' => 'nga',
            ],
            [
                'name' => 'North Macedonia',
                'alpha2' => 'mk',
                'alpha3' => 'mkd',
            ],
            [
                'name' => 'Norway',
                'alpha2' => 'no',
                'alpha3' => 'nor',
            ],
            [
                'name' => 'Oman',
                'alpha2' => 'om',
                'alpha3' => 'omn',
            ],
            [
                'name' => 'Pakistan',
                'alpha2' => 'pk',
                'alpha3' => 'pak',
            ],
            [
                'name' => 'Palau',
                'alpha2' => 'pw',
                'alpha3' => 'plw',
            ],
            [
                'name' => 'Panama',
                'alpha2' => 'pa',
                'alpha3' => 'pan',
            ],
            [
                'name' => 'Papua New Guinea',
                'alpha2' => 'pg',
                'alpha3' => 'png',
            ],
            [
                'name' => 'Paraguay',
                'alpha2' => 'py',
                'alpha3' => 'pry',
            ],
            [
                'name' => 'Peru',
                'alpha2' => 'pe',
                'alpha3' => 'per',
            ],
            [
                'name' => 'Philippines',
                'alpha2' => 'ph',
                'alpha3' => 'phl',
            ],
            [
                'name' => 'Poland',
                'alpha2' => 'pl',
                'alpha3' => 'pol',
            ],
            [
                'name' => 'Portugal',
                'alpha2' => 'pt',
                'alpha3' => 'prt',
            ],
            [
                'name' => 'Qatar',
                'alpha2' => 'qa',
                'alpha3' => 'qat',
            ],
            [
                'name' => 'Romania',
                'alpha2' => 'ro',
                'alpha3' => 'rou',
            ],
            [
                'name' => 'Russian Federation',
                'alpha2' => 'ru',
                'alpha3' => 'rus',
            ],
            [
                'name' => 'Rwanda',
                'alpha2' => 'rw',
                'alpha3' => 'rwa',
            ],
            [
                'name' => 'Saint Kitts and Nevis',
                'alpha2' => 'kn',
                'alpha3' => 'kna',
            ],
            [
                'name' => 'Saint Lucia',
                'alpha2' => 'lc',
                'alpha3' => 'lca',
            ],
            [
                'name' => 'Saint Vincent and the Grenadines',
                'alpha2' => 'vc',
                'alpha3' => 'vct',
            ],
            [
                'name' => 'Samoa',
                'alpha2' => 'ws',
                'alpha3' => 'wsm',
            ],
            [
                'name' => 'San Marino',
                'alpha2' => 'sm',
                'alpha3' => 'smr',
            ],
            [
                'name' => 'Sao Tome and Principe',
                'alpha2' => 'st',
                'alpha3' => 'stp',
            ],
            [
                'name' => 'Saudi Arabia',
                'alpha2' => 'sa',
                'alpha3' => 'sau',
            ],
            [
                'name' => 'Senegal',
                'alpha2' => 'sn',
                'alpha3' => 'sen',
            ],
            [
                'name' => 'Serbia',
                'alpha2' => 'rs',
                'alpha3' => 'srb',
            ],
            [
                'name' => 'Seychelles',
                'alpha2' => 'sc',
                'alpha3' => 'syc',
            ],
            [
                'name' => 'Sierra Leone',
                'alpha2' => 'sl',
                'alpha3' => 'sle',
            ],
            [
                'name' => 'Singapore',
                'alpha2' => 'sg',
                'alpha3' => 'sgp',
            ],
            [
                'name' => 'Slovakia',
                'alpha2' => 'sk',
                'alpha3' => 'svk',
            ],
            [
                'name' => 'Slovenia',
                'alpha2' => 'si',
                'alpha3' => 'svn',
            ],
            [
                'name' => 'Solomon Islands',
                'alpha2' => 'sb',
                'alpha3' => 'slb',
            ],
            [
                'name' => 'Somalia',
                'alpha2' => 'so',
                'alpha3' => 'som',
            ],
            [
                'name' => 'South Africa',
                'alpha2' => 'za',
                'alpha3' => 'zaf',
            ],
            [
                'name' => 'South Sudan',
                'alpha2' => 'ss',
                'alpha3' => 'ssd',
            ],
            [
                'name' => 'Spain',
                'alpha2' => 'es',
                'alpha3' => 'esp',
            ],
            [
                'name' => 'Sri Lanka',
                'alpha2' => 'lk',
                'alpha3' => 'lka',
            ],
            [
                'name' => 'Sudan',
                'alpha2' => 'sd',
                'alpha3' => 'sdn',
            ],
            [
                'name' => 'Suriname',
                'alpha2' => 'sr',
                'alpha3' => 'sur',
            ],
            [
                'name' => 'Sweden',
                'alpha2' => 'se',
                'alpha3' => 'swe',
            ],
            [
                'name' => 'Switzerland',
                'alpha2' => 'ch',
                'alpha3' => 'che',
            ],
            [
                'name' => 'Syrian Arab Republic',
                'alpha2' => 'sy',
                'alpha3' => 'syr',
            ],
            [
                'name' => 'Tajikistan',
                'alpha2' => 'tj',
                'alpha3' => 'tjk',
            ],
            [
                'name' => 'Tanzania, United Republic of',
                'alpha2' => 'tz',
                'alpha3' => 'tza',
            ],
            [
                'name' => 'Thailand',
                'alpha2' => 'th',
                'alpha3' => 'tha',
            ],
            [
                'name' => 'Timor-Leste',
                'alpha2' => 'tl',
                'alpha3' => 'tls',
            ],
            [
                'name' => 'Togo',
                'alpha2' => 'tg',
                'alpha3' => 'tgo',
            ],
            [
                'name' => 'Tonga',
                'alpha2' => 'to',
                'alpha3' => 'ton',
            ],
            [
                'name' => 'Trinidad and Tobago',
                'alpha2' => 'tt',
                'alpha3' => 'tto',
            ],
            [
                'name' => 'Tunisia',
                'alpha2' => 'tn',
                'alpha3' => 'tun',
            ],
            [
                'name' => 'Turkey',
                'alpha2' => 'tr',
                'alpha3' => 'tur',
            ],
            [
                'name' => 'Turkmenistan',
                'alpha2' => 'tm',
                'alpha3' => 'tkm',
            ],
            [
                'name' => 'Tuvalu',
                'alpha2' => 'tv',
                'alpha3' => 'tuv',
            ],
            [
                'name' => 'Uganda',
                'alpha2' => 'ug',
                'alpha3' => 'uga',
            ],
            [
                'name' => 'Ukraine',
                'alpha2' => 'ua',
                'alpha3' => 'ukr',
            ],
            [
                'name' => 'United Arab Emirates',
                'alpha2' => 'ae',
                'alpha3' => 'are',
            ],
            [
                'name' => 'United Kingdom of Great Britain and Northern Ireland',
                'alpha2' => 'gb',
                'alpha3' => 'gbr',
            ],
            [
                'name' => 'United States of America',
                'alpha2' => 'us',
                'alpha3' => 'usa',
            ],
            [
                'name' => 'Uruguay',
                'alpha2' => 'uy',
                'alpha3' => 'ury',
            ],
            [
                'name' => 'Uzbekistan',
                'alpha2' => 'uz',
                'alpha3' => 'uzb',
            ],
            [
                'name' => 'Vanuatu',
                'alpha2' => 'vu',
                'alpha3' => 'vut',
            ],
            [
                'name' => 'Venezuela (Bolivarian Republic of)',
                'alpha2' => 've',
                'alpha3' => 'ven',
            ],
            [
                'name' => 'Viet Nam',
                'alpha2' => 'vn',
                'alpha3' => 'vnm',
            ],
            [
                'name' => 'Yemen',
                'alpha2' => 'ye',
                'alpha3' => 'yem',
            ],
            [
                'name' => 'Zambia',
                'alpha2' => 'zm',
                'alpha3' => 'zmb',
            ],
            [
                'name' => 'Zimbabwe',
                'alpha2' => 'zw',
                'alpha3' => 'zwe',
            ],
        ];

        $locations = [
            'AD,42.546245,1.601554,Andorra',
            'AE,23.424076,53.847818,United Arab Emirates',
            'AB,33.93911,67.709953,Abkhazia',
            'AF,33.93911,67.709953,Afghanistan',
            'AG,17.060816,-61.796428,Antigua and Barbuda',
            'AI,18.220554,-63.068615,Anguilla',
            'AL,41.153332,20.168331,Albania',
            'AM,40.069099,45.038189,Armenia',
            'AN,12.226079,-69.060087,Netherlands Antilles',
            'AO,-11.202692,17.873887,Angola',
            'AQ,-75.250973,-0.071389,Antarctica',
            'AR,-38.416097,-63.616672,Argentina',
            'AS,-14.270972,-170.132217,American Samoa',
            'AT,47.516231,14.550072,Austria',
            'AU,-25.274398,133.775136,Australia',
            'AW,12.52111,-69.968338,Aruba',
            'AZ,40.143105,47.576927,Azerbaijan',
            'BA,43.915886,17.679076,Bosnia and Herzegovina',
            'BB,13.193887,-59.543198,Barbados',
            'BD,23.684994,90.356331,Bangladesh',
            'BE,50.503887,4.469936,Belgium',
            'BF,12.238333,-1.561593,Burkina Faso',
            'BG,42.733883,25.48583,Bulgaria',
            'BH,25.930414,50.637772,Bahrain',
            'BI,-3.373056,29.918886,Burundi',
            'BJ,9.30769,2.315834,Benin',
            'BM,32.321384,-64.75737,Bermuda',
            'BN,4.535277,114.727669,Brunei',
            'BO,-16.290154,-63.588653,Bolivia',
            'BR,-14.235004,-51.92528,Brazil',
            'BS,25.03428,-77.39628,Bahamas',
            'BT,27.514162,90.433601,Bhutan',
            'BV,-54.423199,3.413194,Bouvet Island',
            'BW,-22.328474,24.684866,Botswana',
            'BY,53.709807,27.953389,Belarus',
            'BZ,17.189877,-88.49765,Belize',
            'CA,56.130366,-106.346771,Canada',
            'CC,-12.164165,96.870956,Cocos [Keeling] Islands',
            'CD,-4.038333,21.758664,Congo [DRC]',
            'CF,6.611111,20.939444,Central African Republic',
            'CG,-0.228021,15.827659,Congo [Republic]',
            'CH,46.818188,8.227512,Switzerland',
            "CI,7.539989,-5.54708,Côte d'Ivoire",
            'CK,-21.236736,-159.777671,Cook Islands',
            'CL,-35.675147,-71.542969,Chile',
            'CM,7.369722,12.354722,Cameroon',
            'CN,35.86166,104.195397,China',
            'CO,4.570868,-74.297333,Colombia',
            'CR,9.748917,-83.753428,Costa Rica',
            'CU,21.521757,-77.781167,Cuba',
            'CV,16.002082,-24.013197,Cape Verde',
            'CX,-10.447525,105.690449,Christmas Island',
            'CY,35.126413,33.429859,Cyprus',
            'CZ,49.817492,15.472962,Czech Republic',
            'DE,51.165691,10.451526,Germany',
            'DJ,11.825138,42.590275,Djibouti',
            'DK,56.26392,9.501785,Denmark',
            'DM,15.414999,-61.370976,Dominica',
            'DO,18.735693,-70.162651,Dominican Republic',
            'DZ,28.033886,1.659626,Algeria',
            'EC,-1.831239,-78.183406,Ecuador',
            'EE,58.595272,25.013607,Estonia',
            'EG,26.820553,30.802498,Egypt',
            'EH,24.215527,-12.885834,Western Sahara',
            'ER,15.179384,39.782334,Eritrea',
            'ES,40.463667,-3.74922,Spain',
            'ET,9.145,40.489673,Ethiopia',
            'FI,61.92411,25.748151,Finland',
            'FJ,-16.578193,179.414413,Fiji',
            'FK,-51.796253,-59.523613,Falkland Islands [Islas Malvinas]',
            'FM,7.425554,150.550812,Micronesia',
            'FO,61.892635,-6.911806,Faroe Islands',
            'FR,46.227638,2.213749,France',
            'GA,-0.803689,11.609444,Gabon',
            'GB,55.378051,-3.435973,United Kingdom',
            'GD,12.262776,-61.604171,Grenada',
            'GE,42.315407,43.356892,Georgia',
            'GF,3.933889,-53.125782,French Guiana',
            'GG,49.465691,-2.585278,Guernsey',
            'GH,7.946527,-1.023194,Ghana',
            'GI,36.137741,-5.345374,Gibraltar',
            'GL,71.706936,-42.604303,Greenland',
            'GM,13.443182,-15.310139,Gambia',
            'GN,9.945587,-9.696645,Guinea',
            'GP,16.995971,-62.067641,Guadeloupe',
            'GQ,1.650801,10.267895,Equatorial Guinea',
            'GR,39.074208,21.824312,Greece',
            'GS,-54.429579,-36.587909,South Georgia and the South Sandwich Islands',
            'GT,15.783471,-90.230759,Guatemala',
            'GU,13.444304,144.793731,Guam',
            'GW,11.803749,-15.180413,Guinea-Bissau',
            'GY,4.860416,-58.93018,Guyana',
            'GZ,31.354676,34.308825,Gaza Strip',
            'HK,22.396428,114.109497,Hong Kong',
            'HM,-53.08181,73.504158,Heard Island and McDonald Islands',
            'HN,15.199999,-86.241905,Honduras',
            'HR,45.1,15.2,Croatia',
            'HT,18.971187,-72.285215,Haiti',
            'HU,47.162494,19.503304,Hungary',
            'ID,-0.789275,113.921327,Indonesia',
            'IE,53.41291,-8.24389,Ireland',
            'IL,31.046051,34.851612,Israel',
            'IM,54.236107,-4.548056,Isle of Man',
            'IN,20.593684,78.96288,India',
            'IO,-6.343194,71.876519,British Indian Ocean Territory',
            'IQ,33.223191,43.679291,Iraq',
            'IR,32.427908,53.688046,Iran',
            'IS,64.963051,-19.020835,Iceland',
            'IT,41.87194,12.56738,Italy',
            'JE,49.214439,-2.13125,Jersey',
            'JM,18.109581,-77.297508,Jamaica',
            'JO,30.585164,36.238414,Jordan',
            'JP,36.204824,138.252924,Japan',
            'KE,-0.023559,37.906193,Kenya',
            'KG,41.20438,74.766098,Kyrgyzstan',
            'KH,12.565679,104.990963,Cambodia',
            'KI,-3.370417,-168.734039,Kiribati',
            'KM,-11.875001,43.872219,Comoros',
            'KN,17.357822,-62.782998,Saint Kitts and Nevis',
            'KP,40.339852,127.510093,North Korea',
            'KR,35.907757,127.766922,South Korea',
            'KW,29.31166,47.481766,Kuwait',
            'KY,19.513469,-80.566956,Cayman Islands',
            'KZ,48.019573,66.923684,Kazakhstan',
            'LA,19.85627,102.495496,Laos',
            'LB,33.854721,35.862285,Lebanon',
            'LC,13.909444,-60.978893,Saint Lucia',
            'LI,47.166,9.555373,Liechtenstein',
            'LK,7.873054,80.771797,Sri Lanka',
            'LR,6.428055,-9.429499,Liberia',
            'LS,-29.609988,28.233608,Lesotho',
            'LT,55.169438,23.881275,Lithuania',
            'LU,49.815273,6.129583,Luxembourg',
            'LV,56.879635,24.603189,Latvia',
            'LY,26.3351,17.228331,Libya',
            'MA,31.791702,-7.09262,Morocco',
            'MC,43.750298,7.412841,Monaco',
            'MD,47.411631,28.369885,Moldova',
            'ME,42.708678,19.37439,Montenegro',
            'MG,-18.766947,46.869107,Madagascar',
            'MH,7.131474,171.184478,Marshall Islands',
            'MK,41.608635,21.745275,Macedonia [FYROM]',
            'ML,17.570692,-3.996166,Mali',
            'MM,21.913965,95.956223,Myanmar [Burma]',
            'MN,46.862496,103.846656,Mongolia',
            'MO,22.198745,113.543873,Macau',
            'MP,17.33083,145.38469,Northern Mariana Islands',
            'MQ,14.641528,-61.024174,Martinique',
            'MR,21.00789,-10.940835,Mauritania',
            'MS,16.742498,-62.187366,Montserrat',
            'MT,35.937496,14.375416,Malta',
            'MU,-20.348404,57.552152,Mauritius',
            'MV,3.202778,73.22068,Maldives',
            'MW,-13.254308,34.301525,Malawi',
            'MX,23.634501,-102.552784,Mexico',
            'MY,4.210484,101.975766,Malaysia',
            'MZ,-18.665695,35.529562,Mozambique',
            'NA,-22.95764,18.49041,Namibia',
            'NC,-20.904305,165.618042,New Caledonia',
            'NE,17.607789,8.081666,Niger',
            'NF,-29.040835,167.954712,Norfolk Island',
            'NG,9.081999,8.675277,Nigeria',
            'NI,12.865416,-85.207229,Nicaragua',
            'NL,52.132633,5.291266,Netherlands',
            'NO,60.472024,8.468946,Norway',
            'NP,28.394857,84.124008,Nepal',
            'NR,-0.522778,166.931503,Nauru',
            'NU,-19.054445,-169.867233,Niue',
            'NZ,-40.900557,174.885971,New Zealand',
            'OM,21.512583,55.923255,Oman',
            'PA,8.537981,-80.782127,Panama',
            'PE,-9.189967,-75.015152,Peru',
            'PF,-17.679742,-149.406843,French Polynesia',
            'PG,-6.314993,143.95555,Papua New Guinea',
            'PH,12.879721,121.774017,Philippines',
            'PK,30.375321,69.345116,Pakistan',
            'PL,51.919438,19.145136,Poland',
            'PM,46.941936,-56.27111,Saint Pierre and Miquelon',
            'PN,-24.703615,-127.439308,Pitcairn Islands',
            'PR,18.220833,-66.590149,Puerto Rico',
            'PS,31.952162,35.233154,Palestinian Territories',
            'PT,39.399872,-8.224454,Portugal',
            'PW,7.51498,134.58252,Palau',
            'PY,-23.442503,-58.443832,Paraguay',
            'QA,25.354826,51.183884,Qatar',
            'RE,-21.115141,55.536384,Réunion',
            'RO,45.943161,24.96676,Romania',
            'RS,44.016521,21.005859,Serbia',
            'RU,61.52401,105.318756,Russia',
            'RW,-1.940278,29.873888,Rwanda',
            'SA,23.885942,45.079162,Saudi Arabia',
            'SB,-9.64571,160.156194,Solomon Islands',
            'SC,-4.679574,55.491977,Seychelles',
            'SD,12.862807,30.217636,Sudan',
            'SE,60.128161,18.643501,Sweden',
            'SG,1.352083,103.819836,Singapore',
            'SH,-24.143474,-10.030696,Saint Helena',
            'SI,46.151241,14.995463,Slovenia',
            'SJ,77.553604,23.670272,Svalbard and Jan Mayen',
            'SK,48.669026,19.699024,Slovakia',
            'SL,8.460555,-11.779889,Sierra Leone',
            'SM,43.94236,12.457777,San Marino',
            'SN,14.497401,-14.452362,Senegal',
            'SO,5.152149,46.199616,Somalia',
            'SR,3.919305,-56.027783,Suriname',
            'ST,0.18636,6.613081,São Tomé and Príncipe',
            'SV,13.794185,-88.89653,El Salvador',
            'SY,34.802075,38.996815,Syria',
            'SZ,-26.522503,31.465866,Swaziland',
            'TC,21.694025,-71.797928,Turks and Caicos Islands',
            'TD,15.454166,18.732207,Chad',
            'TF,-49.280366,69.348557,French Southern Territories',
            'TG,8.619543,0.824782,Togo',
            'TH,15.870032,100.992541,Thailand',
            'TJ,38.861034,71.276093,Tajikistan',
            'TK,-8.967363,-171.855881,Tokelau',
            'TL,-8.874217,125.727539,Timor-Leste',
            'TM,38.969719,59.556278,Turkmenistan',
            'TN,33.886917,9.537499,Tunisia',
            'TO,-21.178986,-175.198242,Tonga',
            'TR,38.963745,35.243322,Turkey',
            'TT,10.691803,-61.222503,Trinidad and Tobago',
            'TV,-7.109535,177.64933,Tuvalu',
            'TW,23.69781,120.960515,Taiwan',
            'TZ,-6.369028,34.888822,Tanzania',
            'UA,48.379433,31.16558,Ukraine',
            'UG,1.373333,32.290275,Uganda',
            'UM,,,U.S. Minor Outlying Islands',
            'US,37.09024,-95.712891,United States',
            'UY,-32.522779,-55.765835,Uruguay',
            'UZ,41.377491,64.585262,Uzbekistan',
            'VA,41.902916,12.453389,Vatican City',
            'VC,12.984305,-61.287228,Saint Vincent and the Grenadines',
            'VE,6.42375,-66.58973,Venezuela',
            'VG,18.420695,-64.639968,British Virgin Islands',
            'VI,18.335765,-64.896335,U.S. Virgin Islands',
            'VN,14.058324,108.277199,Vietnam',
            'VU,-15.376706,166.959158,Vanuatu',
            'WF,-13.768752,-177.156097,Wallis and Futuna',
            'WS,-13.759029,-172.104629,Samoa',
            'XK,42.602636,20.902977,Kosovo',
            'YE,15.552727,48.516388,Yemen',
            'YT,-12.8275,45.166244,Mayotte',
            'ZA,-30.559482,22.937506,South Africa',
            'ZM,-13.133897,27.849332,Zambia',
            'ZW,-19.015438,29.154857,Zimbabwe',
        ];

        foreach ($locations as $location) {
            $location = explode(',', $location);
            foreach ($countries as $index => $country) {
                if ($country['alpha2'] == Str::lower($location[0])) {
                    $countries[$index]['location'] = ['type' => 'Point', 'coordinates' => [(float) $location[2], (float) $location[1]]];
                }
            }
        }

        $flags = [
            '16' => json_decode(file_get_contents(database_path().'/json/flags/16x16.json'), true),
            '24' => json_decode(file_get_contents(database_path().'/json/flags/24x24.json'), true),
            '32' => json_decode(file_get_contents(database_path().'/json/flags/32x32.json'), true),
            '48' => json_decode(file_get_contents(database_path().'/json/flags/48x48.json'), true),
            '64' => json_decode(file_get_contents(database_path().'/json/flags/64x64.json'), true),
            '128' => json_decode(file_get_contents(database_path().'/json/flags/128x128.json'), true),
        ];

        foreach ($countries as $index => $country) {
            $countries[$index]['flags'] = [
                '16' => $flags['16'][$country['alpha2']],
                '24' => $flags['24'][$country['alpha2']],
                '32' => $flags['32'][$country['alpha2']],
                '48' => $flags['48'][$country['alpha2']],
                '64' => $flags['64'][$country['alpha2']],
                '128' => $flags['128'][$country['alpha2']],
            ];
        }

        foreach ($countries as $country) {
            $countryExist = Country::query()->where('alpha3', $country['alpha3'])->first();
            if (!$countryExist) {
                Country::create($country);
                echo "Country was created " . $country['name'] . "\n\r";
            }
        }
    }
}
