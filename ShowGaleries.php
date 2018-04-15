<!DOCTYPE html >
  <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <title>Test carte Spotair</title>
    <style>
      /* Always set the map height explicitly to define the size of the div
       * element that contains the map. */
      #map {
        height: 80%;
      }
      /* Optional: Makes the sample page fill the window. */
      html, body {
        height: 100%;
        margin: 0;
        padding: 0;
      }
      #legend {
        font-family: Arial, sans-serif;
        background: #fff;
        padding: 10px;
        margin: 10px;
        border: 3px solid #000;
      }
      #legend h3 {
        margin-top: 0;
      }
      #legend img {
        vertical-align: middle;
      }        
      #filter {
        position: absolute;
        top: 50px;
        left: 1%;
        z-index: 5;
        background-color: #fff;
        padding: 0px;
        border: 1px solid #999;
        text-align: center;
        font-family: 'Roboto','sans-serif';
        line-height: 30px;
        padding-left: 0px;
      }
    </style>
    
<!-- Certains evenements sont geres par du jQuery   -->
      <script type="application/javascript" src="jquery-3.3.1.min.js"></script>

        <script type="application/javascript">//<![CDATA[
        $(function(){
             // fonction qui selectionne toutes les années dans le champ year_sel en cliquant sur le bouton 'Toutes'
           $('#select_all').click(function() {
                $('#year_sel option').prop('selected', true);
            });
            // fonction qui selectionne tous les types en cliquant sur le bouton 'Tous'
            $('#select_all_type').click(function() {
                $('input:checkbox').prop('checked', this.checked);
            });
            // fonction qui regle les parametres de zoom pour afficher la France en cliquant sur le bouton dedie
            $('#zoom1').click( function() {
            moveToLocation( 46.5, 2.5, 6 );
            $('#zoomset').val('1');
            });
             // fonction qui regle les parametres de zoom pour afficher l Europe en cliquant sur le bouton dedie
           $('#zoom2').click( function() {
            moveToLocation( 51, 15, 4 );
            $('#zoomset').val('2');
            });
            // fonction qui regle les parametres de zoom pour afficher les US en cliquant sur le bouton dedie
            $('#zoom3').click( function() {
            moveToLocation( 41, -98, 4 );
            $('#zoomset').val('3');
            });
            // fonction qui regle les parametres de zoom pour afficher le monde en cliquant sur le bouton dedie
            $('#zoom4').click( function() {
            moveToLocation( 15, 25, 3 );
            $('#zoomset').val('4');
            });
            // fonction qui enregistre les parametres de la carte au moment de l'appui
            $('#Submit').click( function() {
            //$('#test').click( function() {
                MapSettings=getMapParam();
            $('#currentCenterLat').val(MapSettings[0]);
            $('#currentCenterLon').val(MapSettings[1]);
            $('#currentZoom').val(MapSettings[2]);
            });
        });//]]>
            

            
        </script>
        
<!--    pour le tout premier chargement de la page, on initialise les valeurs de $_POST-->
        <?php
            if(is_array($_POST) && $_POST){
              //  echo '//$_POST existe';
            }
            else{
                $year=array('0'=>'2009','1'=>'2010','2'=>'2011','3'=>'2012','4'=>'2013','5'=>'2014','6'=>'2015','7'=>'2016','8'=>'2017','9'=>'2018');
                $_POST=array('airshow'=>'on', 'exercice'=>'on', 'spotting'=>'on', 'museum'=>'on', 'year_sel'=>$year, 'member_sel'=>'Tous', 'zoomset'=>'4', 'currentZoom'=>'3', 'currentCenterLat'=>'15', 'currentCenterLon'=>'15', 'debug_mode'=>'false');
              //  echo '<pre>'; print_r($_POST); echo '</pre>';
            }
        ?>
  </head>

  <body>
   
    <div id="filter">
 <!--  Zone de choix des filtres dans un tableau de base  -->
   
<fieldset>
    <form action="" method="post" name="form1">
    <table>
    <tr>
    <th align="center">Types</th>
    <th align="center">Ann&eacute;es</th>
    </tr>
    <tr>
    <td>
