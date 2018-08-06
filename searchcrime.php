<?php
include('connect.php');
    if(isset($_GET['keyword'])){
    $keyword=mysqli_real_escape_string($connection,$_GET['keyword']);
    $strSQL = mysqli_query($connection,"select * from crimelog WHERE concat(crime_id,description,title) REGEXP '".$keyword."';");
    $data = array ();
        if(mysqli_num_rows($strSQL)>0){
        while ($row=mysqli_fetch_row($strSQL)){
           $rowdata=array(
            "id" => $row[0],
            "label" => $row[1],
            "lat" => $row[2],
            "long" => $row[3],
            "time" => $row[4],
            "description" => $row[5],
            "reportedby" => $row[6],
            "dontknow" =>  $row[7],
            "yes" => $row[8],
            "no" => $row[9]
           );
        array_push($data,$rowdata);
      }
        $myJSON = json_encode($data);
        }
    }
?>

<html>
<head>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
<link rel="stylesheet" href="main.css">
    <script src="main.js"></script>
<title>Crime Logger-Search Results</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />

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
        <li class="active"><a href="searchcrime.php?keyword=">Search crime </a></li>
        <li ><a href="report.php">Report Crime </a></li></ul>
      <form class="navbar-form navbar-right" action="searchcrime.php" method="get">
        <div class="form-group">
          <input type="text" class="form-control" placeholder="Search" name="keyword">
        </div>
        <button type="submit" class="btn btn-default">Submit</button>
      </form>

    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>

<div class="container">
	<div class="row">
        <div class="col-md-3">
            <p>&nbsp;</p>
            <h3 style="margin-top:-1em;text-align: center;">Results</h3>
            <p>&nbsp;</p>
            <div class="list-group">

                <script>
                    var i;
                    var localLat=30.324321;
                    var localLong=78.041822;
                    var map ;
                    var marker;
                    function initMap() {
                            map = new google.maps.Map(document.getElementById('map'), {
                                zoom: 10,
                                center: {lat: localLat, lng: localLong}
                        });
                         if(navigator.geolocation){
                            navigator.geolocation.getCurrentPosition(function(position,inError){
                            localLat=position.coords.latitude;
                            localLong= position.coords.longitude;
                            map.setCenter({lat: localLat, lng: localLong});
                            });
                         }
                     }
                        try {
                            var data=JSON.parse('<?php echo $myJSON; ?>');
                        }
                        catch(err) {
                            document.write('<div class="alert alert-info" id="notify">no crime matched your search term</div> ');
                        }

                    for(i in data){
                        document.write('<a class="list-group-item" id="'+i+'" onclick="description(\''+i+'\')" ><p class="list-group-item-text">');
                           document.write(data[i]['label']);
                        document.write('</p></a>');
                    }

                    function description(id){
                        document.getElementById('crime').innerHTML=data[id]['label'];
                        document.getElementById('time').innerHTML="Reported On: " +data[id]['time'];
                        document.getElementById('desc').innerHTML=data[id]['description'];
                        document.getElementById('user').innerHTML=data[id]['reportedby'];
                        document.getElementById('setHeading').innerHTML="Incident Description:";
                        if((getCookie(data[id]['id'])=='')&&(getCookie('PHPSESSID'))){
                                document.getElementById("vote").innerHTML='<h4>Please confirm the crime in this area</h4><button type="button" class="btn" onclick="dontknow('+data[id]['id']+');">Don\'t know</button> <button type="button" class="btn btn-primary" onclick="yes('+data[id]['id']+');">Yes</button> <button type="button" class="btn btn-danger" onclick="no('+data[id]['id']+');">No</button>';
                        }
                            else
                                document.getElementById("vote").innerHTML='';

                        var ctx = document.getElementById('myChart').getContext('2d');
                        let myPieChart = new Chart(ctx,{
                            type: 'doughnut',
                            data:{
                                labels:['Don\'t know','Yes','No'],
                                datasets:[
                                    {
                                        label:'votes',
                                        backgroundColor:['rgb(171,170,178)','rgb(85,170,221)','rgb(0,128,128)'],
                                        data:[data[id]['dontknow'],data[id]['yes'],data[id]['no']]
                                    }
                                ]},
                            options:{
                                animation:{
                                    animateScale:true
                                }
                            }
                        });

                        if(marker!=undefined)
                            marker.setMap(null);
                        latlang={lat: parseFloat(data[id]['lat']), lng: parseFloat(data[id]['long'])};
                        marker = new google.maps.Marker({
                           position: latlang,
                           map: map,
                           title: data[id]['label'],
                        });
                    map.setCenter(latlang);
                    map.setZoom(12);
                    }
                    function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}
                </script>
            </div>
        </div>
        <div class="col-md-9">
            <h2 id="crime"></h2>
            <div id="map"></div>
            <p>&nbsp;</p>
            <small id="time"></small>
            <h2 id="setHeading"></h2>
            <p id="desc"></p>
            <blockquote class="blockquote"><footer  class="blockquote-footer" id="user">Your Location</footer></blockquote>
             <div class="row">
    <div class="col-md-6">
         <canvas id="myChart"></canvas>
    </div>
    <div class="col-md-6">
    <div id="vote"></div>
    </div>
    </div>
        </div>
	</div>

</div>


    <script>
</script>


        <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=_API_KEY_GOES_HERE_&callback=initMap">
    </script>

</body>
</html>
