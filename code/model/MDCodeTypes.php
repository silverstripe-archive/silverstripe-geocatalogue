<?php
/**
 *
 * @author Rainer Spittel (rainer at silverstripe dot com)
 * @package geocatalog
 * @subpackage model
 */
class MDCodeTypes extends Object {

	static $default_for_null_value = 'Not Available';

	static $MDOnlineResourceFunction = array(
//		"" => "",
		"download" => "Download",
		"information" => "Information",
		"offlineAccess" => "Offline access",
		"order" => "Order",
		"search" => "Search"
	);

	static $MDResourceFormat = array(
		"" => "(please select a format)",
		"ESRI Shapefile|" 				=> "ESRI Shapefile (including .shp)",
		"MapInfo TAB format|"			=> "MapInfo TAB format (.tab)",
		"MapInfo Interchange Format|"	=> "MapInfo Interchange Format (.mif)",
		"TIFF|"							=> "TIFF (.tiff/.tif)",
		"GeoTIFF|"						=> "GeoTIFF (.tiff/.tif)",
		"Text file|"					=> "Text file (.txt)",
		"Database file|"				=> "Database file (.dbf)",
		"Comma Separated File|" 		=> "Comma Separated File (.csv)",
		"AutoCAD DWG|"					=> "DWG (.dwg)",
		"JPEG/GeoJPEG|"					=> "JPEG/GeoJPEG (.jpg)",
		"KML|"							=> "KML (.kml)",
		"KMZ|"							=> "KMZ (.kmz)",
		"HTTP|"					=> "HTTP",
		"Paper copy|"					=> "Paper copy",
		"Physical specimen|"  			=> "Physical specimen",
		"Other|"  			=> "Other",

	);
	
	static $MDCategory = array(
		"biota" => "Biota",
		"boundaries" => "Boundaries",
		"climatologyMeteorologyAtmosphere" => "Climatology, meteorology, atmosphere",
		"economy" => "Economy",
		"elevation" => "Elevation",
		"environment" => "Environment",
		"farming" => "Farming",
		"geoscientificInformation" => "Geoscientific information",
		"health" => "Health",
		"imageryBaseMapsEarthCover" => "Imagery base maps earth cover",
		"inlandWaters" => "Inland waters",
		"intelligenceMilitary" => "Intelligence military",
		"location" => "Location",
		"oceans" => "Oceans",
		"planningCadastre" => "Planning cadastre",
		"society" => "Society",
		"structure" => "Structure",
		"transportation" => "Transportation",
		"utilitiesCommunication" => "Utilities communication"
	);
	
