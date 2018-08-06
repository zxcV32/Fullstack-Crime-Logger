<html>
<head>
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
<link rel="stylesheet" href="main.css">
<script src="main.js"></script>
<title>Crime Logger</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<?php
include_once('connect.php');
session_start();
$name=$_SESSION['name'];
$email=$_SESSION['email'];

    if($email==NULL)
      die('
    <script>document.write(\'<div class="alert alert-danger" id="notify">Please <a href="index.php">login</a> and comeback </div>\');</script>
      ');

$strSQL = mysqli_query($connection,"select active from crimeuser where email='$email';");

    if(mysqli_fetch_row($strSQL)[0]==0){
             die('
             <script>document.write(\'<div class="alert alert-danger" id="notify">First activate your account from an email sent to you.<a href="index.php">go to home page</a></div>\');</script>
            ');
            }

            $strSQL = mysqli_query($connection,"select id,lat,lng from crimeuser;");
            $ids = array ();
             if(mysqli_num_rows($strSQL)>0){
                while ($row=mysqli_fetch_row($strSQL)){
                     $rowdata=array(
                        "id" => $row[0],
                        "lat" => $row[1],
                         "long" => $row[2],
                       );
                 array_push($ids,$rowdata);
                }
               }

if(isset($email)){
    $email=$_SESSION['email'];
    $name=$_SESSION['name'];
     if(isset($_POST['title'])){
         $title = mysqli_real_escape_string($connection,$_POST['title']);
         $lat= mysqli_real_escape_string($connection,$_POST['lat']);
         $long= mysqli_real_escape_string($connection,$_POST['long']);
         $time= mysqli_real_escape_string($connection,$_POST['time']);
         $desc = mysqli_real_escape_string($connection,$_POST['desc']);
         $by=$name;
         $desc ="Time of Crime: ".$time." as reported,<br>".$desc;
         $strSQL = mysqli_query($connection,"INSERT IGNORE INTO crimelog (title,latitude,longitude,description,postedby,yes) VALUES('$title','$lat','$long','$desc','$by',1)");
         if($strSQL){

              $strSQL = mysqli_query($connection,"SELECT LAST_INSERT_ID()");

             echo "<script>document.write('<div class=\"alert alert-success\" id=\"notify\">Crime Reported</div> ');
             document.cookie = '".
                 mysqli_fetch_row($strSQL)[0]
                 ."=yes';
             </script>";

             $emailid=explode(',', $_POST['nearby']);
            for( $i = 0; $i< count($emailid); $i++ ) {
                $strSQL = mysqli_query($connection,"select email from crimeuser where id='$emailid[$i]';");
                mail(mysqli_fetch_row($strSQL)[0],"New crime recorded in your area","$name has reported a crime in your area please view report at  <a href='http://localhost/crimelogger/searchcrime.php?keyword=$title'>This Link</a><br>and don't forget to vote.","From:Crime Logger <admin@local.host>\r\nContent-Type: text/html\r\n");
             }
            echo "<script>window.location = 'report.php';</script>";
         }
         else
             echo "<script>document.write('<div class=\"alert alert-success\" id=\"notify\">no good</div> ');</script>";
     }
}else{
    echo "Please Log In First";
    echo "<script>setTimeout(\"location.href = 'index.php';\",1000);</script>";
}

function alert($msg) {
    echo "<script type='text/javascript'>alert('$msg');</script>";
}
?>

</head>
<body>
    <nav class="navbar navbar-default">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="index.php">Home</a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
        <li ><a href="searchcrime.php">Search crime </a></li>
        <li class="active"><a href="report.php">Report Crime </a></li></ul>
      <form class="navbar-form navbar-right" action="searchcrime.php" method="get">
        <div class="form-group">
          <input type="text" class="form-control" placeholder="Search" name="keyword">
        </div>
        <button type="submit" class="btn btn-default">Submit</button>
      </form>

    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>
    <p>&nbsp;</p>
    <div class="container">
	   <div class="row">
           <div class="col-md-8 col-md-offset-2">
               <h2>Report a crime here</h2>
               <form method="post" name="reporter" action="report.php">
                   <div class="form-group">
                        <label for="usr">Title:*</label>
                        <input type="text" class="form-control" id="usr" name="title" required>
                    </div>
                    <div class="form-group form-inline">
                        <label for="time">Date/Time of Crime:*</label>
                        <input type="datetime-local" class="form-control" id="time" name="time" required>
                    </div>
                <label>Location of crime:*</label>
                   <div class="form-group form-inline">
                        <label for="lat">Latitude:</label>
                        <input type="number" step="any" class="form-control" id="lat" name="lat" required>
                        <label for="lat">Longitude:</label>
                        <input type="number" step="any" class="form-control" id="long" name="long" required>
                    </div>

                   <div class="form-group">
                        <label for="comment">Description:*</label>
                       <textarea class="form-control" rows="5" id="description" name="desc" required></textarea>
                   </div>
                   <input type="submit" value="Submit" class="btn btn-default">
                   <p>&nbsp;</p>

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
                   <input type="hidden" id="nearby" name="nearby">
    <div id="map"></div>
    <div id="infowindow-content">
      <img src="" width="16" height="16" id="place-icon">
      <span id="place-name"  class="title"></span><br>
      <span id="place-address"></span>
    </div>

    <script>

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

       var obj = <?php echo json_encode($ids); ?>;

      var ids=[];
     for(var i in obj){
         if(google.maps.geometry.spherical.computeDistanceBetween(ex.latLng,new google.maps.LatLng(obj[i]['lat'],obj[i]['long'])) < 1500)
              ids.push(obj[i]['id'])
      }
      console.log(ids);
      document.getElementById('nearby').value=ids.join(',');
    document.getElementById('lat').value=ex.latLng.lat();
    document.getElementById('long').value=ex.latLng.lng();
  });
}
        var xx=new Date();
        var options = {
    weekday: "long", year: "numeric", month: "short",
    day: "numeric", hour: "2-digit", minute: "2-digit"
};

    </script>

               </form>
           </div>
        </div>
    </div>
    <script async defer
    src="https://maps.googleapis.com/maps/api/js?v=3.32&key=_API_KEY_GOES_HERE_s&libraries=places,geometry&callback=initMap">
    </script>
</body>
</html>
