<link rel="stylesheet" href="<?php LoadStatic(); echo GetStaticFile('sign_in','sign_in.css')?>">
<div class="container">

      <form class="form-signin" id="login">
        <h2 class="form-signin-heading">Please sign in</h2>
        <label for="inputEmail" class="sr-only">Username</label>
        <input type="text" id="inputEmail" class="form-control" placeholder="Username" required="" autofocus="">
        <label for="inputPassword" class="sr-only">Password</label>
        <input type="password" id="inputPassword" class="form-control" placeholder="Password" required="">
        <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
      </form>

    </div>
  <script>
    $(document).ready(function(){
      $("#login").submit(function(){
        var user = $("#inputEmail").val();
        var pass = $("#inputPassword").val();        
        $.ajax({
          method: "POST",
          url: "admin/login",
          dataType:"JSON",
          data: { user: user, pass: pass }
        })
          .done(function( msg ) {
            if(msg == "True"){
              window.location = "admin/dashboard"
            }else{
              alert("Username or password is invalid");
            }
          });
        return false;
      });

    });
  </script>