	static $MDPlaces = array (
		"0;0;0;0" => "Custom Location",
		"60.50417;74.91574;29.40611;38.47198" => "Afghanistan",
		"-17.3;51.1;-34.6;38.2" => "Africa",
		"19.28854;21.05333;39.645;42.66034" => "Albania",
		"-8.66722;11.98648;18.97639;37.08986" => "Algeria",
		"-180;180;-65;65" => "All fishing areas",
		"-170.82323;-170.56187;-14.37556;-14.25431" => "American Samoa",
		"1.42139;1.78172;42.43639;42.65597" => "Andorra",
		"11.73125;24.08444;-18.01639;-4.38899" => "Angola",
		"-63.16778;-62.97271;18.16444;18.27298" => "Anguilla",
		"-180;180;-90;-60.50333" => "Antarctica",
		"-61.89111;-61.66695;16.98972;17.7243" => "Antigua and Barbuda",
		"-180;180;48;90" => "Arctic Sea",
		"-73.5823;-53.65001;-55.05167;-21.78052" => "Argentina",
		"43.45416;46.62054;38.84115;41.29705" => "Armenia",
		"-70.05966;-69.87486;12.41111;12.62778" => "Aruba",
		"31;179.9;10.8;83.5" => "Asia",
		"-68.5052;-68.3546;-78.1136;-50" => "Atlantic, Antarctic",
		"-16.43443;-16;-6.1321;36" => "Atlantic, Eastern Central",
		"55;-27.34081;36;90" => "Atlantic, Northeast",
		"58.20129;-84.35833;34.95;78.17" => "Atlantic, Northwest",
		"-20;30;-50;-6" => "Atlantic, Southeast",
		"-69.6308;-20;-60;5" => "Atlantic, Southwest",
		"-97.8097;-40;5;36" => "Atlantic, Western Central",
		"112.90721;158.96037;-54.75389;-10.1357" => "Australia",
		"9.53357;17.16639;46.40749;49.01875" => "Austria",
		"44.77886;50.37499;38.38915;41.89706" => "Azerbaijan, Republic of",
		"-78.9789;-72.73889;20.91528;26.92917" => "Bahamas",
		"50.45333;50.79639;25.57194;26.28889" => "Bahrain",
		"88.04387;92.66934;20.74482;26.62614" => "Bangladesh",
		"-59.65945;-59.42709;13.05055;13.33708" => "Barbados",
		"23.1654;32.74006;51.25185;56.16749" => "Belarus",
		"2.54167;6.3982;49.50888;51.50125" => "Belgium",
		"-89.2164;-87.77959;15.88985;18.4899" => "Belize",
		"0.77667;3.855;6.21872;12.39666" => "Benin",
		"-64.82306;-64.67681;32.26055;32.37951" => "Bermuda",
		"88.75194;92.11422;26.70361;28.325" => "Bhutan",
		"-69.65619;-57.52112;-22.90111;-9.6792" => "Bolivia",
		"15.74059;19.61979;42.56583;45.26595" => "Bosnia and Herzegovina",
		"19.99611;29.37362;-26.87556;-17.78209" => "Botswana",
		"3.34236;3.48417;-54.46278;-54.38361" => "Bouvet Island",
		"-74.00459;-34.79292;-33.74112;5.27271" => "Brazil",
		"72.3579;72.49429;-7.43625;-7.23347" => "British Indian Ocean Ter",
		"-64.69848;-64.32452;18.38389;18.50486" => "British Virgin Island",
		"114.09508;115.36026;4.01819;5.05305" => "Brunei Darussalam",
		"22.36528;28.60514;41.24305;44.22472" => "Bulgaria",
		"-5.52083;2.39792;9.39569;15.08278" => "Burkina Faso",
		"28.985;30.85319;-4.44806;-2.30156" => "Burundi",
		"102.3465;107.63638;10.42274;14.70862" => "Cambodia",
		"8.50236;16.20701;1.65417;13.085" => "Cameroon",
		"-141.00299;-52.61736;41.67555;83.11388" => "Canada",
		"-25.36056;-22.66611;14.81111;17.19236" => "Cape Verde",
		"-88.81;-56.83;8.11;29.35" => "Caribbean",
		"-81.40084;-81.09306;19.265;19.35416" => "Cayman Island",
		"-9.12;46.71;-19.36;25.16" => "Central Africa",
		"14.41889;27.45972;2.22126;11.00083" => "Central African Republic",
		"-120.17;-74.83;3.21;36.91" => "Central America",
		"13.46194;24.00275;7.45854;23.45055" => "Chad",
		"-109.44611;-66.42063;-55.90223;-17.50528" => "Chile",
		"73.62005;134.76846;18.16888;53.55374" => "China, Mainland",
		"118.2786;122.0004;21.92777;25.28361" => "China, Taiwan Prov of",
		"105.629;105.7519;-10.51097;-10.38408" => "Christmas Island",
		"96.81749;96.86485;-12.19944;-12.13042" => "Cocos Islands",
		"-81.72015;-66.87045;-4.23687;12.59028" => "Colombia",
		"43.21402;44.53042;-12.38306;-11.36695" => "Comoros",
		"10.03;34.77;-9.22;2.1" => "Congo, Dem Republic of",
		"11.14066;18.64361;-5.015;3.71111" => "Congo, Republic of",
		"-165.84834;-157.70377;-21.94083;-10.88132" => "Cook Islands",
		"-85.91139;-82.5614;8.02567;11.21285" => "Costa Rica",
		"13.50479;19.425;42.39999;46.53583" => "Croatia",
		"-84.95293;-74.13126;19.82194;23.19403" => "Cuba",
		"32.26986;34.58604;34.64027;35.68861" => "Cyprus",
		"12.0937;18.85222;48.58138;51.05249" => "Czech Republic",
		"8.09292;15.14917;54.56194;57.74597" => "Denmark",
		"41.75986;43.42041;10.94222;12.70833" => "Djibouti",
		"-61.49139;-61.2507;15.19806;15.63194" => "Dominica",
		"-72.00307;-68.32293;17.60417;19.93083" => "Dominican Republic",
		"40.25;158.71;-12.73;55.02" => "East & South East Asia",
		"124.84;127.35;-9.38;-8.02" => "East Timor",
		"13.55;70.5;-28.11;19.22" => "Eastern Africa",
		"9.79;31.91;39.55;55.46" => "Eastern Europe",
		"-91.6639;-75.21684;-5.00031;1.43778" => "Ecuador",
		"24.7068;36.89583;21.99416;31.64694" => "Egypt",
		"-90.10806;-87.69467;13.15639;14.43198" => "El Salvador",
		"8.42417;11.35389;0.93016;3.76333" => "Equatorial Guinea",
		"36.44328;43.12138;12.36389;17.99488" => "Eritrea",
		"21.83736;28.19409;57.52263;59.66472" => "Estonia",
		"32.9918;47.98824;3.40667;14.88361" => "Ethiopia",
		"-11.5;43.2;35.3;81.4" => "Europe",
		"-29.63;45.05;29.4;82.64" => "Europe, Non-EU Countries",
		"-7.43347;-6.38972;61.38833;62.3575" => "Faeroe Islands",
		"-61.14806;-57.7332;-52.34306;-51.24945" => "Falkland Islands",
		"52.95;148.51;-12.47;56.15" => "Far East",
		"175;180;-19.16278;-16.15347" => "Fiji Islands",
		"19.51139;31.58196;59.8068;70.08861" => "Finland",
		"-5.79028;9.56222;41.36493;51.09111" => "France",
		"-54.60378;-51.64806;2.11347;5.75542" => "French Guiana",
		"-151.49777;-138.80975;-17.87083;-8.7782" => "French Polynesia",
		"51.65083;70.56749;-49.72501;-46.32764" => "French South Terr",
		"8.70083;14.51958;-3.92528;2.3179" => "Gabon",
		"-16.82167;-13.79861;13.05998;13.82639" => "Gambia",
		"34.21666;34.55889;31.21654;31.5961" => "Gaza Strip",
		"40.00297;46.71082;41.04804;43.58472" => "Georgia",
		"5.865;15.03382;47.27472;55.05653" => "Germany",
		"-3.24889;1.20278;4.72708;11.15569" => "Ghana",
		"-5.35617;-5.33451;36.11207;36.16331" => "Gibraltar",
		"19.64;28.23805;34.93055;41.74777" => "Greece",
		"-73.0536;-12.15764;59.79028;83.6236" => "Greenland",
		"-61.78518;-61.59639;11.99694;12.23715" => "Grenada",
		"-61.79611;-61.18708;15.87;16.51292" => "Guadeloupe",
		"144.63416;144.95331;13.235;13.65229" => "Guam",
		"-92.24678;-88.21474;13.74583;17.82111" => "Guatemala",
		"-15.08083;-7.65337;7.19393;12.6775" => "Guinea",
		"-16.71777;-13.64389;10.9251;12.68472" => "Guinea-Bissau",
		"-61.38973;-56.47063;1.18688;8.53528" => "Guyana",
		"-74.46779;-71.62918;18.02278;20.09146" => "Haiti",
		"73.23471;73.77388;-53.19945;-52.96515" => "Heard and McDonald Is",
		"-89.35049;-83.13185;12.98517;16.43583" => "Honduras",
		"16.11181;22.8948;45.74833;48.57618" => "Hungary",
		"-24.5384;-13.49945;63.39;66.5361" => "Iceland",
		"68.14423;97.38054;6.74583;35.50562" => "India",
		"148.0042;148.3898;-70.01807;-45" => "Indian Ocean, Antarctic",
		"77;150;-55;22.7572" => "Indian Ocean, Eastern",
		"30;80;-45;30.5061" => "Indian Ocean, Western",
		"95.21095;141.00702;-10.92965;5.91347" => "Indonesia",
		"18.45;54.91;-5.78;23.34" => "Intergvt Author Devpment",
		"44.03495;63.33027;25.07597;39.77916" => "Iran, Islamic Rep of",
		"38.7947;48.56069;29.06166;37.38368" => "Iraq",
		"-10.47472;-6.01306;51.44555;55.37999" => "Ireland",
		"-4.78715;-4.30868;54.05555;54.41639" => "Isle of Man",
		"34.26758;35.68111;29.48671;33.27027" => "Israel",
		"6.62397;18.51444;36.64916;47.09458" => "Italy",
		"-8.60638;-2.48778;4.34472;10.73526" => "Ivory Coast",
		"-78.3739;-76.22112;17.69722;18.5225" => "Jamaica",
		"123.67886;145.81241;24.25139;45.48638" => "Japan",
		"34.96042;39.30111;29.18889;33.37759" => "Jordan",
		"46.49916;87.34821;40.59444;55.44263" => "Kazakhstan",
		"33.90722;41.90517;-4.66962;4.6225" => "Kenya",
		"-157.5817;-157.17255;1.705;2.03305" => "Kiribati",
		"124.32395;130.69742;37.67138;43.0061" => "Korea, Dem People's Rep",
		"126.09901;129.58687;33.19221;38.62524" => "Korea, Republic of",
		"46.54694;48.41659;28.53888;30.08416" => "Kuwait",
		"69.2495;80.28159;39.19547;43.2169" => "Kyrgyzstan",
		"100.09137;107.69525;13.92666;22.49993" => "Laos",
		"-120.17;-58.78;-25.44;33.17" => "Latin Amer & Caribbean",
		"-117;-33.8;-55.4;32.7" => "Latin America",
		"20.96861;28.23597;55.67484;58.08326" => "Latvia",
		"35.10083;36.62374;33.06208;34.6475" => "Lebanon",
		"27.01397;29.45555;-30.65053;-28.5707" => "Lesotho",
		"-11.49233;-7.3684;4.34361;8.51278" => "Liberia",
		"9.31139;25.15167;19.49907;33.17114" => "Libyan Arab Jamahiriya",
		"9.47464;9.63389;47.05746;47.27454" => "Liechtensten",
		"20.94283;26.81305;53.89034;56.44985" => "Lithuania",
		"5.73444;6.52403;49.44847;50.18181" => "Luxembourg",
		"20.45882;23.03097;40.85589;42.35895" => "Macedonia",
		"43.23682;50.50139;-25.58834;-11.94556" => "Madagascar",
		"32.68187;35.92097;-17.13528;-9.37667" => "Malawi",
		"99.64194;119.27582;0.85278;7.35292" => "Malaysia",
		"72.86339;73.63728;-0.64167;7.02778" => "Maldives",
		"-12.24483;4.25139;10.14215;25.00028" => "Mali",
		"14.3291;14.57;35.8;35.99194" => "Malta",
		"162.32497;169.97162;5.60028;14.59403" => "Marshall Island",
		"-61.23153;-60.81695;14.40278;14.88014" => "Martinique",
		"-17.07556;-4.80611;14.72564;27.29046" => "Mauritania",
		"57.30631;63.49576;-20.52056;-19.67334" => "Mauritius",
		"45.03916;45.22972;-12.9925;-12.6625" => "Mayotte",
		"-5.61;41.7586;30.2736;47.2719" => "Mediterran and Black Sea",
		"-118.40417;-86.73862;14.55055;32.71846" => "Mexico",
		"158.1201;163.04289;5.26167;6.97764" => "Micronesia,Fed States of",
		"-177.39584;-177.36055;28.18416;28.22152" => "Midway Islands",
		"26.635;30.12871;45.44865;48.46832" => "Moldova, Republic of",
		"7.3909;7.43929;43.72755;43.7683" => "Monaco",
		"87.7611;119.93151;41.58666;52.14277" => "Mongolia",
		"18.196;20.396;41.82;43.58" => "Montenegro",
		"-62.23695;-62.13889;16.67139;16.81236" => "Montserrat",
		"-13.17496;-1.01181;27.66424;35.91917" => "Morocco",
		"30.21302;40.84611;-26.86028;-10.47111" => "Mozambique",
		"92.20499;101.16943;9.83958;28.54652" => "Myanmar",
		"11.71639;25.26443;-28.96188;-16.95417" => "Namibia",
		"166.90442;166.95705;-0.55222;-0.49333" => "Nauru",
		"6.72;77.5;-5.1;51.16" => "Near East",
		"-17.13;78.69;-6.47;62.2" => "Near East and North Africa",
		"4.13;44.13;2.27;34.26" => "Near East in Africa",
		"23.55;77.03;5.67;48.42" => "Near East in Asia",
		"80.0522;88.19456;26.36836;30.42472" => "Nepal",
		"-69.16362;-68.19292;12.02056;12.38389" => "Neth Antilles",
		"3.37087;7.21097;50.75388;53.46583" => "Netherlands",
		"170;180;-52.57806;-32.41472" => "New Zealand",
		"163.98274;168.13051;-22.67389;-20.08792" => "NewCaledonia",
		"-87.68983;-83.13185;10.70969;15.02222" => "Nicaragua",
		"0.16667;15.99667;11.69327;23.52231" => "Niger",
		"2.6925;14.64965;4.27285;13.8915" => "Nigeria",
		"-169.95224;-169.78156;-19.14556;-18.96333" => "Niue",
		"167.91095;167.99887;-29.08111;-29.00056" => "Norfolk Island",
		"-15.3;39.1;10.4;46.2" => "North Africa",
		"-166.3;-14.4;7.2;85" => "North America",
		"-18.45;13.3;17.2;39.28" => "North Western Africa",
		"145.57268;145.81809;14.90805;15.26819" => "Northern Mariana Is",
		"4.78958;31.07354;57.98792;71.15471" => "Norway",
		"51.99929;59.84708;16.64278;26.36871" => "Oman",
		"150;-105.2242;-78.5667;-60" => "Pacific, Antarctic",
		"-175;-77.88896;-25;40.5" => "Pacific, Eastern Central",
		"-175;-122.2264;40;66.1336" => "Pacific, Northeast",
		"105.6236;-175;15;66.5" => "Pacific, Northwest",
		"-120;-65.72;-60;7.21119" => "Pacific, Southeast",
		"149.89136;-120;-60;-25" => "Pacific, Southwest",
		"99.1586;-175;-28.15;20" => "Pacific, Western Central",
		"60.8663;77.82393;23.68805;37.06079" => "Pakistan",
		"-83.03029;-77.19833;7.20611;9.62014" => "Panama",
		"140.85886;155.96684;-11.6425;-1.35528" => "Papua New Guinea",
		"-62.64377;-54.2439;-27.58472;-19.29681" => "Paraguay",
		"-81.35515;-68.6739;-18.34855;-0.03687" => "Peru",
		"116.95;126.59804;5.04917;19.39111" => "Philippines",
		"-130.10506;-128.28612;-25.08223;-24.32584" => "Pitcairn Islands",
		"14.14764;24.14347;49.00291;54.83604" => "Poland",
		"-10;-6.19045;35;44" => "Portugal",
		"-67.2664;-65.30112;17.92222;18.51944" => "Puerto Rico",
		"50.75194;51.61583;24.55604;26.1525" => "Qatar",
		"55.22055;55.85305;-21.37389;-20.85653" => "Reunion",
		"20.26102;29.67222;43.62331;48.26389" => "Romania",
		"-36;180;41.19658;81.85193" => "Russian Federation",
		"28.85444;30.89326;-2.82549;-1.05445" => "Rwanda",
		"-5.79278;-5.64528;-16.02195;-15.90375" => "Saint Helena",
		"-62.86278;-62.62251;17.20889;17.41014" => "Saint Kitts and Nevis",
		"-61.07959;-60.87806;13.70944;14.1093" => "Saint Lucia",
		"-56.39778;-56.23264;46.77985;47.13583" => "Saint Pierre & Miquelon",
		"-61.28014;-61.12029;13.13028;13.38319" => "Saint Vincent/Grenadines",
		"-172.78003;-171.4292;-14.0575;-13.46056" => "Samoa",
		"12.40694;12.51111;43.89868;43.98687" => "San Marino",
		"6.46514;7.46347;0.01833;1.70125" => "Sao Tome and Principe",
		"34.57215;55.66611;15.61694;32.15494" => "Saudi Arabia",
		"-17.53278;-11.36993;12.30175;16.69062" => "Senegal",
		"18.825;23.1;42.02;46.14" => "Serbia",
		"46.20569;55.54055;-9.46306;-4.55167" => "Seychelles",
		"-13.29561;-10.26431;6.92361;9.9975" => "Sierra Leone",
		"103.64095;103.99795;1.25903;1.44528" => "Singapore",
		"16.84472;22.55805;47.7375;49.60083" => "Slovakia",
		"13.38347;16.60787;45.42582;46.87625" => "Slovenia",
		"155.6713;166.93184;-11.84583;-6.60552" => "Solomon Islands",
		"40.98861;51.41132;-1.67487;11.97917" => "Somalia",
		"14.40931;37.89222;-46.96973;-22.13639" => "South Africa",
		"-82.1;-33.8;-55.4;13" => "South America",
		"57.44;100.67;-2.2;38.78" => "South Asia",
		"-27.89;65.27;-33.19;30.31" => "South of Sahara",
		"-38.02375;-26.24139;-58.49861;-53.98972" => "SouthGeorgia/Sandwich Is",
		"10.89;33.01;-32.56;-14.9" => "Southern Africa",
		"-18.16987;4.31694;27.6375;43.7643" => "Spain",
		"79.69609;81.89166;5.91806;9.82819" => "Sri Lanka",
		"21.8291;38.6075;3.49339;22.23222" => "Sudan",
		"-58.0714;-53.98612;1.83625;6.00181" => "Suriname",
		"10.48791;33.6375;74.34305;80.76416" => "Svalbard Is",
		"30.79833;32.1334;-27.31639;-25.72834" => "Swaziland",
		"11.11333;24.16701;55.33917;69.0603" => "Sweden",
		"5.96701;10.48821;45.82944;47.80666" => "Switzerland",
		"35.61446;42.37833;32.31361;37.29054" => "Syrian Arab Republic",
		"67.3647;75.18749;36.67184;41.04926" => "Tajikistan",
		"29.34083;40.43681;-11.74042;-0.99722" => "Tanzania, United Rep of",
		"97.34728;105.63929;5.63347;20.45458" => "Thailand",
		"-0.14976;1.7978;6.10055;11.13854" => "Togo",
		"-171.86272;-171.84377;-9.21889;-9.17063" => "Tokelau",
		"-175.36;-173.90683;-21.26806;-18.56806" => "Tonga",
		"-61.9216;-60.52084;10.04035;11.34555" => "Trinidad and Tobago",
		"7.49222;11.58167;30.23439;37.34041" => "Tunisia",
		"25.66583;44.82055;35.81844;42.10999" => "Turkey",
		"52.44007;66.67088;35.14599;42.79617" => "Turkmenistan",
		"-72.03146;-71.63362;21.73972;21.95778" => "Turks and Caicos Is",
		"176.29526;179.23229;-8.56129;-6.08944" => "Tuvalu",
		"166.60898;-177.39584;-0.39806;28.22152" => "US Minor Outlying Is",
		"-64.89612;-64.56258;17.67667;17.7925" => "US Virgin Islands",
		"16.1;183.39;32.18;88.23" => "USSR, Former Area of",
		"29.5743;35.00972;-1.47611;4.22278" => "Uganda",
		"22.15144;40.17875;44.37915;52.3786" => "Ukraine",
		"51.58333;56.38166;22.63333;26.08389" => "United Arab Emirates",
		"-8.17167;1.74944;49.95528;60.84333" => "United Kingdom",
		"-178.21655;-68;18.92548;71.35144" => "United States of America",
		"-58.43861;-53.0983;-34.94382;-30.09667" => "Uruguay",
		"55.99749;73.16755;37.18499;45.5706" => "Uzbekistan",
		"166.52164;169.89386;-20.25417;-13.70722" => "Vanuatu",
		"-73.37807;-59.80306;0.64917;12.1975" => "Venezuela",
		"102.14075;109.46484;8.55924;23.32416" => "Viet Nam",
		"166.60898;166.6622;19.27944;19.32458" => "Wake Island",
		"-178.19028;-176.12193;-14.32389;-13.21486" => "Wallis and Futuna Is",
		"34.88819;35.57061;31.35069;32.54639" => "West Bank",
		"-33.45;17.56;-17.06;29.04" => "Western Africa",
		"-37.32;39.24;26.92;82.99" => "Western Europe",
		"-17.10153;-8.66639;20.7641;27.66696" => "Western Sahara",
		"-180;180;-90;90" => "World",
		"42.55597;54.47347;12.14472;18.99934" => "Yemen",
		"21.99639;33.70228;-18.07492;-8.19167" => "Zambia",
		"25.23792;33.07159;-22.41476;-15.61653" => "Zimbabwe"
	);
	