<!-- Champ Hidden marquant le debut du choix des types de marqueurs dans le tableau de sortie $_POST   -->
    <input type="hidden" name="TYPES" value="TYPES"> 
<!-- On commence par récupérer les elements de la table pour créer les choix de filtres   -->
    <?php
    // les parametres de connexion à la BDD SQL sont stockes dans le fichier config ailleurs sur le serveur
    include("../etc/config.php");
    // connexion à la BDD, on avorte si la connexion est KO
    $bdd=new mysqli($host, $username, $password, $database);
        if ($bdd->connect_error) {
            die("Connection failed: " . $bdd->connect_error);
        }
    // on recupere les types de marqueurs sans doublon
    $query = "SELECT DISTINCT type FROM `test_spotair_map_markers` ORDER BY type";
    $typelist = mysqli_query($bdd, $query);
    $typecount=0;
    //si aucune type n'est retournee par la requete, message d'erreur
    if(!typelist){
        echo 'Aucun type trouv&eacute;';
    }
    //sinon on transforme chaque type en case a cocher
    else{
        while ($type = $typelist->fetch_array()) {
            $typecount++;
            ?>
            <?php
            // si la case etait cochee precedemment, on la garde cochee au rechargement de la page apres avoir appuye sur Mettre a jour
            if($_POST[$type["type"]]=="on"){
                echo'<input type="checkbox" checked id="checkItem" name="'.$type["type"].'" value="on">'.$type["type"].'<br/>';
            }
            // sinon on l'affiche decochee
            else{
                echo'<input type="checkbox" id="checkItem" name="'.$type["type"].'" value="on">'.$type["type"].'<br/>';
            }

        }
    }
    ?>
<!-- Ajout d'un bouton pour selectionner tous les types d'un coup. Voir script jQuery dans le header-->
    <input type="checkbox" id="select_all_type" name="select_all_type" value="Tous">Tous<br/>
    </td>
    <td>
<!-- Champ Hidden marquant le debut du choix des annees dans le tableau de sortie $_POST   -->
    <input type="hidden" name="YEARS" value="YEARS">
<!--Champ a selection multiple possible. Le nom du champ a des crochets pour pouvoir referer aux differentes annees selectionnees sous forme de tableau    -->
    <select name="year_sel[]" id="year_sel" multiple size="10">

    <?php
    // on recupere les annees sans doublon
    $query = "SELECT DISTINCT year FROM `test_spotair_map_markers` ORDER BY year";
    $yearlist = mysqli_query($bdd, $query);
    $yearcount=0;
    //si aucune annee n'est retournee par la requete, message d'erreur
    if(!yearlist){
        echo 'Aucune ann&eacte;e trouv&eacute;e';
    }
    //sinon on transforme chaque annee en option du champ year_sel
    else{
        while ($year = $yearlist->fetch_array()) {
            $yearcount++;
            ?>
            <?php
            // si l'annee etait selectionnee precedemment, on la garde selectionnee au rechargement de la page apres avoir appuye sur Mettre a jour
            if(in_array($year["year"], $_POST[year_sel])){
                echo'<option name="'.$year["year"].'" selected>'.$year["year"].'<//option/>';
            }
            // sinon on l'affiche normalement
            else{
                echo'<option name="'.$year["year"].'">'.$year["year"].'<//option/>';
            }
        }
     }
    ?>
    </select>
    <br/>
<!-- Ajout d'un bouton pour selectionner toutes les annees d'un coup. Voir script jQuery dans le header-->
    <input type="button" id="select_all" name="select_all" value="Toutes">
    </td>
    </tr>
    <tr>
    <th colspan="2" align="center">Auteurs</th>
    </tr>
    <tr>
    <td colspan="2">
<!-- Champ Hidden marquant le debut du choix du photographe dans le tableau de sortie $_POST   -->
    <input type="hidden" name="MEMBER" value="MEMBER"> 
