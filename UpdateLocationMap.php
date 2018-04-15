<!DOCTYPE html>
<html>
  <head>
    <title>Spotair - Ajoutez des coordonnees a la base de lieux</title>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <style>
      /* Always set the map height explicitly to define the size of the div
       * element that contains the map. */
      #map {
        height: 100%;
      }
      /* Optional: Makes the sample page fill the window. */
      html, body {
        height: 100%;
        margin: 0;
        padding: 0;
      }
      #floating-panel {
        position: absolute;
        top: 50px;
        left: 5%;
        z-index: 5;
        background-color: #fff;
        padding: 5px;
        border: 1px solid #999;
        text-align: center;
        font-family: 'Roboto','sans-serif';
        line-height: 30px;
        padding-left: 10px;
      }
        
     #searchbar {
        position: absolute;
        top: 5px;
        left: 120px;
        z-index: 10;
        background-color: #fff;
        padding: 5px;
        border: 1px solid #999;
        text-align: center;
        font-family: 'Roboto','sans-serif';
        line-height: 30px;
        padding-left: 10px;
      }
    </style>
         
    <script type="application/javascript" src="jquery-3.3.1.min.js"></script>

        <script type="application/javascript">//<![CDATA[
        $(function(){
            // le changement de la liste met a jour les coordonnees et le marqueur
            $('#location_sel').change( function() {
                SetMarker(SelectedMarkers);
            });
            // le changement de la liste met a jour les coordonnees et le marqueur
            $('#ResetZoom').click( function() {
                resetZoom();
            });
            // l'appui sur Afficher centre la carte sur les coordonnees courantes
            $('#ShowMe').click( function() {
               ShowCoord();
            });
            // l'appui sur Enregistrer lance le script d'update
 /*           $('#submit').click( function() {
                $("#update").load(location.href + " #update");
               // window.location.reload();
            });*/
        });//]]>
      </script>
        <!-- au premier chargement de la page, on initialise les résultats de $_POST-->
        <?php
        if(is_array($_POST) && $_POST){
 //           echo '//$_POST exists';
        }
        else{
            $_POST=array('LocationID'=>'0', 'LocationName'=>'Init', 'LocationLat'=>'0', 'LocationLon'=>'0');
 //            echo '//$_POST doesnt exist';
        }
 //      echo '<pre>'; print_r($_POST); echo '</pre>';
    ?>
        
  </head>
  <body>
    <div id="searchbar">
        <input type="text" id="SearchName" name="SearchName" size="30" value="Nom du lieu recherché">
        <input id="FindMe" type="button" value="Recherche">
    </div>
    <div id="floating-panel">
    <form action="" method="post" name="form1">
    <select name="location_sel" id="location_sel">
    <!-- On commence par récupérer le premier elements de la base de donnee qui n'a pas de coordonnees geographiques   -->
    <?php
    // les parametres de connexion à la BDD SQL sont stockes dans le fichier config ailleurs sur le serveur
    include("../etc/config.php");
    // connexion à la BDD, on avorte si la connexion est KO
    $bdd=new mysqli($host, $username, $password, $database);
        if ($bdd->connect_error) {
            die("Connection failed: " . $bdd->connect_error);
        }
    $query = "SELECT * FROM `test_spotair_lieux` WHERE 1 ORDER BY `Lieu`";
    $location = mysqli_query($bdd, $query);
    $loc_count=0;
    //si aucun lieu n'est retournee par la requete, message d'erreur
    if(!next_location){
        echo 'Aucun lieu sans geotag trouv&eacute;';
    }
    //sinon on met a jour le panneau de formulaire avec les donnees du lieu
    else{
        while ($loc = $location->fetch_array()) {
        $loc_count++;
        if($_POST[LocationID]!=$loc["ID"]){
            echo'<option name="'.$loc["ID"].'">'.$loc["Lieu"].'<//option/>';    
        }
        else{
            echo'<option name="'.$loc["ID"].'" selected>'.$loc["Lieu"].'<//option/>';   
        }
        }
    }
    mysqli_close($bdd);
    ?>
        </select>
      <br>
      LAT: 
      <input type="text" id="LocationLat" name="LocationLat" size="10" value="<?php echo $_POST[LocationLat]; ?>">
      LON: <input type="text" id="LocationLon" name="LocationLon" size="10" value="<?php echo $_POST[LocationLon]; ?>">
      <input type="hidden" id="LocationID" name="LocationID" value="<?php echo $_POST[LocationID]; ?>">
      <br>
      <input id="ShowMe" type="button" value="Afficher">
      <input name="Submit" id="Submit" type="submit" value="Enregistrer">
       <input id="ResetZoom" type="button" value="Reset Zoom">
        </form>
    </div>
    <div id="update">
        <script>
        <?php
        include("../etc/config.php");
        $bdd=new mysqli($host, $username, $password, $database);
        if ($bdd->connect_error) {
            die("Connection failed: " . $bdd->connect_error);
        }

        $LocationID= mysqli_real_escape_string($bdd,$_POST["LocationID"]); 
        $Lieu = mysqli_real_escape_string($bdd,$_POST["LocationName"]);
        $Lat = mysqli_real_escape_string($bdd,$_POST["LocationLat"]);
        $Lon = mysqli_real_escape_string($bdd,$_POST["LocationLon"]);

        // on regarde si on retrouve dans la base de donnees l'ID du lieu. Si le texte n'a pas ete modifie, alors l'ID sera retrouve
        // et la fonction fera un UPDATE de la ligne. Sinon elle creera une nouvelle ligne
                    
        // identifie si un resultat a ete retourne par la requete. Si c'est le cas, on fait un UPDATE, sinon un INSERT
