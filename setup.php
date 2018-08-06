<?php
include_once('connect.php');
if(isset($_GET['key'])){
    $key=mysqli_real_escape_string($connection,$_GET['key']);
    $name= mysqli_real_escape_string($connection,$_GET['name']);
     $strSQL = mysqli_query($connection,"update crimeuser set active=1 where verification='$key' && name='$name';");
    echo ('
    <script>document.write(\'<div class="alert alert-success" id="notify">account verified </div>\');</script>
    ');
}
if(isset($_GET['lat'])){
    $lat=mysqli_real_escape_string($connection,$_GET['lat']);
    $lng=mysqli_real_escape_string($connection, $_GET['lng']);

    if(isset($_GET['key'])){
        $key=$_GET['key'];
        $name= $_GET['name'];
        $strSQL = mysqli_query($connection,"update crimeuser set lat=$lat,lng=$lng where verification='$key' && name='$name';");
        echo '<script>document.write(\'<div class="alert alert-info" id="notify">Location saved,please <a href="index.php">login</a> and continue. </div>\');</script>';
    }
}
?>
<html>
<head>
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
<link rel="stylesheet" href="main.css">
<script src="main.js"></script>
<title>Crime Logger</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />

</head>
    <body>
        <div class="container">
            <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <p>&nbsp;</p>
                <h3>Please point out your home so that we can send you email based on you location and crimes around you(optional)</h3>

                <form method="get" name="reporter" action="">
                   <div class="form-group form-inline">
                        <label for="lat">Latitude:</label>
                        <input type="number" step="any" class="form-control" id="lat" name="lat" required>
                        <label for="lat">Longitude:</label>
                        <input type="number" step="any" class="form-control" id="lng" name="lng" required>
                    </div>
                    <input type="hidden" value="<?php echo $_GET['key']?>" name="key">
                    <input type="hidden" value="<?php echo $_GET['name']?>" name="name">
                   <input type="submit" value="Submit" class="btn btn-default">
                </form>



    <div class="pac-card" id="pac-card">
      <div>
        <div id="title">
          Autocomplete search
        </div>
        <div id="type-selector" class="pac-controls">
          <input type="radio" name="type" id="changetype-all" checked="checked">
          <label for="changetype-all">All</label>

          <input type="radio" name="type" id="changetype-establishment">
          <label for="changetype-establishment">Establishments</label>

          <input type="radio" name="type" id="changetype-address">
          <label for="changetype-address">Addresses</label>

          <input type="radio" name="type" id="changetype-geocode">
          <label for="changetype-geocode">Geocodes</label>
        </div>
        <div id="strict-bounds-selector" class="pac-controls">
          <input type="checkbox" id="use-strict-bounds" value="">
          <label for="use-strict-bounds">Strict Bounds</label>
        </div>
      </div>
      <div id="pac-container">
        <input id="pac-input" type="text"
            placeholder="Enter a location">
      </div>
    </div>
    <div id="map"></div>
    <div id="infowindow-content">
      <img src="" width="16" height="16" id="place-icon">
      <span id="place-name"  class="title"></span><br>
      <span id="place-address"></span>
    </div>

            </div>
            </div>
        </div>

            <script>
      // This example requires the Places library. Include the libraries=places
      // parameter when you first load the API. For example:
      // <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places">
var map;
      function initMap() {
        var localLat=30.324321;
        var localLong=78.041822;

    if(navigator.geolocation){
        navigator.geolocation.getCurrentPosition(function(position,inError){
            localLat=position.coords.latitude;
            localLong= position.coords.longitude;
            });
        }
        map = new google.maps.Map(document.getElementById('map'), {
          center: {lat: localLat, lng: localLong},
          zoom: 13
        });
        var card = document.getElementById('pac-card');
        var input = document.getElementById('pac-input');
        var types = document.getElementById('type-selector');
        var strictBounds = document.getElementById('strict-bounds-selector');

        map.controls[google.maps.ControlPosition.TOP_RIGHT].push(card);

        var autocomplete = new google.maps.places.Autocomplete(input);

        // Bind the map's bounds (viewport) property to the autocomplete object,
        // so that the autocomplete requests use the current map bounds for the
        // bounds option in the request.
        autocomplete.bindTo('bounds', map);

        var infowindow = new google.maps.InfoWindow();
        var infowindowContent = document.getElementById('infowindow-content');
        infowindow.setContent(infowindowContent);
        var marker = new google.maps.Marker({
          map: map,
          anchorPoint: new google.maps.Point(0, -29)
        });

        autocomplete.addListener('place_changed', function() {
          infowindow.close();
          marker.setVisible(false);
          var place = autocomplete.getPlace();
          if (!place.geometry) {
            // User entered the name of a Place that was not suggested and
            // pressed the Enter key, or the Place Details request failed.
            window.alert("No details available for input: '" + place.name + "'");
            return;
          }

          // If the place has a geometry, then present it on a map.
          if (place.geometry.viewport) {
            map.fitBounds(place.geometry.viewport);
          } else {
            map.setCenter(place.geometry.location);
            map.setZoom(17);  // Why 17? Because it looks good.
          }
          marker.setPosition(place.geometry.location);
          marker.setVisible(true);

          var address = '';
          if (place.address_components) {
            address = [
              (place.address_components[0] && place.address_components[0].short_name || ''),
              (place.address_components[1] && place.address_components[1].short_name || ''),
              (place.address_components[2] && place.address_components[2].short_name || '')
            ].join(' ');
          }

          infowindowContent.children['place-icon'].src = place.icon;
          infowindowContent.children['place-name'].textContent = place.name;
          infowindowContent.children['place-address'].textContent = address;
          infowindow.open(map, marker);
        });

        // Sets a listener on a radio button to change the filter type on Places
        // Autocomplete.
        function setupClickListener(id, types) {
          var radioButton = document.getElementById(id);
          radioButton.addEventListener('click', function() {
            autocomplete.setTypes(types);
          });
        }

        setupClickListener('changetype-all', []);
        setupClickListener('changetype-address', ['address']);
        setupClickListener('changetype-establishment', ['establishment']);
        setupClickListener('changetype-geocode', ['geocode']);

        document.getElementById('use-strict-bounds')
            .addEventListener('click', function() {
              console.log('Checkbox clicked! New state=' + this.checked);
              autocomplete.setOptions({strictBounds: this.checked});
            });

var marker;
  map.addListener('click', function(ex) {
      if(marker!=undefined)
         marker.setMap(null);
    marker = new google.maps.Marker({
    position: ex.latLng,
    map: map
  });
  map.panTo(ex.latLng);

    document.getElementById('lat').value=ex.latLng.lat();
    document.getElementById('lng').value=ex.latLng.lng();
  });
}

    </script>
        <script async defer
    src="https://maps.googleapis.com/maps/api/js?v=3.32&key=_API_KEY_GOES_HERE_&libraries=places&callback=initMap">
    </script>
    </body>
</html>
