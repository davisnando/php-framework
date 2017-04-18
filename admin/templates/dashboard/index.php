
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