<!--Champ a selection unique. On cree en plus une option 'Tous' selectionnee par defaut. Si elle etait selectionnee precedemment, on a la garde selectionne au rechargement de la page  -->
    <select name="member_sel">
    <option name="All" <?php if($_POST[member_sel]=="Tous"){echo "selected";}  ?> >Tous</option>
    <?php
    // on recupere les photographes sans doublon
    $query = "SELECT DISTINCT member FROM `test_spotair_map_markers` ORDER BY member";
    $memberlist = mysqli_query($bdd, $query);
    //si aucun membre n'est retournee par la requete, message d'erreur
    if(!memberlist){
        echo 'Aucun photographe trouv&eacute;';
    }
    //sinon on transforme chaque membre en option du champ member_sel
    else{
        while ($member = $memberlist->fetch_array()) {
            ?>
            <?php
           // si le membre etait selectionne precedemment, on le garde selectionne au rechargement de la page apres avoir appuye sur Mettre a jour
          if($_POST[member_sel]!=$member["member"]){
                echo'<option name="'.$member["member"].'">'.$member["member"].'<//option/>';
            }
            // sinon on l'affiche normalement
            else{
                echo'<option name="'.$member["member"].' " selected>'.$member["member"].'<//option/>';
            }
        }
     }
        ?>
        </select>
        </td>
        </tr>
        <tr>
        <th colspan="2" align="center">Zones</th>
        </tr>
        <tr>
        <td align="center">
<!-- Ajout de boutons pour pre selectionner les parametres de la carte. Voir script jQuery dans le header-->
            <input type="button" id="zoom1" name="zoom1" value="France"><br/>
            <input type="button" id="zoom2" name="zoom2" value="Europe"><br/>
            </td>
            <td align="center">
            <input type="button" id="zoom3" name="zoom3" value="Amérique du Nord"><br/>
            <input type="button" id="zoom4" name="zoom4" value="Monde">
<!-- Champ Hidden pour memoriser le parametrage de la carte avant d'envoyer les donnees du formulaire. La valeur par defaut et celle issue du $_POST si la page a ete rafraichie-->
            <input type="hidden" name="zoomset" id="zoomset" value="<?php echo $_POST[zoomset]; ?>"> 
            <input type="hidden" name="currentZoom" id="currentZoom" value="<?php echo $_POST[currentZoom]; ?>"> 
            <input type="hidden" name="currentCenterLat" id="currentCenterLat" value="<?php echo $_POST[currentCenterLat]; ?>"> 
            <input type="hidden" name="currentCenterLon" id="currentCenterLon" value="<?php echo $_POST[currentCenterLon]; ?>"> 
            </td>
        </tr>
        </table>
       <div align="center">
<!-- Champ Hidden pour activer le mode debug (affichage du contenu de $_POST + affichage de la requete de selection des marqueurs)-->
        <input type="hidden" name="debug_mode" id="debug_mode" value="false"> 
        <?php if($_POST[debug_mode]=="true"){echo '<pre>'; print_r($_POST); echo '</pre>';} ?>
        <!-- Bouton de sousmission du formulaire. Toutes les selections sont concatenees dans le tableau $_POST qui est une variable globale. La page est rechargee apres clic -->
            <!--<input type="button" id="test" name="test" value="test">-->
            <input type="submit" name="Submit" id="Submit" value="Mettre la carte à jour">

        
      </div>
    </form>
</fieldset>
    
    </div>
   
    <div id="map"></div>
    <script>

