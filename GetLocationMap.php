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
    </style>
         
    <script type="application/javascript" src="jquery-3.3.1.min.js"></script>

        <script type="application/javascript">//<![CDATA[
        $(function(){
            // l'appui sur Enregistrer lance le script d'update
            $('#submit').click( function() {
                $("#update").load(location.href + " #update");
               // window.location.reload();
            });
        });//]]>
      </script>
        <!-- au premier chargement de la page, on initialise les résultats de $_POST-->
        <?php
        if(is_array($_POST) && $_POST){
            //echo '//$_POST existe';
        }
        else{
            $_POST=array('LocationID'=>'0', 'LocationName'=>'Test', 'LocationLat'=>'0', 'LocationLon'=>'0');
            //echo '<pre>'; print_r($_POST); echo '</pre>';
        }
    ?>
        
  </head>
  <body>
    <div id="floating-panel">
    <form action="" method="post" name="form1">
    <!-- On commence par récupérer le premier elements de la base de donnee qui n'a pas de coordonnees geographiques   -->
    <?php
    // les parametres de connexion à la BDD SQL sont stockes dans le fichier config ailleurs sur le serveur
    include("../etc/config.php");
    // connexion à la BDD, on avorte si la connexion est KO
    $bdd=new mysqli($host, $username, $password, $database);
        if ($bdd->connect_error) {
            die("Connection failed: " . $bdd->connect_error);
        }
    $query = "SELECT * FROM `test_spotair_lieux` WHERE `Lat` IS NULL OR `Lat` = '' ORDER BY RAND() LIMIT 1";
    $next_location = mysqli_query($bdd, $query);
    $loc = $next_location->fetch_array();
    //si aucun lieu n'est retournee par la requete, message d'erreur
    if(!next_location){
        echo 'Aucun lieu sans geotag trouv&eacute;';
    }
    //sinon on met a jour le panneau de formulaire avec les donnees du lieu
    else{
        $location_ID=$loc["ID"];
        $location_name=$loc["Lieu"];
        $location_lat=$loc["Lat"];
        $location_lon=$loc["Lon"];
    }
    echo ' Lieu: <input type="text" id="LocationName" name="LocationName" size="40" value="'.$location_name.'">';
    echo '<br>';
    echo ' LAT: <input type="text" id="LocationLat" name="LocationLat" size="10" value="'.$location_lat.'">';
    echo ' LON: <input type="text" id="LocationLon" name="LocationLon" size="10" value="'.$location_lon.'">';
    echo ' <input type="hidden" id="LocationID" name="LocationID" value="'.$location_ID.'">';
    mysqli_close($bdd);
    ?>
      <br>
      <input id="FindMe" type="button" value="J'ai de la chance">
      <input name="Submit" id="Submit" type="submit" value="Enregistrer">
      <input type="button" value="Next" onClick="window.location.reload()">
        </form>
    </div>
    <div id="update">
        <script>
        var map;
        var markers = [];
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
            
        $query = 'SELECT * FROM test_spotair_lieux WHERE ID = '.$LocationID;
        
        $sqlsearch = mysqli_query($bdd,$query);
        $resultcount = mysqli_num_rows($sqlsearch);
            
        $result = $sqlsearch->fetch_array();
        
        // identifie si un resultat a ete retourne par la requete. Si c'est le cas, on fait un UPDATE, sinon un INSERT
        if ($resultcount > 0)
        {
$update_query= 'UPDATE test_spotair_lieux SET Lat = '.$Lat.', `Lon` = '.$Lon.' WHERE ID = '.$LocationID;
            if (!mysqli_query($bdd,$update_query)) {
              die('Error: ' . $bdd->connect_error);
            }
            //echo('"Enregistré"');
        } else {
/*$insert_query= 'INSERT INTO test_spotair_lieux (Lieu, Lat, Lon) VALUES ('.$Lieu.', '.$Lat.', '.$Lon.')';
            if (!mysqli_query($bdd,insert_query)) {
              die('Error: ' . $bdd->connect_error);
            }*/
           // echo('"Erreur enregistrement"');
        }
        mysqli_close($bdd);
        ?>

        </script>
    </div>
    <div id="map"></div>
    <script>
      function initMap() {
        map = new google.maps.Map(document.getElementById('map'), {
          zoom: 5,
          center: {lat: 46.5, lng: 2.5}
        });
        var geocoder = new google.maps.Geocoder();

        document.getElementById('FindMe').addEventListener('click', function() {
          geocodeAddress(geocoder, map);
        });
          
        map.addListener('click', function(e) {
          placeMarkerAndPanTo(e.latLng, map);
        });
      }
    

      function geocodeAddress(geocoder, resultsMap) {
        var address = document.getElementById('LocationName').value;
        geocoder.geocode({'address': address}, function(results, status) {
          if (status === 'OK') {
            resultsMap.setCenter(results[0].geometry.location);
            var center = resultsMap.getCenter();
            var lat = center.lat();
            var lon = center.lng();
            document.getElementById('LocationLat').value=lat;
            document.getElementById('LocationLon').value=lon;
            resultsMap.setZoom(14);
            var marker = new google.maps.Marker({
              map: resultsMap,
              position: results[0].geometry.location
            });
            markers.push(marker);
          } else {
            alert('Geocode was not successful for the following reason: ' + status);
          }
        });
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
    </script>
    <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCuFxPepsbmBfLHqPrzkbu7-G76F3Qo7_c&callback=initMap">
    </script>
  </body>
</html>