
  <link rel="stylesheet" href="<?php LoadStatic(); echo GetStaticFile('board','board.css')?>">
    <nav class="navbar navbar-toggleable-md navbar-inverse fixed-top bg-inverse">
      <button class="navbar-toggler navbar-toggler-right hidden-lg-up" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <a class="navbar-brand" href="#">Dashboard</a>

      <div class="collapse navbar-collapse" id="navbarsExampleDefault">
        <ul class="navbar-nav mr-auto">
          <li class="nav-item active">
            <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">Settings</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="logout">Logout</a>
          </li>
        </ul>
      </div>
    </nav>

    <div class="container-fluid">
      <div class="row">
        <nav class="col-sm-3 col-md-2 hidden-xs-down bg-faded sidebar">
          <ul class="nav nav-pills flex-column">
            <li class="nav-item">
              <a class="nav-link active" href="#">Overview <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#">Reports</a>
            </li>
          </ul>
        </nav>

        <main class="col-sm-9 offset-sm-3 col-md-10 offset-md-2 pt-3">
          <h1>Dashboard</h1>
          <h2>Users</h2>
                <?php
                  $db = new model(DB_Database);
                  $db->prepare("SELECT Users.idUsers,Users.email,Users.username, Personal.* FROM Users JOIN Personal on Users.idPersonal=Personal.idPersonal WHERE Users.username=:user");
                  $items = explode('/',$_GET['path']);
                  $user = $items[count($items) - 1];
                  $db->bind(":user",$user);
                  $result = $db->GetAll();
                  $i = 1;
                ?>
            <form method="POST" id="Changes">
                <br>
                <?php
                  $i = 0; 
                  $allKeys = array_keys($result[0]);
                  if(count($result) != 1){
                    header("location: ../dashboard");
                  }
                ?>
                <?php foreach($result[0] as $item): ?>
                   <?php 
                     $itemkey  = $allKeys[$i];
                     $i++;
                   ?>
                   <?php if($itemkey != "idUsers" && $itemkey != "idPersonal"): ?>
                     <input type="" class="form-control" style="width: 30%;" name="<?php echo $itemkey ?>" value="<?php echo $item; ?>">
                     <br>
                   <?php endif;?>
                   <?php if($itemkey == "idUsers"):?>
                     <input type="hidden" name="idUsers" value="<?php echo $item  ?>">
                   <?php endif;?>
                <?php endforeach;?>
              <input type="submit" class="btn btn-success" value="save">
            </form>
          </div>
        </main>
      </div>
    </div>
    <script>
    $("#Changes").submit(function(){
      var data = $('#Changes').serialize();
      console.log(data);
      return false;
      
    });
    </script>