// la variable map doit etre globale pour que les script jQuery puisse y acceder
    var map;
    
    function initialize() //parametrage de la carte
{
            var mapOptions = // definition des parametres de facteur de zoom et de centrage de la carte
            // on reprend les parametres précedent, lus dans $_POST[zoomset].
            // Sans valeur précédent, zoom de 5 sur position 48.95, -0.5
            {
                zoom: <?php
                    echo($_POST[currentZoom]);
/*                    switch($_POST[zoomset]){
                        case 1: 
                            echo '6';
                            break;
                        case 2:
                            echo '4';
                            break;
                        case 3:
                            echo '4';
                            break;
                        case 4:
                            echo '3';
                            break;
                        default:
                            echo '5';
                    }*/
                ?>,
                center: new google.maps.LatLng(
                    <?php
                      echo($_POST[currentCenterLat]);
                      echo(',');
                      echo($_POST[currentCenterLon]);
/*                    switch($_POST[zoomset]){
                        case 1: 
                            echo '46.5, 2.5';
                            break;
                        case 2:
                            echo '51, 15';
                            break;
                        case 3:
                            echo '41, -98';
                            break;
                        case 4:
                            echo '15, 25';
                            break;
                        default:
                            echo '48.95,-0.5';
                    }*/
                ?>
                    ),
            }
            

        
// creer la carte avec les parametres definis precedemment et lance la fonction de placement des marqueurs . Si le mode est actif, la requete generee est affichee
            map = new google.maps.Map(document.getElementById('map'), mapOptions);
            <?php if($_POST[debug_mode]=="true"){echo 'createQuery();';} ?>
            setMarkers(map, SelectedMarkers);
}
        
//creation du tableau de marqueurs en fonction des filtres choisis
            var SelectedMarkers =
            [
            // on cree une requete de base en fonction du choix du photogrape : Tous les photographes ou un photographe en particulier
            <?php
              if($_POST[member_sel]=="Tous"){
                $query = 'SELECT * FROM test_spotair_map_markers WHERE 1';
            }
            else{
                $query = 'SELECT * FROM test_spotair_map_markers WHERE member="'.$_POST[member_sel].'"';
            }
            // les parametres de connexion à la BDD SQL sont stockes dans le fichier config ailleurs sur le serveur
            include("../etc/config.php");
            //connexion a la BDD SQL, en cas d'erreur on avorte
            $bdd=new mysqli($host, $username, $password, $database);
                if ($bdd->connect_error) {
                    die("Connection failed: " . $bdd->connect_error);
                }
            
            //on complete la requete predemment cree en fonction du type d'evenements selectionnes
            //on commence par recuperer les evenements possibles
            $typequery = "SELECT DISTINCT type FROM `test_spotair_map_markers` ORDER BY type";
            $typelist = mysqli_query($bdd, $typequery);
            //si aucune type n'est retournee, on ne rajoute rien a la sous requete
            if(!typelist){
                $subquery1='';
            }
            //sinon on cree une sous requete qu'on concatenera a la requete de base
            else{
                $mod=0; //compteur qui identifie si on est debut de la sous requete 'type' ou pas
                while($type = $typelist->fetch_array()){ // pour chaque type
                    if ($_POST[$type["type"]]=="on"){ // si le type a ete active dans le formulaire
                        if($mod==0){
                            $subquery1='type="'.$type["type"].'"'; //on le rajoute dans la sous requete tel quel si on est au debut de la sous-requete
                        }
                        else{
                            $subquery1=$subquery1.' OR type="'.$type["type"].'"'; //on le rajoute dans la sous requete avec un OU si on n'est pas au debut de la sous-requete
                        }
                    $mod++; 
                    }
                }
            }
            $query =$query.' AND ('.$subquery1.')'; // on rajoute les criteres de la sous requete 'type' a la requete principale en utilisant un ET

            //on fait la meme chose pour les annees
            $yearquery = "SELECT DISTINCT year FROM `test_spotair_map_markers` ORDER BY year";
            $yearlist = mysqli_query($bdd, $yearquery);
            $mod=0;
            while($year = $yearlist->fetch_array()){
                if (in_array($year["year"], $_POST[year_sel])){ // ici on teste si l'annee fait partie des annees selectionnees dans le formulaire
                    if($mod==0){
                        $subquery2='year="'.$year["year"].'"';
                    }
                    else{
                        $subquery2=$subquery2.' OR year="'.$year["year"].'"';
                    }
                $mod++;
                }
            }   
            $query =$query.' AND ('.$subquery2.')'; // on rajoute les criteres de la sous requete 'annee' a la requete principale en utilisant un ET
          
            //on a maintenant une requete du type 'SELECT tous_les_evenements WHERE membre_choisi AND types_choisis AND annees_choisis'
            //on lance la requete pour recuperer les evenements correspondant et les transformer en un tableau de marqueurs
            $tableMarkers = mysqli_query($bdd, $query );
            // si les criteres ne retournent aucun resultat, on cree un marqueur bidon pour ne pas planter la fonction de placement des marqueurs
            if(!$tableMarkers){
                echo "['airshow','Vide','Aucun type selectionné','Sélectionner un type', '0','0','0','2000']";
            }
            // sinon pour chaque evenement retourne par la requete, on transforme les donnees en tableau JavaScript
            else{
                while ($donnees = $tableMarkers->fetch_array())
                {
                ?>
                    ['<?php echo $donnees['type']; ?>',' <?php echo $donnees['name']; ?>','<?php echo $donnees['description']; ?>','  <?php echo $donnees['member']; ?>',' <?php echo $donnees['id']; ?>',' <?php echo $donnees['lat']; ?>',' <?php echo $donnees['lng']; ?>',' <?php echo $donnees['year']; ?>',' <?php echo $donnees['misc']; ?>'],
                     <?php
                }
            }
            ?>
            ];
 
