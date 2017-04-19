
  <link rel="stylesheet" href="<?php LoadStatic(); echo GetStaticFile('board','board.css')?>">

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
          <button class="btn btn-xs btn-info" id='Addbtn' href="#" role="button" data-toggle="modal" data-target="#CreateAccountModel">Add user</button>
          
          <h2>Users</h2>
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Username</th>
                  <th>Email</th>
                  <th>Firstname</th>
                  <th>Lastname</th>
                </tr>
              </thead>
              <tbody>
                <?php
                  $db = new model(DB_Database);
                  $db->prepare("SELECT Users.username,Users.email,Personal.firstname,Personal.lastname FROM Users JOIN Personal ON Users.idPersonal=Personal.idPersonal");
                  $result = $db->GetAll();
                  $i = 1;
                ?>
                <?php  foreach($result as $items): ?>
                <?php $user = $items['username'];?>
                <tr onclick="window.location = 'dashboard/<?php echo $user;?>'">
                  <td><?php echo $i; $i++; ?></td>
                  <?php foreach($items as $item): ?>
                  <td><?php echo $item;?></td>
                  <?php endforeach;?>

                </tr>
                <?php endforeach;?>
              </tbody>
            </table>
          </div>
        </main>
      </div>
    </div>
    <div class="modal fade" id="CreateAccountModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Add user</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
          <form id="AddUserForm">
              <?php 
                $db->prepare("SHOW COLUMNS FROM Users");
                $usertable = $db->GetAll();
                $db->prepare("SHOW COLUMNS FROM Personal");
                $personaltable = $db->GetAll();
                $tables = array_merge($usertable,$personaltable);
                foreach($tables as $table):
                if($table['Key'] == ""):        
                  $tablename = $table['Field'];
                  $type = "text";
                  if($tablename == "password"){
                    $type = "password";
                  }
              ?>
                <input type="<?php echo $type; ?>" class="form-control" name="<?php echo $tablename; ?>" placeholder="<?php echo $tablename; ?>">
                <br>
              <?php 
              endif;
              endforeach;?>
            </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <input type="submit" class="btn btn-primary" value="Save changes" />
          </div>
          </form>

        </div>
      </div>
    </div>
    <script>
      $("#AddUserForm").submit(function(){
        var Getdata  = $("#AddUserForm").serialize();
        $('#AddUserForm').find('input:text').val(''); 
        $('#AddUserForm').find('input:password').val(''); 
        $.ajax({
          method: "POST",
          url: "/admin/create",
          data: Getdata
        })
          .done(function( msg ) {
            console.log(msg);
              if(msg == "Done"){
                alert("Saved");
              }else if(msg == "Username"){
                alert("Username already exist!!");
              }
              else{
                alert("something goes wrong!");
              }
                $('#CreateAccountModel').modal('toggle');

          });
        return false;
      });
    </script>