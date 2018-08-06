$(function() {
    $('#login-form-link').click(function(e) {
		$("#login-form").delay(100).fadeIn(100);
 		$("#register-form").fadeOut(100);
		$('#register-form-link').removeClass('active');
		$(this).addClass('active');
		e.preventDefault();
	});
	$('#register-form-link').click(function(e) {
		$("#register-form").delay(100).fadeIn(100);
 		$("#login-form").fadeOut(100);
		$('#login-form-link').removeClass('active');
		$(this).addClass('active');
		e.preventDefault();
	});

});

function dontknow(crimeid){
      var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      document.getElementById("vote").innerHTML="your response has been recorded as don't know";
        document.cookie = crimeid+"=dontknow";
        
    }
  };
  xhttp.open("POST", "vote.php", true);
  xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhttp.send("vote=dontknow&id="+crimeid);
}
function yes(crimeid){
      var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      document.getElementById("vote").innerHTML="your response has been recorded as don't know";
        document.cookie = crimeid+"=yes";
    }
  };
  xhttp.open("POST", "vote.php", true);
  xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhttp.send("vote=yes&id="+crimeid);
}
function no(crimeid){
      var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      document.getElementById("vote").innerHTML="your response has been recorded as don't know";
        document.cookie =crimeid+"=no";
    }
  };
  xhttp.open("POST", "vote.php", true);
  xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhttp.send("vote=no&id="+crimeid);
}