function createQuery(){//fonction utlisee par le mode debug pour faire afficher la requete, au fonctionnement identique à la premiere partie de SelectedMarkers
    var $query=
    <?php
    echo "'";
    if($_POST[member_sel]!="Tous"){
        $query = 'SELECT * FROM test_spotair_map_markers WHERE member="'.$_POST[member_sel].'"';
    }
    else{
        $query = 'SELECT * FROM test_spotair_map_markers WHERE 1';
    }
    include("../etc/config.php");
    $bdd=new mysqli($host, $username, $password, $database);
        if ($bdd->connect_error) {
            die("Connection failed: " . $bdd->connect_error);
        }
        $typequery = "SELECT DISTINCT type FROM `test_spotair_map_markers` ORDER BY type";
        $typelist = mysqli_query($bdd, $typequery);
        if(!$typelist){printf("Aucun type selectionné");}
        $mod=0;
    while($type = $typelist->fetch_array()){
        if ($_POST[$type["type"]]=="on"){
            if($mod==0){
                $subquery1='type="'.$type["type"].'"';
            }
            else{
                $subquery1=$subquery1.' OR type="'.$type["type"].'"';
            }
        }
        $mod++;
    }
    $query =$query.' AND ('.$subquery1.')';
        
        $yearquery = "SELECT DISTINCT year FROM `test_spotair_map_markers` ORDER BY year";
        $yearlist = mysqli_query($bdd, $yearquery);
        $mod=0;
    while($year = $yearlist->fetch_array()){
        if ($_POST[$year["year"]]=="on"){
            if($mod==0){
                $subquery2='year="'.$year["year"].'"';
            }
            else{
                $subquery2=$subquery2.' OR year="'.$year["year"].'"';
            }
        }
        $mod++;
    }   
    $query =$query.' AND ('.$subquery2.')';
     
    echo $query;
    echo "';";
    ?>
    window.alert($query);
        
}

// fonction de changement de parametres de la carte associee aux scripts jQuery du Header
function moveToLocation(lat, lng, zoom){
    var center = new google.maps.LatLng(lat, lng);
    map.panTo(center);
    map.setZoom(zoom);
}
    

// fonction de recuperation des parametres courant de la carte de maniere a les memoriser au moment du rafraichissement de la page (script jQuery)
function getMapParam(){
    var center = map.getCenter();
    var lat = center.lat();
    var lon = center.lng();
    var zoomlevel = map.getZoom();
    var MapParam= [lat, lon, zoomlevel];
    return MapParam;
} 
        