$update_query= 'UPDATE test_spotair_lieux SET Lat = '.$Lat.', `Lon` = '.$Lon.' WHERE ID = '.$LocationID;
            if (!mysqli_query($bdd,$update_query)) {
              die('Error: ' . $bdd->connect_error);
            }
            
        echo('document.getElementById("LocationID").value='.$LocationID.';');
        echo('document.getElementById("LocationID").value='.$Lat.';');
        echo('document.getElementById("LocationID").value='.$Lon.';');
            //echo('"Enregistré"');
        mysqli_close($bdd);
        ?>

        </script>
    </div>
    <div id="coord">
        <script>
        var SelectedMarkers=
            [
        <?php
        include("../etc/config.php");
        $bdd=new mysqli($host, $username, $password, $database);
        if ($bdd->connect_error) {
            die("Connection failed: " . $bdd->connect_error);
        }
        //echo '<pre>'; print_r($_POST); echo '</pre>';

        $query= 'SELECT * FROM test_spotair_lieux WHERE 1 ORDER BY `Lieu`';
        $location=mysqli_query($bdd,$query); 
        if(!$location){printf("Aucun lieu trouvé");}    
                while ($loc = $location->fetch_array()){
                ?>
                ['<?php echo $loc['ID']; ?>','<?php echo $loc['Lat']; ?>','<?php echo $loc['Lon']; ?>'], <?php
                }
        mysqli_close($bdd);
        ?>
        ];
        </script>
    </div>
    

    <div id="map"></div>
    <script>
        var map;
        var markers = [];
    
        
        function initMap() {
        map = new google.maps.Map(document.getElementById('map'), {
          zoom: 5,
          center: {lat: 46.5, lng: 2.5}
                   }); 
            
        var geocoder = new google.maps.Geocoder();

        document.getElementById('FindMe').addEventListener('click', function() {
          geocodeAddress(geocoder, map);
        });
            
        if(document.getElementById('LocationID').value!=0){
            SetMarker(SelectedMarkers);
        }
                                  

          
        map.addListener('click', function(e) {
          placeMarkerAndPanTo(e.latLng, map);
        });
      }
        
    function SetMarker(SelectedMarkers){
        var Ind = document.getElementById('location_sel').selectedIndex;
        var SelMarker = SelectedMarkers[Ind];
        var ID = SelMarker[0];
        var lat = SelMarker[1];
        var lon = SelMarker[2];
        document.getElementById('LocationID').value=ID;
        document.getElementById('LocationLat').value=lat;
        document.getElementById('LocationLon').value=lon;
        document.getElementById("SearchName").value=document.getElementById('location_sel').value;
        if(lat!=0 && lon !=0){
        var Coord = new google.maps.LatLng( lat , lon );
        placeMarkerAndPanTo(Coord, map);            
        }
        else{
            setMapOnAll(null);
            markers = [];
            resetZoom();
        }
    } 
        
function resetZoom(){
    var center = new google.maps.LatLng(46.5, 2.5);
    map.panTo(center);
    map.setZoom(5);
}

        
    function ShowCoord(lat,lon){
        var lat = document.getElementById('LocationLat').value;
        var lon = document.getElementById('LocationLon').value;
        latLng = new google.maps.LatLng( lat , lon );
        map.panTo(latLng);
        map.setZoom(14);
    }
        
     function placeMarkerAndPanTo(latLng, map) {
        setMapOnAll(null);
        markers = [];
        var marker = new google.maps.Marker({
          position: latLng,
          map: map
        });
        map.panTo(latLng);
        var lat = latLng.lat();
        var lon = latLng.lng();
        document.getElementById('LocationLat').value=lat;
        document.getElementById('LocationLon').value=lon;
        markers.push(marker);
     }
        
      function setMapOnAll(map) {
        for (var i = 0; i < markers.length; i++) {
          markers[i].setMap(map);
        }
      }
        
      function geocodeAddress(geocoder, resultsMap) {
        var address = document.getElementById('SearchName').value;
        geocoder.geocode({'address': address}, function(results, status) {
          if (status === 'OK') {
            resultsMap.setCenter(results[0].geometry.location);
            var center = resultsMap.getCenter();
            resultsMap.setZoom(14);
          } else {
            alert('Geocode was not successful for the following reason: ' + status);
          }
        });
      }
        
    </script>
    <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCuFxPepsbmBfLHqPrzkbu7-G76F3Qo7_c&callback=initMap">
    </script>
  </body>
</html>