	static $MDResourceConstraints = array (
		"copyright" => "Copyright",
	    "intellectualPropertyRights" => "Intellectual property rights",
	    "license" => "License",
	    "otherRestrictions" => "Other restrictions",
	    "patent" => "Patent",
	    "patentPending" => "Patent pending",
	    "restricted" => "Restricted",
	    "trademark" => "Trademark"
	);

	static $MDDateTypes = array (
		"creation" => "Creation",
	    "publication" => "Publication",
	    "revision" => "Revision"
	);	

	static $otherConstraints = array (
		"" => "",
		"Creative Commons Attribution 3.0 New Zealand license" => "Creative Commons Attribution 3.0 New Zealand license",
	    "Creative Commons Attribution-Noncommercial 3.0 New Zealand license" => "Creative Commons Attribution-Noncommercial 3.0 New Zealand license",
	    "Creative Commons Attribution-No Derivative Works 3.0 New Zealand license" => "Creative Commons Attribution-No Derivative Works 3.0 New Zealand license",
	    "Creative Commons Attribution-Noncommercial-No Derivative Works 3.0 New Zealand license" => "Creative Commons Attribution-Noncommercial-No Derivative Works 3.0 New Zealand license",
	    "Creative Commons Attribution-Share Alike 3.0 New Zealand license" => "Creative Commons Attribution-Share Alike 3.0 New Zealand license",
	    "Creative Commons Attribution-Noncommercial-Share Alike 3.0 New Zealand license" => "Creative Commons Attribution-Noncommercial-Share Alike 3.0 New Zealand license",
	    "Other licensing (check with source agency)" => "Other licensing (check with source agency)",
	    "Copyright and either not yet licensed for re-use or application of generic website licensing statement to dataset unclear(check with source agency)" => "Copyright and either not yet licensed for re-use or application of generic website licensing statement to dataset unclear(check with source agency)",
	    "No known copyright-related restrictions on re-use" => "No known copyright-related restrictions on re-use"
	);
	