function setMarkers(map, markers_data) //mise en place des marqueurs. On passe en parametre la carte et le tableau des marqueurs
{
        // mise en place de la fonction MarkerCluster (regroupement de marqueurs proches)
        var mcOptions = {gridSize: 50, maxZoom: 14, imagePath: 'markerclusterer/m'};
        var markerCluster = new MarkerClusterer(map, [], mcOptions);
    
        // definition des couleurs de marqueur en fonction du type
        var iconBase = 'http://maps.google.com/mapfiles/kml/paddle/'; //<= lien vers les marqueurs originaux
        var icons = {
          exercice: {
            icon: iconBase + 'blu-blank.png'
          },
          airshow: {
            icon: iconBase + 'grn-blank.png'
          },
          spotting: {
            icon: iconBase + 'purple-blank.png'
          },
          museum: {
            icon: iconBase + 'red-blank.png'
          }
        };
        
    // pour chaque element du tableau des marqueurs
    for (var i = 0; i < markers_data.length; i++){
        var marker_data  = markers_data[i]; // on extrait les donnees du marqueur courant
        var infoWindow = new google.maps.InfoWindow(); // on prepare la bulle d'information qui apparaitra quand on clique sur le marqueur
        var yearlabel="'"+marker_data[7].substr(3,2); // on prepare l'etiquette qui sera affichee sur le marqueur : les deux derniers chiffre de l'annee de l'evenement
        var decade = (Number(marker_data[7].substr(1,3))-200)*2;
        var year = Number(marker_data[7].substr(4,1));
        if(year>5){
            year=year-5;
            decade=decade+1;
        }
        var offset_step_lat = -0.001;
        var offset_step_lon = 0.001;
        var lat=Number(marker_data[5])+year*offset_step_lat;
        var lon=Number(marker_data[6])+decade*offset_step_lon;

        var myLatLng = new google.maps.LatLng(lat, lon); // on extrait les coordonnees du marqueur et on les décale en fonction de l'année pour pas qu'ils ne se superposent si plusieurs au meme endroit

            
        var marker = new google.maps.Marker // on cree le marqueur sur la carte selon sa position et avec la couleur correspond a son type
            ({
                position: myLatLng,
                map: map,
                icon: {
                    url: icons[marker_data[0]].icon,
                    scaledSize: new google.maps.Size(48, 48),
                    labelOrigin: new google.maps.Point(25,16) // origin
                }
          });
            marker.setLabel(yearlabel); // on rajoute l'etiquette
            markerCluster.addMarker(marker); // on l'ajout a la liste de marqueur geree par la fonction de regroupement
            
            (function(i) //information dans la fenetre d'information
            {
                google.maps.event.addListener(marker, "click", function() // on surveille les clics sur les marqueurs
                    {
                    var info=markers_data[i];                                   //on extrait les donnees du marqueur courant
                    var infowincontent = document.createElement('div');         //
                    var strong = document.createElement('strong');              //on cree la premiere ligne de l'infobulle : le nom de l'evenement en gras
                    strong.textContent = escapeHtml(info[1])
                    infowincontent.appendChild(strong);
                    infowincontent.appendChild(document.createElement('br'));

                    var text = document.createElement('text');                  //on cree la seconde ligne : la description
                    text.textContent = escapeHtml(info[2])
                    infowincontent.appendChild(text);
                    infowincontent.appendChild(document.createElement('br'));
                    
                    var text = document.createElement('text');                  //on cree la troisieme ligne : l'annee
                    text.textContent = escapeHtml(info[7])
                    infowincontent.appendChild(text);
                    infowincontent.appendChild(document.createElement('br'));
                    
                    var text = document.createElement('text');                  //on cree la derniere ligne : le photographe
                    text.textContent = escapeHtml(info[3])
                    infowincontent.appendChild(text);
                    
                    infoWindow.close();                                         //on ajoute le contenu de l'infobulle au marqueur
                    infoWindow.setContent(infowincontent);
                    infoWindow.open(map, this);
                    });
            })(i);
        }
}
        
function escapeHtml(text) {
  var map = {
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#039;'
  };

  return text.replace(/[&<>"']/g, function(m) { return map[m]; });
}
 
        </script>
    
    <script src="markerclusterer/markerclusterer.js"> //script associe a la fonction de regroupement
    </script>
    
     <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCuFxPepsbmBfLHqPrzkbu7-G76F3Qo7_c&callback=initialize"> //script Google Maps
    </script>
  </body>
</html>