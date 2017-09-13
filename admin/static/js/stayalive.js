setInterval(function(){ 

         var xmlhttp = new XMLHttpRequest();
         xmlhttp.open('GET', '/stillAlive', true);
         xmlhttp.send();
         }, 10000);