	static $MDSpatialRepresentationType = array (
		"grid" => "Grid",
	    "stereoModel" => "Stereo model",
	    "tin" => "TIN",
	    "textTable" => "Text table",
	    "vector" => "Vector",
	    "video" => "Video",
	);	
	
	static $MDOnlineResourceProtocol = array (
//		"" => "",
		"ESRI:AIMS--http-get-image" => "ArcIMS Internet Image Map Service",
		"OGC:WMS-1.1.1-http-get-capabilities" => "OGC-WMS Capabilities service (ver 1.1.1)",
		"OGC:WMS-1.1.1-http-get-map" => "OGC Web Map Service (ver 1.1.1)",
		"WWW:DOWNLOAD-1.0-ftp--download" => "File for download through FTP",
		"WWW:DOWNLOAD-1.0-http--download" => "File for download",
		"ESRI:AIMS--http--configuration" => "ArcIMS Map Service Configuration File (*.AXL)",
		"ESRI:AIMS--http-get-feature" => "ArcIMS Internet Feature Map Service",
		"GLG:KML-2.0-http-get-map" => "Google Earth KML service (ver 2.0)",
		"OGC:WCS-1.1.0-http-get-capabilities" => "OGC-WCS Web Coverage Service (ver 1.1.0)",
		"OGC:WFS-1.0.0-http-get-capabilities" => "OGC-WFS Web Feature Service (ver 1.0.0)",
		"OGC:WMC-1.1.0-http-get-capabilities" => "OGC-WMC Web Map Context (ver 1.1)",
		"WWW:LINK-1.0-http--ical" => "iCalendar (URL)",
		"WWW:LINK-1.0-http--link" => "Web address (URL)",
		"WWW:LINK-1.0-http--partners" => "Partner web address (URL)",
		"WWW:LINK-1.0-http--related" => "Related link (URL)",
		"WWW:LINK-1.0-http--rss" => "RSS News feed (URL)",
		"WWW:LINK-1.0-http--samples" => "Showcase product (URL)"
  );

