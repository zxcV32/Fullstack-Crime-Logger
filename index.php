<html>
<head>
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
<link rel="stylesheet" href="main.css">
<title>Crime Logger</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<?php
include_once('connect.php');

if(isset($_COOKIE['email']) && isset($_COOKIE['name'])) {
        session_start();
        $_SESSION['email']=$_COOKIE['email'];
        $_SESSION['name']=$_COOKIE['name'];
}

if(isset($_POST['submit'])){
    if($_POST['submit']=="LogIn"){
        $email = mysqli_real_escape_string($connection,$_POST['email']);
        $password = mysqli_real_escape_string($connection,$_POST['password']);
        $remember = $_POST['remember'];

        $strSQL = mysqli_query($connection,"select name from crimeuser where email='".$email."' and password='".md5($password)."'");
        $Results = mysqli_fetch_array($strSQL);
        if(count($Results)>=1){
            if(isset($remember)){
                setcookie('email',$email,time()+60*60*6);
                setcookie('name',$Results['name'],time()+60*60*6);
            }

            session_start();
            $_SESSION['email']=$email;
            $_SESSION['name']=$Results['name'];
            header('location:report.php');
        }
        else{
            $message = "Invalid email or password!!";
            alert($message);
        }
    }
    elseif($_POST['submit']=="Register"){
        $name       = mysqli_real_escape_string($connection,$_POST['name']);
        $email      = mysqli_real_escape_string($connection,$_POST['email']);
        $password   = mysqli_real_escape_string($connection,$_POST['password']);
        $query = "SELECT email FROM crimeuser where email='".$email."'";
        $result = mysqli_query($connection,$query);
        $numResults = mysqli_num_rows($result);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)){ // Validate email address
            $message =  "Invalid email address please type a valid email!!";
        }
        elseif($numResults>=1){
            die('
             <script>document.write(\'<div class="alert alert-danger" id="notify">'.$email.' Email already exist!! <a href="index.php">Go back</a></div>\');</script>
            ');
        }
        else{
            $verification=generateRandomString();
            mysqli_query($connection,"insert into crimeuser(name,email,password,verification) values('".$name."','".$email."','".md5($password)."','$verification')");
            echo '
             <script>document.write(\'<div class="alert alert-success" id="notify">Signup Sucessfully!! Please check your email to verify and setup your account!</div>\');</script>
            ';
            mail($email,"Please confirm and setup your account","to continue: <a href='http://localhost/crimelogger/setup.php?key=$verification&name=$name'>Click Here</a>","From:Crime Logger <admin@local.host>\r\nContent-Type: text/html\r\n");
        }
    }
}
function generateRandomString($length = 16) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
function alert($msg) {
    echo "<script type='text/javascript'>alert('$msg');</script>";
}
?>


</head>
    <body><p>&nbsp;</p><p>&nbsp;</p>
<div class="container">
	<div class="row">
        <div class="col-md-6 col-md-offset-3">
            <h1 style="margin-top:-1em;text-align: center;">Welcome to Crime logger</h1>
            <p>&nbsp;</p>
            <h4>Search Crimes by key words</h4>
            <form action="searchcrime.php" method="get">
                <div id="custom-search-input">
                <div class="input-group col-md-12">
                    <input type="text" class="form-control input-lg" placeholder="keyword" name= "keyword" required/>
                    <span class="input-group-btn">
                        <button class="btn btn-info btn-lg" style="padding:0.7em;" type="submit">
                            <i class="glyphicon glyphicon-search"></i>
                        </button>
                    </span>
                </div>
            </div>
            </form>
        </div>
	</div>
</div>
         <p>&nbsp;</p>
<div class="container">

    	<div class="row">

            <div class="col-md-8 hidden-sm hidden-xs">
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

    <script>
        var localLat=30.324321;
        var localLong=78.041822;
      function initMap() {
        var map = new google.maps.Map(document.getElementById('map'), {
          center: {lat: localLat, lng: localLong},
          zoom: 13
        });

    if(navigator.geolocation){
        navigator.geolocation.getCurrentPosition(function(position,inError){
            localLat=position.coords.latitude;
            localLong= position.coords.longitude;
             map.setCenter({lat: localLat, lng: localLong});
            });
        }


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


    var marker,i;

var xmlhttp = new XMLHttpRequest();
xmlhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
        var data=JSON.parse(this.responseText);
       for(i in data){
        marker = new google.maps.Marker({
        position: new google.maps.LatLng(+data[i]['lat'], data[i]['long']),
        map: map,
        title: data[i]['label']
      });
       }
    }
};
xmlhttp.open("GET", "crimelist.php", true);
xmlhttp.send();

}
    </script>
            </div>
			<div class="col-md-4" id="loginsystem">
                <h4>To report a crime and get other features</h4>
				<div class="panel panel-login">
					<div class="panel-heading">
						<div class="row">
							<div class="col-xs-6">
								<a href="#" class="active" id="login-form-link">Login</a>
							</div>
							<div class="col-xs-6">
								<a href="#" id="register-form-link">Register</a>
							</div>
						</div>
						<hr>
					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-lg-12">
								<form id="login-form" action="" method="post" role="form" style="display: block;">
									<div class="form-group">
										<input type="text" name="email" id="username" tabindex="1" class="form-control" placeholder="E-mail" value="">
									</div>
									<div class="form-group">
										<input type="password" name="password" id="password" tabindex="2" class="form-control" placeholder="Password">
									</div>
									<div class="form-group text-center">
										<input type="checkbox" tabindex="3" class="" name="remember" id="remember">
										<label for="remember"> Remember Me</label>
									</div>
									<div class="form-group">
										<div class="row">
											<div class="col-sm-6 col-sm-offset-3">
												<input type="submit" name="submit" id="login-submit" tabindex="4" class="form-control btn btn-login" value="LogIn">
											</div>
										</div>
									</div>
									<div class="form-group">
										<div class="row">
											<div class="col-lg-12">
												<div class="text-center">
													<a href="#" tabindex="5" class="forgot-password">Forgot Password?</a>
												</div>
											</div>
										</div>
									</div>
								</form>
								<form id="register-form" action="" method="post" role="form" style="display: none;">
									<div class="form-group">
										<input type="text" name="name" id="username" tabindex="1" class="form-control" placeholder="Name" value="">
									</div>
									<div class="form-group">
										<input type="email" name="email" id="email" tabindex="1" class="form-control" placeholder="Email Address" value="">
									</div>
									<div class="form-group">
										<input type="password" name="password" id="password" tabindex="2" class="form-control" placeholder="Password">
									</div>
									<div class="form-group">
										<div class="row">
											<div class="col-sm-6 col-sm-offset-3">
												<input type="submit" name="submit" id="register-submit" tabindex="4" class="form-control btn btn-register" value="Register">
											</div>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
                    <script>

        var email ;
        var name ;

        var xhttp = new XMLHttpRequest();
         xhttp.onreadystatechange = function() {
             if (this.readyState == 4 && this.status == 200) {
                email=xhttp.responseText;
            }
         };
                xhttp.open("GET", "sessionemail.php", true);
                xhttp.send();

        var xhttp1 = new XMLHttpRequest();
         xhttp1.onreadystatechange = function() {
             if (this.readyState == 4 && this.status == 200) {
                name=xhttp1.responseText;
            }
         };
                xhttp1.open("GET", "sessionname.php", true);
                xhttp1.send();

        setTimeout(function() {
        if (email != "" && name != "") {
            var text="Welcome: "+name+"<br>";
            text+="<a href='report.php'>Report a crime here</a><br>";
            text+="<a href='logout.php'>logout</a><br>";
           document.getElementById('loginsystem').innerHTML=text;
        }
        }, 500);

            </script>
				</div>
			</div>
		</div>
	</div>
    <script src="main.js"></script>
<!--ENTER YOUR OWN GOOGLE MAPS API KEY-->
    <script src="https://maps.googleapis.com/maps/api/js?key=_API_KEY_GOES_HERE_&libraries=places&callback=initMap"
        async defer></script>
</body>
</html>