  static $MDScopeCode = array(
    "" => "(please select a scope code)",
    "attribute" => "information applies to the attribute class",
    "attributeType" => "information applies to the characteristic of a feature",
    "collectionHardware" => "information applies to the collection hardware class",
    "collectionSession" => "information applies to the collection session",
    "dataset" => "information applies to the dataset",
    "series" => "information applies to the series",
    "nonGeographicDataset" => "information applies to non-geographic data",
    "dimensionGroup" => "information applies to a dimension group",
    "feature" => "information applies to a feature",
    "featureType" => "information applies to a feature type",
    "propertyType" => "information applies to a property type",
    "fieldSession" => "information applies to a field session",
    "software" => "information applies to a computer program or routine",
    "service" => "information applies to a capability which a service provider entity makes available to a service user entity through a set of interfaces that define a behaviour, such as a use case",
    "model" => "information applies to a copy or imitation of an existing or hypothetical object",
    "tile" => "information applies to a tile, a spatial subset of geographic data",
    
    // additional scope codes
    "modelSession" => "information applies to a model session or model run for a particular model",
    "document" => "information applies to a document such as a publication, report, record etc.",
    "profile" => "information applies to a profile of an ISO TC 211 standard or specification",
    "dataRepository" => "information applies to a data repository such as a Catalogue Service, Relational Database, Web Registry",
    "codeList" => "information applies to a code list according to the CT_CodelistCatalogue format",
    "metadata" => "information applies to metadata",
    "activity" => "information applies to an activity or intiative",
    "sample" => "information applies to a sample",
    "aggregate" => "information applies to an aggregate resource",
    "product" => "metadata describing an ISO 19131 data product specification",
    "collection" => "information applies to an unstructured set",
    "coverage" => "information applies to a coverage",
    "application" => "information resource hosted on a specific set of hardware and accessible over a network"
  );


	static function get_online_resource_function() {
		return self::$MDOnlineResourceFunction;
	}

	static function get_scope_codes() {
		return self::$MDScopeCode;
	}
	
	static function get_scope_codes_keys() {
		$result=array();
		foreach (self::$MDScopeCode as $key => $value) {
			$result[$key]=$key;
		}
		return $result;
	}

	static function get_online_resource_protocol() {
		return self::$MDOnlineResourceProtocol;
	}

	static function get_resource_formats() {
		return self::$MDResourceFormat;
	}

	static function get_categories() {
		return self::$MDCategory;
	}

	static function get_places() {
		return self::$MDPlaces;
	}

	static function get_resource_constraints() {
		return self::$MDResourceConstraints;
	}
	
	static function get_date_types() {
		return self::$MDDateTypes;
	}	
	
	static function get_other_constraints() {
		return self::$otherConstraints;
	}	

	static function get_spatial_representation_type() {
		return self::$MDSpatialRepresentationType;
	}	

}
